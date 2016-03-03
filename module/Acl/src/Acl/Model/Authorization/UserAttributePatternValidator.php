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
	 * @var AttributePatternFactory
	 */
	private $attributePatternFactory;

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
	 * @param AttributePatternFactory $factory
	 * @return $this
	 */
	public function setAttributePatternFactory(AttributePatternFactory $factory)
	{
		$this->attributePatternFactory = $factory;
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

		$attributePatterns = $this->parseSet($patternSet);

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
				'name' => 'Acl\Model\Authorization\AttributePatternFactory',
				'object' => $this->attributePatternFactory,
			),
			array(
				'name' => 'Acl\Model\Authorization\CaseSensitivity',
				'object' => $this->caseSensitivity,
			),
		);
	}
}