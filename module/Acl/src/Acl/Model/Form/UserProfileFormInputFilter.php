<?php
namespace Acl\Model\Form;


use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class UserProfileFormInputFilter extends InputFilter
{
	public function __construct()
	{
		$this->add(array(
			'name' => 'newCredential-1',
			'required' => 'true',
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'NotEmpty',
					'options' => array(
						'messages' => array(
							NotEmpty::IS_EMPTY => 'New Password Cannot Be Empty',
						),
					),
				),
			),
		));

		$this->add(array(
			'name' => 'newCredential-2',
			'required' => 'true',
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
				array(
					'name' => 'NotEmpty',
					'options' => array(
						'messages' => array(
							NotEmpty::IS_EMPTY => 'Confirm Password Cannot Be Empty',
						),
					),
				),
			),
		));

	}
}