<?php
namespace Acl\Model\Factory;

use Acl\Entity\EntityInterface;

interface EntityFactoryInterface
{
	/**
	 *
	 * @param EntityInterface $entity
	 *
	 * @return $this
	 */
	public function setPrototype(EntityInterface $entity);

	/**
	 * @return EntityInterface
	 */
	public function getPrototypeClone();

	/**
	 *
	 * @param array $config
	 *
	 * @return EntityInterface
	 */
	public function createInstance(array $config);
}