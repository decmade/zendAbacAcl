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
			'required' => true,
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
			),
			'validators' => array(
			),
		));
	}

}