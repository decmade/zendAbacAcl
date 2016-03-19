<?php
namespace Acl\Model\Authorization;

use Acl\Entity\User;
use Acl\Model\DependentObjectTrait;

class UserAttributePatternValidator
{
	use DependentObjectTrait;

	/**
	 *
	 * @var User
	 */
	private $user;

	/**
	 *
	 * @var CaseSensitivity
	 */
	private $caseSensitivity;

	/**
	 *
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 *
	 * @param CaseSensistivity $cs
	 * @return $this
	 */
	public function setCaseSensitivity(CaseSensistivity $cs)
	{
		$this->caseSensitivity = $cs;
		return $this;
	}

	public function validate(array $patternSet)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();


	}

	private function parseSet(array $patternSet)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();



	}

	/**
	 * return dependencies configuration
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Entity\User',
				'object' => $this->user,
			),
			array(
				'name' => 'Acl\Model\Authorization\CaseSensitivity',
				'object' => $this->caseSensitivity,
			),
		);
	}
}