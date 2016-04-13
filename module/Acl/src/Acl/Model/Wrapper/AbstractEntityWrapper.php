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
	 * @return $this
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
}