<?php
namespace AclTest\Entiy;

use Acl\Entity\Session;
use Acl\Entity\User;
use \PHPUnit_Framework_TestCase;
use \DateTime;

class SessionTest extends PHPUnit_Framework_TestCase
{
	public function testSessionInitialState()
	{
		$session = new Session();

		$this->assertNull($session->getId(), 'Session::id should be null');
		$this->assertTrue($session->getAdded() instanceof DateTime, 'Session::added should have a DateTime value.');
		$this->assertTrue($session->getAdded() == new DateTime(), 'Session::added should be set to today when instantiated');
		$this->assertNull($session->getRemoved(), 'Session::removed should be null');

		$this->assertNull($session->getUser(), 'Session::user should be null');
		$this->assertNull($session->getIpAddress(), 'Session::ipAddress should be null');
		$this->assertNull($session->getExpires(),'Session::expires should be null');
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
		$stringPropertyAccessorNames = array(
				'IpAddress',
		);

		$session = new Session();

		foreach($stringPropertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$getMethodName = sprintf("get%s", $propertyName);

			$returnedSession = $session->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to Session::%s() does not return the same instance of Session; no 'return \$this;'", $setMethodName);
			$this->assertSame($session, $returnedSession, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$output = $session->$getMethodName();
			$errorMessage = sprintf("call to Session::%s('%s') and then Session::%s() returned value '%s'",$setMethodName, print_r($input, true), $getMethodName, $output);
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

		$session = new Session();
		$returnedSession = $session->setUser($user);

		/*
		 * assert that there is method chaining active
		 */
		$errorMessage ='call to Session::setName() does not return the same instance of Session; no "return $this;"';
		$this->assertSame($session, $returnedSession, $errorMessage);

		/*
		 * assert that the mock User injected is the same as the user returned by the Session
		 */
		$output = $session->getUser();
		$errorMessage = sprintf("call to Session::setUser('%s') and then Session::getUser() returned value '%s'", print_r($user, true), print_r($output, true));
		$this->assertSame($user, $output, $errorMessage);
	}

	/**
	 * asserts that the User is being passed to the Session by reference
	 * by making changes to the behavior of the User after it is injected
	 * into the Session
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
		 * instatiate the Session and inject the mock User
		 */
		$session = new Session();
		$session->setUser($user);

		/*
		 * change the behavior of the original mock User
		 */
		$user
			->expects($this->once())
			->method($methodName)
			->will($this->returnValue($expectedValue));

		$actualValue = $session->getUser()->$methodName();
		$errorMessage = sprintf("call to User::%s on user injected into Session is not consistent with the test value ofg injected", $methodName);
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