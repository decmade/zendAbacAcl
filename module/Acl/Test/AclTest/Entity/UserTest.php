<?php
namespace AclTest\Entiy;

use Acl\Entity\User;
use Acl\Entity\Attribute;
use PHPUnit_Framework_TestCase;
use \DateTime;

class UserTest extends \PHPUnit_Framework_TestCase
{
	public function testUserInitialState()
	{
		$user = new User();

		$this->assertNull($user->getId(), 'User::id should be null');
		$this->assertTrue($user->getAdded() instanceof DateTime, 'User::added should have a DateTime value.');
		$this->assertTrue($user->getAdded() == new DateTime(), 'User::added should be set to today when instantiated');
		$this->assertNull($user->getRemoved(), 'User::removed should be null');

		$this->assertNull($user->getIdentity(), 'User::identity should be null');
		$this->assertSame( $user::STATUS_ACTIVE, $user->getStatus(), 'User::status code should be set to User:STATUS_ACTIVE by default');
		$this->assertTrue(is_array($user->getAttributes()), 'User::attributes should be an array');
		$this->assertTrue(count($user->getAttributes()) == 0, 'User::attributes array should be empty when instantiated');
		$this->assertTrue(is_array($user->getSessions()), 'User::sessions should be an array');
		$this->assertTrue(count($user->getSessions()) == 0, 'User::sessions array should be empty when instantiated');

	}

	public function testUserPropertyAccessors()
	{
		$user = new User();

		$data = array(
				'identity' => 'devuser',
				'credential' => 'passphrase',
				'status' => $user::STATUS_INACTIVE,
		);

		$user
			->setIdentity($data['identity'])
			->setCredential($data['credential'])
			->setStatus($data['status'])
			->addAttribute(new Attribute());

			$this->assertSame($data['identity'], $user->getIdentity(), sprintf("User::identity property accessors broken. Value set: %s - Value retrieved: %s", $data['identity'], $user->getIdentity() ) );
			$this->assertTrue($user->checkCredential($data['credential']), 'User::credential property accessors broken. Hash patterns do not match');
			$this->assertSame($data['status'], $user->getStatus(), sprintf("User::status property accessors broken. Value set: %s - Value retrieved: %s", $data['status'], $user->getStatus() ) );
			$this->assertTrue(count($user->getAttributes()) == 1, sprintf("User::attributes property accessors broken. One attribute added, %s attributes retrieved",count($user->getAttributes()) ) );


	}



}