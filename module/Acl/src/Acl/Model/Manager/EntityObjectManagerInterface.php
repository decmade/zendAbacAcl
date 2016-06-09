<?php
namespace Acl\Model\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\Factory\EntityFactoryInterface;
use Acl\Model\Wrapper\EntityWrapperInterface;
use Acl\Entity\EntityInterface;


interface EntityObjectManagerInterface
{
	/**
	 * @return EntityManagerInterface
	 */
	public function getEntityManager();

	/**
	 *
	 * @param EntityManagerInterface $em
	 * @return self
	 */
	public function setEntityManager(EntityManagerInterface $em);



	/**
	 * @return EntityFactoryInterface
	 */
	public function getFactory();

	/**
	 *
	 * @param EntityFactoryInterface $factory
	 * @return self
	 */
	public function setFactory(EntityFactoryInterface $factory);

	/**
	 * @return EntityWrapperInterface
	 */
	public function getWrapper();

	/**
	 *
	 * @param EntityWrapperInterface $wrapper
	 * @return self
	 */
	public function setWrapper(EntityWrapperInterface $wrapper);

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
	public function findBy(array $criteria, array $orderBy, $outputFormat);

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
	public function findAll(array $orderBy, $outputFormat);

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
	public function findOneBy(array $criteria, $outputFormat);

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
	public function create(array $config, $persist);

	/**
	 * remove an entity from persistent storage
	 *
	 * @param EntityInterface $entity
	 */
	public function destroy(EntityInterface $entity);

	/**
	 * perform all operations queued for persistent storage
	 *
	 * @return self
	 */
	public function commit();
}