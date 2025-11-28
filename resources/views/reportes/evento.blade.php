<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reporte del Evento</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }

        /* ENCABEZADO GENERAL */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #ff6b1f;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .logo {
            width: 110px;
            height: 110px;
            border-radius: 15px;
            /* 🚀 border-radius solicitado */
            object-fit: cover;
        }

        .header-text {
            text-align: right;
        }

        .project-title {
            font-size: 22px;
            font-weight: bold;
            color: #ff6b1f;
            margin: 0;
        }

        .company-name {
            font-size: 16px;
            margin-top: 5px;
        }

        /* TITULOS */
        h2.section-title {
            font-size: 18px;
            color: #ff6b1f;
            margin-top: 30px;
            border-left: 4px solid #ff6b1f;
            padding-left: 8px;
        }

        /* CARD DE INFORMACIÓN */
        .info-card {
            background: #f8f8f8;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #111;
        }

        /* GRID DE ESTADÍSTICAS */
        .stats-grid {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .stat-box {
            flex: 1;
            background: #fff4e8;
            border-left: 5px solid #ff6b1f;
            border-radius: 10px;
            padding: 12px;
        }

        .stat-title {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <div class="header">
        <img class="logo" src="{{ $logo }}" alt="Logo Taquillería del Sol">

        <div class="header-text">
            <p class="project-title">{{ $proyecto }}</p>
            <p class="company-name"><strong>Empresa:</strong> {{ $empresa->nombre_empresa }}</p>
             <p class="company-name"><strong>NIT:</strong> {{ $empresa->nit }}</p>
            <p><small>Reporte generado el: {{ $fechaReporte }}</small></p>
        </div>
    </div>

    <!-- INFORMACIÓN GENERAL DEL EVENTO -->
    <h2 class="section-title">Información del Evento</h2>

    <div class="info-card">
        <div class="info-row"><span class="info-label">Título:</span> {{ $evento->titulo }}</div>
        <div class="info-row"><span class="info-label">Descripción:</span> {{ $evento->descripcion }}</div>
        <div class="info-row"><span class="info-label">Fecha:</span> {{ $evento->fecha }}</div>
        <div class="info-row"><span class="info-label">Horario:</span> {{ $evento->hora_inicio }} - {{ $evento->hora_final }}</div>
    </div>

    <!-- ESTADÍSTICAS -->
    <h2 class="section-title">Estadísticas del Evento</h2>

    <div class="stats-grid">
        <div class="stat-box">
            <p class="stat-title">Asientos Vendidos</p>
            <p class="stat-value">{{ $asientosVendidos }}</p>
        </div>

        <div class="stat-box">
            <p class="stat-title">Recaudo empresa</p>
            <p class="stat-value">${{ number_format($recaudo_empresa, 0, ',', '.') }}</p>
            <p class="stat-title">Recaudo teatro</p>
            <p class="stat-value">${{ number_format($recaudo_teatro, 0, ',', '.') }}</p>
            <p class="stat-title">Total Recaudado</p>
            <p class="stat-value">${{ number_format($totalDinero, 0, ',', '.') }}</p>
        </div>

        <div class="stat-box">
            <p class="stat-title">Ocupación</p>
            <p class="stat-value">{{ $porcentajeOcupacion }}%</p>
        </div>
    </div>

    <!-- PIE DE PÁGINA -->
    <div class="footer">
        © {{ date('Y') }} Taquillería del Sol — Reporte generado automáticamente.
    </div>

</body>

</html>