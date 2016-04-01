<?php
namespace AclTest\Model;

use Acl\Model\Authentication\DoctrineAuthenticationAdapter;
use Acl\Model\Authentication\Result;
use \PHPUnit_Framework_TestCase;
use AclTest\StandardProvidersTrait;

class DoctrineAuthenticationAdapterTest extends PHPUnit_Framework_TestCase
{
	use StandardProvidersTrait;

	protected $traceError = true;

	public function testDoctrineAuthenticationAdapterInitialState()
	{
		$adapter = new DoctrineAuthenticationAdapter();

		$this->assertNull($adapter->getIdentity(), 'DoctrineAuthenticationAdapter::identity should be null');
		$this->assertNull($adapter->getCredential(), 'DoctrineAuthenticationAdapter::credential should be null');
		$this->assertNull($adapter->getResultPrototype(), 'DoctrineAuthenticationAdapter::resultPrototype should be null');
		$this->assertNull($adapter->getEntityManager(), 'DoctrineAuthenticationAdapter::entityManager should be null');
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
				'Credential',
		);

		$adapter = new DoctrineAuthenticationAdapter();

		foreach($propertyAccessorNames as $propertyName) {
			$setMethodName = sprintf("set%s", $propertyName);
			$getMethodName = sprintf("get%s", $propertyName);

			$returnedAdapter = $adapter->$setMethodName($input);

			/*
			 * assert that there is method chaining active
			 */
			$errorMessage = sprintf("call to Adapter::%s() does not return the same instance of Adapter; no 'return \$this;'", $setMethodName);
			$this->assertSame($adapter, $returnedAdapter, $errorMessage);

			/*
			 * assert thtat the value set is consistent with the expected value returned
			 */
			$output = $adapter->$getMethodName();
			$errorMessage = sprintf("call to Adapter::%s('%s') and then Adapter::%s() returned value '%s'",$setMethodName, print_r($input, true), $getMethodName, $output);
			$this->assertEquals($expected, $output, $errorMessage);
		}
	}

	/**
	 * test that the result prototype injected is a clone
	 * copy of what is returned when the getResultPrototype()
	 * function is called on the adapter
	 */
	public function testResultPrototypeInjection()
	{
		$adapter = new DoctrineAuthenticationAdapter();
		$resultPrototype = new Result();


		$adapter
			->setResultPrototype($resultPrototype);

		$errorMessage = sprintf("DoctrineAuthenticationAdapter::resultPrototype property accessors broken. Value added: %s - Value retrieved: %s", print_r($resultPrototype, true), print_r($adapter->getResultPrototype(), true) );
 		$this->assertEquals($resultPrototype, $adapter->getResultPrototype(), $errorMessage);
	}

	/**
	 * test that the entity manager injected is the same
	 * object returned when the getEntityManager()
	 * function is called on the adapter
	 */
	public function testEntityManagerInjection()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

		$adapter = new DoctrineAuthenticationAdapter();

		$adapter
			->setEntityManager($em);

		$errorMessage = sprintf("DoctrineAuthenticationAdapter::authenticationAdapter property accessors broken. Value added: %s - Value retrieved: %s", print_r($adapter->getEntityManager(), true), print_r($em, true));

		$this->assertSame($adapter->getEntityManager(), $em, $errorMessage );
	}


}