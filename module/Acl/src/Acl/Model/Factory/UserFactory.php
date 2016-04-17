<?php
namespace Acl\Model\Factory;

use Acl\Entity\User;

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
				case 'active' :
					/*
					 * if the value is a string, convert
					 * it to lowercase for comparison
					 */
					if (is_string($value)) {
						$value = strtolower($value);
					}

					/*
					 * what are the possible values for
					 * active
					 */
					$possibleTrueValues = array(
						1,
						'true',
						'y',
						'yes',
					);

					/*
					 * if the value is one of the possible values
					 * above, set the status to active, otherwise set
					 * the user's status as inactive
					 */
					if (in_array($value, $possibleTrueValues)) {
						$user->setStatus(User::STATUS_ACTIVE);
					} else {
						$user->setStatus(User::STATUS_INACTIVE);
					}

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