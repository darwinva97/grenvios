<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = getenv('MAILCHIMP_API_KEY');
    $audienceId = getenv('MAILCHIMP_AUDIENCE_ID') ?: '4dbe9a9245';

    $email = $_POST['mc_email'];

    if (!$apiKey) {
        http_response_code(500);
        echo 'Mailchimp is not configured.';
        exit;
    }

    if (!empty($email)) {
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $audienceId . '/members/';

        $jsonData = json_encode([
            'email_address' => $email,
            'status' => 'subscribed',
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo 'Subscription successful!';
        } elseif ($httpCode === 400) {
            echo $email . ' is already exists!';
        } else {
            echo 'Subscription failed. Please try again later.';
        }
    } else {
        echo 'Email address is required.';
    }
}
