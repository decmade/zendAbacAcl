<?php
namespace Acl\Controller;



use Acl\Model\DependentObjectTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Acl\Model\Manager\EntityObjectManagerInterface;

abstract class AbstractEntityController extends AbstractActionController
{
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityObjectManagerInterface
	 */
	protected $entityObjectManager;


	/**
	 *
	 * @param EntityManagerInterface $em
	 *
	 * @return @this
	 */
	public function setEntityObjectManager(EntityObjectManagerInterface $manager)
	{
		$this->entityObjectManager = $manager;
		return $this;
	}


	/**
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Manager\EntityObjectManagerInterface',
				'object' => $this->entityObjectManager,
			),
		);
	}
}