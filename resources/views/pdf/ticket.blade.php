<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ticket de Evento</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }

        .card {
            width: 100%;
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            color: #1e3a8a;
        }

        .sub {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }

        .info {
            margin-bottom: 20px;
            padding: 15px;
            border-left: 4px solid #2563eb;
            background: #f0f5ff;
            border-radius: 6px;
        }

        .info p {
            margin: 6px 0;
            font-size: 15px;
        }

        .label {
            font-weight: bold;
            color: #1f2937;
        }

        .qr-section {
            text-align: center;
            margin-top: 25px;
        }

        .qr-section p {
            margin-bottom: 10px;
            color: #374151;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>

<body>

    <div class="card">

        <div class="header">
            <h1>🎫 Ticket de Evento</h1>
            <p class="sub">Tu acceso oficial a la Taquillería del Sol ☀️</p>
        </div>

        <div class="info">
            <p><span class="label">Ticket ID:</span> {{ $ticket->ticket_id }}</p>
            <p><span class="label">Cliente:</span> {{ $ticket->nombre }} {{ $ticket->apellido }}</p>
            <p><span class="label">Evento:</span> {{ $ticket->titulo }}</p>
            <p><span class="label">Fecha del evento:</span> {{ \Carbon\Carbon::parse($ticket->fecha_evento)->toDateString() }}</p>
            <p><span class="label">Horario:</span> {{ $ticket->hora_inicio }} - {{ $ticket->hora_final }}</p>
            <ul>
                @foreach ($asientos as $a)
                <li>
                    Fila: {{ $a['fila'] }} —
                    Número: {{ $a['numero'] }} —
                    {{ $a['ubicacion'] }} —
                    ${{ number_format($a['precio'],0,',','.') }}
                </li>
                @endforeach
            </ul>

            <p><span class="label">Total Pagado:</span> ${{ number_format($ticket->total_pagado, 0, ',', '.') }}</p>
            <p><span class="label">Fecha de Compra:</span> {{ \Carbon\Carbon::parse($ticket->fecha_compra)->toDateString() }}</p>
        </div>

        <div class="qr-section">
            <p>Escanea este código en la entrada</p>
            <img src="data:image/svg+xml;base64,{{ $qr }}" width="180">
        </div>

        <div class="footer">
            Gracias por tu compra 💛
            <br>Taquillería del Sol – Vive la experiencia.
        </div>

    </div>

</body>

</html>