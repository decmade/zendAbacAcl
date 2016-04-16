<?php
namespace Acl\Model\Form;

use Zend\Form\Form;

class ImportForm extends Form
{
	public function __construct()
	{
		parent::__construct('import');

		/*
		 * upload type field
		 */
		$this->add(array(
			'name' => 'uploadType',
			'type' => 'select',
			'options' => array(
				'label' => 'Import Type',
				'value_options' => array(
					'/acl/user/import' => 'Users',
					'/acl/attribute/import' => 'Attributes',
				),
			),
			'attributes' => array(
			),
		));

		/*
		 * file upload field
		 */
		$this->add(array(
			'name' => 'uploadFile',
			'type'=> 'file',
			'options' => array(
				'label' => 'Import From',
			),
			'attributes' => array(
			),
		));

				/*
		 * isDefinitive option field
		 */
		$this->add(array(
			'name' => 'isDefinitive',
			'type'=> 'checkbox',
			'options' => array(
				'label' => 'remove entities not included in uploaded file from the database.',
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
				'value' => 'Import Data',
			),
		));
	}
}