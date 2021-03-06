<?php
/*
 * get the key for the route parameter that the
 * Acl\Model\UserAttributeEvaluatorListener is looking for
 */
$aclDqlKey = \Acl\Model\Authorization\UserAttributeEvaluatorListener::ACCESS_DQL_PARAM_NAME;

return array(
	'routes' => array(
		'acl' => array(
			'type' => 'Zend\Mvc\Router\Http\Segment',
			'options' => array(
				'route' => '/acl',
				'defaults' => array(
					'controller' => 'Acl\Controller\Index',
					'action'     => 'index',
					$aclDqlKey =>  array(
						array(
							"a.name = 'admin' AND a.value = '1'",
							"a.name = 'sitecode' AND a.value LIKE '74%'",
						),
						array(
							"a.name = 'siteadministrator' AND a.value = '1'",
						),
					),
				),
			),
			'may_terminate' => true,
			'child_routes' => array(
				'import' => array(
					'type' => 'segment',
					'options' => array(
						'route' => '/import',
						'defaults' => array(
							'action' => 'import',
							$aclDqlKey => array(
									"a.name = 'siteadministrator' and a.value not in ('','0','false')",
							),
						),
					),
				),
				'attribute' => array(
					'type' => 'segment',
					'options' => array(
						'route' => '/attribute',
						'defaults' => array(
							'controller' => 'Acl\Controller\Attribute',
							'action' => 'index',
							$aclDqlKey => null,
						),
					),
					'may_terminate' => true,
					'child_routes' => array(
						'import' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/import',
								'defaults' => array(
									'action' => 'import',
									$aclDqlKey => array(
										"a.name = 'siteadministrator' and a.value not in ('','0','false')",
									),
								),
							),
						),
					),
				),
				'user' => array(
					'type' => 'segment',
					'options' => array(
						'route' => '/user',
						'defaults' => array(
							'controller' => 'Acl\Controller\User',
							'action' => 'index',
							$aclDqlKey => null,
						),
					),
					'may_terminate' => true,
					'child_routes' => array(
						'deny' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/deny',
								'defaults' => array(
									'action' => 'deny',
									$aclDqlKey => null,
								),
							),
						),
							'login' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/login',
								'defaults' => array(
									'action' => 'login',
									$aclDqlKey => null,
								),
							),
						),
						'authenticate' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/authenticate',
								'defaults' => array(
									'action' => 'authenticate',
									$aclDqlKey => null,
								),
							),
						),
						'logout' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/logout',
								'defaults' => array(
									'action' => 'logout',
									$aclDqlKey => null,
								),
							),
						),
						'edit' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/edit[/:userid]',
								'defaults' => array(
									'action' => 'edit',
									$aclDqlKey => null,
								),
							),
						),
						'save' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/save/:userid',
								'defaults' => array(
									'action' => 'save',
									$aclDqlKey => null,
								),
							),
						),
						'import' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/import',
								'defaults' => array(
									'action' => 'import',
									$aclDqlKey => array(
										"a.name = 'siteadministrator' and a.value not in ('','0','false')",
									),
								),
							),
						),
						'list' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/list',
								'defaults' => array(
									'action' => 'list',
									$aclDqlKey => array(
										"a.name = 'siteadministrator' and a.value not in ('','0','false')",
									),
								),
							),
						),
					),
				),

			),
		),
	),
);
