<?php
namespace AclTest\Entiy;

use Acl\Model\Authentication\DoctrineAuthenticationAdapter;
use Acl\Model\Authentication\Result;
use \PHPUnit_Framework_TestCase;

class DoctrineAuthenticationAdapterTest extends PHPUnit_Framework_TestCase
{
	protected $traceError = true;

	public function testDoctrineAuthenticationAdapterInitialState()
	{
		$adapter = new DoctrineAuthenticationAdapter();

		$this->assertNull($adapter->getIdentity(), 'DoctrineAuthenticationAdapter::identity should be null');
		$this->assertNull($adapter->getCredential(), 'DoctrineAuthenticationAdapter::credential should be null');
		$this->assertNull($adapter->getResultPrototype(), 'DoctrineAuthenticationAdapter::resultPrototype should be null');
		$this->assertNull($adapter->getEntityManager(), 'DoctrineAuthenticationAdapter::entityManager should be null');
	}

	public function testDoctrineAuthenticationAdapterPropertyAccessors()
	{
		$adapter = new DoctrineAuthenticationAdapter();
		$resultPrototype = new Result();

		$data = array(
			'identity' => 'DevTestUser',
			'credential' => 'devTe$tPassW0rd',
			'resultPrototype' => $resultPrototype,
		);

		$adapter
			->setIdentity($data['identity'])
			->setCredential($data['credential'])
			->setResultPrototype($data['resultPrototype']);

			$this->assertSame($data['identity'], $adapter->getIdentity(), sprintf("DoctrineAuthenticationAdapter::identity property accessors broken. Value set: %s - Value retrieved: %s", $data['identity'], $adapter->getIdentity() ) );
			$this->assertSame($data['credential'], $adapter->getCredential(), sprintf("DoctrineAuthenticationAdapter::credential property accessors broken. Value set: %s - Value retrieved: %s", $data['credential'], $adapter->getCredential() ) );
 			$this->assertEquals($data['resultPrototype'], $adapter->getResultPrototype(), sprintf("DoctrineAuthenticationAdapter::resultPrototype property accessors broken. Value added: %s - Value retrieved: %s", print_r($data['resultPrototype'], true), print_r($adapter->getResultPrototype(), true)  ) );

	}

}