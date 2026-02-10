<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>NFSe {{ $nota->numero }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .row {
            width: 100%;
            clear: both;
        }

        .col {
            float: left;
            width: 48%;
        }

        .right {
            float: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>NOTA FISCAL DE SERVIÇOS ELETRÔNICA – NFSe</h2>
    <strong>Nº {{ $nota->numero }}</strong>
</div>

<div class="box">
    <strong>Emitente</strong><br>
    {{ $payload['Cabecalho']['cRazaoEmissor'] ?? '-' }}
<br>
    CNPJ: {{ $payload['Cabecalho']['cCNPJEmissor'] ?? '-' }}

</div>

<div class="box">
    <strong>Tomador do Serviço</strong><br>
    {{ $payload['Cabecalho']['cRazaoDestinatario'] ?? '-' }}
<br>
    CNPJ/CPF: {{ $payload['Cabecalho']['cCNPJDestinatario'] ?? '-' }}
</div>

<table>
    <thead>
        <tr>
            <th>Descrição do Serviço</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $payload['Servico']['cDescricao'] ?? 'Serviço prestado conforme contrato.' }}</td>
            <td style="text-align:right">
                R$ {{ number_format($nota->valor_total, 2, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>

<div class="box">
    <strong>Data de Emissão:</strong>
    {{ optional($nota->data_emissao)->format('d/m/Y') }}
</div>

</body>
</html>
