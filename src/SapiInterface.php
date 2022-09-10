<?php

namespace Laz0r\Emitter;

use Psr\Http\Message\StreamInterface;

interface SapiInterface {

	public function header(
		string $header,
		bool $replace = true,
		?int $response_code = null
	): void;

	public function write(string $str): ?int;

}

/* vi:set ts=4 sw=4 noet: */
