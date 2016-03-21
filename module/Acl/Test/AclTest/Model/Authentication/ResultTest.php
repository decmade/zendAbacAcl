<?php
namespace AclTest\Entiy;

use Acl\Model\Authentication\Result;
use \PHPUnit_Framework_TestCase;

class ResultTest extends PHPUnit_Framework_TestCase
{
	public function testResultInitialState()
	{
		$result = new Result();

		$this->assertNull($result->getCode(), 'Result::code should be null');
		$this->assertNull($result->getIdentity(), 'Result::identity should be null');
		$this->assertTrue(is_array($result->getMessages()), 'Result::messages should be an array');
		$this->assertTrue(count($result->getMessages()) == 0, 'Result::messages array should be empty');
	}

	public function testResultPropertyAccessors()
	{
		$result = new Result();

		$data = array(
				'code' => $result::FAILURE,
				'identity' => 8,
				'message' => 'THIS IS A TEST MESSAGE',
		);

		$result
			->setCode($data['code'])
			->setIdentity($data['identity'])
			->addMessage($data['message']);

			$this->assertSame($data['code'], $result->getCode(), sprintf("Result::code property accessors broken. Value set: %s - Value retrieved: %s", $data['code'], $result->getCode() ) );
			$this->assertSame($data['identity'], $result->getIdentity(), sprintf("Result::identity property accessors broken. Value set: %s - Value retrieved: %s", $data['identity'], $result->getIdentity() ) );
			$this->assertTrue(count($result->getMessages()) == 1, sprintf("Result::message property accessors broken. Added 1 message and %s messages retrieved.", count($result->getMessages()) ) );
			$this->assertSame($data['message'], $result->getMessages()[0], sprintf("Result::message property accessors broken. Value added: %s - Value retrieved: %s", $data['message'], $result->getMessages()[0] ) );

	}



}