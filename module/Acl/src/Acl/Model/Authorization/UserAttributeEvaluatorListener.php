<?php
namespace Acl\Model\Authorization;

use Acl\Model\DependentObjectTrait;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;


class UserAttributeEvaluatorListener implements ListenerAggregateInterface
{
	use ListenerAggregateTrait;
	use DependentObjectTrait;

	const ACCESS_DQL_PARAM_NAME = 'accessDqlConfig';
	const UNAUTHENTICATED_CONTROLLER = 'Acl\Controller\Index';
	const UNAUTHENTICATED_ACTION = 'login';
	const UNAUTHORIZED_CONTROLLER = 'Application\Controller\Index';
	const UNAUTHORIZED_ACTION = 'index';
	const DESTINATION_ROUTE_PARAM_NAME = 'destinationRoute';

	/**
	 *
	 * @var UserAttributeEvaluator $evaluator
	 */
	private $userAttributeEvaluator;

	/**
	 *
	 * @var Zend\Authentication\AuthenticationService $authenticationService
	 */
	private $authenticationService;


	/**
	 *
	 * @param UserAttributeEvaluator $evaluator
	 *
	 * @return $this
	 */
	public function setUserAttributeEvaluator(UserAttributeEvaluator $evaluator)
	{
		$this->userAttributeEvaluator = $evaluator;
		return $this;
	}

	/**
	 *
	 * @param AuthenticationService $authService
	 *
	 * @return $this
	 */
	public function setAuthenticationService(AuthenticationService $authService)
	{
		$this->authenticationService = $authService;
		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Zend\EventManager\ListenerAggregateInterface::attach()
	 */
	public function attach(EventManagerInterface $em)
	{
		/*
		 * attach the onDipatch() method to the MvcEvent::EVENT_DISPATCH event
		 */
		$this->listeners[]  = $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 100);
	}

	public function onDispatch(MvcEvent $event)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$evaluator = $this->userAttributeEvaluator;
		$routeMatch = $event->getRouteMatch();

		/*
		 * only act if there is an access configuration
		 * set for the route
		 */
		if ($this->hasAccessDqlConfig($routeMatch)) {
			$accessDqlConfig = $this->getAccessDqlConfig($routeMatch);

			if ($this->hasAuthenticatedUser()) {

				$userId = $this->getAuthenticatedUserId();
				$accessAllowed = $this->evaluateUserAccessWithConfig($userId, $accessDqlConfig);

				/*
				 * if the user should not have access, send them to the
				 * unauthorized route
				 */
				if (!$accessAllowed) {
					$event->setRouteMatch($this->reconfigureRouteMatchAsUnauthorizedRoute($routeMatch));
				}

			} else {
				$event->setRouteMatch($this->reconfigureRouteMatchAsUnathenticatedRoute($routeMatch));
			}
		}


	}

	/**
	 * @see Acl\Model\DependentObjectTrait
	 *
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Acl\Model\Authorization\UserAttributeEvaluator',
				'object' => $this->userAttributeEvaluator,
			),
			array(
				'name' => 'Zend\Authentication\AuthenticationService',
				'object' => $this->authenticationService,
			),
		);
	}

	/**
	 * true if the routeMath passed has the ACCESS_DQL_PARAM_NAME key and
	 * the value of the key is not null
	 * as on of its parametiers
	 *
	 * @param RouteMatch $routeMatch
	 *
	 * @return boolean
	 */
	private function hasAccessDqlConfig(RouteMatch $routeMatch)
	{
		if (array_key_exists(self::ACCESS_DQL_PARAM_NAME, $routeMatch->getParams()) ) {
			if ( $routeMatch->getParam(self::ACCESS_DQL_PARAM_NAME) == null) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * retrieve the ACL DQL configuration set in the
	 * parameters of the route match passed
	 *
	 * @param RouteMatch $routeMatch
	 */
	private function getAccessDqlConfig(RouteMatch $routeMatch)
	{
		/*
		 * if there is no e ACCESS_DQL_PARAM_NAME key in
		 * the route's parameters, return an empty array
		 */
		if (!$this->hasAccessDqlConfig($routeMatch)) {
			return array();
		}

		return $routeMatch->getParam(self::ACCESS_DQL_PARAM_NAME);
	}

	/**
	 * true if there is an authenticated user
	 * false otherwise
	 *
	 * @return boolean
	 */
	private function hasAuthenticatedUser()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$authService = $this->authenticationService;

		return $authService->hasIdentity();
	}

	/*
	 * returnt the surrogate key/ID of the currently authenticated
	 * user, 0 if none
	 */
	private function getAuthenticatedUserId()
	{
		/*
		 * if there is no user authenticated,
		 * return 0,
		 */
		if (!$this->hasAuthenticatedUser()) {
			return 0;
		}

		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$authService = $this->authenticationService;

		return $authService->getIdentity();
	}

	/**
	 * return a copy of the passed route match that points
	 * to the unauthorized route instead of the
	 * original destination
	 *
	 * @param RouteMatch
	 *
	 * @return RouteMatch
	 */
	private function reconfigureRouteMatchAsUnathenticatedRoute(RouteMatch $routeMatch)
	{
		$newMatch = clone $routeMatch;

		$newMatch
			->setParam('controller', self::UNAUTHENTICATED_CONTROLLER)
			->setParam('action', self::UNAUTHENTICATED_ACTION)
			->setParam(self::ACCESS_DQL_PARAM_NAME, null)
			->setParam(self::DESTINATION_ROUTE_PARAM_NAME, $routeMatch->getMatchedRouteName());

		return $newMatch;
	}

	/** return a copy of the passed route match that points
	* to the unauthorized route instead of the
	* original destination
	*
	* @param RouteMatch
	*
	* @return RouteMatch
	*/
	private function reconfigureRouteMatchAsUnauthorizedRoute(RouteMatch $routeMatch)
	{
		$newMatch = clone $routeMatch;

		$newMatch
			->setParam('controller',self::UNAUTHORIZED_CONTROLLER)
			->setParam('action', self::UNAUTHORIZED_ACTION)
			->setParam(self::ACCESS_DQL_PARAM_NAME, null);

		return $newMatch;
	}

	/**
	 *
	 * @param int $userId
	 * @param array|string $accessDqlConfig
	 *
	 * @return boolean
	 */
	private function evaluateUserAccessWithConfig($userId, $accessDqlConfig)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$evaluator = $this->userAttributeEvaluator;

		return $evaluator->evaluate($userId, $accessDqlConfig);
	}
}