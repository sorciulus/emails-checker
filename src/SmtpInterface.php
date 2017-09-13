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

interface SmtpInterface
{
	function validate($email);

	function getCode();

	function isValid();

	function getMessage();

	function getDebug();
}