<?php

namespace Laz0r\EmitterTest;

use Laz0r\Emitter\{OutputEmitter, OutputInterface};
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use ReflectionClass;

/**
 * @coversDefaultClass \Laz0r\Emitter\OutputEmitter
 */
class OutputEmitterTest extends TestCase {

	/**
	 * @return array
	 */
	public function provideStatusCodes(): array {
		return [
			[200, true],
			[204, false],
		];
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructor(): void {
		$Stub = $this->createStub(OutputInterface::class);
		$RC = new ReflectionClass(OutputEmitter::class);
		$Method = $RC->getConstructor();
		$Property = $RC->getProperty("Output");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Sut->__construct($Stub);
		$Property->setAccessible(true);

		$this->assertSame($Stub, $Property->getValue($Sut));
	}

	/**
	 * @covers ::emit
	 *
	 * @return void
	 */
	public function testEmitNoContents(): void {
		$Response = $this->createMock(ResponseInterface::class);
		$Output = $this->createMock(OutputInterface::class);
		$Sut = $this->getMockBuilder(OutputEmitter::class)
			->disableOriginalConstructor()
			->onlyMethods(["getOutput", "isContentStatus"])
			->getMock();
		$headers = ["X-Lazor" => ["Herp", "Derp"]];

		$Response->expects($this->atLeastOnce())
			->method("getHeaders")
			->will($this->returnValue($headers));
		$Response->expects($this->atLeastOnce())
			->method("getProtocolVersion")
			->will($this->returnValue("0.0"));
		$Response->expects($this->atLeastOnce())
			->method("getStatusCode")
			->will($this->returnValue(863));
		$Response->expects($this->atLeastOnce())
			->method("getReasonPhrase")
			->will($this->returnValue("Laz0r"));
		$Response->expects($this->never())
			->method("getBody");
		$Output->expects($this->once())
			->method("outputHeaders")
			->with($this->identicalTo($headers));
		$Output->expects($this->once())
			->method("outputStatus")
			->with(
				$this->identicalTo("0.0"),
				$this->identicalTo(863),
				$this->identicalTo("Laz0r"),
			);
		$Output->expects($this->never())
			->method("outputStream");
		$Sut->expects($this->atLeastOnce())
			->method("getOutput")
			->will($this->returnValue($Output));
		$Sut->expects($this->once())
			->method("isContentStatus")
			->with($this->identicalTo(863))
			->will($this->returnValue(false));
		$Sut->emit($Response);
	}

	/**
	 * @covers ::emit
	 *
	 * @return void
	 */
	public function testEmitWithContents(): void {
		$Body = $this->createMock(StreamInterface::class);
		$Response = $this->createMock(ResponseInterface::class);
		$Output = $this->createMock(OutputInterface::class);
		$Sut = $this->getMockBuilder(OutputEmitter::class)
			->disableOriginalConstructor()
			->onlyMethods(["getOutput", "isContentStatus"])
			->getMock();
		$headers = ["X-Lazor" => ["Hurr", "Durrr"]];

		$Response->expects($this->atLeastOnce())
			->method("getHeaders")
			->will($this->returnValue($headers));
		$Response->expects($this->atLeastOnce())
			->method("getProtocolVersion")
			->will($this->returnValue("8.8"));
		$Response->expects($this->atLeastOnce())
			->method("getStatusCode")
			->will($this->returnValue(863));
		$Response->expects($this->atLeastOnce())
			->method("getReasonPhrase")
			->will($this->returnValue("Laz0r"));
		$Response->expects($this->atLeastOnce())
			->method("getBody")
			->will($this->returnValue($Body));
		$Output->expects($this->once())
			->method("outputHeaders")
			->with($this->identicalTo($headers));
		$Output->expects($this->once())
			->method("outputStatus")
			->with(
				$this->identicalTo("8.8"),
				$this->identicalTo(863),
				$this->identicalTo("Laz0r"),
			);
		$Output->expects($this->once())
			->method("outputStream")
			->with($this->identicalTo($Body));
		$Sut->expects($this->atLeastOnce())
			->method("getOutput")
			->will($this->returnValue($Output));
		$Sut->expects($this->once())
			->method("isContentStatus")
			->with($this->identicalTo(863))
			->will($this->returnValue(true));
		$Sut->emit($Response);
	}

	/**
	 * @covers ::getOutput
	 *
	 * @return void
	 */
	public function testGetOutput(): void {
		$Stub = $this->createStub(OutputInterface::class);
		$RC = new ReflectionClass(OutputEmitter::class);
		$Property = $RC->getProperty("Output");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Property->setAccessible(true);
		$Property->setValue($Sut, $Stub);

		$Result = $Sut->getOutput();

		$this->assertSame($Stub, $Result);
	}

	/**
	 * @covers ::isContentStatus
	 * @dataProvider provideStatusCodes
	 *
	 * @param int $code
	 * @param bool $expect
	 *
	 * @return void
	 */
	public function testIsContentStatus(int $code, bool $expect): void {
		$RC = new ReflectionClass(OutputEmitter::class);
		$Method = $RC->getMethod("isContentStatus");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Method->setAccessible(true);

		$result = $Method->invokeArgs($Sut, [$code]);

		$this->assertSame($expect, $result);
	}

}

/* vi:set ts=4 sw=4 noet: */
