<?php

require_once 'config.inc.php';

function createToken() {
    $seed = random_bytes(8);
    $current_time = time();
    $hash = hash_hmac("sha256", session_id() . $seed . $current_time, CSRF_TOKEN_SECRET, true);
    return urlSafeEncode($seed) . '|' . $current_time . '|' . urlSafeEncode($hash);
}

function validateToken($token) {
    $parts = explode('|', $token);

    if (count($parts) < 3) {
        return false;
    }

    $hash = hash_hmac("sha256", session_id() . urlSafeDecode($parts[0]) . $parts[1], CSRF_TOKEN_SECRET, true);

    if ($hash === urlSafeDecode(implode(array_slice($parts, 2)))) {
        return true;
    }
}

function urlSafeEncode($m) {
    return rtrim(strtr(base64_encode($m), '+/', '-_'), '=');
}

function urlSafeDecode($m) {
    return base64_decode(strtr($m, '-_', '+/'));
}

function replaceNewLineWithHtmlTag($text) {
    if ($text == null || !$text || empty($text)) {
        return "";
    }
    
    return str_replace("\n", "<br>", $text);
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function sendEmail($to, $subject, $message) {
    $mail_headers[] = "MIME-Version: 1.0";
    $mail_headers[] = "From: " . SMTP_FROM;
    $mail_headers[] = "Content-type: text/plain; charset=utf-8";
    
    mb_internal_encoding("UTF-8");
    
    $encoded_subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n", strlen('Subject: '));
    
    return mail($to, $encoded_subject, $message, implode("\r\n", $mail_headers));
}