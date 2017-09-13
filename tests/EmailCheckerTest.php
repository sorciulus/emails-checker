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
use sorciulus\EmailChecker\EmailChecker;
use sorciulus\EmailChecker\ResponseChecker;
use sorciulus\EmailChecker\Exception\EmailCheckerException;

class EmailCheckerTest extends TestCase
{
	private $emailChecker;

	public function setUp()
    {
        $this->emailChecker = new EmailChecker();
    }

    public function testSetEmailAssertTrue()
    {
    	$this->emailChecker->setEmail("sorciulus@gmail.com");

    	$this->assertEquals("sorciulus@gmail.com", $this->emailChecker->getEmail());
    }
	
    public function testSetEmailMustBeValidEmail()
    {
    	$this->expectException(EmailCheckerException::class);

    	$this->emailChecker->setEmail("fakemail@aai");
    }

    public function testSetSenderAssertTrue()
    {
    	$this->emailChecker->setSender("sorciulus@gmail.com");

    	$this->assertEquals("sorciulus@gmail.com", $this->emailChecker->getSender());
    }
	
    public function testSetSenderMustBeValidEmail()
    {
    	$this->expectException(EmailCheckerException::class);

    	$this->emailChecker->setSender("fakemail@aai");
    }

    public function testGetDomainEqualToDomainSetMail()
    {
    	$this->emailChecker->setEmail("truemail@email.com");    	

    	$this->assertEquals("email.com", $this->emailChecker->getDomain());
    }

    public function testGetDomainMustBeSettedEmail()
    {
    	$this->expectException(EmailCheckerException::class);

    	$this->assertEquals("email.com", $this->emailChecker->getDomain());
    }

    public function testSetTimeoutAssertTrue()
    {
    	$this->emailChecker->setTimeout(60);    	

    	$this->assertEquals(60, $this->emailChecker->getTimeout());
    }

    public function testSetTimeoutNotSet()
    {
    	$this->emailChecker->setTimeout("60");    	

    	$this->assertEquals(10, $this->emailChecker->getTimeout());
    }

    public function testValidationTrue()
    {    	
    	$this->emailChecker->setEmail("sorciulus@gmail.com"); 

    	$this->assertInstanceOf(ResponseChecker::class, $this->emailChecker->validate());
    }

    public function testValidationEmailNoRecordMx()
    {    	
    	$this->expectException(EmailCheckerException::class);

    	$this->emailChecker->setEmail("sorciulus@aaabbbccc.com"); 

    	$this->emailChecker->validate();
    }
}