<?php
namespace Acl\Model\Wrapper;

use Acl\Entity\EntityInterface;
use Acl\Model\DependentObjectTrait;

abstract class AbstractEntityWrapper implements EntityWrapperInterface, EntityInterface
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
	 * copy the relevant properties of the entity passed
	 *
	 * @param EntityInterface $template
	 *
	 * @return self
	 */
	abstract public function copy(EntityInterface $template);

	/**
	 * @return array
	 */
	abstract protected function getDependenciesConfig();

	/**
	 * retrieve the unique properties of the entity as an
	 * array that can be passed to the EntityManager as criteria
	 * to find the same instance of the entity in the database
	 *
	 *
	 * @return array
	 */
	abstract public function getUniquePropertiesArray();

	/**
	 *
	 * @param EntityInterface $entity
	 *
	 * @return self
	 */
	public function setEntity(EntityInterface $entity)
	{
		$this->entity = $entity;
		return $this;
	}

	/**
	 * get the encapsulated entity
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/*
	 * BEGIN: FACADE METHODS
	 */

	/**
	 * @return int
	 */
	public function getId()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getId();
		} else {
			return 0;
		}
	}

	/**
	 * @return DateTime|null
	 */
	public function getAdded()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getAdded();
		} else {
			return null;
		}
	}

	/**
	 * @return DateTime|null
	 */
	public function getRemoved()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getRemoved();
		} else {
			return null;
		}
	}

	/**
	 * @return self
	 */
	public function setRemoved()
	{
		if ($this->hasDependencies()) {
			$this->entity->setRemoved();
		}

		return $this;
	}

	/**
	 * @return self
	 */
	public function clearRemoved()
	{
		if ($this->hasDependencies()) {
			$this->entity->clearRemoved();
		}

		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Entity\EntityInterface::getEntityClass()
	 */
	static public function getEntityClass()
	{
		if ($this->hasDependencies()) {
			$entity = $this->entity;
			return $entity::getEntityClass();
		} else {
			return '';
		}
	}
}