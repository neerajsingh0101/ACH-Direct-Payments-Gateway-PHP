<?php 
ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . '../src/');

require_once 'Services/ACHDirect.php';

$merchantID = 0;
$apiLoginID = 'xxxxxxx';
$secureTransactionKey = 'xxxxxxx';

$achDirectWS = new Services_ACHDirect($merchantID, $apiLoginID, $secureTransactionKey);

$transactionResponse = $achDirectWS->getTransaction('d3d38944-fbe9-4710-b905-cb440484c794');

print_r($transactionResponse);
