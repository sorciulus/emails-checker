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

use sorciulus\EmailChecker\Exception\SmtpCheckerException;
use sorciulus\EmailChecker\MxChecker;
use \Graze\TelnetClient\Exception\TelnetException;
use \miyahan\network\Telnet;
/**
* This Class check the email address is valid 
* through commands executed on the SMTP Server
*/
class SmtpChecker implements SmtpInterface
{
	/**
	 * Instance of Telnet class
	 * @var object Telnet
	 */
	private $client;

	/**
	 * The domain of SMTP Server
	 * @var string
	 */
	private $domain;

	/**
	 * Response Code SMTP Server
	 * @var integer
	 */
	private $code;

	/**
	 * The given email is valid or not.
	 * @var boolean
	 */
	private $isValid;

	/**
	 * List of command and response executed
	 * @var array
	 */
	private $debug = [];

	/**
	 * The sender of SMTP check email
	 * @var string
	 */
	private $sender;

	// some smtp response codes
    const SMTP_CONNECT_SUCCESS = 220;
    const SMTP_QUIT_SUCCESS = 221;
    const SMTP_GENERIC_SUCCESS = 250;
    const SMTP_USER_NOT_LOCAL = 251;
    const SMTP_CANNOT_VRFY = 252;
    const SMTP_SERVICE_UNAVAILABLE = 421;
    // 450  Requested mail action not taken: mailbox unavailable (e.g.,
    // mailbox busy or temporarily blocked for policy reasons)
    const SMTP_MAIL_ACTION_NOT_TAKEN = 450;
    // 451  Requested action aborted: local error in processing
    const SMTP_MAIL_ACTION_ABORTED = 451;
    // 452  Requested action not taken: insufficient system storage
    const SMTP_REQUESTED_ACTION_NOT_TAKEN = 452;
    // 500  Syntax error (may be due to a denied command)
    const SMTP_SYNTAX_ERROR = 500;
    // 502  Comment not implemented
    const SMTP_NOT_IMPLEMENTED = 502;
    // 503  Bad sequence of commands (may be due to a denied command)
    const SMTP_BAD_SEQUENCE = 503;
    // 550  Requested action not taken: mailbox unavailable (e.g., mailbox
    // not found, no access, or command rejected for policy reasons)
    const SMTP_MBOX_UNAVAILABLE = 550;
    const SMTP_USER_NOT_LOCAL_FORWARD = 551;
    // 552  Requested mail action aborted: exceeded storage allocation
    const SMTP_EXCEEDED_STORAGE_ALLOCAION = 552;
    //553  Requested action not taken: mailbox name not allowed (e.g.,
    //mailbox syntax incorrect)
    const SMTP_MBOX_NAME_NOT_ALLOWED = 553;
    // 554  Seen this from hotmail MTAs, in response to RSET :(
    const SMTP_TRANSACTION_FAILED = 554;

    /**    
     * @param array $domains List of SMTP domains 
     * @param boolean $sender Set sender of STMP command 
     * @param integer $timeout Timeout of the stream
     *
     * @throws SmtpCheckerException when all domain not valid
     */
	function __construct(MxInterface $domains, $sender = "no-replay@email.com", int $timeout = 10)
	{						
		foreach ($domains->getRecordMx() as $domain) {
			try {				
				$this->debug  = ["Connect to : ".$domain["target"]." on port 25"];
				$this->client = new Telnet($domain["target"], 25, $timeout);								
				$this->domain = $domain;
				break;
			} catch (\Exception $ex) {							
				continue;
			}
		}
		if (empty($this->getDomain())) {
			throw new SmtpCheckerException("Error Processing Request ".$ex->getMessage());			
		}
		if (!empty($sender)) {
			$this->setSender($sender);
		}
		$this->sayHello();
		$this->setFrom();		
	}

