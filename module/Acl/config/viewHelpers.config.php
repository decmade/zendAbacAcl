<?php
return array(
	'aliases' => array(
	),
	'invokables' => array(
	),
	'factories' => array(
		'currentUser' => function($serviceLocator) {
			$sm = $serviceLocator->getServiceLocator();

			$service = $sm->get('Acl\Authentication\Service');
			$em = $sm->get('Acl\Entity\Manager');
			$guest = $sm->get('Acl\GuestUser');
			$wrapper = $sm->get('Acl\Wrapper\User');

			$helper = new \Acl\Model\View\CurrentUser();
			$helper
				->setAuthenticationService($service)
				->setEntityManager($em)
				->setGuestUser($guest)
				->setWrapper($wrapper)
			;

			return $helper;
		},
	),
);