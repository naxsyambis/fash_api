<?php
$JWT_SECRET = "RAHASIA_SUPER_KUAT_123";
$JWT_EXPIRE = 3600; // 1 jam

function generateJWT($payload) {
    global $JWT_SECRET, $JWT_EXPIRE;

    $header = base64_encode(json_encode([
        "alg" => "HS256",
        "typ" => "JWT"
    ]));

    $payload['iat'] = time();
    $payload['exp'] = time() + $JWT_EXPIRE;
    $payload = base64_encode(json_encode($payload));

    $signature = hash_hmac(
        "sha256",
        "$header.$payload",
        $JWT_SECRET,
        true
    );

    return "$header.$payload." . base64_encode($signature);
}

function verifyJWT($token) {
    global $JWT_SECRET;

    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $valid = base64_encode(hash_hmac(
        "sha256",
        "$header.$payload",
        $JWT_SECRET,
        true
    ));

    if ($signature !== $valid) return false;

    $payload = json_decode(base64_decode($payload), true);

    if ($payload['exp'] < time()) return false;

    return $payload;
}
?>
