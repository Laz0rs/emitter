<?php

namespace Laz0r\Emitter;

interface SapiInterface {

	/**
	 * Invoke the header function
	 *
	 * @param string $header
	 * @param bool $replace
	 * @param int|null $response_code
	 *
	 * @return void
	 */
	public function header(
		string $header,
		bool $replace = true,
		?int $response_code = null
	): void;

	/**
	 * Copy string to output stream
	 *
	 * @param string $str
	 *
	 * @return int|null
	 */
	public function write(string $str): ?int;

}

/* vi:set ts=4 sw=4 noet: */
