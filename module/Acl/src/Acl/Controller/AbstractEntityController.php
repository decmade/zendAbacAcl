<?php
namespace Acl\Controller;



use Acl\Model\Factory\EntityFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\Wrapper\EntityWrapperInterface;
use Acl\Model\DependentObjectTrait;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractEntityController extends AbstractActionController
{
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityManagerInterface
	 */
	protected $entityManager;

	/**
	 *
	 * @var EntityFactoryInterface $factory
	 */
	protected $factory;

	/**
	 *
	 * @var EntityWrapperInterface $wrapper
	 */
	protected $wrapper;

	/**
	 *
	 * @param EntityManagerInterface $em
	 *
	 * @return @this
	 */
	public function setEntityManager(EntityManagerInterface $em)
	{
		$this->entityManager = $em;
		return $this;
	}

	/**
	 *
	 * @param EntityFactoryInterface $factory
	 *
	 * @return $this
	 */
	public function setFactory(EntityFactoryInterface $factory)
	{
		$this->factory = $factory;
		return $this;
	}

	/**
	 *
	 * @param EntityWrapperInterface $wrapper
	 *
	 * @return $this
	 */
	public function setWrapper(EntityWrapperInterface $wrapper)
	{
		$this->wrapper = $wrapper;
		return $this;
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