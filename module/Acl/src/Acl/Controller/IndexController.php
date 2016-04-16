<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Acl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationServiceInterface;
use Acl\Model\DependentObjectTrait;
use Zend\Form\Form;

class IndexController extends AbstractActionController
{
	use DependentObjectTrait;

	/**
	 *
	 * @var unknown
	 */
	private $authenticationService;

	/**
	 *
	 * @var Form
	 */
	private $importForm;

	/**
	 *
	 * @param AuthenticationServiceInterface $service
	 *
	 * @return self
	 */
	public function setAuthenticationService(AuthenticationServiceInterface $service)
	{
		$this->authenticationService = $service;
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
//     	$this->testUserCreation();
    	return array();

    }

    public function loginAction() {
    	$sm = $this->getServiceLocator();

    	$em = $sm->get('Acl\Entity\Manager');
    	$user = $em->getRepository('Acl\Entity\User')->find(2);

    	$result = $this->authenticationTest();

        return array(
        );
    }

    /**
     * the logout action and feedback
     */
    public function logoutAction()
    {
		/*
		 * check dependencies
		 */
    	$this->checkDependencies();

    	$authService = $this->authenticationService;

    	if ($authService->hasIdentity()) {
	    	$userId = $authService->getIdentity();

	    	$authService->clearIdentity();
    	} else {
    		$userId = 0;
    	}

    	return array(
			'userId' => $userId,
    	);
    }

    public function importAction()
    {
		/*
		 * run dependency check
		 */
    	$this->checkDependencies();

    	return array(
    		'form' => $this->importForm,
    	);
    }


    /**
     *
     * @return array
     */
    protected function getDependenciesConfig()
    {
    	return array(
    		array(
    			'name' => 'Zend\Authentication\AuthenticationService',
    			'object' => $this->authenticationService,
    		),
    		array(
    			'name' => 'Acl\Model\Form\ImportForm',
    			'object' => $this->importForm,
    		),
    	);
    }
}
