<?php
namespace Acl\Model;

abstract class zzzAbstractFactory
{
	use DependentObjectTrait;

	protected $prototype;

	/**
	 * create an instance of the object suggested
	 * by the configuration
	 *
	 * @param array $config
	 */
	abstract public function createInstace(array $config);

	/**
	 * inject the factory's prototype object
	 *
	 * @param mixed $object
	 * @return $this
	 */
	public function setPrototype($object)
	{
		$this->prototype = $object;
		return $this;
	}

	/**
	 * return dependencies configuration
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\AbstractFactory::prototype',
				'object' => $this->prototype,
			),
		);
	}
}