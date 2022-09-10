<?php

namespace Laz0r\Emitter;

use Laz0r\Util\AbstractConstructOnce;
use Psr\Http\Message\ResponseInterface;

class OutputEmitter extends AbstractConstructOnce implements EmitterInterface {

	private OutputInterface $Output;

	public function __construct(OutputInterface $Output) {
		parent::__construct();

		$this->Output = $Output;
	}

	public function emit(ResponseInterface $Response): void {
		$this->getOutput()->outputHeaders($Response->getHeaders());
		$this->getOutput()->outputStatus(
			$Response->getProtocolVersion(),
			$Response->getStatusCode(),
			$Response->getReasonPhrase()
		);

		if ($this->isContentStatus($Response->getStatusCode())) {
			$this->getOutput()->outputStream($Response->getBody());
		}
	}

	public function getOutput(): OutputInterface {
		return $this->Output;
	}

	protected function isContentStatus(int $code): bool {
		return !in_array($code, [204, 205, 304]);
	}

}

/* vi:set ts=4 sw=4 noet: */
