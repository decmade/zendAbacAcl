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
    	$validator = $sm->get('Acl\UserAttributeValidator');
    	$accessDqlConfig = $router->match($request)->getParam('accessDqlConfig');

    	$validator->validate($result['identity'], $accessDqlConfig);

    	$isValid = $validator->validate($result['identity'], $accessDqlConfig);
    	$cachedAttributes = $validator->getCachedAttributes();
    	$attributesFound = array();

    	foreach($cachedAttributes as $attribute)
    	{
    		$attributesFound[] = array(
    			'name' => $attribute->getName(),
    			'value' => $attribute->getValue(),
    		);
    	}

        return array(
        	'test' => array(
        		'authenticationTest' => $result,
        		'accessDqlConfig' => $accessDqlConfig ,
        		'hasAccess' => ($isValid) ? 'YES' : 'NO',
        		'attributesFound' => $attributesFound,
        	),
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

    	$authService->clearIdentity();

    	return array(

    	);
    }

    private function authenticationTest()
    {
    	$sm = $this->getServiceLocator();
    	$em = $sm->get('Acl\Entity\Manager');
    	$authService = $sm->get('Acl\Authentication\Service');
    	$authAdapter = $sm->get('Acl\Authentication\Adapter');

    	if ($authService->hasIdentity()) {
    		$identity = $authService->getIdentity();

    		return array(
    			'identity' => $identity,
    			'messages' => array(
    				sprintf("User ID[%s] already logged in.", $identity),
    			),
    		);
    	} else {
    		$authAdapter
	    		->setIdentity('dev')
	    		->setCredential('testPass');
    		$result = $authService->authenticate($authAdapter);

    		if ($result->getCode() == $result::SUCCESS) {
    			$identity = $authService->getIdentity();

	    		return array(
	    			'identity' => $identity,
	    			'messages' => array(
	    				sprintf("User ID[%s] successfully logged in.", $identity),
	    			),
	    		);
    		} else {
    			return array(
    				'identity' => '',
    				'messages' => $authService->getMessages(),
    			);
    		}
    	}

    	return $output;
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
