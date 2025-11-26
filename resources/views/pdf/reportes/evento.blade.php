<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte del Evento</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        h1 { color: #FF6B1F; margin-bottom: 10px; }
        .section { margin-top: 20px; }
        .label { font-weight: bold; }
        .card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<h1>Reporte del Evento</h1>

<div class="card">
    <p><span class="label">Evento:</span> {{ $evento->titulo }}</p>
    <p><span class="label">Fecha:</span> {{ $evento->fecha }}</p>
</div>

<div class="card">
    <p><span class="label">Asientos Vendidos:</span> {{ $asientosVendidos }}</p>
    <p><span class="label">Total Recaudado:</span> ${{ number_format($totalDinero, 0, ',', '.') }}</p>
    <p><span class="label">Ocupación:</span> {{ $porcentajeOcupacion }}%</p>
</div>

<div class="section">
    <p><span class="label">Reporte generado:</span> {{ $fechaReporte }}</p>
</div>

</body>
</html>
