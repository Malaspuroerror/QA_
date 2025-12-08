<?php
require_once 'config.php';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
	die("Composer autoload not found. Please run in project root:\ncomposer require google/apiclient:^2.12 phpmailer/phpmailer\n");
}
require_once $autoload;

if (!class_exists('Google_Client')) {
	die("Google API Client not found. Install it with:\ncomposer require google/apiclient:^2.12\n");
}

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope('email');
$client->addScope('profile');

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
