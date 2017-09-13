<?php 

/*
 * This file is part of EmailChecker.
 *
 * (c) Corrado Ronci <sorciulus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sorciulus\EmailChecker\Exception;

/**
 * Exception when the server SMTP return error 
 */
class SmtpCheckerException extends EmailCheckerException
{
	public function __construct($msg, array $debug = [])
    {        
        parent::__construct($msg, $debug);
    }
}