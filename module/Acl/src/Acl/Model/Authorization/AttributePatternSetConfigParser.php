<?php
namespace Acl\Model\Authorization;

use Acl\Model\DependentObjectTrait;

class AttributePatternSetConfigParser
{
	use DependentObjectTrait;

	/**
	 *
	 * @var AttributePatternFactory
	 */
	private $attributePatternFactory;

	/**
	 *
	 * @param AttributePatternFactory $factory
	 * @return $this
	 */
	public function setAttributePatternFactory(AttributePatternFactory $factory)
	{
		$this->attributePatternFactory = $factory;
		return $this;
	}


	public function parse(array $set)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$factory = $this->attributePatternFactory;
		$output = array();

		foreach($set as $node) {
			switch(true) {
				/*
				 * skip any elements of the root array
				 * that are not themselves arrays
				 *
				 * there should never be a key=>value pair at
				 * the root of an attribute pattern set
				 * configuration, there is always at least an array of
				 * a (name, value) pair
				 */
				case (!is_array($node)) :
					continue;
				/*
				 * if the node is a valid AttributePattern configuration
				 * then create the object and add it to the output
				 */
				case ($this->isValidConfig($node)) :
					$output[] = $factory->createInstace($node);
					break;
				/*
				 * if the node is a valid nested configuration set
				 * then recursively pass it to this function and add
				 * the output array in place of the nested configuration
				 */
				case ($this->isNestedConfig($node)) :
					$output[] = $this->parse($node);
					break;
			}

		}

		return $output;


	}

	/**
	 * test if the configuration array is a valid AttributePattern
	 * configuration set
	 *
	 * @param array $patternSet
	 * @return boolean
	 */
	private function isValidConfig(array $config)
	{
		switch(true) {
			/*
			 * if the "name" key does not exist,
			 * this is not a valid AttributePattern
			 * configuration
			 */
			case (!array_key_exists('name', $config)) :
				return false;
			/*
			 * if the "value" key does not exist,
			 * this is not a valid AttributePattern
			 * configuration
			*/
			case (!array_key_exists('value', $config)) :
				return false;
			/*
			 * if both the "name" and "value" keys are present
			 * then you make it to this part of the code
			 * and this is a valid set
			 */
			default :
				return true;
		}
	}

	/**
	 * check to make certain that the array passed contains
	 * a nested set of AttributePattern configurations
	 *
	 * @param array $set
	 * @return boolean
	 */
	private function isNestedConfig(array $config)
	{
		foreach($config as $node) {
			/*
			 * if the node is an array then ...
			 */
			if  (is_array($node) ) {
				/*
				 * if any node is not a valid AttributePattern configuration AND is not a
				 * nested AttributePattern configuration set then this is not a
				 * valid nested configuration
				 */
				if ( !$this->isValidConfig($node) && !$this->isNestedConfig($node) ) {
					return false;
				}
			} else {
				/*
				 * if at least one node is not an array
				 * then this is not a nested configuration
				 */
				return false;
			}
		}

		/*
		 * if the configuration reaches this part of the code
		 * then the null hypotheses have been rejected and it is
		 * a nested configuration
		 */
		return true;
	}

	/**
	 * return dependencies configuration
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Authorization\AttributePatternFactory',
				'object' => $this->attributePatternFactory,
			),
		);
	}
}