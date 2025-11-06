@component('mail::message')
{{-- Encabezado personalizado con fondo cálido y logo centrado --}}
<div style="text-align:center; background:linear-gradient(135deg, #f8d49d, #f3a84f); padding:20px; border-radius:10px;">
    <h1 style="font-family:'Poppins', sans-serif; color:#4a2c00; margin:0;">Taquillería del Sol</h1>
</div>

{{-- Cuerpo del mensaje --}}
<div style="margin-top:25px; font-family:'Poppins', sans-serif; color:#333;">
    <h2 style="text-align:center; color:#222;">Código de Verificación</h2>
    <p style="font-size:16px; line-height:1.6; text-align:center;">
        Has solicitado restablecer tu contraseña.  
        Ingresa el siguiente código para continuar con el proceso:
    </p>
</div>

{{-- Panel del código con estilo teatral --}}
<div style="text-align:center; margin:30px 0;">
    <div style="display:inline-block; background:#fff3e0; border:2px solid #f3a84f; color:#b45f06; 
                font-size:28px; font-weight:bold; letter-spacing:3px; padding:15px 25px; 
                border-radius:8px; box-shadow:0 3px 8px rgba(0,0,0,0.15);">
        {{ $datos['codigo'] }}
    </div>
</div>

{{-- Mensaje de advertencia --}}
<p style="text-align:center; font-size:14px; color:#666;">
    Este código expirará en <strong>15 minutos</strong> y es de único uso.  
    Si no solicitaste este correo, por favor ignóralo.
</p>

{{-- Pie de página con color cálido --}}
<div style="margin-top:30px; text-align:center; font-size:13px; color:#aaa;">
    <hr style="border:none; height:1px; background:#f3a84f; margin:15px 0;">
    <p>Gracias por confiar en <strong>Taquillería del Sol</strong> 🌞🎭</p>
    <p style="font-size:12px; color:#999;">© {{ date('Y') }} Taquillería del Sol. Todos los derechos reservados.</p>
</div>
@endcomponent
