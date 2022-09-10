<?php

namespace Laz0r\Emitter;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface {

	public function emit(ResponseInterface $Response): void;

}

/* vi:set ts=4 sw=4 noet: */
