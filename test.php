<?php

require_once 'vendor/autoload.php';

use sorciulus\EmailChecker\EmailChecker;
use sorciulus\EmailChecker\Exception\EmailCheckerException;
$check = new EmailChecker();
try {
	$check->setEmail("sorciulus@gmail.com");
	$result = $check->validate();
	var_dump($result);
} catch (EmailCheckerException $ex) {
	echo $ex->getMessage().PHP_EOL;	
	var_dump($ex->getDebug());
}
	