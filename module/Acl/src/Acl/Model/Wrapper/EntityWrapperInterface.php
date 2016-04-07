<?php
namespace Acl\Model\Wrapper;

use Acl\Entity\EntityInterface;

interface EntityWrapperInterface
{
	/**
	 * convert the object to an array
	 * representation for JSON conversions
	 *
	 * @return array
	 */
	public function toArray();

	/**
	 *
	 * @param EntityInterface $entity
	 *
	 * @return $this
	 */
	public function setEntity(EntityInterface $entity);
}