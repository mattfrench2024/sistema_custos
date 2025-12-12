<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Verreschi Management — Lousa</title>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<style>
  /* Fonts */
  @font-face{
    font-family:InterVar;
    src: local("Inter"), local("Inter var"), url("https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3Q8I1sF6y0bQJro.woff2") format("woff2");
    font-weight:100 900;
    font-style:normal;
    font-display:swap;
  }
  :root{
    --bg:#0f1724;
    --card:#0b1220;
    --muted:#98a0b3;
    --glass: rgba(255,255,255,0.04);
    --accent-from:#ff7a18;
    --accent-to:#ffb347;
    --accent-purple:#7e22cc;
    --soft-white: rgba(255,255,255,0.95);
    --radius:14px;
    --max-width:1200px;
  }
  html,body{height:100%}
  body{
    margin:0;
    font-family:InterVar, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    background:
      radial-gradient(1200px 500px at 10% 10%, rgba(126,34,204,0.06), transparent 6%),
      linear-gradient(180deg, #071021 0%, #071021 100%);
    color:var(--soft-white);
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    line-height:1.45;
  }

  /* Layout */
  .wrap{max-width:calc(var(--max-width) + 40px); margin:36px auto; padding:24px; display:grid; grid-template-columns: 1fr 420px; gap:28px;}
  @media (max-width:1100px){ .wrap{grid-template-columns: 1fr} }

  .card{
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    border:1px solid rgba(255,255,255,0.04);
    border-radius:var(--radius);
    padding:20px;
    box-shadow: 0 6px 20px rgba(3,6,23,0.6);
    backdrop-filter: blur(8px) saturate(120%);
  }

  header.lheader{
    display:flex; gap:16px; align-items:center; justify-content:space-between;
    margin-bottom:8px;
  }
  .brand{
    display:flex; gap:14px; align-items:center;
  }
  .logo{
    width:64px; height:64px; border-radius:12px;
    background:linear-gradient(135deg,var(--accent-from),var(--accent-to));
    display:grid; place-items:center; font-weight:800; color:white; font-size:18px;
    box-shadow: 0 6px 30px rgba(255,122,24,0.14);
  }
  h1{margin:0; font-size:20px; font-weight:700; letter-spacing:-0.2px; color:linear-gradient(90deg,var(--accent-from),var(--accent-to));}
  .subtitle{color:var(--muted); font-size:13px; margin-top:6px;}

  /* Controls */
  .controls{display:flex; gap:10px; align-items:center;}
  .btn{
    background:transparent; border:1px solid rgba(255,255,255,0.06); padding:8px 12px; border-radius:10px; color:var(--soft-white);
    font-size:13px; cursor:pointer; display:inline-flex; gap:8px; align-items:center;
  }
  .btn.primary{
    background:linear-gradient(90deg,var(--accent-from),var(--accent-to));
    border: none; color:#071021; font-weight:700; box-shadow: 0 8px 28px rgba(255,122,24,0.12);
  }
  .icon{width:16px;height:16px; display:inline-block; opacity:0.95}

  /* Content */
  .content{display:flex; flex-direction:column; gap:18px;}
  .lead{font-size:14px; color:var(--muted);}

  /* Sections */
  .section{margin-top:4px;}
  .section h2{margin:0 0 8px 0; font-size:16px; color:var(--soft-white)}
  .section .desc{color:var(--muted); font-size:13px; margin-bottom:12px}

  /* Grid lists */
  .grid-3{display:grid; grid-template-columns:repeat(3,1fr); gap:12px;}
  @media (max-width:700px){ .grid-3{grid-template-columns:repeat(1,1fr)} }

  .feature{
    background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.008));
    border:1px solid rgba(255,255,255,0.03);
    padding:12px; border-radius:12px; font-size:13px;
    display:flex; gap:12px; align-items:flex-start;
  }
  .feature .dot{width:38px;height:38px;border-radius:10px; display:grid; place-items:center; font-weight:700;
    background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); color:var(--accent-from); border:1px solid rgba(255,255,255,0.02)
  }

  /* Table */
  .table-wrap{overflow:auto; border-radius:12px}
  table.readme-table{width:100%; border-collapse:collapse; font-size:13px;}
  table.readme-table thead th{position:sticky; top:0; background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); color:var(--muted); text-align:left; padding:10px 12px; font-weight:700; font-size:12px; border-bottom:1px solid rgba(255,255,255,0.03)}
  table.readme-table tbody td{padding:12px; border-bottom:1px solid rgba(255,255,255,0.03); color:var(--soft-white)}
  .small{font-size:12px; color:var(--muted)}

  /* Sidebar */
  .sidebar{position:sticky; top:36px; display:flex; flex-direction:column; gap:16px}
  .meta{display:flex; gap:12px; align-items:center; justify-content:space-between}
  .meta .kpi{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); padding:12px; border-radius:12px; text-align:center; min-width:120px}
  .meta .kpi .main{font-size:18px; font-weight:700; color:var(--soft-white)}
  .meta .kpi .sub{font-size:12px; color:var(--muted)}

  /* Collapsible */
  .collapsible{border-radius:12px; overflow:hidden}
  .collapsible summary{list-style:none; padding:12px 14px; display:flex; justify-content:space-between; align-items:center; cursor:pointer; background:linear-gradient(90deg, rgba(255,255,255,0.01), transparent); border-bottom:1px solid rgba(255,255,255,0.02)}
  .collapsible summary::-webkit-details-marker{display:none}
  .collapsible .body{padding:14px; color:var(--muted); font-size:13px; background:linear-gradient(180deg, rgba(255,255,255,0.008), transparent)}
  .badge{display:inline-block; padding:6px 10px; font-size:12px; border-radius:999px; background: rgba(255,255,255,0.02); color:var(--muted); border:1px solid rgba(255,255,255,0.03) }

  /* Footer / small utilities */
  .tools{display:flex; gap:8px; margin-top:6px}
  .search{display:flex; gap:8px; align-items:center}
  input.search-input{background:transparent; border:1px solid rgba(255,255,255,0.04); padding:8px 10px; border-radius:10px; color:var(--soft-white)}
  .muted{color:var(--muted); font-size:12px}

  /* Print friendly */
  @media print{
    body{background:white;color:black}
    .card{box-shadow:none;border:none;background:white}
    .wrap{grid-template-columns:1fr}
  }
