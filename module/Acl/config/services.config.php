<?php
return array(
	'aliases' => array(
		'Acl\Entity\Manager' => 'Doctrine\ORM\EntityManager',
	),
	'invokables' => array(
		'Acl\Authentication\Result' => 'Acl\Model\Authentication\Result',
		'Acl\Entity\Session' => 'Acl\Entity\Session',
		'Acl\Entity\User' => 'Acl\Entity\User',
		'Acl\Entity\Attribute' => 'Acl\Entity\Attribute',
		'Acl\DefaultViewModel' => 'Zend\View\Model\ViewModel',
		'Acl\Form\UserLogin\InputFilter' => 'Acl\Model\Form\UserLoginFormInputFilter',
		'Acl\Form\UserProfile\InputFilter' => 'Acl\Model\Form\UserProfileFormInputFilter',
		'Acl\Form\Import\InputFilter' => 'Acl\Model\Form\ImportFormInputFilter',
		'Acl\Wrapper\User' => 'Acl\Model\Wrapper\UserWrapper',
		'Acl\Wrapper\Attribute' => 'Acl\Model\Wrapper\AttributeWrapper',
		'Acl\Wrapper\ColumnDefinition' => 'Acl\Model\Import\ColumnDefinitionWrapper',
		'Acl\Import\Adapter\CsvFile' => 'Acl\Model\Import\CsvFileImportAdapter',
		'Acl\Import\ColumnDefinition' => 'Acl\Model\Import\ColumnDefinition',
		'Acl\Import\Validator\UniqueValue' => 'Acl\Model\Import\UniqueValue',
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
			$routeForwardingContainer = $sm->get('Acl\Authentication\Storage\RouteForwarding');

			$listener = new \Acl\Model\Authorization\UserAttributeEvaluatorListener();
			$listener
				->setAuthenticationService($authService)
				->setUserAttributeEvaluator($evaluator)
				->setRouteForwardingContainer($routeForwardingContainer)
			;

			return $listener;
		},
		'Acl\View\CurrentUserListener' => function($sm) {
			$view = $sm->get('Acl\DefaultViewModel');

			$listener = new \Acl\Model\View\CurrentUserListener();
			$listener
				->setViewModel($view);

			return $listener;
		},
		'Acl\View\FlashMessengerListener' => function($sm) {
			$view = $sm->get('Acl\DefaultViewModel');

			$listener = new \Acl\Model\View\FlashMessengerListener();
			$listener
				->setViewModel($view);

			return $listener;
		},
		'Acl\GuestUser' => function($sm) {
			$user = $sm->get('Acl\Entity\User');
			$user
				->setIdentity('guest');

			return $user;
		},
		'Acl\Factory\Attribute' => function($sm) {
			$attribute = $sm->get('Acl\Entity\Attribute');

			$factory = new \Acl\Model\Factory\AttributeFactory();
			$factory
				->setPrototype($attribute);

			return $factory;
		},
		'Acl\Factory\User' => function($sm) {
			$user = $sm->get('Acl\Entity\User');

			$factory = new \Acl\Model\Factory\UserFactory();
			$factory
				->setPrototype($user);

			return $factory;
		},
		'Acl\Factory\ColumnDefinition' => function($sm) {
			$prototype = $sm->get('Acl\Import\ColumnDefinition');

			$factory = new \Acl\Model\Factory\ColumnDefinitionFactory();
			$factory
				->setPrototype($prototype);

			return $factory;
		},
		'Acl\Form\UserLogin' => function($sm) {

			$inputFilter = $sm->get('Acl\Form\UserLogin\InputFilter');

			$form = new \Acl\Model\Form\UserLoginForm();
			$form->setInputFilter($inputFilter);

			return $form;
		},
		'Acl\Form\UserProfile' => function($sm) {

			$inputFilter = $sm->get('Acl\Form\UserProfile\InputFilter');

			$form = new \Acl\Model\Form\UserProfileForm();
			$form->setInputFilter($inputFilter);

			return $form;
		},
		'Acl\Form\Import' => function($sm) {

			$inputFilter = $sm->get('Acl\Form\Import\InputFilter');

			$form = new \Acl\Model\Form\ImportForm();
			$form->setInputFilter($inputFilter);

			return $form;
		},
		'Acl\Authentication\Storage\RouteForwarding' => function($sm) {
			$namespace = 'Zend\Acl\RouteForwarding';
			return new \Zend\Session\Container($namespace);
		},
		'Acl\Import\User\Validator' => function($sm) {
			$wrapper = $sm->get('Acl\Wrapper\ColumnDefinition');

			$validator = new \Acl\Model\Import\ImportValidator();
			$validator
				->setColumnDefinitionWrapper($wrapper)
				->addColumnDefinition($sm->get('Acl\Import\ColumnDefinition\Identity'))
			;

			return $validator;
		},
		'Acl\Import\ColumnDefinition\Identity' => function($sm) {
			$validator = $sm->get('Acl\Import\ColumDefinition\Identity\ValidatorChain');
			$factory = $sm->get('Acl\Factory\ColumnDefinition');

			return $factory->createInstance(array(
				'name' => 'identity',
				'validator' => $validator,
				'required' => true,
			));
		},
		'Acl\Import\ColumDefinition\Identity\ValidatorChain' => function($sm) {
			/*
			 * using a clone with a cleared cache to make certain that the values are only
			 * being compared to other values in this data domain
			 */
			$uniqueValidator = clone $sm->get('Acl\Import\Validator\UniqueValue');
			$uniqueValidator->clearCache();

			$chain = new \Zend\Validator\ValidatorChain();
			$chain
				->attachByName('NotEmpty', array(), true)
				->attach($uniqueValidator, true)
			;

			return $chain;
		},
		'Acl\Import\User' => function($sm) {
			$manager = $sm->get('Acl\Entity\Manager');
			$factory = $sm->get('Acl\Factory\User');
			$wrapper = $sm->get('Acl\Wrapper\User');
			$adapter = $sm->get('Acl\Import\Adapter\CsvFile');
			$validator = $sm->get('Acl\Import\User\Validator');

			$import = new \Acl\Model\Import\EntityImport();
			$import
				->setManager($manager)
				->setFactory($factory)
				->setWrapper($wrapper)
				->setAdapter($adapter)
				->setValidator($validator)
			;

			return $import;
		},
		'Acl\Import\Attribute' => function($sm) {
			$manager = $sm->get('Acl\Entity\Manager');
			$factory = $sm->get('Acl\Factory\Attribute');
			$wrapper = $sm->get('Acl\Wrapper\Attribute');
			$adapter = $sm->get('Acl\Import\Adapter\CsvFile');
			$validator = $sm->get('Acl\Import\Attribute\Validator');

			$import = new \Acl\Model\Import\EntityImport();
			$import
				->setManager($manager)
				->setFactory($factory)
				->setWrapper($wrapper)
				->setAdapter($adapter)
				->setValidator($validator)
			;

			return $import;
		},
	),

);