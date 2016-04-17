<?php
namespace Acl\Model\View;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\DependentObjectTrait;
use Acl\Entity\User;
use Acl\Model\Wrapper\UserWrapper;

class CurrentUser extends AbstractHelper
{
	use DependentObjectTrait;

	/**
	 *
	 * @var AuthenticationService
	 */
	private $authenticationService;

	/**
	 *
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * a dummy user object that is used
	 * to represent a gues user
	 *
	 * this object has no persistence beyond it's
	 * configuration
	 *
	 * @var User
	 */
	private $guestUser;

	/**
	 * wrapper class that adds extra functionality to
	 * the user class
	 *
	 * @var UserWrapper $wrapper
	 */
	private $wrapper;

	/**
	 *
	 * @param AuthenticationService $service
	 *
	 * @return self
	 */
	public function setAuthenticationService(AuthenticationService $service)
	{
		$this->authenticationService = $service;
		return $this;
	}

	/**
	 *
	 * @param EntityManagerInterface $em
	 *
	 * @return self
	 */
	public function setEntityManager(EntityManagerInterface $em)
	{
		$this->entityManager = $em;
		return $this;
	}

	/**
	 *
	 * @param User $guest
	 *
	 * @return self
	 */
	public function setGuestUser(User $guest)
	{
		$this->guestUser = $guest;
		return $this;
	}

	/**
	 *
	 * @param UserWrapper $wrapper
	 *
	 * @return self
	 */
	public function setWrapper(UserWrapper $wrapper)
	{
		$this->wrapper = $wrapper;
		return $this;
	}

	public function __invoke()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$userId = $this->getAuthenticatedUserId();
		$user = $this->guestUser;
		$wrapper = $this->wrapper;

		if ($userId != null) {
			$user = $this->getUserById($userId);
		}

		return $wrapper->setEntity($user);
	}


	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Zend\Authentication\AuthenticationService',
				'object' => $this->authenticationService,
			),
			array(
				'name' => 'Doctrine\ORM\EntityManagerInterface',
				'object' => $this->entityManager,
			),
			array(
				'name' => 'Acl\Entity\User',
				'object' => $this->guestUser,
			),
			array(
				'name' => 'Acl\Model\Wrapper\UserWrapper',
				'object' => $this->wrapper,
			),
		);
	}

	/**
	 * return the user represented by the ID
	 * integer passed
	 *
	 * @param int $id
	 *
	 * @return User
	 */
	private function getUserById($id)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$em = $this->entityManager;

		return $em->getRepository(User::getEntityClass())->find($id);
	}

	/**
	 * get the ID of the authenticated user or NULL if
	 * none are authenticated
	 *
	 * @return int|null
	 */
	private function getAuthenticatedUserId()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$service = $this->authenticationService;

		if ($service->hasIdentity()) {
			return $service->getIdentity();
		} else {
			return null;
		}
	}


}