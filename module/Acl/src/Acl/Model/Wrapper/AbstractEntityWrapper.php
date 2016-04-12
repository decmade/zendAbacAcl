<?php
namespace Acl\Model\Wrapper;

use Acl\Entity\EntityInterface;
use Acl\Model\DependentObjectTrait;
use Acl\Model\FacadeTrait;

abstract class AbstractEntityWrapper implements EntityWrapperInterface
{
	use DependentObjectTrait;
	use FacadeTrait;

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
	 * overloaded reference to the subject class
	 * for the benefit of the FacadeTrai
	 */
	protected function getSubject()
	{
		return $this->entity;
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