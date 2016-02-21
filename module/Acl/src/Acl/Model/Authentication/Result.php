<?php
namespace Acl\Model\Authentication;

use Zend\Authentication\Result as ZendResult;


/**
 * extending the Zend\Authentication\Result object because
 * the object included with the framework only allows you
 * to set properties during instantiation
 *
 * this project is going to use the prototyp method
 * so that we can take advantage of the dependency
 * injection techniques
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class Result extends ZendResult
{
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
		$this->code = (int)$value;
		return $this;
	}

	/**
	 *
	 * @param unknown $value
	 */
	public function setIdentity($value)
	{
		$this->identity = $value;
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
		$this->messages[] = (string)$value;
		return $this;
	}
}