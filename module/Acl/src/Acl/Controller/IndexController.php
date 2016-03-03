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
	 * @param AuthenticationServiceInterface $service
	 *
	 * @return $this
	 */
	public function setAuthenticationService(AuthenticationServiceInterface $service)
	{
		$this->authenticationService = $service;
		return $this;
	}

    public function indexAction()
    {

    	$sm = $this->getServiceLocator();

    	$em = $sm->get('Acl\Entity\Manager');
    	$user = $em->getRepository('Acl\Entity\User')->find(2);

    	$result = $this->authenticationTest();

    	$router = $sm->get('Router');
    	$request = $this->getRequest();
    	$attributePatternSetConfig = $router->match($request)->getParam('attributePatternSetConfig');
		$parser = $sm->get('Acl\Authorization\AttributePatternSetConfigParser');
		$attributePatterns = $parser->parse($attributePatternSetConfig);


        return array(
        	'test' => array(
        		'authenticationTest' => $result,
        		'attributePatternSetConfig' => $attributePatternSetConfig,
        		'attributePatternSet' => $attributePatterns,
        	),
        );
    }

    public function logoutAction()
    {
		/*
		 * check dependencies
		 */
    	$this->checkDependencies();

    	$authService = $this->authenticationService;

    	$authService->clearIdentity();
    }

    private function authenticationTest()
    {
    	$sm = $this->getServiceLocator();
    	$em = $sm->get('Acl\Entity\Manager');
    	$authService = $sm->get('Acl\Authentication\Service');
    	$authAdapter = $sm->get('Acl\Authentication\Adapter');

    	$authAdapter
    		->setIdentity('dev')
    		->setCredential('testPass');

    	if ($authService->hasIdentity()) {
    		return array(
    			'identity' => $authService->getIdentity(),
    		);
    	} else {
    		return $authService->authenticate($authAdapter);
    	}

    }

    private function testUserCreation()
    {
    	$em = $this->getServiceLocator()->get('Acl\Entity\Manager');
    	$user = new \Acl\Entity\User();
    	$user
    		->setIdentity('dev')
    		->setCredential('testPass');

    	$em->persist($user);
    	$em->flush();

    	return $user;
    }

    protected function getDependenciesConfig()
    {
    	return array(
    		array(
    			'name' => 'Zend\Authentication\AuthenticationService',
    			'object' => $this->authenticationService,
    		),
    	);
    }
}
