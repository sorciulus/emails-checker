<?php 

/*
 * This file is part of EmailChecker.
 *
 * (c) Corrado Ronci <sorciulus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace sorciulus\EmailChecker\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use sorciulus\EmailChecker\DisponsableChecker;
use sorciulus\EmailChecker\Exception\DisponsableException;

class DisponsableCheckerTest extends TestCase
{
	
    public function testDisponsableCheckerInstanceTrue()
    {
        $instance = new DisponsableChecker();

        $this->assertInstanceOf(DisponsableChecker::class, $instance);
    }

    public function testIsEnableIsBooleanTrue()
    {         
        $instance = new DisponsableChecker(); 
        $this->assertInternalType("boolean", $instance->isEnable());
                       
    }

    public function testApiCall()
    {
        $client = new Client("https://open.kickbox.io", array(
            'request.options' => array(
                'exceptions' => false,
            )
        ));

        $response = $client->get("/v1/disposable/gmail.com");
        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        $this->assertInternalType("string", $body);
        $this->assertInternalType("array", $data);
        $this->assertArrayHasKey("disposable", $data);
        $this->assertInternalType("boolean", $data["disposable"]);
    }
}