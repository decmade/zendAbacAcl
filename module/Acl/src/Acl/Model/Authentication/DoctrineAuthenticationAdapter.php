<?php
namespace Acl\Model\Authentication;

use Zend\Authentication\Adapter\AdapterInterface;
use Doctrine\ORM\EntityManager;
use Acl\Model\DependentObjectTrait;
use Acl\Model\StandardInputFiltersTrait;
use Acl\Entity\User;

class DoctrineAuthenticationAdapter implements AdapterInterface
{
	use DependentObjectTrait;
	use StandardInputFiltersTrait;

	/**
	 *
	 * @var string
	 */
	private $identity;

	/**
	 *
	 * @var string
	 */
	private $credential;

	/**
	 *
	 * @var Result
	 */
	private $resultPrototype;

	/**
	 *
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @return string
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setIdentity($value)
	{
		/*
		 * apply the standard string input filter to $value
		 */
		$this->identity = $this->filterStringInput($value);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCredential()
	{
		return $this->credential;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setCredential($value)
	{
		$this->credential = $this->filterStringInput($value);
		return $this;
	}

	/**
	 * @return Result|null
	 */
	public function getResultPrototype()
	{
		if ($this->resultPrototype) {
			return clone $this->resultPrototype;
		} else {
			return null;
		}
	}

	/**
	 *
	 * @param Result $prototype
	 *
	 * @return $this
	 */
	public function setResultPrototype(Result $prototype)
	{
		$this->resultPrototype = $prototype;
		return $this;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 *
	 * @param EntityManager $em
	 * @return $this
	 */
	public function setEntityManager(EntityManager $em)
	{
		$this->entityManager = $em;
		return $this;
	}

	/**
	 * @return Result
	 */
	public function authenticate()
	{
		/*
		 * check dependencies
		 */
		$this->checkDependencies();

		$em = $this->entityManager;
		$authenticationTests = $this->getAuthenticationTests();
		$result = null;

		foreach($authenticationTests as $test) {
			$result = $test($this);
			/*
			 * if any of the tests fail, stop testing
			 * and return the failed result
			 */
			if ( $result->getCode() != $result::SUCCESS ) {
				/*
				 * TODO: if you want to scrub the specific result failure messages
				 * you can do so here and change the message and code to something
				 * more ambiguous to the user. you can uncomment the example below
				 * for starters
				 */
// 				$result
// 					->setCode($result::FAILURE)
// 					->addMessage('Invalid Username Or Password');

				break;
			}
		}

		return $result;

	}

	/**
	 *
	 * @return array
	 */
	protected function getDependenciesConfg()
	{
		return array(
			array(
				'name' => 'Acl\Model\Authentication\Result',
				'object' => $this->resultPrototype,
			),
			array(
				'name' => 'Doctrine\ORM\EntityManager',
				'object' => $this->entityManager,
			),
		);
	}

	/**
	 * tests to be run during authentication
	 *
	 * @return array
	 */
	private function getAuthenticationTests()
	{
		return array(

			/*
			 * test for empty user identity
			 */
			function ($adapter) {
				$identity = $adapter->getIdentity();
				$result = $adapter->getResultPrototype();

				if ( empty($identity) ) {
					$result
						->setCode($result::FAILURE_IDENTITY_NOT_FOUND)
						->addMessage('Invalid Username Or Password')
						->addMessage('Username Cannot Be Empty');
				} else {
					$result->setCode($result::SUCCESS);
				}

				return $result;
			},

			/*
			 * test for empty user credential
			 */
			function ($adapter) {
				$credential = $adapter->getCredential();
				$result = $adapter->getResultPrototype();

				if ( empty($credential) ) {
					$result
						->setCode($result::FAILURE_CREDENTIAL_INVALID)
						->addMessage("Invalid Username Or Password")
						->addMessage('Password Cannot Be Empty');
				} else {
					$result->setCode($result::SUCCESS);
				}

				return $result;
			},

			/*
			 * test for a valid user
			 */
			function ($adapter) {
				$identity = $adapter->getIdentity();
				$em = $adapter->getEntityManager();
				$result = $adapter->getResultPrototype();

				/*
				 * find active user with this identity
				 */
				$users = $em->getRepository(User::getEntityClass())->findBy(array(
					'identity' => $identity,
					'status' => User::STATUS_ACTIVE,
					'removed' => null,
				));

				if ( count($users) == 0 ) {
					$result
						->setCode($result::FAILURE_IDENTITY_NOT_FOUND)
						->addMessage('Invalid Username Or Password')
						->addMessage('User Not Found');
				} else {
					$result
						->setCode($result::SUCCESS);
				}

				return $result;
			},

			/*
			 * actually authenticate the user against
			 * user database
			 *
			 * this test should always be the final test
			 * it assumes that all other tests have been
			 * passed
			 */
			function ($adapter) {
				$identity = $adapter->getIdentity();
				$credential = $adapter->getCredential();
				$em = $adapter->getEntityManager();
				$result = $adapter->getResultPrototype();

				/*
				 * find active user with this identity
				 */
				$users = $em->getRepository(User::getEntityClass())->findBy(array(
					'identity' => $identity,
					'status' => User::STATUS_ACTIVE,
					'removed' => null,
				));

				/*
				 * loop through each user found to test
				 * credentials
				 */
				foreach($users as $user) {
					/*
					 * if the user credentials check out
					 * then return a successful result with the user
					 * identity populated
					 */
					if ($user->checkCredential($credential)) {
						$result
							->setIdentity($user->getId()) // go with the database's surrogate key for information hiding
							->setCode($result::SUCCESS)
							->addMessage(sprintf("User %s Has Been Authenticated Successfully", $identity) );

						return $result;
					}
				}

				/*
				 * fall through to return an invalid credential
				 * result if no positive result is acheived above
				 *
				 * TODO: you could also trigger an event here to keep track
				 * of the number of failed attempts. each failed attempt
				 * can trigger an event that caches the failed attempt count per user.
				 *
				 * it can finally set a user as inactive after so many attempts if done so.
				 *
				 * i leave this up to the developer at this revision to implement based on
				 * their specific business rules. you would have to make this class
				 * event aware and add the event manager as a dependency i
				 * would imagine. Zend makes it easy enough by using the
				 * EventManagerAwareTrait
				 */
				$result
					->setCode($result::FAILURE_CREDENTIAL_INVALID)
					->addMessage("Invalid Username Or Password")
					->addMessage("An Invalid Login Attempt From This IP Address Has Been Recorded"); // may be extra, just to scare them a little

				return $result;
			},
		);
	}

}