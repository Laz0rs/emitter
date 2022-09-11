<?php

namespace Laz0r\Emitter;

use Iterator;
use Laz0r\Util\AbstractConstructOnce;
use Psr\Http\Message\StreamInterface;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class SapiOutput extends AbstractConstructOnce implements OutputInterface {

	private SapiInterface $Sapi;

	/**
	 * @param \Laz0r\Emitter\SapiInterface $Sapi
	 */
	public function __construct(SapiInterface $Sapi) {
		parent::__construct();

		$this->Sapi = $Sapi;
	}

	/**
	 * @return \Laz0r\Emitter\SapiInterface
	 */
	public function getSapi(): SapiInterface {
		return $this->Sapi;
	}

	public function outputHeaders(array $headers): void {
		$Sapi = $this->getSapi();
		$haveContentType = false;

		/**
		 * @var string $name
		 * @var string $value
		 */
		foreach ($this->iterateHeaders($headers) as $name => $value) {
			$haveContentType = $haveContentType || ($name === "Content-Type");
			$Sapi->header("$name: $value", $name !== "Set-Cookie");
		}

		if (!$haveContentType) {
			// Prevent sending a bogus Content-Type
			$Sapi->header("Content-Type:");
		}
	}

	public function outputStatus(
		string $version,
		int $code,
		string $reason = ""
	): void {
		$this->getSapi()->header(
			rtrim(sprintf("HTTP/%s %d %s", $version, $code, $reason)),
			true,
			$code,
		);
	}

	public function outputStream(StreamInterface $Stream): void {
		assert($Stream->isReadable());

		if ($Stream->isSeekable()) {
			$Stream->rewind();
		}

		while ($this->getSapi()->write($Stream->read(4096)));
	}

	/**
	 * @param array $headers
	 *
	 * @return \Iterator
	 */
	protected function iterateHeaders(array $headers): Iterator {
		$Iterator = new RecursiveArrayIterator($headers);

		/** @var string $value */
		foreach (new RecursiveIteratorIterator($Iterator) as $value) {
			yield ucwords(strval($Iterator->key()), "-") => $value;
		}
	}

}

/* vi:set ts=4 sw=4 noet: */
