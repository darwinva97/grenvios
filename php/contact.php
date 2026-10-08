<?php
require_once __DIR__ . '/enviar.php';
// Procesa solo solicitudes POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Helper: limpia y normaliza un campo de texto.
    $clean = function ($key) {
        if (empty($_POST[$key])) return '';
        $v = strip_tags(trim($_POST[$key]));
        return str_replace(array("\r", "\n"), array(" ", " "), $v);
    };

    // Campos del formulario.
    $firstname     = $clean("firstname");
    $lastname      = $clean("lastname");
    $name          = trim($firstname . ' ' . $lastname);
    $email         = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone         = $clean("phone");
    $country       = $clean("country");        // País de destino
    $shipping_type = $clean("shipping_type");  // Documento / Paquete / Carga
    $content_desc  = $clean("content");         // Contenido del envío
    $weight        = $clean("weight");          // Peso en kg
    $dimensions    = $clean("dimensions");      // Medidas alto x largo x ancho
    $message       = trim($_POST["message"] ?? '');

    // Validación mínima.
    if (empty($firstname) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Hubo un problema con tu envío. Completa el formulario e inténtalo de nuevo.";
        exit;
    }

    // Destinatario de los mensajes del formulario.
    $recipient = "info@grenvios.com";
    $subject   = "Nueva cotización / consulta de $name";
    $asunto_plano = $subject;   // wp_mail codifica el asunto él mismo

    // Contenido del correo.
    $email_content  = "Nombre: $name\n";
    $email_content .= "Correo: $email\n";
    if ($phone !== '')         $email_content .= "Teléfono: $phone\n";
    if ($country !== '')       $email_content .= "País de destino: $country\n";
    if ($shipping_type !== '') $email_content .= "Tipo de envío: $shipping_type\n";
    if ($content_desc !== '')  $email_content .= "Contenido: $content_desc\n";
    if ($weight !== '')        $email_content .= "Peso (kg): $weight\n";
    if ($dimensions !== '')    $email_content .= "Medidas (cm): $dimensions\n";
    $email_content .= "\nMensaje:\n$message\n";

    if (grenvios_form_enviar($asunto_plano, $email_content, $name, $email, 'Contacto')) {
        http_response_code(200);
        echo "¡Gracias! Tu mensaje ha sido enviado. Te responderemos a la brevedad.";
    } else {
        http_response_code(500);
        echo "Lo sentimos, ocurrió un error y no pudimos enviar tu mensaje.";
    }

} else {
    // No es una solicitud POST.
    http_response_code(403);
    echo "Hubo un problema con tu envío, inténtalo de nuevo.";
}
