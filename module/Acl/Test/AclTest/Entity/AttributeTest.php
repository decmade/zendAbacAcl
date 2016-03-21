<?php
namespace AclTest\Entiy;

use Acl\Entity\Attribute;
use Acl\Entity\User;
use \PHPUnit_Framework_TestCase;
use \DateTime;

class AttributeTest extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 */
	public function testInitialState()
	{
		$attribute = new Attribute();

		$this->assertNull($attribute->getId(), 'Attribute::id should be null');
		$this->assertTrue($attribute->getAdded() instanceof DateTime, 'Attribute::added should have a DateTime value.');
		$this->assertTrue($attribute->getAdded() == new DateTime(), 'Attribute::added should be set to today when instantiated');
		$this->assertNull($attribute->getRemoved(), 'Attribute::removed should be null');

		$this->assertNull($attribute->getUser(), 'Attribute::user should be null');
		$this->assertNull($attribute->getName(), 'Attribute::name should be null');
		$this->assertNull($attribute->getValue(),'Attribute::value should be null');
	}

	/**
	 *
	 * @param mixed $input
	 * @param string $expected
	 * @param string $errorMessage
	 *
	 * @dataProvider providerTestStringPropertyAccessors
	 */
	public function testStringPropertyAccessors($input, $expected)
	{
		$stringPropertyAccessorNames = array(
			'Name',
			'Value',
		);

		$attribute = new Attribute();

		foreach($stringPropertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$getMethodName = sprintf("get%s", $propertyName);

			$returnedAttribute = $attribute->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to Attribute::%s() does not return the same instance of Attribute; no 'return \$this;'", $setMethodName);
			$this->assertSame($attribute, $returnedAttribute, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$output = $attribute->$getMethodName();
			$errorMessage = sprintf("call to Attribute::%s('%s') and then Attribute::%s() returned value '%s'",$setMethodName, print_r($input, true), $getMethodName, $output);
			$this->assertEquals($expected, $output, $errorMessage);
		}
	}

	/**
	 * data sets for $this::testNamePropertyAccessors()
	 *
	 * @return array
	 */
	public function providerTestStringPropertyAccessors()
	{
		return array(
			array('development', 'development'),
			array(5478902578402, '5478902578402'),
			array(0.254, '0.254'),
			array(true, '1'),
			array(new \stdClass(), '<invalid>'),
			array(array(1,2,3), '<invalid>'),
			array(null, ''),
		);
	}

	/**
	 * test the property accessors for injecting and retrieving a User
	 */
	public function testUserPropertyAccessors()
	{
		$user = $this->getMockBuilder('Acl\Entity\User')
			->getMock();

		$attribute = new Attribute();
		$returnedAttribute = $attribute->setUser($user);

		/*
		 * assert that there is method chaining active
		 */
		$errorMessage ='call to Attribute::setName() does not return the same instance of Attribute; no "return $this;"';
		$this->assertSame($attribute, $returnedAttribute, $errorMessage);

		/*
		 * assert that the mock User injected is the same as the user returned by the Attribute
		 */
		$output = $attribute->getUser();
		$errorMessage = sprintf("call to Attribute::setUser('%s') and then Attribute::getUser() returned value '%s'", print_r($user, true), print_r($output, true));
		$this->assertSame($user, $output, $errorMessage);
	}

	/**
	 * asserts that the User is being passed to the Attribute by reference
	 * by making changes to the behavior of the User after it is injected
	 * into the Attribute
	 *
	 * @param string $methodName
	 * @param string $expectedValue
	 *
	 * @dataProvider providerTestThatUserPropertyIsPassedByReference
	 */
	public function testThatUserPropertyIsPassedByReference($methodName, $expectedValue)
	{

		/*
		 * create a mock User
		 */
		$user = $this->getMockBuilder('Acl\Entity\User')
			->getMock();

		/*
		 * instatiate the Attribute and inject the mock User
		 */
		$attribute = new Attribute();
		$attribute->setUser($user);

		/*
		 * change the behavior of the original mock User
		 */
		$user
			->expects($this->once())
			->method($methodName)
			->will($this->returnValue($expectedValue));

		$actualValue = $attribute->getUser()->$methodName();
		$errorMessage = sprintf("call to User::%s on user injected into Attribute is not consistent with the test value ofg injected", $methodName);
		$this->assertEquals($expectedValue, $actualValue, $errorMessage);
	}

	/**
	 * @return array
	 */
	public function providerTestThatUserPropertyIsPassedByReference()
	{
		return array(
				array('getStatus', User::STATUS_ACTIVE),
				array('getIdentity', 'twasBrillig34'),
				array('getSessions', array(1,2,3,4,5)),
		);
	}

}