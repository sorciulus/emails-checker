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
use sorciulus\EmailChecker\Exception\MxCheckerException;
use sorciulus\EmailChecker\Exception\MxFunctionException;

class MxCheckerTest extends TestCase
{
	
    public function testMxCheckerInstanceTrue()
    {
        $instance = new MxChecker("gmail.com");

        $this->assertInstanceOf(MxChecker::class, $instance);
    }

    public function testGetRecordMxTrue()
    {         
        $instance = new MxChecker("gmail.com"); 
        $this->assertInternalType("array", $instance->getRecordMx());
        foreach ($instance->getRecordMx() as $value) {
            $this->assertArrayHasKey("host", $value);
            $this->assertArrayHasKey("class", $value);
            $this->assertArrayHasKey("ttl", $value);
            $this->assertArrayHasKey("type", $value);
            $this->assertArrayHasKey("pri", $value);
            $this->assertArrayHasKey("target", $value);           
        }               
    }

    public function testGetRecordMxException()
    {
        $this->expectException(MxCheckerException::class);

        $instance = new MxChecker("fail"); 

        $instance->getRecordMx();       
    }
}