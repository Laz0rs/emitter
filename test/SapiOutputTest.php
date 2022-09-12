<?php

namespace Laz0r\EmitterTest;

use Countable;
use EmptyIterator;
use Iterator;
use Laz0r\Emitter\{SapiInterface, SapiOutput};
use Laz0r\Util\SpongeIterator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use ReflectionClass;
use SeekableIterator;

/**
 * @coversDefaultClass \Laz0r\Emitter\SapiOutput
 */
class SapiOutputTest extends TestCase {

	/**
	 * @return array
	 */
	public function headersProvider(): array {
		return [
			[0, "Herp ", "derp"],
			[1, "X-Firin", "mah"],
			[2, "X-Firin", "Laz0r"],
		];
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructor(): void {
		$Stub = $this->createStub(SapiInterface::class);
		$RC = new ReflectionClass(SapiOutput::class);
		$Property = $RC->getProperty("Sapi");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Sut->__construct($Stub);
		$Property->setAccessible(true);

		$this->assertSame($Stub, $Property->getValue($Sut));

	}

	/**
	 * @covers ::getSapi
	 *
	 * @return void
	 */
	public function testGetSapi(): void {
		$Stub = $this->createStub(SapiInterface::class);
		$RC = new ReflectionClass(SapiOutput::class);
		$Property = $RC->getProperty("Sapi");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Property->setAccessible(true);
		$Property->setValue($Sut, $Stub);

		$Result = $Sut->getSapi();

		$this->assertSame($Stub, $Result);
	}

	/**
	 * @covers ::outputHeaders
	 *
	 * @return void
	 */
	public function testOutputHeadersEmptyIterator(): void {
		$headers = [];
		$Iterator = new EmptyIterator();
		$Mock = $this->createMock(SapiInterface::class);
		$Sut = $this->getMockBuilder(SapiOutput::class)
			->disableOriginalConstructor()
			->onlyMethods(["getSapi", "iterateHeaders"])
			->getMock();

		$Mock->expects($this->once())
			->method("header")
			->with($this->identicalTo("Content-Type:"));
		$Sut->expects($this->atLeastOnce())
			->method("getSapi")
			->will($this->returnValue($Mock));
		$Sut->expects($this->once())
			->method("iterateHeaders")
			->with($this->identicalTo($headers))
			->will($this->returnValue($Iterator));

		$Sut->outputHeaders($headers);
	}

	/**
	 * @covers ::outputHeaders
	 *
	 * @return void
	 */
	public function testOutputHeadersStubIterator(): void {
		$headers = [
			"Lol" => ["wut"],
			"U" => ["mad", "bru"],
		];
		$Iterator = call_user_func(static function() {
			yield "X-Powered-By" => "Laz0r";
			yield "Set-Cookie" => "internet=not";
			yield "Content-Type" => "application/laz0r";
			yield "Set-Cookie" => "internet=not";
			yield "Series-Of" => "Tubes";
		});
		$Mock = $this->createMock(SapiInterface::class);
		$Sut = $this->getMockBuilder(SapiOutput::class)
			->disableOriginalConstructor()
			->onlyMethods(["getSapi", "iterateHeaders"])
			->getMock();

		$Mock->expects($this->exactly(5))
				->method("header")
				->withConsecutive(
					[
						$this->identicalTo("X-Powered-By: Laz0r"),
						$this->identicalTo(true),
					],
					[
						$this->identicalTo("Set-Cookie: internet=not"),
						$this->identicalTo(false),
					],
					[
						$this->identicalTo("Content-Type: application/laz0r"),
						$this->identicalTo(true),
					],
					[
						$this->identicalTo("Set-Cookie: internet=not"),
						$this->identicalTo(false),
					],
					[
						$this->identicalTo("Series-Of: Tubes"),
						$this->identicalTo(true),
					],
				);
		$Sut->expects($this->atLeastOnce())
			->method("getSapi")
			->will($this->returnValue($Mock));
		$Sut->expects($this->once())
			->method("iterateHeaders")
			->with($this->identicalTo($headers))
			->will($this->returnValue($Iterator));

		$Sut->outputHeaders($headers);
	}

	/**
	 * @covers ::outputStatus
	 *
	 * @return void
	 */
	public function testOutputStatus(): void {
		$Mock = $this->createMock(SapiInterface::class);
		$Sut = $this->getMockBuilder(SapiOutput::class)
			->disableOriginalConstructor()
			->onlyMethods(["getSapi"])
			->getMock();

		$Mock->expects($this->once())
			->method("header")
			->with(
				$this->identicalTo("HTTP/5.9 863 Laz0r"),
				$this->identicalTo(true),
				$this->identicalTo(863),
			);
		$Sut->expects($this->once())
			->method("getSapi")
			->will($this->returnValue($Mock));

		$Sut->outputStatus("5.9", 863, "Laz0r");
	}

	/**
	 * @covers ::outputStream
	 *
	 * @return void
	 */
	public function testOutputStream(): void {
		$Mock = $this->createMock(SapiInterface::class);
		$Stream = $this->createMock(StreamInterface::class);
		$Sut = $this->getMockBuilder(SapiOutput::class)
			->disableOriginalConstructor()
			->onlyMethods(["getSapi"])
			->getMock();

		$Mock->expects($this->exactly(4))
			->method("write")
			->withConsecutive(
				[$this->identicalTo("imma")],
				[$this->identicalTo("firin")],
				[$this->identicalTo("mah")],
				[$this->identicalTo("laz0r")],
			)
			->will($this->onConsecutiveCalls(0xdead, 0xbeef, 0xcafe, 0));
		$Stream->expects($this->any())
			->method("isReadable")
			->will($this->returnValue(true));
		$Stream->expects($this->once())
			->method("isSeekable")
			->will($this->returnValue(true));
		$Stream->expects($this->once())
			->method("rewind");
		$Stream->expects($this->exactly(4))
			->method("read")
			->will($this->onConsecutiveCalls("imma", "firin", "mah", "laz0r"));
		$Sut->expects($this->atLeastOnce())
			->method("getSapi")
			->will($this->returnValue($Mock));

		$Sut->outputStream($Stream);
	}

	/**
	 * @covers ::iterateHeaders
	 *
	 * @return \Laz0r\Util\SpongeIterator
	 */
	public function testIterateHeadersCreatesIterator(): SpongeIterator {
		$RC = new ReflectionClass(SapiOutput::class);
		$Method = $RC->getMethod("iterateHeaders");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Method->setAccessible(true);
		$Result = $Method->invokeArgs($Sut, [[
			"herp " => ["derp"],
			"x-firin" => [
				"mah",
				"Laz0r",
			],
		]]);

		$this->assertInstanceOf(Iterator::class, $Result);

		return new SpongeIterator($Result);
	}

	/**
	 * @coversNothing
	 * @depends testIterateHeadersCreatesIterator
	 *
	 * @param \Countable $Test
	 *
	 * @return void
	 */
	public function testIterateHeadersResultCount(Countable $Test): void {
		$this->assertCount(3, $Test);
	}

	/**
	 * @dataProvider headersProvider
	 * @depends testIterateHeadersCreatesIterator
	 * @coversNothing
	 *
	 * @param int $pos
	 * @param string $key
	 * @param string $value
	 * @param \SeekableIterator $Test
	 *
	 * @return void
	 */
	public function testIterateHeadersResultData(
		int $pos,
		string $key,
		string $value,
		SeekableIterator $Test
	): void {
		$Test->seek($pos);
		$this->assertSame($key, $Test->key());
		$this->assertSame($value, $Test->current());
	}

}

/* vi:set ts=4 sw=4 noet: */
