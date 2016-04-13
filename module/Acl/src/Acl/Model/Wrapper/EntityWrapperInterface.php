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

	/**
	 * copy the relevant properties of the entity passed
	 *
	 * @param EntityInterface $template
	 *
	 * @return self
	 */
	public function copy(EntityInterface $template);

	/**
	 * retrieve the unique properties of the entity as an
	 * array that can be passed to the EntityManager as criteria
	 * to find the same instance of the entity in the database
	 *
	 *
	 * @return array
	 */
	public function getUniquePropertiesArray();
}