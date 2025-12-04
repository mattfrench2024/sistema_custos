import pandas as pd
import mysql.connector
from mysql.connector import errorcode

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3307,
    'user': 'laravel',
    'password': 'laravel',
    'database': 'sistema'
}

PLANILHA = 'Cópia de Custos Utilities.xlsx'  # Substitua pelo caminho correto do xlsx
TABELA = 'costs_base'

MESES = ['Pago jan','Pago fev','Pago mar','Pago abr','Pago mai','Pago jun',
         'Pago jul','Pago ago','Pago set','Pago out','Pago nov','Pago dez']
ANO = 2025

def parse_valor(valor):
    """Converte string com vírgula em decimal e remove R$ e espaços"""
    if pd.isna(valor):
        return 0.0
    if isinstance(valor, (int, float)):
        return float(valor)
    valor = str(valor).replace('R$', '').replace('.', '').replace(',', '.').strip()
    try:
        return float(valor)
    except:
        return 0.0

try:
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    # Ler a planilha (sem header automático)
    df = pd.read_excel(PLANILHA, header=0)

    # Checar nomes das colunas
    print("Colunas lidas do Excel:", df.columns.tolist())
    
    # Remover espaços extras dos nomes das colunas
    df.columns = [c.strip() for c in df.columns]

    # Limitar até linha 27
    df = df.iloc[:27]

    cursor.execute(f"""
    CREATE TABLE IF NOT EXISTS {TABELA} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        Categoria VARCHAR(255) NOT NULL,
        AJUSTES DECIMAL(15,2) DEFAULT 0,
        {', '.join([f"`{mes}` DECIMAL(15,2) DEFAULT 0" for mes in MESES])},
        Ano SMALLINT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    """)

    # Inserir dados
    for idx, row in df.iterrows():
        categoria = str(row.get('Categoria', '')).strip()
        ajustes = parse_valor(row.get('AJUSTES', 0))
        valores_colunas = [categoria, ajustes]

        for mes in MESES:
            valor = parse_valor(row.get(mes, 0))
            valores_colunas.append(valor)

        valores_colunas.append(ANO)

        placeholders = ','.join(['%s'] * len(valores_colunas))
        sql = f"INSERT INTO {TABELA} (Categoria, AJUSTES, {', '.join([f'`{mes}`' for mes in MESES])}, Ano) VALUES ({placeholders})"
        cursor.execute(sql, valores_colunas)

    conn.commit()
    print("Importação concluída com sucesso!")

except mysql.connector.Error as err:
    print(err)
except Exception as e:
    print("Erro:", e)
finally:
    cursor.close()
    conn.close()
