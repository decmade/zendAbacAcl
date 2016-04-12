<?php
namespace Acl\Model\Import;



use Zend\Validator\ValidatorInterface;

interface ColumnDefinitionInterface
{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setName($value);

	/**
	 * @return int
	 */
	public function getValidator();

	/**
	 *
	 * @param int $value
	 *
	 * @return $this
	 */
	public function setValidator(ValidatorInterface $validator);

	/**
	 * @return bool
	 */
	public function getIsRequired();

	/**
	 *
	 * @param boolean $value
	 *
	 * @return $this
	 */
	public function setIsRequired($value);
}