<?php
namespace Acl\Model\View;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Element;

class ConvertRouteSelectListToUrl extends AbstractHelper
{
	public function __invoke(Element $routeSelectInput)
	{
		$uploadTypeRoutesTable = $routeSelectInput->getValueOptions();
		$options = array();

		/*
		 * use the url() view helper to convert each route
		 * to an actual url on this setup
		 */
		foreach ($uploadTypeRoutesTable as $route => $type ) {
			$url = $this->getView()->url($route);
			$options[$url] = $type;
		}

		/*
		 * set the option list to tne modified version
		 */
		$routeSelectInput->setValueOptions($options);
	}
}