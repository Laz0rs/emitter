<?php

namespace Laz0r\Emitter;

use Laz0r\Emitter\Exception\SapiException;
use Laz0r\Util\AbstractConstructOnce;
use ReflectionFunction;
use SplFileObject;

class SapiService extends AbstractConstructOnce implements SapiInterface {

	private ReflectionFunction $HeaderFn;
	private SplFileObject $OutputFile;
	private ReflectionFunction $SentFn;

	public function __construct() {
		parent::__construct();

		$this->HeaderFn = new ReflectionFunction("header");
		$this->OutputFile = new SplFileObject("php://output", "wb");
		$this->SentFn = new ReflectionFunction("headers_sent");
	}

	public function header(
		string $header,
		bool $replace = true,
		?int $response_code = null
	): void {
		if ($this->getSentFunction()->invokeArgs([])) {
			throw new SapiException("Headers already sent");
		}

		$this->getHeaderFunction()->invokeArgs(
			array_merge(
				[$header, $replace],
				is_int($response_code) ? [$response_code] : []
			)
		);
	}

	public function write(string $str): ?int {
		/** @var bool|int $ret */
		$ret = $this->getOutputFile()->fwrite($str);

		return !is_bool($ret) ? $ret : null;
	}

	protected function getHeaderFunction(): ReflectionFunction {
		return $this->HeaderFn;
	}

	protected function getOutputFile(): SplFileObject {
		return $this->OutputFile;
	}

	protected function getSentFunction(): ReflectionFunction {
		return $this->SentFn;
	}

}

/* vi:set ts=4 sw=4 noet: */
