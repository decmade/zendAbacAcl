<?php
namespace Acl\Model\Import;


use Acl\Model\StandardInputFiltersTrait;
use Zend\Validator\ValidatorInterface;

class ColumnDefinition implements ColumnDefinitionInterface
{
	use StandardInputFiltersTrait;

	/**
	 *
	 * @var string
	 */
	private $name;

	/**
	 *
	 * @var int
	 */
	private $type;

	/**
	 *
	 * @var boolean
	 */
	private $isRequired;

	/**
	 *
	 * @var ValidatorInterface
	 */
	private $validator;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setName($value)
	{
		$this->name = $this->filterStringInput($value);
		return $this;
	}

	/**
	 * @return int
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 *
	 * @param int $value
	 *
	 * @return $this
	 */
	public function setValidator(ValidatorInterface $validator)
	{
		$this->validator = $validator;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsRequired()
	{
		return $this->isRequired;
	}

	/**
	 *
	 * @param boolean $value
	 *
	 * @return $this
	 */
	public function setIsRequired($value)
	{
		$this->isRequired = $this->filterBooleanInput($value);
		return $this;
	}
}