<?php
namespace Acl\Controller;


use Zend\Form\Form;
use Acl\Model\Import\EntityImportInterface;
use Zend\Http\PhpEnvironment\Response;

class AttributeController extends AbstractEntityController
{

	/**
	 *
	 * @var EntityImportInterface
	 */
	private $attributeImport;

	/**
	 *
	 * @var Form
	 */
	private $importForm;


	/**
	 *
	 * @param EntityImportInterface $import
	 *
	 * @return self
	 */
	public function setAttributeImport(EntityImportInterface $import)
	{
		$this->attributeImport = $import;
		return $this;
	}

	/**
	 *
	 * @param Form $form
	 *
	 * @return self
	 */
	public function setImportForm(Form $form)
	{
		$this->importForm = $form;
		return $this;
	}

	public function indexAction()
	{
		return $this->redirect()->toRoute('acl/user/edit');
	}


	/**
	 *
	 */
	public function importAction()
	{
		/*
		 * run dependency checks
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

    	$form = $this->importForm;
    	$import = $this->attributeImport;

   		$tmpFile = null;
   		$options = array();

		$prg = $this->fileprg($form);

		switch(true) {
    		case ( $prg instanceof Response) :
    			return $prg;
    			break;
    		case ( is_array($prg)) :

    			if ($form->isValid()) {
    				$data = $form->getData();
    				$tmpFile = $data['uploadFile']['tmp_name'];
    				$options = array(
    					'isDefinitive' => $data['isDefinitive'],
    				);
    			} else {
    				$fileInput = $form->get('uploadFile');
    				$tmpFile = $fileInput->getValue()['tmp_name'];
    				$errors = $fileInput->getMessages();

    				if (!empty($errors)) {
						foreach($form->get('uploadFile')->getMessages() as $message) {
							$this->queueMessage($message, 'error');
						}
						$this->redirect()->toRoute('acl/import');
   					}

   				}
   				break;
   		}

   	   	if (is_file($tmpFile) ) {
			$results = $import->import($tmpFile, $options);
   	   		unlink($tmpFile);
			return $results;
   	   	} else {
   	   		$this->queueMessage('No file was uploaded', 'error');
   	   		$this->redirect()->toRoute('acl/import');
   	   	}

   	   	return $results;

	}

	/**
	 * @return array
	 */
	protected function getLocalDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Import\EntityImportInterface',
				'object' => $this->attributeImport,
			),
			array(
    			'name' => 'Acl\Model\Form\ImportForm',
    			'object' => $this->importForm,
    		),
		);
	}

	/**
	 * $messageType can be one of:
	 * 		'success'
	 * 		'info'
	 * 		'warning',
	 *		'error'
	 *
	 * @param int $userId
	 * @param string $messageTemplate // using "%s" parameter to include username
	 */
	private function queueMessage($message, $messageType = 'info')
	{
		$this->flashMessenger()->setNamespace($messageType)->addMessage($message);

	}
}