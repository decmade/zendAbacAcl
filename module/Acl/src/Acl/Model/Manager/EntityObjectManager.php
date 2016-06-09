<?php
namespace Acl\Model\Manager;

use Acl\Model\DependentObjectTrait;
use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\Factory\EntityFactoryInterface;
use Acl\Model\Wrapper\EntityWrapperInterface;
use Acl\Entity\EntityInterface;

class EntityObjectManager implements EntityObjectManagerInterface
{
	use DependentObjectTrait;

	const OUTPUT_OBJECTS = 'objects';
	const OUTPUT_ARRAY = 'array';
	const OUTPUT_JSON = 'json';

	/**
	 *
	 * @var EntityManagerInterface
	 */
	protected $entityManager;

	/**
	 *
	 * @var EntityFactoryInterface
	 */
	protected $factory;

	/**
	 *
	 * @var EntityWrapperInterface
	 */
	protected $wrapper;

	/**
	 *
	 * @param EntityManagerInterface $em
	 * @return self
	 */
	public function setEntityManager(EntityManagerInterface $em)
	{
		$this->entityManager = $em;
		return $this;
	}

	/**
	 *
	 * @return EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 *
	 * @param EntityFactoryInterface $factory
	 * @return self
	 */
	public function setFactory(EntityFactoryInterface $factory)
	{
		$this->factory = $factory;
		return $this;
	}

	/**
	 * @return EntityFactoryInterface
	 */
	public function getFactory()
	{
		return $this->factory;
	}

	/**
	 *
	 * @param EntityWrapperInterface $wrapper
	 * @return self
	 */
	public function setWrapper(EntityWrapperInterface $wrapper)
	{
		$this->wrapper = $wrapper;
		return $this;
	}

	/**
	 * @return EntityWrapperInterface
	 */
	public function getWrapper()
	{
		return $this->wrapper;
	}

	/**
	 * retrieve a single object from persistent storage by simple criteria
	 * this is translated into a fetchBy($critera) call to
	 * the Doctrine EntityManager
	 *
	 * @param array $criteria
	 * @param string $outputFormat
	 *
	 * @return EntityInterface|null
	 */
	public function findOneBy(array $criteria, $outputFormat = self::OUTPUT_OBJECTS )
	{
		$results = $this->findBy($criteria, array(), $outputFormat);
		return ( count($results) > 0) ? $results[0] : null;
	}

	/**
	 * retrieve objects from persistent storage by simple criteria
	 * this is translated into a fetchBy($critera, $orderBy) call to
	 * the Doctrine EntityManager
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @param string $outputFormat
	 *
	 * @return array|string
	 */
	public function findBy(array $criteria, array $orderBy = array(), $outputFormat = self::OUTPUT_OBJECTS)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$em = $this->entityManager;
		$factory = $this->factory;

		$prototype = $factory->getPrototypeClone();
		$repo = $em->getRepository(get_class($prototype));
		$results = null;

		/*
		 * if an orderby clause is passed, then include it in the
		 * call to the repository
		 */
		if (count($orderBy) > 0) {
			$results = $repo->findBy($criteria, $orderBy);
		} else {
			$results = $repo->findBy($criteria);
		}

		return $this->transformOutput($outputFormat, $results);
	}

	/**
	 * fetch all objects in persisitent storage
	 *
	 * @uses $this->findBy(array, array, string)
	 *
	 * @param array $orderBy
	 * @param string $outputFormat
	 *
	 * @return array|string
	 */
	public function findAll(array $orderBy = array(), $outputFormat = self::OUTPUT_OBJECTS)
	{
		/*
		 * if an orderby clause is passed, then include it in the
		 * call to the repository
		 */
		if (count($orderBy) > 0) {
			$results = $this->findBy(array(), $orderBy);
		} else {
			$results = $this->fetchBy(array());
		}

		return $this->transformOutput($outputFormat, $results);
	}

	/**
	 *
	 * create an enitity through the factory given the array configuration
	 * if $persist = true then save to persisitent storage
	 *
	 * @param array $config
	 * @param bool $persist (default: true)
	 *
	 * @return EntityInterface
	 */
	public function create(array $config, $persist = true)
	{
		/*
		 * run dependecny check
		 */
		$this->checkDependencies();

		$factory = $this->factory;
		$em = $this->entityManager;

		$entity = $factory->createInstance($config);

		/*
		 * if the factory did not create an object, short-circuit
		 * and return null
		 */
		if ($entity == null ) {
			return null;
		}

		/*
		 * persist in storage if $persist = true
		 */
		if ($persist) {
			$em->persist($entity);
		}

		return $entity;
	}

	/**
	 * remove an entity from persistent storage
	 *
	 * @param EntityInterface $entity
	 */
	public function destroy(EntityInterface $entity)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$this->entityManager->remove($entity);
	}

	/**
	 * commit all database operations queued for persistent storage
	 *
	 * @return self
	 */
	public function commit()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$this->entityManager->flush();
		return $this;
	}

	/**
	 * a switcher that transforms the results into the format
	 * specified by the $outputFormat parameter
	 *
	 * @param string $outputFormat
	 * @param array $results
	 *
	 * @return array|string
	 */
	protected function transformOutput($outputFormat, array $results)
	{
		switch($outputFormat) {
			case self::OUTPUT_ARRAY :
				return $this->serializeToArray($results);
				break;
			case self::OUTPUT_JSON :
				return $this->serializeToJson($results);
				break;
			default :
				return $results;
		}
	}

	/**
	 * serialize the array of hydrated objects into
	 * a nested associative array
	 *
	 * @param array $results
	 * @return array
	 */
	protected function serializeToArray(array $results)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$wrapper = $this->wrapper;
		$output = array();

		/*
		 * append the array serialized version of the entity to
		 * the output
		 */
		foreach($results as $entity) {
			$output[] = $wrapper->setEntity($entity)->toArray();
		}

		return $output;
	}

	/**
	 * serialize an array of hydrated objects into a
	 * JSON formatted array of objects
	 *
	 * @uses $this->serializeToArray(array)
	 *
	 * @param array $results
	 * @return string
	 */
	protected function serializeToJson(array $results)
	{
		$resultsArray = $this->serializeToArray($results);
		return json_encode($resultsArray);
	}

	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Doctrine\ORM\EntityManagerInterface',
				'object' => $this->entityManager,
			),
			array(
				'name' => 'Acl\Model\Factory\EntityFactoryInterface',
				'object' => $this->factory,
			),
			array(
				'name' => 'Acl\Model\Wrapper\EntityWrapperInterface',
				'object' => $this->wrapper,
			),
		);
	}

}