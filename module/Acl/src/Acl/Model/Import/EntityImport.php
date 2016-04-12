<?php
namespace Acl\Model\Import;

use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\Factory\EntityFactoryInterface;
use Acl\Model\StandardInputFiltersTrait;
use Acl\Model\DependentObjectTrait;
use Acl\Entity\EntityInterface;

abstract class AbstractEntityImport implements EntityImportInterface
{
	use StandardInputFiltersTrait;
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 *
	 * @var EntityFactoryInterface
	 */
	private $factory;

	/**
	 *
	 * @var array
	 */
	private $options;

	/**
	 * used to cache the entity class name through a lazy
	 * load from the getEntityClassName() method
	 *
	 * @var string
	 */
	private $entityClassName;

	/**
	 * merge the data in the associative array with the entity
	 * in storage
	 *
	 * return an array of metrics
	 * 	->totalRecords
	 *  ->addedRecords
	 *  ->removedRecords
	 *  ->updatedRecords
	 *
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	abstract public function import();

	/**
	 * @return self
	 */
	public function __construct()
	{
		$this->entityClassName = null;

		/*
		 * default options and only options available
		 * for the import
		 */
		$this->options = array(
			'isDefinitive' => true,
		);
	}

	/**
	 *
	 * @param EntityManagerInterface $manager
	 *
	 * @return self
	 */
	public function setManager(EntityManagerInterface $manager)
	{
		$this->manager = $manager;
		return $this;
	}

	/**
	 *
	 * @param EntityFactoryInterface $factory
	 *
	 * @return self
	 */
	public function setFactory(EntityFactoryInterface $factory)
	{
		$this->factory = $factory;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @return self
	 */
	public function setOption($name, $value)
	{
		if ($this->hasOption($name)) {
			$this->options[$name] = $this->filterStringInput($value);
		}

		return $this;
	}

	/**
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getOption($name)
	{
		if ($this->hasOption($name)) {
			return $this->options[$name];
		}
	}




	/**
	 * returns true if the option is available to
	 * the import
	 *
	 * @return boolean
	 */
	private function hasOption($name)
	{
		return array_key_exists($name, $this->options);
	}

	/**
	 * lazy-load the imported entity class name by pulling
	 * it from the factory's prototype
	 *
	 * @return string
	 */
	private function getEntityClassName()
	{
		if ( $this->entityClassName == null ) {
			/*
			 * run dependency check
			 */
			$this->checkDependencies();

			$this->entityClassName = $this->factory->getPrototypeClone()->getEntityClass();
		}

		return $this->entityClassName;
	}

	/**
	 * loop through an array of options and assign
	 * them to the options array
	 *
	 * @param array $options
	 *
	 * @return self
	 */
	private function processOptionsArray(array $options)
	{
		foreach($options as $name=>$value) {
			$this->setOption($name, $value);
		}

		return $this;
	}

	/**
	 *
	 * @param array $config
	 *
	 * @return EntityInterface
	 */
	private function hydrateEntity(array $config)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		return $this->factory->createInstance($config);
	}

	/**
	 *
	 * @param array $critera
	 *
	 * @return EntityInterface
	 */
	private function retrieveEntitiesByCriteria(array $critera)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$className = $this->getEntityClassName();

		return $this->manager->getRepository($className)->findBy($criteria);
	}

	/**
	 * @see DependentObjectTrait
	 *
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Doctrine\ORM\EntityManagerInterface',
				'object' => $this->manager,
			),
			array(
				'name' => 'Acl\Factory\EntityFactoryInterface',
				'object' => $this->factory,
			),
		);
	}
}