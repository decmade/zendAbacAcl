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

	public function testSessionPropertyAccessors()
	{
		$session = new Session();
		$user = new User();
		$user->setIdentity('DevTestUser');

		$data = array(
				'user' => $user,
				'ipAddress' => '192.168.1.255',
				'expires' => new DateTime(),
		);

		$session
			->setUser($data['user'])
			->setIpAddress($data['ipAddress'])
			->setExpires($data['expires']);

			$this->assertSame($data['user'], $session->getUser(), sprintf("Session::user property accessors broken. User set: %s - User retrieved: %s", $data['user']->getIdentity(), $session->getUser()->getIdentity() ) );
			$this->assertSame($data['ipAddress'], $session->getIpAddress(), sprintf("Session::name property accessors broken. Value set: %s - Value retrieved: %s", $data['ipAddress'], $session->getIpAddress() ) );
			$this->assertSame($data['expires'], $session->getExpires(), sprintf("Session::value property accessors broken. Value set: %s - Value retrieved: %s", $data['expires']->format('Y-m-d'), $session->getExpires()->format('Y-m-d') ) );


	}



}