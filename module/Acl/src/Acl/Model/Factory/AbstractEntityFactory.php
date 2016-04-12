<?php
namespace Acl\Model\Factory;

use Acl\Entity\EntityInterface;

abstract Class AbstractEntityFactory extends AbstractObjectFactory implements EntityFactoryInterface
{

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