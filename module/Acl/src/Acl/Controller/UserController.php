<?php
namespace Acl\Controller;


use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Acl\Model\Authentication\DoctrineAuthenticationAdapter;
use Zend\Session\Container;

class UserController extends AbstractEntityController
{
	const USER_REPOSITORY_CLASS = 'Acl\Entity\User';

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

	/**
	 * present login form to unauthenticated user
	 */
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

	/**
	 * authenticate user, process login attempt
	 */
	public function authenticateAction()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();
		$this->checkDependencies('getLocalDependenciesConfig');

		$authService = $this->authenticationService;
		$authAdapter = $this->authenticationAdapter;
		$form = $this->loginForm;

		$form->setData($this->params()->fromPost());

		if ($form->isValid()) {
			$data = $form->getData();

			$authAdapter
				->setIdentity($data['identity'])
				->setCredential($data['credential']);

			$result = $authService->authenticate($authAdapter);

			if ($result->getCode() == $result::SUCCESS) {

				/*
				 * add failure messages to flash messenger plugin
				 */
				foreach($result->getMessages() as $message) {
					$this->queueFlashMessage($message, 'success', $result->getIdentity());
				}

				/*
				 * redirect to initial destination, which defaults to the home
				 * route if there was no attempt to access protected content
				 */
				$destination = $this->getSavedDestinationInformation();
				$this->redirect()->toRoute($destination['route'], $destination['parameters']);
			} else {

				/*
				 * add failure messages to flash messenger plugin
				 */
				foreach($result->getMessages() as $message) {
					$this->queueFlashMessage($message, 'error');
				}

				$this->redirect()->toRoute('acl/user/login');
			}

		} else {
			/*
			 * add all error messages to the flashMessenger plugin
			 */
			foreach($form->getMessages() as $inputName => $messages) {
				foreach($messages as $message) {
					$this->flashMessenger()->addErrorMessage($message);
				}
			}

			$this->redirect()->toRoute('acl/user/login');
		}
	}

	/**
	 * remove any record of an authenticated use
	 *
	 * @return \Zend\Http\Response
	 */
	public function logoutAction()
	{
		/*
		 * run dependency checks
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		$authService = $this->authenticationService;

		/*
		 * queue up a flash message for user feedback
		 */
		$this->queueFlashMessage("User %s Has Logged Out", 'info', $authService->getIdentity());

		$authService->clearIdentity();

		return $this->redirect()->toRoute('home');
	}

	/**
	 * present form that is user profile
	 */
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

	/**
	 * $messageType can be one of:
	 * 		'success'
	 * 		'info'
	 * 		'warning',
	 *		'error'
	 *
	 * @param int $userId
	 * @param string $messageTemplate // using "%s" parameter to include username
	 */
	private function queueFlashMessage($messageTemplate, $messageType = 'info', $userId = 0)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$em = $this->entityManager;

		/*
		 * add success message to flash messenger plugin
		 */
		$user = $em->getRepository(self::USER_REPOSITORY_CLASS)->find($userId);
		$username = ($user) ? $user->getIdentity() : 'guest';
		$message = sprintf($messageTemplate, $username);

		$this->flashMessenger()->addMessage($message, $messageType);

	}
}