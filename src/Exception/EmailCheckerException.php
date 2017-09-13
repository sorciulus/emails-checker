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
 * Exception when the given email is not valid.
 */
class EmailCheckerException extends \RuntimeException
{
	/**
     * The debug info 
     * @var mixed
     */
    private $debug;

	public function __construct($msg, array $debug = [])
    {
        $this->debug = $debug;        
        parent::__construct($msg);
    }
    /**
     * @return Debug Info
     */
    public function getDebug()
    {
        return $this->debug;
    }
}