#!/usr/bin/env python3
# sync_pagamentos.py
"""
Sync result of complex SQL Server query into MySQL table tb_pagamentos_processados.

Usage:
  python sync_pagamentos.py
or configure environment variables and run as daemon/cron.
"""

import os
import sys
import time
import logging
from datetime import datetime, date
from dotenv import load_dotenv

load_dotenv()  # .env in same folder (or set env vars)

# SQL Server connection (ODBC)
SQLSRV_DRIVER = os.getenv("ODBC_DRIVER", "ODBC Driver 17 for SQL Server")
SQLSRV_SERVER = os.getenv("EASY_HOST", "192.168.1.3")
SQLSRV_PORT   = os.getenv("EASY_PORT", "1433")
SQLSRV_DB     = os.getenv("EASY_DATABASE", "EASYCOLLECTOR")
SQLSRV_USER   = os.getenv("EASY_USERNAME", "ti")
SQLSRV_PASS   = os.getenv("EASY_PASSWORD", "Ti_2025@Ver.")

# MySQL connection (Laravel DB)
MYSQL_HOST = os.getenv("DB_HOST", "127.0.0.1")
MYSQL_PORT = int(os.getenv("DB_PORT", "3307"))
MYSQL_DB   = os.getenv("DB_DATABASE", "sistema")
MYSQL_USER = os.getenv("DB_USERNAME", "laravel")
MYSQL_PASS = os.getenv("DB_PASSWORD", "laravel")

# default dates (can be taken from env or parameters)
DT_INICIAL = os.getenv("SYNC_DT_INICIAL", "2025-11-19")
DT_FINAL   = os.getenv("SYNC_DT_FINAL", "2025-12-15")

# loop interval seconds (for near realtime). Set to 60 for once per minute.
LOOP_INTERVAL = int(os.getenv("SYNC_INTERVAL_SECONDS", "60"))

# logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)]
)
logger = logging.getLogger("sync_pagamentos")

