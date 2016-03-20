<?php
namespace AclTest\Entiy;

use Acl\Entity\Attribute;
use Acl\Entity\User;
use PHPUnit_Framework_TestCase;
use \DateTime;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
	public function testAttributeInitialState()
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

	public function testAttributePropertyAccessors()
	{
		$attribute = new Attribute();

		$data = array(
				'user' => new User(),
				'name' => 'testAttribute',
				'value' => 'testValue',
		);

		$attribute
			->setUser($data['user'])
			->setName($data['name'])
			->setValue($data['value']);

			$this->assertSame($data['user'], $attribute->getUser(), 'Attribute::user property accessors broken. Value set: %s - Value retrieved: %s' );
			$this->assertSame($data['name'], $attribute->getName(), sprintf("Attribute::name property accessors broken. Value set: %s - Value retrieved: %s", $data['name'], $attribute->getName() ) );
			$this->assertSame($data['value'], $attribute->getValue(), sprintf("Attribute::value property accessors broken. Value set: %s - Value retrieved: %s", $data['value'], $attribute->getValue() ) );


	}



}