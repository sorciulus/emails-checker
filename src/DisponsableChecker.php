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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use sorciulus\EmailChecker\Exception\DisponsableException;
/**
* Class to check if email is disponsable
* through https://open.kickbox.io API
*/
class DisponsableChecker
{
	/**
	 * Client HTTP
	 * 
	 * @var GuzzleHttp\Client
	 */
	private $client;

	/**
	 *	This setting allows you to disable 
	 *	disponsable email checker. If FALSE
	 *	the function isDisponsable return NULL
	 * 
	 * @var boolean
	 */
	private $enable = TRUE;

	public function __construct()
	{
		$this->client = new Client([		    
		    "base_uri" => "https://open.kickbox.io",		    
		    "timeout"  => 2.0,
		]);
	}	

	/**
    * Check if service is enabled
    *
    * @return boolean
    */
    public function isEnable()
    {
        return $this->enable;
    }

	/**
	 * Call Kickbox API and return the value
	 * 
	 * @param  string $domain
	 * @return boolean 
	 * @throws DisponsableException
	 */
	public function isDisponsable($domain)
	{
		if (!$this->enable) {
			throw new DisponsableException("DisponsableChecker is disable");
		}
		try {	
			$response = $this->client->get("/v1/disposable/" . $domain);
			$body = json_decode((string)$response->getBody());	
			if (property_exists($body, "disposable")) {
				return $body->disposable;	
			} else {
				throw new DisponsableException("Disponsable service unknown response"); 
			}					
		} catch (TransferException $e) {
			throw new DisponsableException($e->getMessage());
		}
		
	}
}