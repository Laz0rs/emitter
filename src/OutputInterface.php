<?php

namespace Laz0r\Emitter;

use Psr\Http\Message\StreamInterface;

interface OutputInterface {

	public function outputHeaders(array $headers): void;

	public function outputStatus(
		string $version,
		int $code,
		string $reason = ""
	): void;

	public function outputStream(StreamInterface $Stream): void;

}

/* vi:set ts=4 sw=4 noet: */
