<?php
namespace Acl\Model;

trait StandardInputFiltersTrait
{
	/**
	 * takes in a value and filters it to a string
	 * if at all possible
	 * otherwise returns the string '<invalid>'
	 *
	 * @param mixed $value
	 * @return string
	 */
	protected function filterStringInput($value) {
		switch(true) {
			case (is_string($value)) :
				return $value;
				break;
			case (is_bool($value)) :
			case (is_numeric($value)) :
			case (is_null($value)) :
				return (string)$value;
				break;
			default :
				return '<invalid>';
		}
	}

	/**
	 * takes in a value and filters it to an integer
	 * if at all possible
	 * otherwise returns 0
	 *
	 * @param mixed $value
	 * @return int
	 */
	protected function filterIntegerInput($value) {
		switch(true) {
			case (is_integer($value)) :
				return $value;
				break;
			case (is_bool($value)) :
			case (is_string($value)) :
			case (is_null($value)) :
			case (is_numeric($value)) :
				return (int)$value;
				break;
			default :
				return 0;
		}
	}
}