# heavy T-SQL: final select only returns desired columns.
# Important: remove "USE ... GO" since pyodbc does not accept GO. We'll fully qualify objects and run in the DB.
TSQL = f"""
SET NOCOUNT ON;

DECLARE @DT_INICIAL DATE = CONVERT(date, '{DT_INICIAL}', 23);
DECLARE @DT_FINAL   DATE = CONVERT(date, '{DT_FINAL}', 23);

-- Build temporary tables and final select
-- NOTE: we fully replicate the logic you provided but avoid 'USE' and 'GO'
-- CAPACITY
IF OBJECT_ID('tempdb..#TB_TEMP_CAPACITY') IS NOT NULL DROP TABLE #TB_TEMP_CAPACITY;
SELECT
      NU_CPF_CNPJ = CAST(IDNUMBER AS BIGINT)
    , NM_NOME = NAME
    , DT_ADMISSAO = DATEADMISSION
    , CARTEIRA = UPPER(DEPARTMENT)
    , POSICAO = UPPER(POSITION)
    , TIPO = UPPER(TYPECONTRACT)
    , META_PONTO = CAST(NULL AS DECIMAL(18,2))
INTO #TB_TEMP_CAPACITY
FROM [DB_MIS].[dbo].[TB_CAPACITY_SOLIDES] WITH (NOLOCK)
WHERE DT_REF = @DT_INICIAL
  AND DATEDISMISSAL IS NULL;

-- PAGTO ANALITICO
IF OBJECT_ID('tempdb..#TB_TEMP_PAGAMENTOS_ANALITICO') IS NOT NULL DROP TABLE #TB_TEMP_PAGAMENTOS_ANALITICO;
SELECT
      NU_CPF_CNPJ = U.NM_CPF
    , U.NM_USUARIO
    , F.ID_CLIENTE
    , F.ID_FATURA
    , DT_PAGAMENTO = CAST(F.DT_PAGAMENTO AS DATE)
    , F.VL_PAGO
INTO #TB_TEMP_PAGAMENTOS_ANALITICO
FROM dbo.TB_FATURA F WITH (NOLOCK)
JOIN dbo.TB_ACORDO A WITH (NOLOCK)
  ON F.ID_CLIENTE = A.ID_CLIENTE
 AND F.ID_ACORDO = A.ID_ACORDO
LEFT JOIN dbo.TB_USUARIO U WITH (NOLOCK)
  ON A.ID_USUARIO = U.ID_USUARIO
WHERE CAST(F.DT_PAGAMENTO AS DATE) BETWEEN @DT_INICIAL AND @DT_FINAL
  AND F.NU_PARCELA = 1;

-- IDENTIFICA ACOES PREVENTIVO
IF OBJECT_ID('tempdb..#TB_TEMP_PAGAMENTOS_ANALITICO_PREVENTIVO') IS NOT NULL DROP TABLE #TB_TEMP_PAGAMENTOS_ANALITICO_PREVENTIVO;
SELECT
      C.NM_CEDENTE
    , CA.ID_CLIENTE
    , PA.ID_FATURA
    , PA.DT_PAGAMENTO
    , CA.ID_CLIENTE_ACAO
    , DT_ACAO = CAST(CA.DT_ACAO AS DATE)
    , CA.ID_USUARIO
    , NU_CPF_CNPJ = U.NM_CPF
    , U.NM_USUARIO
    , PA.VL_PAGO
    , NU_ORDEM_PREVENTIVO = ROW_NUMBER() OVER (PARTITION BY CA.ID_CLIENTE, PA.ID_FATURA ORDER BY ID_CLIENTE_ACAO DESC)
INTO #TB_TEMP_PAGAMENTOS_ANALITICO_PREVENTIVO
FROM dbo.TB_CLIENTE_ACAO CA WITH (NOLOCK)
JOIN #TB_TEMP_PAGAMENTOS_ANALITICO PA ON CA.ID_CLIENTE = PA.ID_CLIENTE
JOIN dbo.TB_USUARIO U WITH (NOLOCK) ON CA.ID_USUARIO = U.ID_USUARIO
JOIN dbo.TB_CLIENTE CL WITH (NOLOCK) ON CA.ID_CLIENTE = CL.ID_CLIENTE
JOIN dbo.TB_CEDENTE C WITH (NOLOCK) ON CL.ID_CEDENTE = C.ID_CEDENTE
WHERE CA.ID_ACAO IN (SELECT ID_ACAO FROM dbo.TB_ACAO WITH (NOLOCK) WHERE NM_ACAO LIKE '%PREVENTIVO POSITIVO%')
  AND CAST(CA.DT_ACAO AS DATE) BETWEEN DATEADD(DD, -5, @DT_INICIAL) AND @DT_FINAL
  AND U.ID_EQUIPE = 178
  AND CASE WHEN DATEDIFF(DD, PA.DT_PAGAMENTO, CAST(CA.DT_ACAO AS DATE)) BETWEEN -5 AND 0 THEN 1 END = 1;

DELETE FROM #TB_TEMP_PAGAMENTOS_ANALITICO_PREVENTIVO WHERE NU_ORDEM_PREVENTIVO > 1;

-- PAGTOS TOTAL
IF OBJECT_ID('tempdb..#TB_TEMP_PAGAMENTOS') IS NOT NULL DROP TABLE #TB_TEMP_PAGAMENTOS;
SELECT
      NU_CPF_CNPJ
    , NM_USUARIO
    , QTD_PAGAMENTOS = COUNT(1)
    , VL_PAGAMENTO = SUM(VL_PAGO)
    , DT_ULT_PAGAMENTO = CAST(MAX(DT_PAGAMENTO) AS DATE)
INTO #TB_TEMP_PAGAMENTOS
FROM #TB_TEMP_PAGAMENTOS_ANALITICO F WITH (NOLOCK)
GROUP BY NU_CPF_CNPJ, NM_USUARIO;

-- PAGTOS PREVENTIVO (33%)
IF OBJECT_ID('tempdb..#TB_TEMP_PAGAMENTOS_PREVENTIVO') IS NOT NULL DROP TABLE #TB_TEMP_PAGAMENTOS_PREVENTIVO;
SELECT
      NU_CPF_CNPJ
    , NM_USUARIO
    , QTD_PAGAMENTOS = COUNT(1)
    , VL_PAGAMENTO = CAST(SUM(VL_PAGO) * 0.33 AS DECIMAL(18,2))
    , DT_ULT_PAGAMENTO = CAST(MAX(DT_PAGAMENTO) AS DATE)
INTO #TB_TEMP_PAGAMENTOS_PREVENTIVO
FROM #TB_TEMP_PAGAMENTOS_ANALITICO_PREVENTIVO
GROUP BY NU_CPF_CNPJ, NM_USUARIO;

-- Remove duplicates from total and insert the preventive ones
DELETE FROM #TB_TEMP_PAGAMENTOS
WHERE NU_CPF_CNPJ IN (SELECT NU_CPF_CNPJ FROM #TB_TEMP_PAGAMENTOS_PREVENTIVO);

INSERT INTO #TB_TEMP_PAGAMENTOS
SELECT * FROM #TB_TEMP_PAGAMENTOS_PREVENTIVO;

-- Update capacity where preventivo
UPDATE #TB_TEMP_CAPACITY
SET CARTEIRA = 'PREVENTIVO'
WHERE NU_CPF_CNPJ IN (SELECT NU_CPF_CNPJ FROM #TB_TEMP_PAGAMENTOS_PREVENTIVO);

-- PAGTOS CREDZ (external db reference)
IF OBJECT_ID('tempdb..#TB_TEMP_PAGAMENTOS_CREDZ') IS NOT NULL DROP TABLE #TB_TEMP_PAGAMENTOS_CREDZ;
SELECT
      NU_CPF_CNPJ = NU_CPF_CNPJ_OPERADOR
    , NM_USUARIO = OPERADOR
    , QTD_PAGAMENTOS = COUNT(1)
    , VL_PAGAMENTO = SUM(VL_PAGTO)
    , DT_ULT_PAGAMENTO = CAST(MAX(DT_PAGAMENTO) AS DATE)
INTO #TB_TEMP_PAGAMENTOS_CREDZ
FROM [DB_PLANEJAMENTO].[dbo].[TB_CARGA_PAGAMENTO_CREDZ] WITH (NOLOCK)
WHERE DT_PAGAMENTO BETWEEN @DT_INICIAL AND @DT_FINAL
  AND NU_PARCELA = 1
  AND NU_CPF_CNPJ_OPERADOR IN (SELECT NU_CPF_CNPJ FROM #TB_TEMP_CAPACITY WHERE CARTEIRA = 'CREDZ')
GROUP BY NU_CPF_CNPJ_OPERADOR, OPERADOR;

DELETE FROM #TB_TEMP_PAGAMENTOS
WHERE NU_CPF_CNPJ IN (SELECT NU_CPF_CNPJ FROM #TB_TEMP_PAGAMENTOS_CREDZ WHERE NU_CPF_CNPJ IN (SELECT NU_CPF_CNPJ FROM #TB_TEMP_CAPACITY WHERE CARTEIRA = 'CREDZ'));

INSERT INTO #TB_TEMP_PAGAMENTOS
SELECT * FROM #TB_TEMP_PAGAMENTOS_CREDZ
WHERE NU_CPF_CNPJ IN (SELECT NU_CPF_CNPJ FROM #TB_TEMP_CAPACITY WHERE CARTEIRA = 'CREDZ');

-- Update metas
UPDATE #TB_TEMP_CAPACITY
SET META_PONTO = CASE
    WHEN CARTEIRA = 'BAUDUCCO' THEN 5710
    WHEN CARTEIRA = 'C&A' THEN 160
    WHEN CARTEIRA = 'CEMIG' THEN 1943.63
    WHEN CARTEIRA = 'CREDZ' THEN 160
    WHEN CARTEIRA = 'DIGITAL' THEN 900
    WHEN CARTEIRA = 'DM' THEN 160
    WHEN CARTEIRA = 'MOVIDA PJ' THEN 3200
    WHEN CARTEIRA = 'MOVIDA RAC' THEN 550
    WHEN CARTEIRA = 'MOVIDA ZEROKM' THEN 2600
    WHEN CARTEIRA = 'PORTO CDC' THEN 2500
    WHEN CARTEIRA = 'PORTO SEM GARANTIA' THEN 250
    WHEN CARTEIRA = 'PORTO JURIDICO' THEN 820
    WHEN CARTEIRA = 'SERASA' THEN 750
    WHEN CARTEIRA = 'SERASA FEE' THEN 80
    WHEN CARTEIRA = 'SERASA FIXA' THEN 730
    WHEN CARTEIRA = 'SERASA REG' THEN 3500
    WHEN CARTEIRA = 'TORRA TORRA' THEN 120
    WHEN CARTEIRA = 'VILA VITORIA' THEN 2500
    WHEN CARTEIRA = 'SICREDI' THEN 535.77
    WHEN CARTEIRA = 'SUCUMBENCIA JURIDICO' THEN 255.03
    WHEN CARTEIRA = 'PORTO JURIDICO' THEN 820
    WHEN CARTEIRA = 'SICREDI JURIDICO' THEN 535.77
    WHEN CARTEIRA = 'PREVENTIVO' THEN 147.67
    ELSE 0
END;

-- FINAL RESULT
SELECT
      C.CARTEIRA
    , P.NU_CPF_CNPJ
    , P.NM_USUARIO
    , P.VL_PAGAMENTO
    , VERRESCHI_CASH = CAST(P.VL_PAGAMENTO / NULLIF(C.META_PONTO,0) AS INT)
    , C.META_PONTO
FROM #TB_TEMP_PAGAMENTOS P
INNER JOIN #TB_TEMP_CAPACITY C ON P.NU_CPF_CNPJ = C.NU_CPF_CNPJ
WHERE C.META_PONTO NOT IN (0)
ORDER BY C.CARTEIRA ASC, CAST(P.VL_PAGAMENTO / NULLIF(C.META_PONTO,0) AS INT) DESC;
"""

