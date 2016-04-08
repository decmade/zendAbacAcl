<?php
namespace Acl\Model\Form;


use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class UserLoginFormInputFilter extends InputFilter
{
	public function __construct()
	{
		$this->add(array(
			'name' => 'identity',
			'required' => 'true',
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
// 				array('name' => 'Zend\I18n\Filter\Alnum'),	// each Zend\I18n\Filter component requires the intl PHP extension
			),
			'validators' => array(
				array(
					'name' => 'NotEmpty',
					'options' => array(
						'messages' => array(
							NotEmpty::IS_EMPTY => 'Username must NOT be empty',
						),
					),
				),
			),
		));

		$this->add(array(
			'name' => 'credential',
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
							NotEmpty::IS_EMPTY => 'Password must NOT be empty',
						),
					),
				),
			),
		));

	}
}