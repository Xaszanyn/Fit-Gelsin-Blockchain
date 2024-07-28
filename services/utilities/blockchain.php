<?php

function circle_get($url, $key)
{
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $key"
            ),
        )
    );

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Error:' . curl_error($curl);
        curl_close($curl);
        return null;
    }

    curl_close($curl);
    return json_decode($response, true);
}

function circle_post($url, $data, $key, $headers = [])
{
    $curl = curl_init();

    $defaultHeaders = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $key
    ];

    foreach ($headers as $header => $value) {
        $defaultHeaders[] = $header . ': ' . $value;
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $defaultHeaders,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
        curl_close($curl);
        return null;
    }

    curl_close($curl);
    return json_decode($response, true);
}

function uuid()
{
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
}

/* ==================================================================================================== */

function getAppId($key)
{
    $response = circle_get("https://api.circle.com/v1/w3s/config/entity", $key);

    return isset($response['data']['appId']) ? $response['data']['appId'] : null;
}

function createUser($key)
{
    $userId = uuid();

    $response = circle_post("https://api.circle.com/v1/w3s/users", ['userId' => $userId], $key);

    return [
        'userId' => $userId,
        'status' => $response['data']['status'] ?? null
    ];
}

function acquireSessionToken($userId, $key)
{
    $response = circle_post("https://api.circle.com/v1/w3s/users/token", ['userId' => $userId], $key);

    return $response['data'] ?? null;
}

function initializeUser($token, $key)
{
    $response = circle_post("https://api.circle.com/v1/w3s/user/initialize", [
        'idempotencyKey' => uuid(),
        'blockchains' => ['MATIC-AMOY']
    ], $key, [
        'X-User-Token' => $token
    ]);

    return $response['data']['challengeId'] ?? null;
}

/* !!! IT DOES NOT WORK FOR USER CONTROLLED WALLETS !!! */

// function getUserWallet($userId, $key)
// {
//     $response = circle_get("https://api.circle.com/v1/w3s/walletSets", $key);

//     foreach ($response["data"]["walletSets"] as $walletSet) {
//         if ($userId == $walletSet["userId"]) {
//             return $walletSet["id"];
//         }
//     }
// }