# ---------- Helper functions ----------
def connect_sqlsrv():
    try:
        import pyodbc
    except ImportError:
        logger.error("pyodbc not installed. Run: pip install pyodbc")
        raise
    conn_str = (
        f"DRIVER={{{SQLSRV_DRIVER}}};"
        f"SERVER={SQLSRV_SERVER},{SQLSRV_PORT};"
        f"DATABASE={SQLSRV_DB};UID={SQLSRV_USER};PWD={SQLSRV_PASS};"
    )
    return pyodbc.connect(conn_str, autocommit=True)

def connect_mysql():
    try:
        import mysql.connector
    except ImportError:
        logger.error("mysql-connector-python not installed. Run: pip install mysql-connector-python")
        raise
    return mysql.connector.connect(
        host=MYSQL_HOST,
        port=MYSQL_PORT,
        database=MYSQL_DB,
        user=MYSQL_USER,
        password=MYSQL_PASS,
        autocommit=False,
        charset='utf8mb4'
    )

def upsert_into_mysql(rows, process_date):
    if not rows:
        logger.info("Nenhum registro para inserir.")
        return 0
    conn = connect_mysql()
    cursor = conn.cursor()
    # prepare statement with ON DUPLICATE KEY UPDATE (unique key: carteira+nu_cpf_cnpj+data_processamento)
    sql = """
    INSERT INTO tb_pagamentos_processados
      (carteira, nu_cpf_cnpj, nm_usuario, vl_pagamento, verreschi_cash, meta_ponto, data_processamento)
    VALUES (%s, %s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE
      nm_usuario = VALUES(nm_usuario),
      vl_pagamento = VALUES(vl_pagamento),
      verreschi_cash = VALUES(verreschi_cash),
      meta_ponto = VALUES(meta_ponto),
      data_processamento = VALUES(data_processamento);
    """
    inserted = 0
    try:
        for r in rows:
            carteira = r.CARTEIRA if hasattr(r, 'CARTEIRA') else r[0]
            nu = str(r.NU_CPF_CNPJ).strip() if hasattr(r, 'NU_CPF_CNPJ') else str(r[1]).strip()
            usuario = r.NM_USUARIO if hasattr(r, 'NM_USUARIO') else r[2]
            vl = float(r.VL_PAGAMENTO) if hasattr(r, 'VL_PAGAMENTO') and r.VL_PAGAMENTO is not None else 0.0
            cash = int(r.VERRESCHI_CASH) if hasattr(r, 'VERRESCHI_CASH') and r.VERRESCHI_CASH is not None else None
            meta = float(r.META_PONTO) if hasattr(r, 'META_PONTO') and r.META_PONTO is not None else None
            cursor.execute(sql, (carteira, nu, usuario, vl, cash, meta, process_date))
            inserted += 1
        conn.commit()
        logger.info(f"{inserted} registros inseridos/atualizados em MySQL.")
    except Exception as e:
        conn.rollback()
        logger.exception("Erro ao inserir no MySQL: %s", e)
        raise
    finally:
        cursor.close()
        conn.close()
    return inserted

def fetch_from_sqlsrv():
    conn = connect_sqlsrv()
    cursor = conn.cursor()
    try:
        logger.info("Executando consulta T-SQL no SQL Server (pode demorar)...")
        cursor.execute(TSQL)
        # fetchall returns list of pyodbc.Row or tuples
        rows = cursor.fetchall()
        logger.info(f"{len(rows)} registros retornados do SQL Server.")
        return rows
    finally:
        cursor.close()
        conn.close()

def process_once():
    process_date = date.today()
    rows = fetch_from_sqlsrv()
    # transform rows if needed, then upsert into mysql
    upsert_into_mysql(rows, process_date)
    return len(rows)

# ---------- main loop ----------
def main():
    logger.info("Iniciando sync_pagamentos loop. CTRL+C para parar.")
    try:
        while True:
            try:
                n = process_once()
                logger.info(f"Execução concluída: {n} registros processados.")
            except Exception as e:
                logger.exception("Execução falhou: %s", e)
            time.sleep(LOOP_INTERVAL)
    except KeyboardInterrupt:
        logger.info("Parando por solicitação do usuário.")

if __name__ == "__main__":
    main()
