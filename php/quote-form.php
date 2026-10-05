<?php
// Procesa solo solicitudes POST. Atiende el formulario de cotización (/cotizar/).
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Helper: limpia y normaliza un campo de texto.
    $clean = function ($key) {
        if (empty($_POST[$key])) return '';
        $v = strip_tags(trim($_POST[$key]));
        return str_replace(array("\r", "\n"), array(" ", " "), $v);
    };

    // Campos del formulario de cotización.
    $name           = $clean("name");
    $email          = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone          = $clean("phone");
    $origin         = $clean("origin");
    $country        = $clean("country");
    $city           = $clean("city");
    $postal_code    = $clean("postal_code");
    $shipment_type  = $clean("shipment_type");
    $weight         = $clean("weight");
    $height         = $clean("height");
    $length         = $clean("length");
    $width          = $clean("width");
    $declared_value = $clean("declared_value");
    $message        = trim($_POST["message"] ?? '');

    /* El cotizador rápido del hero pide UN solo dato de contacto («WhatsApp o
     * email»): si viene, se reparte al campo que corresponda según su forma. */
    $contact = $clean("contact");
    if ($contact !== '') {
        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $email = $contact;
        } elseif ($phone === '') {
            $phone = $contact;
        }
    }

    /* Validación mínima. El correo deja de ser obligatorio por sí solo: basta con
     * UNA vía de contacto, porque el cotizador del hero admite WhatsApp o email
     * en un único campo. El de /cotizar/ sigue pidiendo ambos en el navegador. */
    $has_email   = filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
    $has_contact = $has_email || $phone !== '';
    /* El peso ya no es obligatorio en el servidor: el cotizador del hero lo pide
     * dentro de «Detalles del envío», no como campo suelto. El de /cotizar/ lo
     * sigue exigiendo en el navegador. */
    if (empty($name) || empty($country) || empty($shipment_type) || !$has_contact) {
        http_response_code(400);
        echo "Hubo un problema con tu solicitud. Completa los campos obligatorios e inténtalo de nuevo.";
        exit;
    }

    // Destinatario de las cotizaciones.
    $recipient = "info@grenvios.com";
    $subject   = "Nueva cotización de $name — $country";
    if (function_exists('mb_encode_mimeheader')) {
        $subject = mb_encode_mimeheader($subject, 'UTF-8');
    }

    // Medidas en una sola línea, si se indicaron.
    $dimensions = '';
    if ($height !== '' || $length !== '' || $width !== '') {
        $dimensions = ($height !== '' ? $height : '?') . ' × ' . ($length !== '' ? $length : '?') . ' × ' . ($width !== '' ? $width : '?') . ' cm';
    }

    // Contenido del correo.
    $email_content  = "Nombre: $name\n";
    if ($email !== '') $email_content .= "Correo: $email\n";
    if ($phone !== '') $email_content .= "Teléfono / WhatsApp: $phone\n";
    if ($origin !== '')         $email_content .= "Origen: $origin\n";
    $email_content .= "País de destino: $country\n";
    if ($city !== '')           $email_content .= "Ciudad de destino: $city\n";
    if ($postal_code !== '')    $email_content .= "Código postal: $postal_code\n";
    $email_content .= "Tipo de envío: $shipment_type\n";
    if ($weight !== '') $email_content .= "Peso (kg): $weight\n";
    if ($dimensions !== '')     $email_content .= "Medidas: $dimensions\n";
    if ($declared_value !== '') $email_content .= "Valor de la mercancía: $declared_value\n";
    if ($message !== '')        $email_content .= "\nDetalles adicionales:\n$message\n";

    // Cabeceras del correo. El From usa el dominio del sitio (info@grenvios.com) para
    // pasar SPF/DKIM y NO caer en spam; las respuestas van al visitante (Reply-To).
    $email_headers  = "From: Grenvios <info@grenvios.com>\r\n";
    if ($has_email) $email_headers .= "Reply-To: $name <$email>\r\n";
    $email_headers .= "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Envío.
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo "¡Gracias! Recibimos tu solicitud de cotización. Te responderemos en breve.";
    } else {
        http_response_code(500);
        echo "Lo sentimos, ocurrió un error y no pudimos enviar tu solicitud.";
    }

} else {
    // No es una solicitud POST.
    http_response_code(403);
    echo "Hubo un problema con tu solicitud, inténtalo de nuevo.";
}
