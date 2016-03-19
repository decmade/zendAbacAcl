<?php
namespace Acl\Model\Authorization;

use Acl\Model\AbstractFactory;

class AttributePatternFactory extends AbstractFactory
{

	/**
	 * return object hydrated by the values
	 * specified in the configuration array
	 *
	 * @param array $config
	 */
	public function createInstace(array $config)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$attributePattern = clone $this->prototype;

		foreach($config as $param=>$value) {
			$param = strtolower($param);

			switch($param) {
				case 'name' :
					$attributePattern->setNamePattern($value);
					break;
				case 'value' :
					$attributePattern->setValuePattern($value);
					break;
			}
		}

		return $attributePattern;
	}
}