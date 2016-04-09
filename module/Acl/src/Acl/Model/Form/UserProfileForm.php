<?php
namespace Acl\Model\Form;

use Zend\Form\Form;

/**
 * user profile options, at this time, the user should only
 * be able to change their passwords
 *
 * all other attributes are maanged by administrator import
 * since permissions are potentially attached to any attribute
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class UserProfileForm extends Form
{
	public function __construct()
	{
		parent::__construct('userProfile');

		/*
		 * password field
		 */
		$this->add(array(
			'name' => 'newCredential-1',
			'type'=> 'password',
			'options' => array(
				'label' => 'New Password',
			),
			'attributes' => array(
				'placeholder' => 'new password',
			)
		));

		/*
		 * password field
		 */
		$this->add(array(
				'name' => 'newCredential-2',
				'type'=> 'password',
				'options' => array(
						'label' => 'Confirm Password',
				),
				'attributes' => array(
						'placeholder' => 'confirm password',
				)
		));

		/*
		 * submit button
		 */
		$this->add(array(
			'name' => 'submit',
			'type' => 'submit',
			'attributes' => array(
				'value' => 'Change Password',
			),
		));

	}
}