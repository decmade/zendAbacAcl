<?php
namespace Acl\Model;

trait FacadeTrait
{
	/**
	 * this function facilitates the facade over the entity
	 * by allowing you to call the entity's functions
	 * from the wrapper's interface
	 *
	 * @param string $methodName
	 * @param array $args
	 *
	 * @return unknown
	 */
	public function __call($methodName, $args)
	{
		if ( $subject == null ) {
			return null;
		}

		if (method_exists($this->getSubject(), $methodName) )
		{
			return call_user_func_array(array($this->getSubject(), $methodName), $args);
		} else {
			return null;
		}
	}

	/**
	 * return a reference to the subject class in this
	 * facade pattern
	 *
	 * this method should be overloaded to return a reference
	 * of the subject class for the facade trait to work
	 *
	 * @return mixed
	 */
	protected function getSubject()
	{
		return null;

	}
}