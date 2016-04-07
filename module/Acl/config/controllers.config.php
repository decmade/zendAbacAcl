<?php
return array(
	'invokables' => array(
	),
	'factories' => array(
		'Acl\Controller\Index' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$authenticationService = $sm->get('Acl\Authentication\Service');

			$controller = new \Acl\Controller\IndexController();
			$controller
				->setAuthenticationService($authenticationService);

			return $controller;
		},
		'Acl\Controller\User' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$em = $sm->get('Acl\Entity\Manager');
			$factory = $sm->get('Acl\Factory\User');
			$wrapper = $sm->get('Acl\Wrapper\User');
			$loginForm = $sm->get('Acl\Form\UserLogin');
			$authService = $sm->get('Acl\Authentication\Service');
			$authAdapter = $sm->get('Acl\Authentication\Adapter');
			$routeForwardingContainer = $sm->get('Acl\Authentication\Storage\RouteForwarding');

			$controller = new \Acl\Controller\UserController();
			$controller
				->setEntityManager($em)
				->setFactory($factory)
				->setWrapper($wrapper)
				->setLoginForm($loginForm)
				->setAuthenticationService($authService)
				->setAuthenticationAdapter($authAdapter)
				->setRouteForwardingContainer($routeForwardingContainer)
			;

			return $controller;

		},

	),
);