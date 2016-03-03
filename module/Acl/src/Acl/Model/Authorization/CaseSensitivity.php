<?php
namespace Acl\Model\Authorization;

class CaseSensitivity
{
	const CASE_SENSITIVE = 1;
	const CASE_INSENSITIVE = 0;

	private $state;

	/**
	 * @return int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 *
	 * @param int $value
	 * @return $this
	 */
	public function setState($value)
	{
		$this->state = (int)$value;
		return $this;
	}
}