	/**
    * Sets the The sender of SMTP check email.
    *
    * @param string $sender the sender
    *
    * @return void
    */
    private function setSender($sender)
    {
        if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
			throw new SmtpCheckerException("Email Sender not valid");		
		}
		$this->sender = $sender;				
		       		
    }

	/**
    * Gets the value of sender.
    *
    * @return string
    */
	private function getSender()
	{
		return $this->sender;
	}

	/**
    * Gets the value of domain.
    *
    * @return array
    */
	private function getDomain()
	{
		return $this->domain;
	}

	/**
    * Gets the value of getDomain in object.
    *
    * @return object
    */
	private function getDomainObj()
	{
		return (object) $this->getDomain();
	}

	/**
	 * Send command HELO to SMTP Server
	 * 
	 * @return void
	 *
	 * @throws SmtpCheckerException when the SMTP server return error
	 */
	private function sayHello()
	{
		$host = $this->getDomainObj()->host;
		try{				
			$status = $this->commandExec(sprintf("HELO %s", $host));				
			if (!in_array(self::SMTP_GENERIC_SUCCESS, $status)) {
				$this->disconnect();
				throw new SmtpCheckerException("SMTP Status unexpected: ".implode(",", $status), $this->getDebug());	 
			}
		}catch (\Exception $ex) {			
			throw new SmtpCheckerException("Error Processing Request ".$ex->getMessage(), $this->getDebug());							
		}
		
	}

	/**
	 * Set MAIL FROM to SMTP Server
	 * 
	 * @return void
	 *
	 * @throws SmtpCheckerException when the SMTP server return error
	 */
	private function setFrom()
	{		
		try{
			$status = $this->commandExec(sprintf("MAIL FROM:<%s>", $this->getSender()));				
			if (!in_array(self::SMTP_GENERIC_SUCCESS, $status)) {
				$this->disconnect();
				throw new SmtpCheckerException("SMTP Status unexpected: ".implode(",", $status), $this->getDebug());	 
			}
		}catch (\Exception $ex) {					
			throw new SmtpCheckerException("Error Processing Request setFrom : ".$ex->getMessage(), $this->getDebug());
		}
		
	}

	/**
	 * Wrapper to Telnet Client to Execute SMTP command, will return the status code
	 * 
	 * @param  string $command   command execute
	 * @return array  SMPT status
	 */
	private function commandExec($command)
	{		
		$this->debug[] = $command;	
		$exec = $this->client->exec($command);	
		return $this->getResponseStatus($exec);		
	}

	/**
	 * Get response status code of previous SMTP request 
	 * 
	 * @return array $status The status of SMTP request
	 *
	 * @throws SmtpCheckerException when the SMTP server return unknown error
	 */
	private function getResponseStatus($output)
	{
		$status = [];
		$strout = explode("\n", $output);			
		if (empty($strout)) {
			$strout = [$output];
		}				
		foreach ($strout as $str) {
			$this->debug[] = $str;
			if(preg_match('/^[0-9]{3}/', $str, $match)) {				
				$status[] = current($match);
			}
		}
		if(empty($status)) {			
			throw new SmtpCheckerException("SMTP Status request Unknown, ". $output, $this->getDebug());			
		}
		return $status;
	}


	/**
	 * Close connection to SMTP Server
	 * 
	 * @return void
	 */
	private function disconnect()
	{
		$this->client->exec("quit");		
		$this->client->disconnect();
		$this->debug[] = "QUIT";
	}

	/**
	 *	Set RCPT command to SMTP server
	 * 
	 * @param string $email The email to validate
	 * @return self
	 *
	 * @throws SmtpCheckerException when the SMTP server return error
	 */
	public function validate($email)
	{
		try{			
			$command = sprintf("RCPT TO:<%s>", $email);
			$exec = $this->client->exec($command);
			$this->debug[] = $command;			
			$status = $this->getResponseStatus($exec);										
			$this->disconnect();
			$this->code = end($status);
			$this->isValid = false;
			if (in_array($this->getCode(), [self::SMTP_USER_NOT_LOCAL, self::SMTP_GENERIC_SUCCESS])) {
				$this->isValid = true;
			} 
			return $this;			
		}catch (\Exception $ex) {						
			throw new SmtpCheckerException("Error Processing Request Validate : ".$ex->getMessage(), $this->getDebug());
		}
	}

    /**
    * Gets the value of code.
    *
    * @return integer
    */
    public function getCode()
    {
        return (int) $this->code;
    } 

    /**
    * Gets the value of isValid.
    *
    * @return boolean
    */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
    * Gets the value of debug.
    *
    * @return array
    */
    public function getDebug()
    {
        return $this->debug;
    } 

    /**
    * Gets the value of message.
    *
    * @return string
    */
    public function getMessage()
    {
        $code     = $this->getCode();
        $messages = [
        	self::SMTP_CONNECT_SUCCESS => "SMTP connect Success",
		    self::SMTP_QUIT_SUCCESS => "Quit Success",
		    self::SMTP_GENERIC_SUCCESS => "SMTP Generic Success",
		    self::SMTP_USER_NOT_LOCAL => "SMTP User not local",
		    self::SMTP_CANNOT_VRFY => "SMTP Cannot VRFY user",
		    self::SMTP_SERVICE_UNAVAILABLE => "SMTP Service not available",
		    self::SMTP_MAIL_ACTION_NOT_TAKEN => "SMTP mailbox busy or temporarily blocked for policy reasons",		    
		    self::SMTP_MAIL_ACTION_ABORTED => "SMTP Requested action aborted: local error in processing",		    
		    self::SMTP_REQUESTED_ACTION_NOT_TAKEN => "SMTP Requested action not taken: insufficient system storage",		    
		    self::SMTP_SYNTAX_ERROR => "SMTP Syntax error, command unrecognized",		    
		    self::SMTP_NOT_IMPLEMENTED => "SMTP Command not implemented",	    
		    self::SMTP_BAD_SEQUENCE => "SMTP Bad sequence of commands",
		    self::SMTP_MBOX_UNAVAILABLE => "SMTP Requested action not taken: mailbox unavailable",
		    self::SMTP_USER_NOT_LOCAL_FORWARD => "SMTP User not local",		    
		    self::SMTP_EXCEEDED_STORAGE_ALLOCAION => "SMTP Requested mail action aborted: exceeded storage allocation",		    
		    self::SMTP_MBOX_NAME_NOT_ALLOWED => "Requested action not taken: mailbox name not allowed",
		    self::SMTP_TRANSACTION_FAILED => "SMTP Transaction failed"
        ];

        return
        	(array_key_exists($code, $messages))
        	? $messages[$code]
        	: "Unknown message from SMTP";
    }
}