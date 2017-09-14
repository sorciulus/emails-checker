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


use sorciulus\EmailChecker\Exception\MxCheckerException;
use sorciulus\EmailChecker\Exception\MxFunctionException;
/**
* Class to extract mx record from domain
*/
class MxChecker implements MxInterface
{
	/**
	 * List of record mx found
	 * @var array
	 */
	private $recordMx;

	/**
	 * @param string $domain
	 *
	 * @throws MxFunctionException if function not found
	 */
	function __construct($domain)
	{
		if (!function_exists("dns_get_record")) {						
			throw new MxFunctionException("dns_get_record() has been disabled for security reasons. Try to enable.");              
		}
		$this->recordMx = @dns_get_record($domain, DNS_MX);
	}

	/**
	 * Get the value of recordMx
	 * 
	 * @return array 
	 *
	 * @throws MxCheckerException if record not found
	 */
	public function getRecordMx()
	{
        if(empty($this->recordMx)){
        	throw new MxCheckerException("Record Mx not found");
        }
        $this->sortRecordByPriority();        	
        return $this->recordMx;
	}

	/**
	 * Sort asc mx record by priority
	 * 
	 * @return array
	 */
	private function sortRecordByPriority()
	{
		$pri = [];
		foreach ($this->recordMx as $value) {
			$pri[] = $value["pri"];
		}
		array_multisort($pri, SORT_ASC, $this->recordMx);
	}
}