<?php

require "./utilities/database.php";
require "./utilities/blockchain.php";

$address = getWalletAddress(get_user_wallet_id($_GET["email"]));

$flag = false;
foreach (getTransactionList() as $transaction) {
    if ($transaction["sourceAddress"] == $address && $transaction["amounts"][0] == $_GET["price"]) {
        $flag = true;
        echo json_encode(["status" => "success"]);
    }
}

if (!$flag)
    echo json_encode(["status" => "error"]);


// $check = check($address, $_GET["price"], $_GET["date"]);

// if ($check) {

//     foreach (getTransactionList() as $transaction) {
//         if ($transaction["sourceAddress"] == $address && )
//         echo $transaction . "\n";
//     }

//     echo json_encode(["status" => "success"]);

// } else
//     