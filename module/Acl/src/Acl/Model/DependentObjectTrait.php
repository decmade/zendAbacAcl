<?php
namespace Acl\Model;

use \Exception;

/**
 * this trait is used throughout by classes that have
 * dependencies. provides clear messages about missing
 * dependencies when then exceptions are thrown.
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
trait DependentObjectTrait
{

	/**
	 * returns an array of {name, object} pairs to check for
	 * as dependencies. the checkDependencies() function depends
	 * upon the output of this function
	 *
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array();
	}


	/**
	 * checks for all dependencies defined in the array returned
	 * by getDependenciesConfig() and returns true if they are
	 * all present in the current class
	 *
	 * @throws Exception
	 */
	protected function hasDependencies($getConfigMethodName = 'getDependenciesConfig')
	{
		$dependencies = $this->$getConfigMethodName();

		/*
		 * loop through the dependencies and check to see
		 * if each object is present
		 */
		foreach( $dependencies as $dependency) {
			$object = ( isset($dependency['object']) ) ? $dependency['object'] : false;
			$objectName = ( isset($dependency['name']) ) ? $dependency['name'] : '[undefined]';

			if (!$object || $object == null) {
				return false;
			}
		}
	}

	/**
	 * checks for all dependencies defined in the array returned
	 * by getDependenciesConfig() and throws an exception if
	 * a dependency is missing
	 *
	 * @throws Exception
	 */
	protected function checkDependencies($getConfigMethodName = 'getDependenciesConfig')
	{
		$dependencies = $this->$getConfigMethodName();

		/*
		 * loop through the dependencies and check to see
		 * if each object is present
		 */
		foreach( $dependencies as $dependency) {
			$object = ( isset($dependency['object']) ) ? $dependency['object'] : false;
			$objectName = ( isset($dependency['name']) ) ? $dependency['name'] : '[undefined]';

			if (!$object || $object == null) {
				/*
				 * get information about the original call to
				 * build the error message
				 */
				$className = get_called_class();
				$callingInfo = $this->getCallingInformation();
				$methodName = $callingInfo['function'];
				$lineNumber = $callingInfo['line'];
				$arguments = $callingInfo['args'];

				$message = sprintf("%s->%s(%s) @ line #%s: you must inject \"%s\" prior to making this call",
					$className,
					$methodName,
					$arguments,
					$lineNumber,
					$objectName
				);
				throw new Exception($message);
			}
		}
	}

	/**
	 * extract the information on the caller from the
	 * debug trace stack
	 *
	 * @return array
	 */
	protected function getCallingInformation()
	{
		$e = new Exception();
		/*
		 * select the call that is the third on the stack at this point
		 * which should be the original call to the extending class
		 *
		 *  [2] => original call from subclass
		 *  [1] => call to $this->checkDependencies() which calls this function
		 *  [0] => call to $this->getCallingInformation() OR call to this function
		 */
		$trace = $e->getTrace()[2];

		return array(
			'function' => $trace['function'],
			'line' => $trace['line'],
			'args' => $this->extractArgs($trace['args']),
			'file' => $trace['file'],
		);
	}

	/**
	 * extract a summarized version of the arguments
	 * passed to the stack trace object as a comma separated
	 * string
	 *
	 * @param array $traceArgs
	 * @return string
	 */
	protected function extractArgs(array $traceArgs)
	{
		$output = array();

		/*
		 * filter to apply to each argument element
		 */
		$callable = function($arg) use (&$output) {
			switch(true) {
				/*
				 * if argument is an array just use '[ARRAY]' as a placeholder
				 */
				case (is_array($arg)) :
					$output[] = '[ARRAY]';
					break;
				default :
					$output[] = (string)$arg;
					break;
			}
		};

		/*
		 * apply the filter to each arugment
		 */
		array_walk($traceArgs, $callable);

		/*
		 * return the summarized string
		 */
		return implode(',', $output);
	}
}