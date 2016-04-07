<?php
namespace Acl\Model\Form;

use Zend\Form\Form;

class UserLoginForm extends Form
{
	public function __construct()
	{
		parent::__construct('userLogin');

		/*
		 * username field
		 */
		$this->add(array(
			'name' => 'identity',
			'type' => 'text',
			'options' => array(
				'label' => 'Username',
			),
			'attributes' => array(
				'placeholder' => 'username',
			),
		));

		/*
		 * password field
		 */
		$this->add(array(
			'name' => 'credential',
			'type'=> 'password',
			'options' => array(
				'label' => 'Password',
			),
			'attributes' => array(
				'placeholder' => 'password',
			)
		));

		/*
		 * submit button
		 */
		$this->add(array(
			'name' => 'submit',
			'type' => 'submit',
			'attributes' => array(
				'value' => 'Login',
			),
		));

// 		foreach($this->getElements() as $element) {
// 			die($element->getType());
// 			$type = $element->getType();

// 			switch($type) {
// 				case 'text' :
// 					break;
// 			}
// 		}
	}
}