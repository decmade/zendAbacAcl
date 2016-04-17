<?php
namespace Acl\Model\Import;

use Acl\Entity\User;

/**
 * this class represents the import of attribute entities which have a foreign key
 * relationship with a user entity. the initial purpose of this class is to support the use
 * case of importing attributes with user identities specified for each
 * attribute to prevent attributes for a single user from being duplicated without
 * the site administrator having to know the surrogate keys of each user when creating
 * the import file
 *
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class AttributeEntityImport extends EntityImport
{

	/**
	 *
	 * @param array $config
	 *
	 * @return EntityInterface
	 */
	protected function hydrateEntity(array $config)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$attribute = $this->factory->createInstance($config);

		/*
		 * use the identity specified in the configuration to
		 * get the existing user
		 */
		$user = $this->manager->getRepository(User::getEntityClass())->findOneBy(array(
			'identity' => $config['identity'],
		));

		/*
		 * if the user is found, return the attribute with the user
		 * attached
		 *
		 * otherwise return null, which will make the import method skip this record
		 */
		if ($user) {
			return $attribute->setUser($user);
		} else {
			return null;
		}


	}

}