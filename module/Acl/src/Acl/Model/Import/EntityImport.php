<?php
namespace Acl\Model\Import;

use Doctrine\ORM\EntityManagerInterface;
use Acl\Model\Factory\EntityFactoryInterface;
use Acl\Model\StandardInputFiltersTrait;
use Acl\Model\DependentObjectTrait;
use Acl\Entity\EntityInterface;
use \DateTime;
use Acl\Model\Wrapper\EntityWrapperInterface;

class EntityImport implements EntityImportInterface
{
	use StandardInputFiltersTrait;
	use DependentObjectTrait;

	/**
	 *
	 * @var EntityManagerInterface
	 */
	protected $manager;

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
	 * @var ImportAdapterInterface
	 */
	protected $adapter;

	/**
	 *
	 * @var ImportValidatorInterface
	 */
	protected $validator;


	/**
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * used to cache the entity class name through a lazy
	 * load from the getEntityClassName() method
	 *
	 * @var string
	 */
	protected $entityClassName;


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
			'isDefinitive' => 'flase', // means that the data being imported should be the only active data
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
	 * @param EntityWrapperInterface $wrapper
	 *
	 * @return self
	 */
	public function setWrapper(EntityWrapperInterface $wrapper)
	{
		$this->wrapper = $wrapper;
		return $this;
	}

	/**
	 *
	 * @param ImportAdapterInterface $adapter
	 *
	 * @return self
	 */
	public function setAdapter(ImportAdapterInterface $adapter)
	{
		$this->adapter = $adapter;
		return $this;
	}

	/**
	 *
	 * @param ImportValidatorInterface $validator
	 *
	 * @return self
	 */
	public function setValidator(ImportValidatorInterface $validator)
	{
		$this->validator = $validator;
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
			$value = strtolower($value);
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
	 * merge the data in the associative array with the entity
	 * in storage
	 *
	 * return an array of metrics
	 * 	->totalRecords
	 *  ->addedRecords
	 *  ->updatedRecords
	 *
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function import($source, array $options = array())
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$em = $this->manager;
		$factory = $this->factory;
		$wrapper = $this->wrapper;
		$validator = $this->validator;
		$counts = array(
			'total' => 0,
			'added' => 0,
			'updated' => 0,
			'skipped' => 0,
		);
		$messages = array();

		/*
		 * process options array
		 */
		$this->processOptionsArray($options);

		/*
		 * retrieve the data from the source
		 * through the adapter
		 */
		$data = $this->getDataFromAdapter($source);

		if ($validator->isValid($data)) {

			/*
			 * handle any options that address items that occur prior
			 * to the import
			 */
			switch(true) {
				case ($this->getOption('isDefinitive') == 'true') :
				case ($this->getOption('isDefinitive') == '1') :
					$this->removeExistingEntities();
					break;
			}

			/*
			 * loop through the data to either insert or update
			 * each user according to the data currently in the table
			 */
			foreach($data as $row) {
				$counts['total']++;

				$importedEntity = $this->hydrateEntity($row);

				/*
				 * if the hydratedEntity function does not return an object,
				 * something is wrong with the data, so skip this record
				 */
				if ($importedEntity == null) {
					$counts['skipped']++;

					$messages[] = sprintf("skipped record at row #%s", $counts['total'] );
					continue;
				}

				$existingEntities = $this->retrieveEntitiesByCriteria(
					$wrapper
						->setEntity($importedEntity)
						->getUniquePropertiesArray()
				);


				if (count($existingEntities) == 0) {
					$em->persist($importedEntity);
					$counts['added']++;
				} else {
					foreach($existingEntities as $original) {
						$wrapper
							->setEntity($original)
							->copy($importedEntity);

						$counts['updated']++;
					}
				}
			}

			$em->flush();
		} else {
			$messages = $validator->getMessages();
		}


		return array(
			'counts' => $counts,
			'messages' => $messages,
		);

	}


	/**
	 * returns true if the option is available to
	 * the import
	 *
	 * @return boolean
	 */
	protected function hasOption($name)
	{
		return array_key_exists($name, $this->options);
	}

	/**
	 * lazy-load the imported entity class name by pulling
	 * it from the factory's prototype
	 *
	 * @return string
	 */
	protected function getEntityClassName()
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
	protected function processOptionsArray(array $options)
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
	protected function hydrateEntity(array $config)
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
	protected function retrieveEntitiesByCriteria(array $criteria)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$className = $this->getEntityClassName();

		return $this->manager->getRepository($className)->findBy($criteria);
	}

	/**
	 * sets the Removed date on all entities, marking them to be ignored going forward
	 */
	protected function removeExistingEntities()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$em = $this->manager;
		$removedTimeStamp = new DateTime();
		$entities = $em->getRepository($this->getEntityClassName())->findAll();

		foreach($entities as $entity) {
			$entity->setRemoved($removedTimeStamp);
		}

		$em->flush();
	}

	/**
	 * retrieve the data from the source
	 * using the appropriate ImportAdapterInterface
	 *
	 * @param mized $source
	 */
	protected function getDataFromAdapter($source)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		return $this->adapter->import($source);

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
				'name' => 'Acl\Model\Factory\EntityFactoryInterface',
				'object' => $this->factory,
			),
			array(
				'name' => 'Acl\Model\Wrapper\EntityWrapperInterface',
				'object' => $this->wrapper,
			),
			array(
				'name' => 'Acl\Model\Import\ImportAdapterInterface',
				'object' => $this->adapter,
			),
			array(
				'name' => 'Acl\Model\Import\ImportValidatorInterface',
				'object' => $this->validator,
			),
		);
	}
}