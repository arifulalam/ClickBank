# ClickBank&reg; API Library

## Initialize:
first of all have to include this file

```php
include('clickbank.php');
```

Then create an object of clickbank class

```php
$clickbank = new ClickBank(array(
    'account' 	=> 'ACCOUNT_NAME',
    'api_key' 	=> 'API-KEY',
    'dev_key' 	=> 'DEV-KEY',
    'secret_key'=> 'SECRET_KEY'
    )
  );
```

where,<br/>
ACCOUNT_NAME = ClickBank&reg; Account Name (Selling a/c) item_id.**account**.pay.clickbank.net<br/>
API-KEY = API Key that available in ClickBank&reg; Master A/c<br/>
DEV-KEY = DEV key that available in ClickBank&reg; Account (selling a/c) SETTINGS -> My Account -> Developer API Keys<br/>
SECRET_KEY = Available in ClickBank&reg; Account (selling a/c) SETTINGS -> My Site -> Advanced Tools<br/>

After creating object will be able to call functions

### To get all the orders of an email
```php
$orders = $clickbank->getOrderByEmail('email@domain.com');
print_r($orders);
```

### To get all the orders of given receipt
```php
$orders = $clickbank->getOrderByReceipt('XXXYYYZZZ', SKU);
print_r($orders);
```
where,<br/>
**XXXYYYZZZ** = Receipt number (required)<br/>
**SKU** = receipt product/item ID/SKU (optional)<br/>

### To get all the orders of given item id/sku
```php
$orders = $clickbank->getOrderByItemID(SKU);
print_r($orders);
```
where,<br/>
**SKU** = receipt product/item ID/SKU (optional)<br/>

### To get all the orders of given Date(s)
```php
$orders = $clickbank->getOrderByDate(startDate, endIDate);
print_r($orders);
```
where,<br/>
**startDate** = The beginning date for the search (yyyy-mm-dd) <br/>
**endDate** = The end date for the search (yyyy-mm-dd) (optional) if not given, current date will be used.<br/>

### To get order by ClickBank&reg; specific parameters
Parameters are optional. If parameter(s) given, then matching orders will be returned. Otherwise all orders will be returned.
```php
$params = array(
    'startDate' => date('Y-m-d', strtotime('-1 week')),
    'endDate' 	=> date('Y-m-d', strtotime('-1 day')),
    'type' 		=> 'SALE', //SALE / RFND / CGBK / FEE / BILL / TEST_SALE / TEST_BILL / TEST_RFND /TEST_FEE
    'email' 	=> 'ariful-alam@hotmail.com',
    'item' 		=> 1
    'vendor' 	=> 'VENDOR_NAME',
    'affiliate' => 'AFFILIATE_NAME',
    'lastName' 	=> 'CUSTOMER_LAST_NAME',
    'tid' 		=> 'TRACKING ID/ PROMO CODE',
    'role' 		=> 'VENDOR', //VENDOR / AFFILIATE
    'postalCode'=> 'CUSTOMER_POSTAL/ZIP_CODE',
    'amount' 	=> 'TOTAL_TRANSACTION_AMOUNT'
  );
$orders = $clickbank->getOrderByParam($params);
print_r($orders);

$orders = $clickbank->getOrderByParam();
print_r($orders);
```
where<br/>
**startDate** = The beginning date for the search (yyyy-mm-dd) <br/>
**endDate** = The end date for the search (yyyy-mm-dd)<br/>
*_NOTE_*: If startDate is given but not endDate, then current date will be used as endDate. If endDate is given but not startDate, then endDate will be removed from given parameters.<br/>
**type** = The type of transactions to be returned. Supported types are [SALE / RFND / CGBK / FEE / BILL / TEST_SALE / TEST_BILL / TEST_RFND / TEST_FEE]. 
If not specified all types will be returned. If an invalid type is specified, no transactions will be returned.<br/>
**email** = The email of the customer. Supports wildcard searches using the '%' character. (Wildcards are converted to %25 after url encoding is done by the client)<br/>
**item** = ClickBank&reg; product sku<br/>
**vendor** = The vendor name. Supports wildcard searches using the '%' character. (Wildcards are converted to %25 after url encoding is done by the client)<br/>
**affiliate** = The affiliate name. Supports the word 'none' to search for transactions without affiliates, and wildcard searches using the '%' character. (Wilcards are converted to %25 after url encoding is done by the client)<br/>
**lastName** = Customers last name. Supports wildcard searches using the '%' character. (Wildcards are converted to %25 after url encoding is done by the client)<br/>
**tid** = The TID (Tracking ID / Promo Code) to search on. This will search both vendor and affiliate tracking codes and be returned in the promo field<br/>
**role** = Role account was of transaction options are [VENDOR, AFFILIATE]<br/>
**postalCode**	=	Customer's zip or postal code. Supports wildcard searches.<br/>
**amount**	= The transaction total amount<br/>

