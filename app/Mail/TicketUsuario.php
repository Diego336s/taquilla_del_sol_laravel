<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;

use Illuminate\Queue\SerializesModels;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\Snappy\Facades\SnappyPdf;
class TicketUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $asientos;
    protected $qr; // cache interno

    public function __construct($ticket, $asientos)
    {
        $this->ticket = $ticket;
        $this->asientos = $asientos;
        // no generar heavy stuff aquí si no quieres; usamos lazy getter
    }

    protected function generateQr(): string
    {
        if ($this->qr) {
            return $this->qr;
        }

        $payload = json_encode([
            'ticket_id'   => $this->ticket->ticket_id,
            'evento'      => $this->ticket->titulo,
            'cliente'     => $this->ticket->nombre . ' ' . $this->ticket->apellido,
            'documento'   => $this->ticket->documento,
            'asientos'    => $this->asientos,
            'total_pagado'=> $this->ticket->total_pagado,
            'fecha_compra'=> (string)$this->ticket->fecha_compra,
        ]);

        $this->qr = base64_encode(
            QrCode::format('svg')
                ->size(250)
                ->errorCorrection('H')
                ->generate($payload)
        );

        return $this->qr;
    }

    public function content(): Content
    {
        return new Content(
            view: 'pdf.ticket',
            with: [
                'ticket'   => $this->ticket,
                'asientos' => $this->asientos,
                'qr'       => $this->generateQr(), // <-- pasar qr aquí
            ]
        );
    }

    public function attachments(): array
    {
        $qr = $this->generateQr(); // usa la misma imagen

        $pdf = SnappyPdf::loadView('pdf.ticket', [
            'ticket'   => $this->ticket,
            'asientos' => $this->asientos,
            'qr'       => $qr,
        ]);

        $pdf->setPaper('a4')->setOption('margin-top', '10mm');

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                'ticket-' . $this->ticket->ticket_id . '.pdf'
            )->withMime('application/pdf')
        ];
    }
}
