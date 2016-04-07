<?php
namespace Acl\Model\Wrapper;

use Acl\Entity\EntityInterface;
use Acl\Model\DependentObjectTrait;

abstract class AbstractEntityWrapper implements EntityWrapperInterface
{
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityInterface $entity
	 */
	protected $entity;

	/**
	 * convert the object to an array
	 * representation for JSON conversions
	 *
	 * @return array
	 */
	abstract public function toArray();

	/**
	 *
	 * @param EntityInterface $entity
	 *
	 * @return $this
	 */
	public function setEntity(EntityInterface $entity)
	{
		$this->entity = $entity;
		return $this;
	}

	/**
	 * this function facilitates the facade over the entity
	 * by allowing you to call the entity's functions
	 * from the wrapper's interface
	 *
	 * @param string $methodName
	 * @param array $args
	 *
	 * @return unknown
	 */
	public function __call($methodName, $args)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		if (method_exists($this->entity, $methodName) )
		{
			return call_user_func_array(array($this->entity, $methodName), $args);
		} else {
			return null;
		}
	}

	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Entity\EntityInterface',
				'object' => $this->entity,
			),
		);
	}
}