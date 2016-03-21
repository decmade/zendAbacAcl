<?php
namespace Acl\Model;

trait StandardInputFilters
{
	/**
	 * takes in a value and filters it to a string
	 * if at all possible
	 * otherwise returns the string '<invalid>'
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function filterStringInput($value) {
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
}