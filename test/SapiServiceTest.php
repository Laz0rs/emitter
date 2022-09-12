<?php

namespace Laz0r\EmitterTest;

use Laz0r\Emitter\Exception\SapiException;
use Laz0r\Emitter\SapiService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionFunction;
use SplFileObject;

/**
 * @coversDefaultClass \Laz0r\Emitter\SapiService
 */
class SapiServiceTest extends TestCase {

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructor(): void {
		$Sut = new SapiService();
		$RC = new ReflectionClass(SapiService::class);

		$Property = $RC->getProperty("HeaderFn");
		$Property->setAccessible(true);
		$this->assertIsObject($Property->getValue($Sut));

		$Property = $RC->getProperty("OutputFile");
		$Property->setAccessible(true);
		$this->assertIsObject($Property->getValue($Sut));

		$Property = $RC->getProperty("SentFn");
		$Property->setAccessible(true);
		$this->assertIsObject($Property->getValue($Sut));
	}

	/**
	 * @covers ::header
	 *
	 * @return void
	 */
	public function testHeaderInvokesHeaderWithCode(): void {
		$Sent = $this->createMock(ReflectionFunction::class);
		$Header = $this->createMock(ReflectionFunction::class);
		$Sut = $this->getMockBuilder(SapiService::class)
			->disableOriginalConstructor()
			->onlyMethods(["getHeaderFunction", "getSentFunction"])
			->getMock();

		$Sent->expects($this->once())
			->method("invokeArgs")
			->with($this->identicalTo([]))
			->will($this->returnValue(false));
		$Header->expects($this->once())
			->method("invokeArgs")
			->with($this->identicalTo(["X-Herp: derp", true, 500]));
		$Sut->expects($this->once())
			->method("getSentFunction")
			->will($this->returnValue($Sent));
		$Sut->expects($this->once())
			->method("getHeaderFunction")
			->will($this->returnValue($Header));

		$Sut->header("X-Herp: derp", true, 500);
	}

	/**
	 * @covers ::header
	 *
	 * @return void
	 */
	public function testHeaderInvokesHeaderWithoutCode(): void {
		$Sent = $this->createMock(ReflectionFunction::class);
		$Header = $this->createMock(ReflectionFunction::class);
		$Sut = $this->getMockBuilder(SapiService::class)
			->disableOriginalConstructor()
			->onlyMethods(["getHeaderFunction", "getSentFunction"])
			->getMock();

		$Sent->expects($this->once())
			->method("invokeArgs")
			->with($this->identicalTo([]))
			->will($this->returnValue(false));
		$Header->expects($this->once())
			->method("invokeArgs")
			->with($this->identicalTo(["Lol: wut", false]));
		$Sut->expects($this->once())
			->method("getSentFunction")
			->will($this->returnValue($Sent));
		$Sut->expects($this->once())
			->method("getHeaderFunction")
			->will($this->returnValue($Header));

		$Sut->header("Lol: wut", false);
	}

	/**
	 * @covers ::header
	 *
	 * @return void
	 */
	public function testHeaderThrowsException(): void {
		$Sent = $this->createMock(ReflectionFunction::class);
		$Sut = $this->getMockBuilder(SapiService::class)
			->disableOriginalConstructor()
			->onlyMethods(["getSentFunction"])
			->getMock();

		$Sent->expects($this->once())
			->method("invokeArgs")
			->with($this->identicalTo([]))
			->will($this->returnValue(true));
		$Sut->expects($this->once())
			->method("getSentFunction")
			->will($this->returnValue($Sent));

		$this->expectException(SapiException::class);

		$Sut->header("X-Powered-By: Laz0r");
	}

	/**
	 * @covers ::write
	 *
	 * @return void
	 */
	public function testWriteReturnsInt(): void {
		$Mock = $this->getMockBuilder(SplFileObject::class)
			->setConstructorArgs(["php://memory"])
			->getMock();
		$Sut = $this->getMockBuilder(SapiService::class)
			->disableOriginalConstructor()
			->onlyMethods(["getOutputFile"])
			->getMock();

		$Mock->expects($this->once())
			->method("fwrite")
			->with($this->identicalTo("LOL"))
			->will($this->returnValue(9000));
		$Sut->expects($this->once())
			->method("getOutputFile")
			->will($this->returnValue($Mock));

		$result = $Sut->write("LOL");

		$this->assertSame(9000, $result);
	}

	/**
	 * @covers ::write
	 *
	 * @return void
	 */
	public function testWriteReturnsNull(): void {
		$Mock = $this->getMockBuilder(SplFileObject::class)
			->setConstructorArgs(["php://memory"])
			->getMock();
		$Sut = $this->getMockBuilder(SapiService::class)
			->disableOriginalConstructor()
			->onlyMethods(["getOutputFile"])
			->getMock();

		$Mock->expects($this->once())
			->method("fwrite")
			->with($this->identicalTo("wut"))
			->will($this->returnValue(true));
		$Sut->expects($this->once())
			->method("getOutputFile")
			->will($this->returnValue($Mock));

		$result = $Sut->write("wut");

		$this->assertNull($result);
	}

	/**
	 * @covers ::getHeaderFunction
	 *
	 * @return void
	 */
	public function testGetHeaderFunction(): void {
		$Stub = new ReflectionFunction("pow");
		$RC = new ReflectionClass(SapiService::class);
		$Method = $RC->getMethod("getHeaderFunction");
		$Property = $RC->getProperty("HeaderFn");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Property->setAccessible(true);
		$Property->setValue($Sut, $Stub);
		$Method->setAccessible(true);

		$Result = $Method->invokeArgs($Sut, []);

		$this->assertSame($Stub, $Result);
	}

	/**
	 * @covers ::getOutputFile
	 *
	 * @return void
	 */
	public function testGetOutputFile(): void {
		$Stub = new SplFileObject("php://memory");
		$RC = new ReflectionClass(SapiService::class);
		$Method = $RC->getMethod("getOutputFile");
		$Property = $RC->getProperty("OutputFile");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Property->setAccessible(true);
		$Property->setValue($Sut, $Stub);
		$Method->setAccessible(true);

		$Result = $Method->invokeArgs($Sut, []);

		$this->assertSame($Stub, $Result);
	}

	/**
	 * @covers ::getSentFunction
	 *
	 * @return void
	 */
	public function testGetSentFunction(): void {
		$Stub = new ReflectionFunction("md5");
		$RC = new ReflectionClass(SapiService::class);
		$Method = $RC->getMethod("getSentFunction");
		$Property = $RC->getProperty("SentFn");
		$Sut = $RC->newInstanceWithoutConstructor();

		$Property->setAccessible(true);
		$Property->setValue($Sut, $Stub);
		$Method->setAccessible(true);

		$Result = $Method->invokeArgs($Sut, []);

		$this->assertSame($Stub, $Result);
	}

}

/* vi:set ts=4 sw=4 noet: */
