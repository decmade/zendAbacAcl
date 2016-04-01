<?php
namespace AclTest\Entiy;

use Acl\Entity\User;
use \PHPUnit_Framework_TestCase;
use \DateTime;
use Acl\Entity\Attribute;
use Acl\Entity\Session;
use AclTest\StandardProvidersTrait;

class UserTest extends PHPUnit_Framework_TestCase
{
	use StandardProvidersTrait;

	public function testUserInitialState()
	{
		$user = new User();

		$this->assertNull($user->getId(), 'User::id should be null');
		$this->assertTrue($user->getAdded() instanceof DateTime, 'User::added should have a DateTime value.');
		$this->assertEquals($user->getAdded(), new DateTime(), 'User::added should be set to today when instantiated');
		$this->assertNull($user->getRemoved(), 'User::removed should be null');

		$this->assertNull($user->getIdentity(), 'User::identity should be null');
		$this->assertSame( $user::STATUS_ACTIVE, $user->getStatus(), 'User::status code should be set to User:STATUS_ACTIVE by default');
		$this->assertTrue(is_array($user->getAttributes()), 'User::attributes should be an array');
		$this->assertTrue(count($user->getAttributes()) == 0, 'User::attributes array should be empty when instantiated');
		$this->assertTrue(is_array($user->getSessions()), 'User::sessions should be an array');
		$this->assertTrue(count($user->getSessions()) == 0, 'User::sessions array should be empty when instantiated');

	}

	/**
	 *
	 * @param mixed $input
	 * @param string $expected
	 *
	 * @dataProvider providerTestStringPropertyAccessors
	 */
	public function testStringPropertyAccessors($input, $expected)
	{
		$propertyAccessorNames = array(
				'Identity',
		);

		$user = new User();

		foreach($propertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$getMethodName = sprintf("get%s", $propertyName);

			$returnedUser = $user->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to User::%s() does not return the same instance of User; no 'return \$this;'", $setMethodName);
			$this->assertSame($user, $returnedUser, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$output = $user->$getMethodName();
			$errorMessage = sprintf("call to User::%s('%s') and then User::%s() returned value '%s'",$setMethodName, print_r($input, true), $getMethodName, $output);
			$this->assertEquals($expected, $output, $errorMessage);
		}
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
				'Status',
		);

		$user = new User();

		foreach($propertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$getMethodName = sprintf("get%s", $propertyName);

			$returnedUser = $user->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to User::%s() does not return the same instance of User; no 'return \$this;'", $setMethodName);
			$this->assertSame($user, $returnedUser, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$output = $user->$getMethodName();
			$errorMessage = sprintf("call to User::%s('%s') and then User::%s() returned value '%s'",$setMethodName, print_r($input, true), $getMethodName, $output);
			$this->assertEquals($expected, $output, $errorMessage);
		}
	}

	/**
	 *
	 * @param mixed $input
	 * @param bool $expected
	 *
	 * @dataProvider providerTestHashedPropertyAccessors
	 */
	public function testHashedPropertyAccessors($input, $expected)
	{
		$propertyAccessorNames = array(
				'Credential',
		);

		$user = new User();

		foreach($propertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$checkMethodName = sprintf("check%s", $propertyName);

			$returnedUser = $user->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to User::%s() does not return the same instance of User; no 'return \$this;'", $setMethodName);
			$this->assertSame($user, $returnedUser, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$errorMessage = sprintf("call to User::%s('%s') and then User::%s() with the same input returned '%s'",$setMethodName, print_r($input, true), $checkMethodName, !$expected);
			if ($expected == true ) {
				$this->assertTrue($user->$checkMethodName($input), $errorMessage);
			} else {
				$this->assertFalse($user->$checkMethodName($input), $errorMessage);
			}
		}
	}

	/**
	 *
	 * @param mixed $input
	 *
	 * @dataProvider providerTestCollectionPropertyAccessors
	 */
	public function testCollectionPropertyAccessors($propertyName, $input)
	{
		$user = new User();

		$addMethodName = sprintf("add%s", $propertyName);
		$getMethodName = sprintf("get%ss", $propertyName);

		$returnedUser = $user->$addMethodName($input);

		/*
		 * assert that there is method chaining active
		 */
		$errorMessage = sprintf("call to User::%s() does not return the same instance of User; no 'return \$this;'", $addMethodName);
		$this->assertSame($user, $returnedUser, $errorMessage);

		/*
		 * assert thtat the value set is consistent with the expected value returned
		 */
		$errorMessage = sprintf("call to last element of User::%s() does not match the entity added.", $getMethodName);
		$elements = $user->$getMethodName();
		$lastElement = end($elements);
		$this->assertSame($input, $lastElement, $errorMessage);
	}

	/**
	 * data sets for $this::testCollectionPropertyAccessors()
	 *
	 * @return array
	 */
	public function providerTestCollectionPropertyAccessors()
	{
		$attribute = new Attribute();
		$session = new Session();

		return array(
			array('Attribute', $attribute),
			array('Attribute', $attribute->setName('TestProperty')),
			array('Attribute', $attribute->setValue('TestPropteryValue')),
			array('Session', $session),
		);
	}


}