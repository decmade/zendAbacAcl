<?php
namespace Acl\Model\View;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\DependentObjectTrait;
use Zend\View\Model\ViewModel;
use Acl\Entity\User;

class CurrentUser extends AbstractHelper
{
	use DependentObjectTrait;

	const USER_REPOSITORY_CLASS = 'Acl\Entity\User';

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
	 *
	 * @param AuthenticationService $service
	 *
	 * @return $this
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
	 * @return $this
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
	 * @return $this
	 */
	public function setGuestUser(User $guest)
	{
		$this->guestUser = $guest;
		return $this;
	}

	public function __invoke()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$userId = $this->getAuthenticatedUserId();

		if ($userId == null) {
			return $this->guestUser;
		} else {
			return $this->getUserById($userId);
		}
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

		return $em->getRepository(self::USER_REPOSITORY_CLASS)->find($id);
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