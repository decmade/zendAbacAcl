<?php
namespace Acl\Model\Wrapper;

class UserWrapper extends AbstractEntityWrapper
{

	/**
	 *
	 * @var AttributeWrapper
	 */
	private $attributeWrapper;

	/**
	 *
	 * @var SessionWrapper
	 */
	private $sessionWrapper;

	/**
	 *
	 * @param AttributeWrapper $wrapper
	 *
	 * @return $this
	 */
	public function setAttributeWrapper(AttributeWrapper $wrapper)
	{
		$this->attributeWrapper = $wrapper;
		return $this;
	}

	/**
	 *
	 * @param SessionWrapper $wrapper
	 *
	 * @return $this
	 */
	public function setSessionWrapper(SessionWrapper $wrapper)
	{
		$this->sessionWrapper = $wrapper;
		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Model\Wrapper\AbstractEntityWrapper::toArray()
	 */
	public function toArray()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$user = $this->entity;
		$attributesArray = $this->getAttributesArray($user->getAttributes());
		$sessionsArray = $this->getSessionsArray($user->getSessions());


		return array(
			'id' => $user->getId(),
			'added' => $user->getAdded(),
			'removed' => $user->getRemoved(),
			'identity' => $user->getIdentity(),
			'status' => $user->getStatus(),
			'attributes' => $attributesArray,
			'sessions' => $sessionsArray,
		);
	}

	/**
	 * @return array
	 */
	protected function getLocalDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Wrapper\AttributeWrapper',
				'object' => $this->attributeWrapper,
			),
			array(
				'name' => 'Acl\Model\Wrapper\SessionWrapper',
				'object' => $this->sessionWrapper,
			),
		);
	}

	/**
	 * converts a collection of attributes into
	 * a nested array
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	private function getAttributesArray(array $attributes)
	{
		/*
		 * check local dependencies
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		$wrapper = $this->attributeWrapper;
		$output = array();

		foreach($attributes as $attribute) {
			$output[] = $wrapper->setEntity($attribute)->toArray();
		}

		return $output;
	}

	/**
	 * converts a collection of attributes into
	 * a nested array
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	private function getSessionsArray(array $sessions)
	{
		/*
		 * check local dependencies
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		$wrapper = $this->sessionWrapper;
		$output = array();

		foreach($sessions as $session) {
			$output[] = $wrapper->setEntity($session)->toArray();
		}

		return $output;
	}
}