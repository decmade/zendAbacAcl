<?php
namespace Acl\Model\Factory;

class UserFactory extends AbstractEntityFactory
{
	/**
	 *
	 * {@inheritDoc}
	 * @see \Acl\Model\Factory\AbstractEntityFactory::createInstance()
	 */
	public function createInstance(array $config)
	{
		$user = $this->getPrototypeClone();

		foreach($config as $param => $value) {
			$param = strtolower($param);

			switch($param) {
				case 'identity' :
					$user->setIdentity($value);
					break;
				case 'credential' :
					$user->setCredential($value);
					break;
				case 'status' :
					$user->setStatus($value);
					break;
				case 'removed' :
					if (!empty($value)) {
						$user->setRemoved();
					}

					break;
			}
		}

		return $user;
	}
}