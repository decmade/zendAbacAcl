<?php
namespace Acl\Model\Wrapper;

use Acl\Entity\EntityInterface;

class UserWrapper extends AbstractEntityWrapper
{

	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Model\Wrapper\AbstractEntityWrapper::toArray()
	 */
	public function toArray()
	{
		if ($this->hasDependencies()) {
			$user = $this->entity;

			return array(
				'id' => $user->getId(),
				'added' => $user->getAdded(),
				'removed' => $user->getRemoved(),
				'identity' => $user->getIdentity(),
				'status' => $user->getStatus(),
			);

		} else {
			return array(
				'id' => 0,
				'added' => null,
				'removed' => null,
				'identity' => '',
				'status' => 0,
			);
		}
	}

	/**
	 * copy the relevant properties of the entity passed into
	 * the enveloped entity
	 *
	 * @param EntityInterface $template
	 *
	 * @return self
	 */
	public function copy(EntityInterface $template)
	{
		if ($this->hasDependencies()) {
			$entity = $this->entity;

			$entity
				->setIdentity($template->getIdentity())
				->setStatus($template->getStatus());

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
	 * retrieve the unique properties of the entity as an
	 * array that can be passed to the EntityManager as criteria
	 * to find the same instance of the entity in the database
	 *
	 *
	 * @return array
	 */
	public function getUniquePropertiesArray()
	{
		if ($this->hasDependencies()) {
			return array(
				'identity' => $this->entity->getIdentity(),
			);
		} else {
			return array();
		}
	}

	/**
	 * get a user attribute by name
	 *
	 * @param string $name
	 *
	 * @return Attribute
	 */
	public function getAttribute($name)
	{
		$name = strtolower($name);
		$attributes = $this->getAttributes();

		$filter = function($attribute) use ($name) {
			return ($attribute->getName() == $name);
		};

		return array_filter($attributes, $filter);
	}

	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Entity\User',
				'object' => $this->entity,
			),
		);
	}

	/*
	 * BEGIN: Facade Methods
	 */

	/**
	 * @return string
	 */
	public function getIdentity()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getIdentity();
		} else {
			return '';
		}
	}

	/**
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setIdentity($value)
	{
		if ($this->hasDependencies()) {
			$this->entity->setIdentity($value);
		}

		return $this;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return boolean
	 */
	public function checkCredential($value)
	{
		if ($this->hasDependencies()) {
			return $this->entity->checkCredential($value);
		} else {
			return false;
		}
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setCredential($value)
	{
		if ($this->hasDependencies()) {
			$this->entity->setCredential($value);
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getStatus();
		} else {
			return 0;
		}
	}

	/**
	 *
	 * @param int $value
	 *
	 * @return $this
	 */
	public function setStatus($value)
	{
		if ($this->hasDependencies()) {
			$this->entity->setStatus($value);
		}

		return $this;
	}

	/**
	 *
	 * @param Attribute $attribute
	 *
	 * @return $this
	 */
	public function addAttribute(Attribute $attribute)
	{
		if ($this->hasDependencies()) {
			$this->entity->addAttribute($attribute);
		}

		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getAttributes()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getAttributes();
		} else {
			return array();
		}
	}


	/**
	 *NEVER USED BY DoctrineSessionStorage
	 * @param Session $session
	 *
	 * @return $this
	 */
	public function addSession(Session $session)
	{
		if ($this->hasDependencies() ) {
			$this->entity->addSession($session);
		}

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getSessions()
	{
		if ($this->hasDependencies()) {
			return $this->entity->getSessions();
		} else {
			return array();
		}
	}
}