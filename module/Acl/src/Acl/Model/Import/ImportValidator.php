<?php
namespace Acl\Model\Import;


use Acl\Model\DependentObjectTrait;

class ImportValidator implements ImportValidatorInterface
{
	use DependentObjectTrait;

	/**
	 *
	 * @var array
	 */
	private $columnDefinitions;

	/**
	 *
	 * @var ColumnDefinitionWrapper $columnDefinitionWrapper
	 */
	private $columnDefinitionWrapper;

	/**
	 *
	 * @var array
	 */
	private $messages;



	public function __construct()
	{
		$this->columnDefinitions = array();
		$this->messages = array();
	}

	/**
	 *
	 * @param ColumnDefinition $def
	 *
	 * @return self
	 */
	public function addColumnDefinition(ColumnDefinition $definition)
	{
		$this->columnDefinitions[] = $definition;
		return $this;
	}

	/**
	 *
	 * @param ColumnDefinitionWrapper $wrapper
	 *
	 * @return self
	 */
	public function setColumnDefinitionWrapper(ColumnDefinitionWrapper $wrapper)
	{
		$this->columnDefinitionWrapper = $wrapper;
		return $this;
	}

	/**
	 * get error messages that are populated with causes for
	 * an isValid() value of false
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * detect if the data is valid for the column's defined
	 * by the ColumnDefinitin objects added
	 *
	 * @param array $data
	 */
	public function isValid(array $data)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$wrapper = $this->columnDefinitionWrapper;


		/*
		 * reset error message cache
		 */
		$this->messages = array();
		$rowCounter = 0;

		foreach($data as $row) {
			$rowCounter++;

			/*
			 * loop through each column definition to validate this row
			 * for each column
			 */
			foreach($this->columnDefinitions as $definition) {
				/*
				 * enclose the defintion in its facade wrapper
				 * class
				 */
				$wrapper->set($definition);

				switch(true) {
					case ( !$this->columnNameIsAnArrayKey($definition, $row) ) :
						if ($wrapper->getIsRequired()) {
							$this->messages[] = sprintf("missing column [%s] @ row %s", $wrapper->getName(), $rowCounter);
						}
						break;
					case ( !$this->rowDataIsValidForColumnName($row, $wrapper)) :
						foreach($wrapper->getValidator()->getMessages() as $message) {
							$this->messages[] = sprintf("%s @ row %s", $message, $rowCounter);
						}
						break;
				}
			}
		}

		return (count($messages) == 0);
	}

	/**
	 *
	 * @param ColumnDefinitionInterface $definition
	 * @param array $row
	 *
	 * @return boolean
	 */
	private function columnNameIsAnArrayKey(ColumnDefinitionInterface $definition, array $row)
	{
		$defColumnName = $definition->getName();

		return array_key_exists($defColumnName, $row);
	}

	/**
	 * validate the data in the row that matches the columnDefinition
	 * with the same name against the ColumnDefinition's validator
	 *
	 * @param array $row
	 * @param ColumnDefinitionWrapper $definition
	 */
	private function rowDataIsValidForColumnName(array $row, ColumnDefinitionWrapper $definition)
	{
		$value = $row[$definition->getName()];

		return $definition->isValid($value);
	}

	/**
	 * get dependencies configuration for DependentObjectTrait
	 *
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Import\ColumnDefinitionWrapper',
				'object' => $this->columnDefinitionWrapper,
			),
		);
	}


}