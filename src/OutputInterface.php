<?php

namespace Laz0r\Emitter;

use Psr\Http\Message\StreamInterface;

interface OutputInterface {

	/**
	 * @param array $headers
	 *
	 * @return void
	 */
	public function outputHeaders(array $headers): void;

	/**
	 * @param string $version
	 * @param int $code
	 * @param string $reason
	 *
	 * @return void
	 */
	public function outputStatus(
		string $version,
		int $code,
		string $reason = ""
	): void;

	/**
	 * @param \Psr\Http\Message\StreamInterface $Stream
	 *
	 * @return void
	 */
	public function outputStream(StreamInterface $Stream): void;

}

/* vi:set ts=4 sw=4 noet: */
