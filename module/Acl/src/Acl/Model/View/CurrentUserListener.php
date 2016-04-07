<?php
namespace Acl\Model\View;

use Acl\Model\DependentObjectTrait;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Code\Scanner\DirectoryScanner;


class CurrentUserListener implements ListenerAggregateInterface
{
	use ListenerAggregateTrait;
	use DependentObjectTrait;

	/**
	 *
	 * @var ViewModel
	 */
	private $viewModel;

	/**
	 *
	 * @param ViewModel $view
	 *
	 * @return $this
	 */
	public function setViewModel(ViewModel $view)
	{
		$this->viewModel = $view;
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
		$this->listeners[]  = $em->attach(MvcEvent::EVENT_RENDER, array($this, 'onRender'), -1000);
	}

	/**
	 * actions for the UserAttributeEvalutor to perform
	 * whenever a route is dispatched
	 *
	 * @param MvcEvent $event
	 */
	public function onRender(MvcEvent $event)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$currentLayout = $event->getViewModel();
		$templateName = $this->getTemplateName();

		$view = $this->getViewModel();
		$view
			->setTemplate($templateName)
			->setCaptureTo('userBadge');

		$currentLayout->addChild($view);
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
				'name' => 'Zend\View\Model\ViewModel',
				'object' => $this->viewModel,
			),
		);
	}

	/**
	 * we always need a new instance of the view model
	 * just in cast the one in the servive is referenced somewhere else
	 * as well
	 *
	 * i don't like instantiating classes outside of factories
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getViewModel()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		return clone $this->viewModel;
	}

	/**
	 * format the template name according
	 * to the directory separator method
	 * used on the current platform
	 *
	 * @return string
	 */
	private function getTemplateName()
	{
		$templateParts = array(
			'acl',
			'user',
			'badge',
		);

		return implode(DIRECTORY_SEPARATOR, $templateParts);
	}


}