<?php
return array(
	'aliases' => array(
		'Acl\Entity\Manager' => 'Doctrine\ORM\EntityManager',
	),
	'invokables' => array(
		'Acl\Authentication\Result' => 'Acl\Model\Authentication\Result',
		'Acl\Entity\Session' => 'Acl\Entity\Session',
		'Acl\Authorization\AttributePattern' => 'Acl\Model\Authorization\AttributePattern',
	),
	'factories' => array(
		'Acl\Authentication\Adapter' => function($sm) {
			$entityManager = $sm->get('Acl\Entity\Manager');
			$resultPrototype = $sm->get('Acl\Authentication\Result');

			$adapter = new Acl\Model\Authentication\DoctrineAuthenticationAdapter();
			$adapter
				->setEntityManager($entityManager)
				->setResultPrototype($resultPrototype);

			return $adapter;

		},
		'Acl\Authentication\Storage\Session' => function($sm) {
			$namespace = 'Zend\Acl';
			return new \Zend\Authentication\Storage\Session($namespace);
		},
		'Acl\Authentication\Storage\Doctrine' => function($sm) {
			/*
			 * the doctrine storage object points to the session record in the database
			 *
			 * the container/session variable is used to store the surrogate key
			 * for the session recorded in the database
			 *
			 * the user and the session entities are never stored in
			 * the container/session, only de-referenced when needed by the
			 * entity manager using their surrogate keys
			 */
			$container = $sm->get('Acl\Authentication\Storage\Session');
			$sessionPrototype = $sm->get('Acl\Entity\Session');
			$entityManager = $sm->get('Acl\Entity\Manager');

			$storage = new \Acl\Model\Authentication\DoctrineSessionStorage();
			$storage
				->setContainer($container)
				->setSessionPrototype($sessionPrototype)
				->setEntityManager($entityManager);

			return $storage;
		},
		'Acl\Authentication\Service' => function($sm) {
			$storage = $sm->get('Acl\Authentication\Storage\Doctrine');

			$service = new \Zend\Authentication\AuthenticationService();
			$service
				->setStorage($storage);

			return $service;
		},
// 		'Acl\Authorization\AttributePattern\Factory' => function($sm) {
// 			$prototype = $sm->get('Acl\Authorization\AttributePattern');

// 			$factory = new \Acl\Model\Authorization\AttributePatternFactory();
// 			$factory
// 				->setPrototype($prototype);

// 			return $factory;
// 		},
// 		'Acl\Authorization\AttributePatternSetConfigParser' => function($sm) {
// 			$factory = $sm->get('Acl\Authorization\AttributePattern\Factory');

// 			$parser = new \Acl\Model\Authorization\AttributePatternSetConfigParser();
// 			$parser
// 				->setAttributePatternFactory($factory);

// 			return $parser;
// 		},
		'Acl\Authorization\UserAttributeEvaluator' => function($sm) {
			$em = $sm->get('Acl\EntityManager');

			$evaluator = new \Acl\Model\Authorization\UserAttributeEvaluator();
			$evaluator
				->setEntityManager($em);

			return $evaluator;
		},
		'Acl\Authorization\UserAttributeEvaluatorListener' => function($sm) {
			$evaluator = $sm->get('Acl\Authorization\UserAttributeEvaluator');
			$authService = $sm->get('Acl\Authentication\Service');

			$listener = new \Acl\Model\Authorization\UserAttributeEvaluatorListener();
			$listener
				->setAuthenticationService($authService)
				->setUserAttributeEvaluator($evaluator);

			return $listener;
		},
	),

);