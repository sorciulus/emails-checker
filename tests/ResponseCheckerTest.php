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
use sorciulus\EmailChecker\MxChecker;
use sorciulus\EmailChecker\ResponseChecker;

class ResponseCheckerTest extends TestCase
{
	protected $responseChecker;
    protected $mxChecker;

	public function setUp()
    {
        $this->responseChecker = new ResponseChecker();
        $this->mxChecker = $this->createMock(MxChecker::class); 
        $this->mxChecker->method('getRecordMx')
            ->willReturn
            (
                [
                    [
                        "target" => "alt2.gmail-smtp-in.l.google.com",
                        "host"   => "gmail.com"
                    ]
                ]
            )
        ;
    }

    public function testSetCodeAssertTrue()
    {
    	$this->responseChecker->setCode(50);

    	$this->assertEquals(50, $this->responseChecker->getCode());
    }
	
    public function testSetIsValidAssertTrue()
    {
        $this->responseChecker->setIsValid(true);

        $this->assertTrue($this->responseChecker->IsValid());
    }

    public function testSetMessageAssertTrue()
    {
        $this->responseChecker->setMessage("Message");

        $this->assertEquals("Message", $this->responseChecker->getMessage());
    }

    public function testSetRecordMxAssertTrue()
    {
        $this->responseChecker->setRecordMx($this->mxChecker);

        $this->assertEquals($this->mxChecker, $this->responseChecker->getRecordMx());
    }

    public function testSetDebugAssertTrue()
    {
        $this->responseChecker->setDebug(["a","b","c"]);

        $this->assertEquals(["a","b","c"], $this->responseChecker->getDebug());
    }
    
}