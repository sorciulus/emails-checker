# Email Checker

[![Packagist](https://img.shields.io/badge/packagist-0.2.0-lightgrey.svg)](https://packagist.org/packages/sorciulus/email-checker) ![Build Status](https://travis-ci.org/sorciulus/emails-checker.svg?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sorciulus/emails-checker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sorciulus/emails-checker/?branch=master) [![Code Climate](https://codeclimate.com/github/sorciulus/emails-checker/badges/gpa.svg)](https://codeclimate.com/github/sorciulus/emails-checker) [![Issue Count](https://codeclimate.com/github/sorciulus/emails-checker/badges/issue_count.svg)](https://codeclimate.com/github/sorciulus/emails-checker) [![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)](http://php.net/manual/en/migration70.new-features.php) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

PHP library to check if an email exists to SMTP Server. 

## Installation

Via [Composer](http://getcomposer.org/):

```
composer require sorciulus/email-checker
```
## Usage

Basic use of EmailChecker with email argument constructor :

```php
<?php
require_once 'vendor/autoload.php';

use sorciulus\EmailChecker\EmailChecker;
use sorciulus\EmailChecker\Exception\EmailCheckerException;
try {
    $check = new EmailChecker("foo@rmqkr.net", "sender@email.com");
	$result = $check->validate()->isValid(); // instance of ResponseChecker
	if ($result) {
	    echo "Email is valid";
	} else {
	    echo "Email not valid";
	}   
} catch (EmailCheckerException $ex) {
	echo $ex->getMessage();	
}
	
```

Loop usage of EmailChecker with setEmail method : 

```php
<?php
require_once 'vendor/autoload.php';

use sorciulus\EmailChecker\EmailChecker;
use sorciulus\EmailChecker\Exception\EmailCheckerException;
$emails = ["bar@rmqkr.net", "foo@rmqkr.net", "vakol@yopmail.com"];
$check = new EmailChecker("vufeti@vipepe.com");
// you can set Sender outside loop 
// $check->setSender("sender@email.com");
foreach($emails as $email) {
    try {
        // or you can set Sender inside loop 
        //$check->setSender("sender@email.com");
        $check->setEmail($email);
    	$result = $check->validate()->isValid(); // instance of ResponseChecker
    	if ($result) {
    	    echo "Email is valid";
    	} else {
    	    echo "Email not valid";
    	}   
    } catch (EmailCheckerException $ex) {
    	echo $ex->getMessage();	
    }
}
	
```

Debugging is always present in both the ResponseChecker object and in the EmailCheckerException :

 ```php
try {
    $check  = new EmailChecker("foo@rmqkr.net");
	$result = $check->validate(); // instance of ResponseChecker
	$debug  = $result->getDebug();
	if ($result->isValid()) {
	    echo "Email is valid";
	} else {
	    echo "Email not valid";
	}   
} catch (EmailCheckerException $ex) {
    $debug  = $ex->getDebug();
	echo $ex->getMessage();	
}
 
```
The debug is an array of SMTP command executed and relative response .

> In case of massive use i would suggest using a machine side proxy.

### Laravel 5
Once this operation is complete, simply add the service provider class to your project's `config/app.php` file:

#### Service Provider
```php
sorciulus\EmailChecker\Laravel\EmailCheckerServiceProvider::class,
```

#### Facade
To use facade you have to add this line in `config/app.php` in aliases array
```php
'EmailChecker' => sorciulus\EmailChecker\Laravel\Facades\EmailChecker::class,
```
#### Example Usage With Facade
 
 ```php
use \EmailChecker; 
use sorciulus\EmailChecker\Exception\EmailCheckerException; 

try{
	EmailChecker::setEmail("foo@rmqkr.net"); 
	$validate = EmailChecker::validate();	
    if ($validate->isValid()) {
       echo "Email is valid";
   } else {
       echo "Email not valid";
   } 
} catch (EmailCheckerException $e) {
	echo $e->getMessage();
}

```

***

### Todos

 - Integration with Symfony2

License
----
This Library is released under the MIT License. Please see License File for more information.
