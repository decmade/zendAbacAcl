<?php
namespace Acl\Model\Factory;

class AttributeFactory extends AbstractEntityFactory
{
	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Model\Factory\AbstractEntityFactory::createInstance()
	 */
	public function createInstance(array $config)
	{
		$attribute = $this->getPrototypeClone();

		foreach($config as $param => $value) {
			$param = strtolower($param);

			switch($param) {
				case 'name' :
					$attribute->setName($value);
					break;
				case 'value' :
					$attribute->setValue($value);
					break;
			}
		}

		return $attribute;
	}
}