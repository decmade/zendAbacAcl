<?php
namespace Acl\Controller;


use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Acl\Model\Authentication\DoctrineAuthenticationAdapter;
use Zend\Session\Container;

class UserController extends AbstractEntityController
{
	/**
	 *
	 * @var Form $loginForm
	 */
	protected $loginForm;

	/**
	 *
	 * @var AuthenticationService $authenticationService
	 */
	protected $authenticationService;

	/**
	 *
	 * @var DoctrineAuthenticationAdapter $authenticationAdapter
	 */
	protected $authenticationAdapter;

	/**
	 * used to persist a destination while authentication is pending
	 * once authentication is successfully, the user will be forwarded to
	 * the route that triggered the authentication requirement
	 *
	 * @var Container
	 */
	protected $routeForwardingContainer;

	/**
	 *
	 * @param Form $loginForm
	 *
	 * @return $this
	 */
	public function setLoginForm(Form $loginForm)
	{
		$this->loginForm = $loginForm;
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
	 * @param DoctrineAuthenticationAdapter $adapter
	 *
	 * @return $this
	 */
	public function setAuthenticationAdapter(DoctrineAuthenticationAdapter $adapter)
	{
		$this->authenticationAdapter = $adapter;
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

	public function indexAction()
	{
		return array();
	}

	public function loginAction()
	{
		/*
		 * run dependency checks
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		$form = $this->loginForm;
		$savedRoute = $this->routeForwardingContainer;

		return array(
			'form' => $this->loginForm,
		);
	}

	public function authenticateAction()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();
		$this->checkDependencies('getLocalDependenciesConfig');

		$authService = $this->authenticationService;
		$authAdapter = $this->authenticationAdapter;

		$identity = $this->params()->fromPost('identity');
		$credential = $this->params()->fromPost('credential');

		/*
		 * TODO: need loginForm validation here
		 */

		$authAdapter
			->setIdentity($identity)
			->setCredential($credential);

		$result = $authService->authenticate($authAdapter);

		if ($result->getCode() == $result::SUCCESS) {
			$destination = $this->getSavedDestinationInformation();
			$this->redirect()->toRoute($destination['route'], $destination['parameters']);
		} else {
			$this->redirect()->toRoute('acl/user/login');
		}

	}

	public function logoutAction()
	{
		/*
		 * run dependency checks
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		$authService = $this->authenticationService;

		$authService->clearIdentity();

		return $this->redirect()->toRoute('home');
	}

	public function editAction()
	{
		return array();
	}

	public function saveAction()
	{
		return array();
	}

	/**
	 * @return array
	 */
	protected function getLocalDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Zend\Form\Form',
				'object' => $this->loginForm,
			),
			array(
				'name' => 'Zend\Authentication\AuthenticationService',
				'object' => $this->authenticationService,
			),
			array(
				'name' => 'Acl\Model\Authentication\DoctrineAuthenticationAdapter',
				'object' => $this->authenticationAdapter,
			),
			array(
				'name' => 'Zend\Session\Container',
				'object' => $this->routeForwardingContainer,
			),
		);
	}

	/**
	 * retrieves the stored routeMatch information that was saved by the
	 * authorization listener when a route is requested and no
	 * user is authenticated
	 *
	 * clears the saved data once it is consumed to prevent subsequent
	 * authentication attempts from being routed to this same destination
	 *
	 * @return array
	 */
	private function getSavedDestinationInformation()
	{
		/*
		 * run local dependency check
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		$routeForwardingContainer = $this->routeForwardingContainer;

		if (isset($routeForwardingContainer->destination) ) {
			$savedRouteMatch = $routeForwardingContainer->destination;

			$destination = array(
				'route' => $savedRouteMatch->getMatchedRouteName(),
				'parameters' => $savedRouteMatch->getParams(),
			);

			/*
			 * remove the object from session storage
			 * now that it has been consumed
			 */
			$routeForwardingContainer->offsetUnset('destination');

		} else {
			/*
			 * default to the home page if no prior destination
			 * is stored in the session
			 *
			 * this means the user deliberately invoked the login
			 * rather than being asked after requesting protected
			 * content
			 */
			$destination = array(
				'route' => 'home',
				'parameters' => array(),
			);
		}

		return $destination;
	}
}