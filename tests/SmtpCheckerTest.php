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
use sorciulus\EmailChecker\SmtpChecker;
use sorciulus\EmailChecker\Exception\SmtpCheckerException;

class SmtpCheckerTest extends TestCase
{
	
    protected $mxChecker;

    protected function setUp()
    {
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

    public function testSmtpCheckerInstanceTrue()
    {
        $instance = new SmtpChecker(
            $this->mxChecker,
            "tester@gmail.com",
            1
        );

        $this->assertInstanceOf(SmtpChecker::class, $instance);
    }

    public function testSmtpCheckerInstanceException()
    {
        $this->expectException(\TypeError::class);

        new SmtpChecker(["a","b","c"]);
    }

    public function testSmtpCheckerSetWrongSenderException()
    {
        $this->expectException(SmtpCheckerException::class);

        new SmtpChecker(
            $this->mxChecker,
            "tester@test"
        );
    }
}