<?php
namespace Acl\Model\Import;


interface EntityImportInterface
{
	/**
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @return self
	 */
	public function setOption($name, $value);

	/**
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function getOption($name);

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
	 * @param mixed $source
	 * @param array $options
	 *
	 *
	 * @return array
	 */
	public function import($data, array $options = array());
}