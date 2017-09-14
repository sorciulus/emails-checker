<?php

/*
 * This file is part of EmailChecker.
 *
 * (c) Corrado Ronci <sorciulus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sorciulus\EmailChecker;

use sorciulus\EmailChecker\Exception\EmailCheckerException;
use sorciulus\EmailChecker\Exception\MxCheckerException;
use sorciulus\EmailChecker\Exception\MxFunctionException;
use sorciulus\EmailChecker\MxChecker;
use sorciulus\EmailChecker\SmtpChecker;
use sorciulus\EmailChecker\ResponseChecker;

/**
 * Check Email Class
 */
class EmailChecker
{	

	/**
	 * Email Check
	 * @var string
	 */
	private $email;

	/**
	 * Email to use from Sender
	 * @var string
	 */
	private $sender;

	/**
	 * Sets the stream timeout.
	 * @var integer
	 */
	private $timeout = 10;

	/**
	 * @param string $email The email address to check
	 * @param string $sender The email address to set for sender
	 */
	function __construct($email = "", $sender = "")
	{
		if (!empty($email)) {
			$this->setEmail($email);
		}
		if (!empty($sender)) {
			$this->setSender($sender);
		}		
	}

	/**
	 *	Set Email 
	 * 
	 * @param string $email The email address to check
	 *
	 * @throws EmailCheckerException if the email is not valid
	 */
	public function setEmail($email)
	{		
		$this->clearEmail();
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new EmailCheckerException("Email not valid");
		}
		$this->email = $email;	
	}

	/**
	 *	Set Sender 
	 * 
	 * @param string $sender The email address to set for sender
	 *
	 * @throws EmailCheckerException if the email is not valid
	 */
	public function setSender($sender)
	{
		if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
			throw new EmailCheckerException("Sender not valid");				
		}
		$this->sender = $sender;	
	}

	/**
	 *  Clear Email address
	 */
	private function clearEmail()
	{
		$this->email = null;
	}

	/**
	 * Extract domain from set Email
	 * 
	 * @return string $domain The domain extract from set Email
	 *
	 * @throws EmailCheckerException if the email setted are invalid or empty
	 */
	public function getDomain()
	{
		if (empty($this->getEmail())) {
			throw new EmailCheckerException("Email was not empty");				
		}
		$domain = explode("@", $this->getEmail());				
		return end($domain);
	}

	/**
    * Get the value of email.
    *
    * @return string
    */
	public function getEmail()
	{
		return $this->email;
	}

	/**
    * Get the value of sender.
    *
    * @return string
    */
	public function getSender()
	{
		return $this->sender;
	}
	
	/**
    * Gets the Sets the stream timeout.
    *
    * @return integer
    */
    public function getTimeout()
    {
        return $this->timeout;
    } 

    /**
    * Sets the Sets the stream timeout.
    *
    * @param integer $timeout the timeout
    *
    * @return self
    */
    public function setTimeout($timeout)
    {
        if (is_int($timeout)) {
        	$this->timeout = $timeout;
        }        
		return $this;
    }	

	/**
    * This function extract the mx record from domain
    *
    * @return sorciulus\EmailChecker\MxChecker
    *
    * @throws MxCheckerException if domain is not avaible
    */
	private function checkDomain(){

		return new MxChecker($this->getDomain());		 
	}

	/**
    * This function check email is valid from SMTP command
    *
    * @param  object MxChecker $recordMx
    *
    * @return object SmtpChecker 
    *
    * @throws SmtpCheckerException if SMTP return error
    */
	private function checkSMTP(MxInterface $recordMx)
	{
		$smtp = new SmtpChecker(
			$recordMx,
			$this->getSender(),
			$this->getTimeout()
		);
		return $smtp->validate($this->getEmail());
	}

	/**
	 * This function is a wrapper for two function. 
	 * First extract and get MX record from domain email @MxChecker
	 * Second call the SMTP server to ask the email are valid @SmtpChecker
	 * 
	 * @return object ResponseChecker $response
	 * 
	 * @throws SmtpCheckerException if SMTP return error
	 * @throws EmailCheckerException if the email setted are invalid or empty
	 */
	public function validate()
	{	
		if (empty($this->getEmail())) {
			throw new EmailCheckerException("Email was not empty");	
		}	
		$recordMx  = $this->checkDomain();
		$checkSMTP = $this->checkSMTP($recordMx);
		$response  = new ResponseChecker();
		$response
			->setRecordMx($recordMx)
			->setIsValid($checkSMTP->isValid())
			->setMessage($checkSMTP->getMessage())
			->setCode($checkSMTP->getCode())
			->setDebug($checkSMTP->getDebug())
		;
		return $response; 	
	}
}	