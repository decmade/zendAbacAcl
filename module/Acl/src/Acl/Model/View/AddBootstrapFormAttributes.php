<?php
namespace Acl\Model\View;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Form;

class AddBootstrapFormAttributes extends AbstractHelper
{
	const DEFAULT_FORM_CLASS = 'form-horizontal';
	const DEFAULT_LABEL_CLASS = 'control-label col-md-2';
	const DEFAULT_INPUT_CLASS = 'form-control';
	const DEFAULT_SUBMIT_BUTTON_CLASS = 'btn btn-success';

	public function __invoke(Form $form)
	{

		$form->setAttribute('class', self::DEFAULT_FORM_CLASS);

		foreach($form->getElements() as $element) {
			/*
			 * controls how far the form indents into
			 * the page using Twitter:Bootstrap CSS
			 *
			 */
			$defLabelAttributes = array(
				'class' => self::DEFAULT_LABEL_CLASS,
			);

			$element->setLabelAttributes( $defLabelAttributes);
			$element->setAttribute('class', self::DEFAULT_INPUT_CLASS);

			/*
			 * set the id attribute of all inputs to be equal to their names
			 *
			 * makes life simple when trying to make the view
			 * dynamic
			 */
			$element->setAttribute('id', $element->getName());
		}

		/*
		 * the submit button is a little different, it uses
		 * a button class to proper rendering
		 *
		 */
		$form->get('submit')->setAttribute('class', self::DEFAULT_SUBMIT_BUTTON_CLASS);

		return $form;
	}

}