</style>
</head>
<body>
  <main class="wrap">

    <!-- LEFT: LARGE CONTENT -->
    <section class="card" aria-labelledby="title">
      <header class="lheader">
        <div class="brand">
          <div class="logo">VM</div>
          <div>
            <h1 id="title">Verreschi Management — Sistema Corporativo de Custos & Operações</h1>
            <div class="subtitle">Plataforma modular para Custos, Financeiro, Folha, RH, Inventário, Auditoria e Integrações corporativas.</div>
          </div>
        </div>

        <div class="controls" role="toolbar" aria-label="controls">
          <button class="btn" id="btn-copy" title="Copiar lousa">📋 Copiar</button>
          <button class="btn" id="btn-print" title="Imprimir lousa">🖨 Imprimir</button>
          <button class="btn primary" id="btn-download" title="Salvar HTML">⬇ Exportar</button>
        </div>
      </header>

      <div class="content">
        <p class="lead">Uma visão condensada e executiva do produto — escrita para times de engenharia, produto e stakeholders. A lousa abaixo organiza arquitetura, módulos, tabelas, integrações e roadmap de forma clara e pronta para apresentação.</p>

        <!-- Overview -->
        <div class="section">
          <h2>✨ Visão Geral</h2>
          <div class="desc">O Verreschi Management é uma solução corporativa construída para proporcionar: confiabilidade, observabilidade e experiência premium para operações financeiras e de TI.</div>

          <div class="grid-3" style="margin-top:12px">
            <div class="feature">
              <div class="dot">💰</div>
              <div>
                <div style="font-weight:700">Controle de Custos</div>
                <div class="small">Lançamentos, centros de custo, ajustes e comparativos mensais.</div>
              </div>
            </div>

            <div class="feature">
              <div class="dot">📦</div>
              <div>
                <div style="font-weight:700">Inventário & Patrimônio</div>
                <div class="small">Movimentação, auditoria e conservação do patrimônio TI.</div>
              </div>
            </div>

            <div class="feature">
              <div class="dot">🧾</div>
              <div>
                <div style="font-weight:700">Financeiro & Folha</div>
                <div class="small">Sincronização com SQL Server + processamento via ETL Python.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- UI/UX -->
        <div class="section">
          <h2>🎨 UI / UX — Premium</h2>
          <div class="desc">Design minimalista com foco em tranquilidade visual. Componentes reutilizáveis, cabeçalhos claros, micro-interações e densidade controlada de informação (Calm UI).</div>

          <details class="collapsible" open>
            <summary>
              <div><strong>Pilares do design</strong><div class="small">clique para expandir</div></div>
              <div class="badge">Interativo</div>
            </summary>
            <div class="body">
              Paleta equilibrada (accent laranja → gradiente), espaçamento consistente, tipografia escalonada e badges com micro animação. Priorize legibilidade em tabelas e contraste na leitura longa.
            </div>
          </details>
        </div>

        <!-- Architecture -->
        <div class="section">
          <h2>🧱 Arquitetura Técnica</h2>
          <div class="desc">Backend Laravel 12+, MySQL primário, SQL Server para fontes externas e Python para sincronização/ETL. Arquitetura pensada para observabilidade e escalabilidade.</div>

          <div class="table-wrap" style="margin-top:12px">
            <table class="readme-table" aria-describedby="arch-desc">
              <thead>
                <tr>
                  <th>Camada</th>
                  <th>Componente</th>
                  <th>Responsabilidade</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>API / Backend</td><td>Laravel (Controllers / Services)</td><td>Regras de negócio, policies, jobs e validações</td></tr>
                <tr><td>Persistência</td><td>MySQL 8</td><td>Dados principais, logs e históricos</td></tr>
                <tr><td>Fonte Externa</td><td>SQL Server</td><td>Folha, pagamentos e bases de integração</td></tr>
                <tr><td>ETL / Sync</td><td>Python (pyodbc/mysql-connector)</td><td>ETL, normalização e inserção em tb_pagamentos_processados</td></tr>
                <tr><td>UI</td><td>Blade + Tailwind</td><td>Camada de apresentação, acessibilidade e temas</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Database -->
        <div class="section">
          <h2>🛢 Estrutura do Banco (Resumo)</h2>
          <div class="desc">Principais tabelas que suportam os módulos do sistema.</div>

          <table class="readme-table" style="margin-top:12px">
            <thead>
              <tr><th>Tabela</th><th>Descrição</th></tr>
            </thead>
            <tbody>
              <tr><td><strong>cost_entries</strong></td><td>Lançamentos de custos</td></tr>
              <tr><td><strong>costs_base</strong></td><td>Cadastro principal de custos base</td></tr>
              <tr><td><strong>tb_pagamentos_processados</strong></td><td>Importação ETL proveniente do SQL Server</td></tr>
              <tr><td><strong>payrolls</strong></td><td>Folha de pagamento (sincronizada)</td></tr>
              <tr><td><strong>audit_logs</strong></td><td>Trilha de auditoria</td></tr>
              <tr><td><strong>users / roles / sessions</strong></td><td>Autenticação e autorização</td></tr>
            </tbody>
          </table>
        </div>

        <!-- Modules -->
        <div class="section">
          <h2>🧩 Módulos do Sistema</h2>
          <div class="desc">Funcionalidades por domínio — prontas para operação e extensíveis.</div>

          <div class="grid-3" style="margin-top:12px">
            <div class="feature"><div class="dot">🔎</div><div><strong>Custos</strong><div class="small">Fases, subfases, progresso e Kanban (opcional).</div></div></div>
            <div class="feature"><div class="dot">💳</div><div><strong>Financeiro</strong><div class="small">Fluxo pagar/receber, dashboards e notas.</div></div></div>
            <div class="feature"><div class="dot">👥</div><div><strong>RH & Folha</strong><div class="small">Integração com SQL Server e processamento em lote.</div></div></div>
          </div>
        </div>

        <!-- Integrations -->
        <div class="section">
          <h2>🔌 Integrações</h2>
          <div class="desc">Conectores e rotinas para manter dados consistentes entre fontes.</div>

          <details class="collapsible">
            <summary><strong>Fluxo de sincronização (ETL)</strong> <span class="small">— Python daemon / cron</span></summary>
            <div class="body">
              1) Pyodbc conecta no SQL Server e executa consulta consolidada (sem GO). 2) Normaliza e calcula colunas (VERRESCHI_CASH, META_PONTO). 3) Persiste em MySQL na tabela `tb_pagamentos_processados` com upsert. 4) Jobs Laravel consomem a tabela para gerar KPIs e dashboards.
            </div>
          </details>
        </div>

        <!-- Roadmap -->
        <div class="section">
          <h2>🚀 Roadmap Prioritário</h2>
          <div class="desc">Próximas entregas com impacto direto em SaaS e monetização.</div>

          <ul class="small" style="margin-top:8px">
            <li><strong>Multi-tenant</strong> (separação por empresa / schema)</li>
            <li><strong>Billing & Subscriptions</strong> (Stripe / Mercado Pago / boleto)</li>
            <li><strong>Onboarding</strong> automatizado e templates de integração</li>
            <li><strong>API pública</strong> (REST + rate limiting)</li>
            <li><strong>Observability</strong> (logs centralizados + métricas)</li>
          </ul>
        </div>

        <!-- Conclusion -->
        <div class="section">
          <h2>🏆 Conclusão</h2>
          <div class="desc">Produto maduro para operação interna e com base técnica para escalar como SaaS. Alta qualidade visual e engenharia sólida tornam o sistema investível e competitivo.</div>
        </div>

      </div>
    </section>

    <!-- RIGHT: SIDEBAR -->
    <aside class="sidebar">

      <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px">
          <div>
            <div class="small">Status do Projeto</div>
            <div style="font-weight:800; font-size:18px; margin-top:6px">Verreschi Management</div>
          </div>
          <div style="text-align:right">
            <div class="badge">Produção: parcial</div>
            <div class="small" style="margin-top:8px">Última sincronização: <strong id="last-sync">—</strong></div>
          </div>
        </div>

        <div style="margin-top:12px" class="meta">
          <div class="kpi"><div class="main" id="kpi-tables">25</div><div class="sub">tabelas</div></div>
          <div class="kpi"><div class="main" id="kpi-routes">112</div><div class="sub">endpoints</div></div>
        </div>

        <div style="margin-top:12px">
          <div class="small">Ações rápidas</div>
          <div class="tools">
            <button class="btn" onclick="highlight('Arquitetura')">📐 Arquitetura</button>
            <button class="btn" onclick="highlight('ETL')">🔁 ETL</button>
            <button class="btn" onclick="highlight('Roadmap')">🚀 Roadmap</button>
          </div>
        </div>
      </div>

      <div class="card">
        <h3 style="margin:0 0 8px 0">Tabelas Principais</h3>
        <div class="small">Resumo rápido das tabelas que importam para operações financeiras.</div>

        <div style="margin-top:12px">
          <table class="readme-table">
            <thead>
              <tr><th>tabela</th><th>uso</th></tr>
            </thead>
            <tbody>
              <tr><td>costs_base</td><td>Cadastro</td></tr>
              <tr><td>cost_entries</td><td>Lançamentos</td></tr>
              <tr><td>tb_pagamentos_processados</td><td>Import ETL</td></tr>
              <tr><td>payrolls</td><td>Folha</td></tr>
            </tbody>
          </table>
        </div>

        <div style="margin-top:10px; display:flex; gap:8px; justify-content:space-between">
          <button class="btn" id="btn-toggle-theme">🌗 Tema</button>
          <button class="btn" id="btn-search">🔍 Buscar</button>
        </div>
      </div>

      <div class="card">
        <h3 style="margin:0 0 8px 0">Pesquisa / Atalhos</h3>
        <div class="search">
          <input class="search-input" id="search" placeholder="Digite termo (ex: payroll, ETL, tb_pagamentos_processados)" />
          <button class="btn" onclick="doSearch()">Ir</button>
        </div>

        <div style="margin-top:12px" class="small muted">Use os botões acima para focar nas seções. A lousa é exportável e imprimível.</div>
      </div>

    </aside>

  </main>

