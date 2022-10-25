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