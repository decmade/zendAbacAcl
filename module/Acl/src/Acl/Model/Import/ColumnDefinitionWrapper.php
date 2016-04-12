<?php
namespace Acl\Model\Import;

use Acl\Model\DependentObjectTrait;
use Acl\Model\FacadeTrait;

/**
 * facade wrapped around a ColumnDefinition
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class ColumnDefinitionWrapper implements ColumnDefinitionInterface
{
	use DependentObjectTrait;

	/**
	 *
	 * @var ColumnDefinition
	 */
	private $definition;

	/**
	 *
	 * @param ColumnDefinition $definition
	 *
	 * @return self
	 */
	public function setDefinition(ColumnDefinition $definition)
	{
		$this->definition = $defintion;
		return $this;
	}


	/**
	 * checks to see if the value passed is
	 * valid input for the ColumnDefinition
	 * injected
	 *
	 * @param mixed $value
	 */
	public function isValid($value)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$validator = $this->definition->getValidator();

		if ($validator != null ) {
			return $validator->isValid($value);
		} else {
			return true;
		}
	}

	/*
	 * 	BEGIN: ColumnDefinitionInterface Methods
	 */

	/**
	 * @return string
	 */
	public function getName()
	{
		if ($this->hasDependencies()) {
			return $this->definition->getName();
		} else {
			return '';
		}
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setName($value)
	{
		if ($this->hasDependencies()) {
			$this->definition->setName($value);
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function getValidator()
	{
		if ($this->hasDependencies()) {
			return $this->definition->getValidator();
		} else {
			return null;
		}

	}

	/**
	 *
	 * @param int $value
	 *
	 * @return $this
	 */
	public function setValidator(ValidatorInterface $validator)
	{
		if ($this->hasDependencies()) {
			$this->definition->setValidator($validator);
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsRequired()
	{
		if ($this->hasDependencies()) {
			return $this->definition->getIsRequired();
		} else {
			return false;
		}

	}

	/**
	 *
	 * @param boolean $value
	 *
	 * @return $this
	 */
	public function setIsRequired($value)
	{
		if ($this->hasDependencies()) {
			$this->definition->setIsRequired($value);
		}

		return $this;
	}

	/**
	 * get the dependencies for the DependentObjectTrait
	 *
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Import\ColumnDefinition',
				'object' => $this->definition,
			),
			array(

			),
		);
	}

}