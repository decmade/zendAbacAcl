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

			$em = $sm->get('Acl\Entity\Manager');
			$factory = $sm->get('Acl\Factory\User');
			$wrapper = $sm->get('Acl\Wrapper\User');
			$loginForm = $sm->get('Acl\Form\UserLogin');
			$profileForm = $sm->get('Acl\Form\UserProfile');
			$authService = $sm->get('Acl\Authentication\Service');
			$authAdapter = $sm->get('Acl\Authentication\Adapter');
			$routeForwardingContainer = $sm->get('Acl\Authentication\Storage\RouteForwarding');
			$userImport = $sm->get('Acl\Import\User');
			$importForm = $sm->get('Acl\Form\Import');
			$controller = new \Acl\Controller\UserController();
			$controller
				->setEntityManager($em)
				->setFactory($factory)
				->setWrapper($wrapper)
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

			$em = $sm->get('Acl\Entity\Manager');
			$factory = $sm->get('Acl\Factory\Attribute');
			$wrapper = $sm->get('Acl\Wrapper\Attribute');
			$attributeImport = $sm->get('Acl\Import\Attribute');
			$importForm = $sm->get('Acl\Form\Import');
			$controller = new \Acl\Controller\AttributeController();
			$controller
				->setEntityManager($em)
				->setFactory($factory)
				->setWrapper($wrapper)
				->setAttributeImport($attributeImport)
				->setImportForm($importForm)
			;

			return $controller;
		},
	),
);