<script>
/* ---------- Helper: copy / print / export ---------- */
const btnCopy = document.getElementById('btn-copy');
const btnPrint = document.getElementById('btn-print');
const btnDownload = document.getElementById('btn-download');
const searchInput = document.getElementById('search');

btnCopy.addEventListener('click', async () => {
  const el = document.querySelector('section.card').innerText;
  try {
    await navigator.clipboard.writeText(el);
    alert('Lousa copiada para a área de transferência ✅');
  } catch (e) {
    alert('Não foi possível copiar automaticamente. Selecione e copie manualmente.');
  }
});

btnPrint.addEventListener('click', ()=> window.print());

btnDownload.addEventListener('click', ()=> {
  const blob = new Blob([document.documentElement.outerHTML], {type:'text/html;charset=utf-8'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = 'verreschi_lousa.html'; document.body.appendChild(a); a.click();
  setTimeout(()=>{ URL.revokeObjectURL(url); a.remove(); }, 600);
});

/* ---------- Highlight utility ---------- */
function highlight(term){
  // basic scroll-to heuristics
  const mapping = {
    'Arquitetura': '🧱',
    'ETL': '🔌',
    'Roadmap': '🚀'
  };
  // attempt to find section by heading text
  const headings = Array.from(document.querySelectorAll('h2'));
  const target = headings.find(h => h.textContent.toLowerCase().includes(term.toLowerCase()) || h.textContent.includes(mapping[term]));
  if(target){
    target.scrollIntoView({behavior:'smooth', block:'center'});
    flash(target);
  } else {
    alert('Seção não encontrada: ' + term);
  }
}
function flash(el){
  const orig = el.style.boxShadow;
  el.style.boxShadow = '0 4px 30px rgba(255,186,87,0.18)';
  setTimeout(()=> el.style.boxShadow = orig, 1500);
}

/* ---------- Search ---------- */
function doSearch(){
  const t = searchInput.value.trim().toLowerCase();
  if(!t){ alert('Digite um termo para buscar.'); return; }
  const allText = document.querySelector('section.card').innerText.toLowerCase();
  if(allText.includes(t)){
    // find the first containing node
    const nodes = document.querySelectorAll('section.card h2, section.card p, section.card div, section.card table');
    for(const n of nodes){
      if(n.innerText && n.innerText.toLowerCase().includes(t)){
        n.scrollIntoView({behavior:'smooth', block:'center'});
        flash(n);
        return;
      }
    }
    alert('Encontrado no documento mas não foi possível localizar o trecho visualmente.');
  } else {
    alert('Termo não encontrado.');
  }
}

/* ---------- Theme toggle (light / dark) ---------- */
const btnTheme = document.getElementById('btn-toggle-theme');
btnTheme.addEventListener('click', ()=> {
  if(document.documentElement.style.getPropertyValue('--bg') === ''){
    // quick invert: apply light styles
    document.documentElement.style.setProperty('--bg', '#ffffff');
    document.documentElement.style.setProperty('--card', '#ffffff');
    document.documentElement.style.setProperty('--soft-white', '#0b1220');
    document.documentElement.style.setProperty('--muted', '#495057');
    alert('Tema claro ativado (visual temporário).');
  } else {
    // remove inline to restore defaults
    document.documentElement.style.removeProperty('--bg');
    document.documentElement.style.removeProperty('--card');
    document.documentElement.style.removeProperty('--soft-white');
    document.documentElement.style.removeProperty('--muted');
    alert('Tema padrão restaurado.');
  }
});

/* ---------- Populate meta data (example) ---------- */
document.getElementById('last-sync').textContent = new Date().toLocaleString();

/* ---------- Small accessibility enhancement: keyboard '/' focus search ---------- */
document.addEventListener('keydown', (e)=>{
  if(e.key === '/'){
    e.preventDefault();
    searchInput.focus();
  }
});
</script>
</body>
</html>
