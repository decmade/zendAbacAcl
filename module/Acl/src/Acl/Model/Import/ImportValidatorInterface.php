<?php
namespace Acl\Model\Import;

interface ImportValidatorInterface
{
	/**
	 *
	 * @param ColumnDefinition $def
	 *
	 * @return self
	 */
	public function addColumnDefinition(ColumnDefinition $definition);

	/**
	 *
	 * @param ColumnDefinitionWrapper $wrapper
	 *
	 * @return self
	 */
	public function setColumnDefinitionWrapper(ColumnDefinitionWrapper $wrapper);

	/**
	 * get error messages that are populated with causes for
	 * an isValid() value of false
	 */
	public function getMessages();

	/**
	 * detect if the data is valid for the column's defined
	 * by the ColumnDefinitin objects added
	 *
	 * @param array $data
	 */
	public function isValid(array $data);
}