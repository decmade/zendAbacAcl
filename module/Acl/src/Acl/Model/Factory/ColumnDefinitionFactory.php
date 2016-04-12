<?php
namespace Acl\Model\Factory;


class ColumnDefinitionFactory extends AbstractObjectFactory
{
	public function createInstance(array $config)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$definition = $this->prototype;

		foreach($config as $param => $value) {
			$param = strtotlower($param);

			switch($param) {
				case 'name' :
					$definition->setName($value);
					break;
				case 'validator' :
					$definition->setValidator($value);
					break;
				case 'required' :
					$definition->setRequired($value);
					break;
			}
		}

		return $definition;
	}


	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Model\Factory\AbstractObjectFactory::getDependenciesConfig()
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Import\ColumnDefinition',
				'object' => $this->prototype,
			),
		);
	}
}