<?php
// Check for empty fields
if (empty($_POST['name']) ||
    empty($_POST['email']) ||
    empty($_POST['message']) ||
    empty($_POST['captcha']) ||
    !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
) {
    http_response_code(400);
    echo 'please check your details and the captcha!';
    exit();
}

$captcha = strip_tags(htmlspecialchars($_POST['captcha']));
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = ['secret'   => file_get_contents('captcha_secret'), 'response' => $captcha, 'remoteip' => $_SERVER['REMOTE_ADDR']];
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];
$context  = stream_context_create($options);
$response = json_decode(file_get_contents($url, false, $context), true);
if($response['success'] == false) {
    http_response_code(400);
    echo 'it seems that there was a captcha error!';
    exit();
}

$name = strip_tags(htmlspecialchars($_POST['name']));
$email_address = strip_tags(htmlspecialchars($_POST['email']));
$message = strip_tags(htmlspecialchars($_POST['message']));

$to = 'info@securitysquad.de';
$email_subject = "Website Contact Form:  $name";
$email_body = "You have received a new message from your website contact form.\n\n" . "Here are the details:\n\nName: $name\n\nEmail: $email_address\n\nMessage:\n$message";
// This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
$headers = "From: Securitysquad Website <noreply@securitysquad.de>\n";
$headers .= "Reply-To: $email_address";
mail($to, $email_subject, $email_body, $headers);
