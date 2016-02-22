<?php
namespace Acl\Model\Authentication;

use Zend\Authentication\Storage\StorageInterface;
use Doctrine\ORM\EntityManager;
use Acl\Entity\Session;
use Acl\Model\DependentObjectTrait;
use \DateTime;
use \DateInterval;


class DoctrineSessionStorage implements StorageInterface
{
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 *
	 * @var StorageInterface
	 */
	private $container;

	/**
	 *
	 * @var Session
	 */
	private $sessionPrototype;

	/**
	 *
	 * @param EntityManager $em
	 *
	 * @return $this
	 */
	public function setEntityManager(EntityManager $em)
	{
		$this->entityManager = $em;
		return $this;
	}

	/**
	 *
	 * @param StorageInterface $storage
	 *
	 * @return $this
	 */
	public function setContainer(StorageInterface $storage)
	{
		$this->container = $storage;
		return $this;
	}

	/**
	 *
	 * @param Session $session
	 *
	 * @return $this
	 */
	public function setSessionPrototype(Session $session)
	{
		$this->sessionPrototype = $session;
		return $this;
	}

	/**
	 * proxy for $this->isExpiredOrEmpty() that honors the StorageInterface
	 *
	 * when the AuthenticationService calls this function, if the session is
	 * missing or has expired then it is considered not to be active.
	 *
	 * {@inheritDoc}
	 * @see \Zend\Authentication\Storage\StorageInterface::isEmpty()
	 */
	public function isEmpty()
	{

		return $this->isExpiredOrEmpty();
	}

	/**
	 * checks to see if the current session either does not
	 * exist or is expired
	 *
	 * @return boolean
	 */
	public function isExpiredOrEmpty()
	{
		$now = new DateTime();
		$sessionId = $this->getSessionIdFromStorage();

		/*
		 * if the session id returnd is 0
		 * then no need to make a call to the ORM
		 * we already know that there is no session
		 *
		 * otherwise retrive the session entity
		 */
		if ($sessionId > 0) {
			$session = $this->getSessionEntityById($sessionId);

			switch(true) {
				case ( $session == null ) :
					/*
					 * there is no session with this id in the database
					 * so session is empty
					 */
					return true;
				case ($session->getRemoved() != null) :
					/*
					 * the session has a removed date value
					 *
					 * this indicates that an logout action has
					 * been applied to the session
					 */
					return true;
				case ( $session->getExpires() < $now ) :
					/*
					 * session has expired
					 */
					return true;
				default :
					return false;
			}
		} else {
			/*
			 * there is no session id stored so the session
			 * is empty
			 */
			return true;
		}

	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Zend\Authentication\Storage\StorageInterface::read()
	 */
	public function read()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		/*
		 * if the session exists and is active
		 * return the user id associated with the
		 * session
		 */
		if (!$this->isExpiredOrEmpty()) {
			$id = $this->getSessionIdFromStorage();
			$session = $this->getSessionEntityById($id);

			/*
			 * if it is an hour or less before the session expires
			 */
			if ($this->isWithinAnHourOf($session->getExpires())) {
				/*
				 * reset the session's expiration
				 *
				 * this keeps the session from expiring on
				 * the user while they are still using the
				 * application and causing them login again
				 */
				$session->setExpires($this->getDefaultExpiresValue());
				$this->entityManager->flush();
			}

			/*
			 * only return the surrogate key of the user
			 *
			 * no other data on the user is to be stored in
			 * the session variable
			 */
			return $session->getUser()->getId();
		} else {
			return null;
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Zend\Authentication\Storage\StorageInterface::write()
	 */
	public function write($userId)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		/*
		 * make sure we are not clobbering the
		 * integrity of the session data by
		 * clearing any existing data first
		 */
		$this->clear();

		$em = $this->entityManager;
		$user = $em->getRepository('Acl\Entity\User')->find($userId);
		$session = clone $this->sessionPrototype;

		$session
			->setUser($user)
			->setExpires($this->getDefaultExpiresValue())
			->setIpAddress($this->getClientIpAddress());
		/*
		 * go ahead and persist and flush to
		 * generate the surrogate key for the session
		 */
		$em->persist($session);
		$em->flush();

		/*
		 * write session id to PHP session variable
		 *
		 * has to occur after the entity manager
		 * is flush()-ed, otherwise there is no
		 * surrogate key to keep up with
		 */
		$this->container->write($session->getId());
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Zend\Authentication\Storage\StorageInterface::clear()
	 */
	public function clear()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		/*
		 * only do anything if the session is not empty or expired
		 * already
		 *
		 *
		 */
		if (!$this->isExpiredOrEmpty()) {
			$now = new DateTime();
			$em = $this->entityManager;
			$sessionId = $this->getSessionIdFromStorage();
			$session = $this->getSessionEntityById($sessionId);

			/*
			 * expire the session now
			 */
			$session->setRemoved($now);
			$em->flush();
		}

		/*
		 * reset the container/php session variable
		 */
		$this->container->clear();
	}

	/**
	 * @return int
	 */
	private function getSessionIdFromStorage()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		/*
		 * if the container is empty return 0
		 * else return the session id stored
		 */
		if ($this->container->isEmpty()) {
			return 0;
		} else {
			return $this->container->read();
		}
	}

	/**
	 *
	 * @param int $id
	 *
	 * @return Entity
	 */
	private function getSessionEntityById($id)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$em = $this->entityManager;
		return $em->getRepository('Acl\Entity\Session')->find($id);
	}

	/**
	 * this is the function used to determine how
	 * long a session in the database will
	 * be valid for before it expires
	 *
	 * @return DateTime
	 */
	private function getDefaultExpiresValue()
	{
		$interval = new DateInterval('PT2H');
		$now = new DateTime();
		return $now->add($interval);
	}

	/**
	 * true if it is at least an hour before
	 * the time passed
	 *
	 * @param DateTime $expires
	 */
	private function isWithinAnHourOf(DateTime $futureDate)
	{
		$interval = new DateInterval('PT1H');
		$milestone = $futureDate->sub($interval);
		$now = new DateTime();

		return ($now >= $milestone);
	}

	/**
	 * return the ip address of the client
	 *
	 * @return string
	 */
	private function getClientIpAddress()
	{
		/*
		 * possible server values that can hold
		 * the client Ip address
		 * in the order of which they should be checked
		 */
		$possibleIpAddressVariables = array(
				'HTTP_CLIENT_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'REMOTE_ADDR',
		);

		/*
		 * loop through the possible variables
		 * and return the first one with
		 * a value
		 */
		foreach($possibleIpAddressVariables as $var) {
			if ( isset($_SERVER[$var]) ) {
				if ( $ipAddress = $_SERVER[$var] ) {
					return $ipAddress;
				}
			}

		}
		/*
		 * default value when ipAddress cannot be determined
		 */
		return '0.0.0.0';
	}
	/**
	 *
	 * get dependencies configuration for DependentObjectTrait
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Doctrine\ORM\EntityManager',
				'object' => $this->entityManager,
			),
			array(
				'name' => 'Zend\Authentication\Storage\StorageInterface',
				'object' => $this->container,
			),
			array(
				'name' => 'Acl\Entity\Session',
				'object' => $this->sessionPrototype,
			)
		);
	}
}