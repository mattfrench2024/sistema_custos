<?php

$ch = curl_init("https://app.omie.com.br/api/v1/geral/tiposdoc/");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'app_key'    => '4508256158406',
        'app_secret' => '39b1561e8a07688ddc7352250bbf69d2',
    ]),
    // 🔴 APENAS PARA DEBUG
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
]);

$response = curl_exec($ch);

if ($response === false) {
    var_dump(curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

var_dump($httpCode, $response);
