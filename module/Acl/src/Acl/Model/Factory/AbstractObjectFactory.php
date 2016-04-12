<?php
namespace Acl\Model\Factory;

use Acl\Model\DependentObjectTrait;

abstract Class AbstractObjectFactory
{
	use DependentObjectTrait;

	/**
	 *
	 * @var mixed $prototype
	 */
	protected $prototype;


	/**
	 *
	 * @param array $config
	 *
	 * @return mixed
	 */
	abstract public function createInstance(array $config);

	/**
	 * @return array
	 */
	abstract protected function getDependenciesConfig();

	/**
	 *
	 * @param mixed $object
	 *
	 * @return $this
	 */
	public function setPrototype($object)
	{
		$this->prototype = $object;
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
}