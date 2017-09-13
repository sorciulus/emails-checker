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

use sorciulus\EmailChecker\MxInterface;

/**
 * This class rapresents the response object from EmailChecker
 */
class ResponseChecker
{
	
	/**
     * Code response from SMTP Server
     * @var integer
     */
    private $code;

    /**
     * Check if email is valid
     * @var boolean
     */
	private $isValid;

    /**
     * Message response from SMTP Server
     * @var string
     */
	private $message;

    /**
     * instance of MxInterface
     * @var object
     */
	private $recordMx;

    /**
     * List SMTP Action of Library
     * @var array
     */
    private $debug;

    /**
    * Gets the value of code.
    *
    * @return integer
    */
    public function getCode()
    {
        return $this->code;
    } 
    /**
    * Sets the value of code.
    *
    * @param mixed $code the code
    *
    * @return self
    */
    public function setCode($code)
    {
        $this->code = $code;
	
		return $this;
    } 
    /**
    * Gets the value of isValid.
    *
    * @return boolean
    */
    public function IsValid()
    {
        return $this->isValid;
    } 
    /**
    * Sets the value of isValid.
    *
    * @param mixed $isValid the is valid
    *
    * @return self
    */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
	
		return $this;
    } 
    /**
    * Gets the value of message.
    *
    * @return string
    */
    public function getMessage()
    {
        return $this->message;
    } 
    /**
    * Sets the value of message.
    *
    * @param mixed $message the message
    *
    * @return self
    */
    public function setMessage($message)
    {
        $this->message = $message;
	
		return $this;
    } 
    /**
    * Gets the value of recordMx.
    *
    * @return MxInterface
    */
    public function getRecordMx()
    {
        return $this->recordMx;
    } 
    /**
    * Sets the value of recordMx.
    *
    * @param MxInterface
    *
    * @return self
    */
    public function setRecordMx(MxInterface $recordMx)
    {
        $this->recordMx = $recordMx;
	
		return $this;
    }
 
    /**
    * Gets the value of debug.
    *
    * @return mixed
    */
    public function getDebug()
    {
        return $this->debug;
    } 
    /**
    * Sets the value of debug.
    *
    * @param mixed $debug the debug
    *
    * @return self
    */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    
        return $this;
    }
}