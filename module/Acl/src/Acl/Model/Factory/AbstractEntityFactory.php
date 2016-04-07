<?php
namespace Acl\Model\Factory;

use Acl\Entity\EntityInterface;
use Acl\Model\DependentObjectTrait;

abstract Class AbstractEntityFactory implements EntityFactoryInterface
{
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityInterface $prototype
	 */
	protected $prototype;


	/**
	 *
	 * @param array $config
	 *
	 * @return EntityInterface
	 */
	abstract public function createInstance(array $config);

	/**
	 *
	 * @param EntityInterface $entity
	 *
	 * @return $this
	 */
	public function setPrototype(EntityInterface $entity)
	{
		$this->prototype = $entity;
		return $this;
	}

	/**
	 * @return EntityInterface
	 */
	public function getPrototypeClone()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		return clone $this->prototype;
	}

	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Entity\EntityInterface',
				'object' => $this->prototype,
			),
		);
	}

}