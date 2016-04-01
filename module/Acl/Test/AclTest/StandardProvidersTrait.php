<?php
namespace AclTest;

use Acl\Entity\User;

trait StandardProvidersTrait
{
	/**
	 * data sets for $this::testNamePropertyAccessors()
	 *
	 * @return array
	 */
	public function providerTestStringPropertyAccessors()
	{
		return array(
				array('development', 'development'),
				array(5478902578402, '5478902578402'),
				array(0.254, '0.254'),
				array(true, '1'),
				array(new \stdClass(), '<invalid>'),
				array(array(1,2,3), '<invalid>'),
				array(null, ''),
		);
	}

	/**
	 * data sets for $this::testNamePropertyAccessors()
	 *
	 * @return array
	 */
	public function providerTestIntegerPropertyAccessors()
	{
		return array(
				array('development', 0),
				array(5478902578402, '5478902578402'),
				array(0.254, 0),
				array(true, '1'),
				array(new \stdClass(), 0),
				array(array(1,2,3), 0),
				array(null, 0),
		);
	}


	/**
	 * data sets for $this::testNamePropertyAccessors()
	 *
	 * @return array
	 */
	public function providerTestHashedPropertyAccessors()
	{
		return array(
				array('development', true),
				array(5478902578402, true),
				array(0.254, true),
				array(true, true),
				array(new \stdClass(), false),
				array(array(1,2,3), false),
				array(null, true),
		);
	}

	/**
	 * @return array
	 */
	public function providerTestThatUserPropertyIsPassedByReference()
	{
		return array(
				array('getStatus', User::STATUS_ACTIVE),
				array('getIdentity', 'twasBrillig34'),
				array('getSessions', array(1,2,3,4,5)),
		);
	}
}