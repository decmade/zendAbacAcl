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
		$this->name = strtolower($this->filterStringInput($value));
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
	 * checks to see if the value passed is
	 * valid input for the ColumnDefinition
	 * injected
	 *
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	public function isValid($value)
	{

		if ($this->validator) {
			return $this->validator->isValid($value);
		} else {
			return false;
		}
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