README
======

This project provides PHP library to use ACH Direct Web Services.

http://www.paymentsgateway.com/developerDocumentation/Integration/webservices/merchantservice.aspx

Key Features
------------
* Authorization with 3scale based on app_id & app_key
* XML & JSON friendly formatted responses


The project requires:

* Zend Framework 1.11 (http://framework.zend.com/)
* ACH Direct merchant account with API access


Examples
---------------------
getTransaction.php - example which returns a transaction information
searchTransactions.php - example which returns array of transactions in a specific day
