<?php
namespace Acl\Model\Import;


class ImportValidator implements ImportValidatorInterface
{

	/**
	 *
	 * @var array
	 */
	private $columnDefinitions;

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
	public function addColumnDefinition(ColumnDefinitionInterface $definition)
	{
		$this->columnDefinitions[] = $definition;
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

				switch(true) {
					case ( !array_key_exists($definition->getName(), $row ) ) :
						if ($definition->getIsRequired()) {
							$this->messages[] = sprintf("missing required column [%s] @ row %s", $definition->getName(), $rowCounter);
						}
						break;
					case ( !$definition->isValid($row[$definition->getName()])) :
						foreach($definition->getValidator()->getMessages() as $message) {
							$this->messages[] = sprintf("%s @ row %s", $message, $rowCounter);
						}
						break;
				}
			}
		}

		if (count($this->messages) > 0) {
			$this->messages[] = 'Import file failed validation and was not processed';
			return false;
		} else {
			return true;
		}
	}





}