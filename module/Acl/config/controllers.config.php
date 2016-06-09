<?php
return array(
	'invokables' => array(
	),
	'factories' => array(
		'Acl\Controller\Index' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$authenticationService = $sm->get('Acl\Authentication\Service');
			$importForm = $sm->get('Acl\Form\Import');

			$controller = new \Acl\Controller\IndexController();
			$controller
				->setAuthenticationService($authenticationService)
				->setImportForm($importForm)
			;

			return $controller;
		},
		'Acl\Controller\User' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$loginForm = $sm->get('Acl\Form\UserLogin');
			$profileForm = $sm->get('Acl\Form\UserProfile');
			$authService = $sm->get('Acl\Authentication\Service');
			$authAdapter = $sm->get('Acl\Authentication\Adapter');
			$routeForwardingContainer = $sm->get('Acl\Authentication\Storage\RouteForwarding');
			$userImport = $sm->get('Acl\Import\User');
			$importForm = $sm->get('Acl\Form\Import');
			$manager = $sm->get('Acl\Entity\Manager\User');

			$controller = new \Acl\Controller\UserController();
			$controller
				->setEntityObjectManager($manager)
				->setLoginForm($loginForm)
				->setProfileForm($profileForm)
				->setAuthenticationService($authService)
				->setAuthenticationAdapter($authAdapter)
				->setRouteForwardingContainer($routeForwardingContainer)
				->setUserImport($userImport)
				->setImportForm($importForm)
			;

			return $controller;
		},
		'Acl\Controller\Attribute' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$manager = $sm->get('Acl\Entity\Manager\Attribute');
			$attributeImport = $sm->get('Acl\Import\Attribute');
			$importForm = $sm->get('Acl\Form\Import');
			$controller = new \Acl\Controller\AttributeController();
			$controller
				->setEntityObjectManager($manager)
				->setAttributeImport($attributeImport)
				->setImportForm($importForm)
			;

			return $controller;
		},
	),
);