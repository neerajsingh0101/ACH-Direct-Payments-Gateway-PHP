<?php
ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . '../src/');

require_once 'Services/ACHDirect.php';
require_once 'Zend/Date.php';

$merchantID = 0;
$apiLoginID = 'xxxxxxx';
$secureTransactionKey = 'xxxxxxx';

$achDirectWS = new Services_ACHDirect($merchantID, $apiLoginID, $secureTransactionKey);

$date = new Zend_Date(array('year' => 2011, 'month' => 03, 'day' => 31));
echo "looking for " .$date->toString() . "\n";
$transactions = $achDirectWS->searchTransactionsByDay($date);
		
foreach ($transactions as $transaction) {
	print_r($transaction);
}