<?php
// Honeypot anti-spam
if (!empty($_POST['empresa'])) { http_response_code(400); exit('Bot'); }

// Sanitizar / validar
$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$mensaje  = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $mensaje === '') {
  $back = $_SERVER['HTTP_REFERER'] ?? 'index.html';
  header('Location: '.$back.'#error');
  exit;
}

// Datos de envío
$to      = 'reservas@regishotel.com.ar';      // <-- destino
$from    = 'no-reply@regishotel.com.ar';      // del mismo dominio (mejor SPF/DMARC)
$subject = 'Nuevo contacto desde la web';
$body    = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\n\nMensaje:\n$mensaje\n";
$headers = "From: Hotel Regis <{$from}>\r\n";
$headers .= "Reply-To: {$nombre} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Enviar (el -f mejora entregabilidad en algunos hosts)
$ok = @mail($to, $subject, $body, $headers, "-f {$from}");

// Volver a la misma página con hash para mostrar el modal
$back = $_SERVER['HTTP_REFERER'] ?? 'index.html';
header('Location: '.$back.($ok ? '#gracias' : '#error'));
exit;
