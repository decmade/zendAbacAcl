<?php
namespace Acl\Model\Authentication;

use Zend\Authentication\Result as ZendResult;
use Acl\Model\StandardInputFiltersTrait;


/**
 * extending the Zend\Authentication\Result object because
 * the object included with the framework only allows you
 * to set properties during instantiation
 *
 * this project is going to use the prototype method
 * so that we can take advantage of the dependency
 * injection techniques
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class Result extends ZendResult
{
	use StandardInputFiltersTrait;

	/**
	 * initialize without parameters
	 */
	public function __construct()
	{
		$this->messages = array();
	}

	/**
	 *
	 * @param int $value
	 *
	 * @return $this
	 */
	public function setCode($value)
	{
		$this->code = $this->filterIntegerInput($value);
		return $this;
	}

	/**
	 * set the surrogate key that uniquely identifies
	 * the user in the database
	 *
	 * @param int $value
	 */
	public function setIdentity($value)
	{
		$this->identity = $this->filterIntegerInput($value);
		return $this;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function addMessage($value)
	{
		$this->messages[] = $this->filterStringInput($value);
		return $this;
	}
}