<?php
namespace Acl\Controller;


use Zend\Form\Form;
use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Acl\Model\Authentication\DoctrineAuthenticationAdapter;
use Zend\Session\Container;

class UserController extends AbstractEntityController
{
	const USER_REPOSITORY_CLASS = 'Acl\Entity\User';

	/**
	 *
	 * @var Form
	 */
	protected $loginForm;

	/**
	 *
	 * @var Form
	 */
	protected $profileForm;

	/**
	 *
	 * @var AuthenticationService
	 */
	protected $authenticationService;

	/**
	 *
	 * @var DoctrineAuthenticationAdapter
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
	 * @param Form $form
	 *
	 * @return $this
	 */
	public function setLoginForm(Form $form)
	{
		$this->loginForm = $form;
		return $this;
	}

	/**
	 *
	 * @param Form $form
	 *
	 * @return $this
	 */
	public function setProfileForm(Form $form)
	{
		$this->profileForm = $form;
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
		return $this->redirect()->toRoute('acl/user/edit');
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
	 * authenticate user/ process login form
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
				$this->queueAuthenticationResultMessages($result, 'success');

				/*
				 * redirect to initial destination, which defaults to the home
				 * route if there is nothing saved
				 */
				$destination = $this->getSavedDestinationInformation();
				$this->redirect()->toRoute($destination['route'], $destination['parameters']);
			} else {
				$this->queueAuthenticationResultMessages($result);

				$this->redirect()->toRoute('acl/user/login');
			}

		} else {
			$this->queueFormValidationMessages($form);

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
		$user = $this->getCurrentUser();
		$message = sprintf("User %s Has Logged Out", $user->getIdentity());
		$this->queueMessage($message, 'info');

		$authService->clearIdentity();

		return $this->redirect()->toRoute('home');
	}

	/**
	 * present form that is user profile
	 */
	public function editAction()
	{
		/*
		 * check dependencies
		 */
		$this->checkDependencies('getLocalDependenciesConfig');

		return array(
			'form' => $this->profileForm,
		);
	}

	public function saveAction()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();
		$this->checkDependencies('getLocalDependenciesConfig');

		$em = $this->entityManager;
		$form = $this->profileForm;
		$user = $this->getCurrentUser();

		$form->setData($this->params()->fromPost());

		if ($form->isValid()) {
			$data = $form->getData();


			if ($data['newCredential-1'] == $data['newCredential-2']) {
				$user->setCredential($data['newCredential-1']);
				$em->flush();
				$this->queueMessage(sprintf("User %s's Password Has Been Updated", $user->getIdentity()), 'success');
			} else {
				$this->queueMessage(sprintf("New Passwords and Confirm Password Fields Do Not Match"),'error');
				$this->queueMessage(sprintf("User %s's Password Was Not Updated.", $user->getIdentity()), 'info');
			}
		} else {
			$this->queueFormValidationMessages($form);
			$this->queueMessage(sprintf("User %s's Password Was Not Updated.", $user->getIdentity()), 'info');
		}

		$this->redirect()->toRoute('acl/user/edit');
	}

	/**
	 * @return array
	 */
	protected function getLocalDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Zend\Form\Form (Login Form)',
				'object' => $this->loginForm,
			),
			array(
				'name' => 'Zend\Form\Form (Profile Form)',
				'object' => $this->profileForm,
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
	 * get the currently authenticated user
	 *
	 * @return User|null
	 */
	private function getCurrentUser()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$authService = $this->authenticationService;
		$em = $this->entityManager;

		if ($authService->hasIdentity()) {
			$id = $authService->getIdentity();
			$user = $em->getRepository(self::USER_REPOSITORY_CLASS)->find($id);
			return $user;
		} else {
			return null;
		}
	}

	/**
	 *
	 * @param Form $form
	 * @param string $type
	 */
	private function queueFormValidationMessages(Form $form, $type = 'error')
	{
	/*
		/*
		 * add authentication service messages to flash messenger plugin
		 */
		foreach($form->getMessages() as $message) {
			$this->queueMessage($message, $type);
		}
	}

	/**
	 *
	 * @param Result $result
	 * @param string $type
	 */
	private function queueAuthenticationResultMessages(Result $result, $type = 'error')
	{
	/*
		/*
		 * add authentication service messages to flash messenger plugin
		 */
		foreach($result->getMessages() as $message) {
			$this->queueMessage($message, $type);
		}
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
	private function queueMessage($message, $messageType = 'info')
	{
		$this->flashMessenger()->addMessage($message, $messageType);

	}
}