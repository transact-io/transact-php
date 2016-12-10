## transact-io.php

This is a library for use with  transact.io

See an example in the demo/  folder


## Working with this repository
```
git clone  <repository URL>
cd transact-io-php
composer install
```

## Usage

Instancate a new instance of the TransactIoMsg class

```php
$transact = new TransactIoMsg(); // get new instance
```


Set the secret that is in the Developer Settings / Keys of the transact
publisher menu
```php
$transact->setSecret('Signing Secret');
```


Set the Account ID of who recieves the funds
```
// Required: set ID of who gets paid
$transact->setRecipient('5206507264147456');
````


Required: set the URL of what is being purchased.   
```
// Note that the host name MUST match the real name for cross site 
// messaging to work. 
$transact->setURL($_REQUEST['url']);
```



