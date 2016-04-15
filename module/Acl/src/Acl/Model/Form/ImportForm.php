<?php
namespace Acl\Model\Form;

use Zend\Form\Form;

class ImportForm extends Form
{
	public function __construct()
	{
		parent::__construct('import');

		/*
		 * username field
		 */
		$this->add(array(
			'name' => 'uploadType',
			'type' => 'select',
			'options' => array(
				'label' => 'Import Type',
				'value_options' => array(
					'acl/user/import' => 'Users',
					'acl/attribute/import' => 'Attributes',
				),
			),
			'attributes' => array(
			),
		));

		/*
		 * password field
		 */
		$this->add(array(
			'name' => 'uploadFile',
			'type'=> 'file',
			'options' => array(
				'label' => 'Select Import File',
			),
			'attributes' => array(
			),
		));

		/*
		 * submit button
		 */
		$this->add(array(
			'name' => 'submit',
			'type' => 'submit',
			'attributes' => array(
				'value' => 'Import File',
			),
		));
	}
}