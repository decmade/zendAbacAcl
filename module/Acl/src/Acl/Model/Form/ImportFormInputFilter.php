<?php
namespace Acl\Model\Form;


use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class ImportFormInputFilter extends InputFilter
{
	public function __construct()
	{
		$this->add(array(
			'name' => 'uploadFile',
			'required' => 'true',
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array(
					'name' => 'FileRenameUpload',
					'options' => array(
 						'target' => implode(DIRECTORY_SEPARATOR, array(
 							__DIR__,
 							'uploads',
 							'import.csv',
 						)),
						'randomize' => true,
					),
				),
// 				array('name' => 'Zend\I18n\Filter\Alnum'),	// each Zend\I18n\Filter component requires the intl PHP extension
			),
			'validators' => array(
// 				array(
// 						/*
// 						 * not dependable across different browsers
// 						 */
// 					'name' => 'FileMimeType',
// 					'options' => array(
// 						'mimeType' => 'text/plain',
// 						'messages' => array(
// 						),
// 					),
// 				),
			),
		));

// 		$this->add(array(
// 			'name' => 'credential',
// 			'required' => 'true',
// 			'filters' => array(
// 				array('name' => 'StripTags'),
// 				array('name' => 'StringTrim'),
// 			),
// 			'validators' => array(
// 				array(
// 					'name' => 'NotEmpty',
// 					'options' => array(
// 						'messages' => array(
// 							NotEmpty::IS_EMPTY => 'Password must NOT be empty',
// 						),
// 					),
// 				),
// 			),
// 		));

	}
}