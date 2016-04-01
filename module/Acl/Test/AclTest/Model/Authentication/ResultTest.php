<?php
namespace AclTest\Entiy;

use Acl\Model\Authentication\Result;
use \PHPUnit_Framework_TestCase;
use AclTest\StandardProvidersTrait;

class ResultTest extends PHPUnit_Framework_TestCase
{
	use StandardProvidersTrait;

	public function testResultInitialState()
	{
		$result = new Result();

		$this->assertNull($result->getCode(), 'Result::code should be null');
		$this->assertNull($result->getIdentity(), 'Result::identity should be null');
		$this->assertTrue(is_array($result->getMessages()), 'Result::messages should be an array');
		$this->assertTrue(count($result->getMessages()) == 0, 'Result::messages array should be empty');
	}


	/**
	 *
	 * @param mixed $input
	 * @param string $expected
	 *
	 * @dataProvider providerTestIntegerPropertyAccessors
	 */
	public function testIntegerPropertyAccessors($input, $expected)
	{
		$propertyAccessorNames = array(
				'Code',
				'Identity',
		);

		$result = new Result();

		foreach($propertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$getMethodName = sprintf("get%s", $propertyName);

			$returnedResult = $result->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to Result::%s() does not return the same instance of Result; no 'return \$this;'", $setMethodName);
			$this->assertSame($result, $returnedResult, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$output = $result->$getMethodName();
			$errorMessage = sprintf("call to Result::%s('%s') and then Result::%s() returned value '%s'",$setMethodName, print_r($input, true), $getMethodName, $output);
			$this->assertEquals($expected, $output, $errorMessage);
		}
	}
}