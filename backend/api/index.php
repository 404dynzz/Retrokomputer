<?php

// Decode database password if passed as base64 (to bypass GitHub push protection)
if (isset($_ENV['DB_PASSWORD_BASE64'])) {
    $_ENV['DB_PASSWORD'] = base64_decode($_ENV['DB_PASSWORD_BASE64']);
    putenv('DB_PASSWORD=' . $_ENV['DB_PASSWORD']);
}
if (isset($_SERVER['DB_PASSWORD_BASE64'])) {
    $_SERVER['DB_PASSWORD'] = base64_decode($_SERVER['DB_PASSWORD_BASE64']);
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Illuminate\Http\Request::capture());
