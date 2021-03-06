<?php
namespace Acl\Model\Wrapper;

use Acl\Entity\EntityInterface;
class AttributeWrapper extends AbstractEntityWrapper
{
	/**
	 * convert the object to an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		/*
		 * run dependency check
		 */
		if ($this->hasDependencies()) {
			$attribute = $this->entity;

			return array(
				'id' => $attribute->getId(),
				'added' => $attribute->getAdded(),
				'removed' => $attribute->getRemoved(),
				'name' => $attribute->getName(),
				'value' => $attribute->getValue(),
				'user' => $attribute->getUser()->getIdentity(),
			);
		} else {
			return array(
				'id' => 0,
				'added' => null,
				'removed' => null,
				'name' => '',
				'value' => '',
			);
		}
	}

	/**
	 * copy the properties of the attribute into
	 * the attribute that is the subject of
	 * this facade
	 *
	 * @param Attribute $subject
	 *
	 * @return self
	 */
	public function copy(EntityInterface $template)
	{
		/*
		 * run dependency check
		 */
		if ($this->hasDependencies() ) {
			$entity = $this->entity;

			$entity
				->setValue($template->getValue())
			;

		/*
		 * if the subject has been removed but the copy is not removed
		 */
			if ($entity->getRemoved() != null && $template->getRemoved() == null ) {
				$entity	->clearRemoved();
			}
		}



		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Model\Wrapper\AbstractEntityWrapper::getUniquePropertiesArray()
	 */
	public function getUniquePropertiesArray()
	{
		/**
		 * run dependency check
		 */
		if ($this->hasDependencies()) {
			$entity = $this->entity;

			return array(
				'user' => $entity->getUser(),
				'name' => $entity->getName(),
			);
		} else {
			return array();
		}

	}

	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Entity\Attribute',
				'object' => $this->entity,
			),
		);
	}

	/*
	 * 	BEGIN: Facade Classes
	 */

	/**
	 * @return string
	 */
	public function getName()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getName();
		} else {
			return '';
		}
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return self
	 */
	public function setName($value)
	{
		if ($this->hasDependencies()) {
			$this->entity->setName($value);
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getValue();
		} else {
			return '';
		}
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return self
	 */
	public function setValue($value)
	{
		if ($this->hasDependencies()) {
			$this->entity->setValue($value);
		}

		return $this;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getUser();
		} else {
			return null;
		}

	}

	/**
	 *
	 * @param User $user
	 *
	 * @return $this
	 */
	public function setUser(User $user)
	{
		if ($this->hasDependencies()) {
			$this->entity->setUser($user);
		}

		return $this;
	}

}