### To count total orders of given email
```php
$orders = $clickbank->getOrderCountByEmail('email@domain.com');
print_r($orders);
```

### To count total orders of given Date(s)
```php
$orders = $clickbank->getOrderByDate(startDate, endIDate);
print_r($orders);
```
where,<br/>
**startDate** = The beginning date for the search (yyyy-mm-dd) <br/>
**endDate** = The end date for the search (yyyy-mm-dd) (optional) if not given, current date will be used.<br/>

### To count total orders of given parameters
Parameters are optional. If parameter(s) given, then matching orders will be returned. Otherwise all orders will be returned.
```php
$params = array(
    'startDate' => date('Y-m-d', strtotime('-1 week')),
    'endDate' 	=> date('Y-m-d', strtotime('-1 day')),
    'type' 		=> 'SALE', //SALE / RFND / CGBK / FEE / BILL / TEST_SALE / TEST_BILL / TEST_RFND /TEST_FEE
    'email' 	=> 'ariful-alam@hotmail.com',
    'item' 		=> 1
    'vendor' 	=> 'VENDOR_NAME',
    'affiliate' => 'AFFILIATE_NAME',
    'lastName' 	=> 'CUSTOMER_LAST_NAME',
    'tid' 		=> 'TRACKING ID/ PROMO CODE',
    'role' 		=> 'VENDOR', //VENDOR / AFFILIATE
  );
$orders = $clickbank->getOrderCountByParam($params);
print_r($orders);

$orders = $clickbank->getOrderCountByParam();
print_r($orders);
```
where<br/>
**startDate** = The beginning date for the search (yyyy-mm-dd) <br/>
**endDate** = The end date for the search (yyyy-mm-dd)<br/>
*_NOTE_*: If startDate is given but not endDate, then current date will be used as endDate. If endDate is given but not startDate, then endDate will be removed from given parameters.<br/>
**type** = The type of transactions to be returned. Supported types are [SALE / RFND / CGBK / FEE / BILL / TEST_SALE / TEST_BILL / TEST_RFND / TEST_FEE]. 
If not specified all types will be returned. If an invalid type is specified, no transactions will be returned.<br/>
**email** = The email of the customer. Supports wildcard searches using the '%' character. (Wildcards are converted to %25 after url encoding is done by the client)<br/>
**item** = ClickBank&reg; product sku<br/>
**vendor** = The vendor name. Supports wildcard searches using the '%' character. (Wildcards are converted to %25 after url encoding is done by the client)<br/>
**affiliate** = The affiliate name. Supports the word 'none' to search for transactions without affiliates, and wildcard searches using the '%' character. (Wilcards are converted to %25 after url encoding is done by the client)<br/>
**lastName** = Customers last name. Supports wildcard searches using the '%' character. (Wildcards are converted to %25 after url encoding is done by the client)<br/>
**tid** = The TID (Tracking ID / Promo Code) to search on. This will search both vendor and affiliate tracking codes and be returned in the promo field<br/>
**role** = Role account was of transaction options are [VENDOR, AFFILIATE]<br/>

### To check given recurring receipt product is active or not
```php
$orders = $clickbank->getOrderStatus('XXXYYYZZZ', SKU);
print_r($orders);
```
where,<br/>
**XXXYYYZZ** = Recurring product's receipt number <br/>
**SKU** = Specific product/item ID/SKU (optional)<br/>
will return 0 if not active or 1 if active.<br/>

(will continue to update this doc...)
