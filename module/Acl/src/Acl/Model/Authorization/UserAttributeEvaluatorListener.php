<?php
namespace Acl\Model\Authorization;

use Acl\Model\DependentObjectTrait;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container;


class UserAttributeEvaluatorListener implements ListenerAggregateInterface
{
	use ListenerAggregateTrait;
	use DependentObjectTrait;

	const ACCESS_DQL_PARAM_NAME = 'accessDqlConfig';
	const ROUTE_FORWARDING_SESSION_KEY = 'destination';
	const UNAUTHENTICATED_CONTROLLER = 'Acl\Controller\User';
	const UNAUTHENTICATED_ACTION = 'login';
	const UNAUTHORIZED_CONTROLLER = 'Acl\Controller\User';
	const UNAUTHORIZED_ACTION = 'deny';

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
	 * used to persist a destination while authentication is pending
	 * once authentication is successfully, the user will be forwarded to
	 * the route that triggered the authentication requirement
	 *
	 * @var Container
	 */
	private $routeForwardingContainer;


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
	 * @param Container $container
	 *
	 * @return $this
	 */
	public function setRouteForwardingContainer(Container $container)
	{
		$this->routeForwardingContainer = $container;
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

	/**
	 * actions for the UserAttributeEvalutor to perform
	 * whenever a route is dispatched
	 *
	 * @param MvcEvent $event
	 */
	public function onDispatch(MvcEvent $event)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$evaluator = $this->userAttributeEvaluator;
		$routeMatch = $event->getRouteMatch();

		/*
		 * remove any saved destination data if user
		 * jumps off the authentication path
		 */
		$this->forgetSavedDestinationWhenNotAuthenticating($routeMatch);

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
			array(
				'name' => 'Zend\Session\Container',
				'object' => $this->routeForwardingContainer,
			),
		);
	}

	/**
	 * true if the routeMatch passed has the ACCESS_DQL_PARAM_NAME key
	 * as a parameter and the value of the key is an array with elements
	 * or a string that is not empty
	 *
	 * @param RouteMatch $routeMatch
	 *
	 * @return boolean
	 */
	private function hasAccessDqlConfig(RouteMatch $routeMatch)
	{
		if (array_key_exists(self::ACCESS_DQL_PARAM_NAME, $routeMatch->getParams()) ) {
			$paramValue = $routeMatch->getParam(self::ACCESS_DQL_PARAM_NAME);

			switch(true) {
				case ( is_array($paramValue)) :
					if (count($paramValue) == 0 ) {
						return false;
					} else {
						return true;
					}
				case ( is_string($paramValue)) :
					if (empty($paramValue)) {
						return false;
					} else {
						return true;
					}
				default :
					return false;
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
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$routeForwarder = $this->routeForwardingContainer;
		$destinationKey = self::ROUTE_FORWARDING_SESSION_KEY;
		$routeForwarder->$destinationKey = $routeMatch;

		$newMatch = clone $routeMatch;
		$newMatch
			->setParam('controller', self::UNAUTHENTICATED_CONTROLLER)
			->setParam('action', self::UNAUTHENTICATED_ACTION)
			->setParam(self::ACCESS_DQL_PARAM_NAME, null)
		;

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

	/**
	 * this is a weird one
	 *
	 * i noticed a use case during testing where if an unauthenticated user attempts
	 * to navigate to a protected route and the destination is saved,
	 * they could click on anything else an avoid the authentication process
	 * and since the saved destination information is never consumed, it persists
	 * and if that user does authenitcate later, it will send them to that
	 * initial route whether they had requested it or not
	 *
	 * this removes that saved information as soon as the user selects a route that does
	 * not have the same controller that is used to
	 * @param RouteMatch $routeMatch
	 */
	private function forgetSavedDestinationWhenNotAuthenticating(RouteMatch $routeMatch)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$routeForwardingContainer = $this->routeForwardingContainer;
		$controller = $routeMatch->getParam('controller');

		if ($controller != self::UNAUTHENTICATED_CONTROLLER) {
			$routeForwardingContainer->offsetUnset(self::ROUTE_FORWARDING_SESSION_KEY);
		}
	}
}