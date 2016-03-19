<?php
namespace Acl\Model\Authorization;

class AttributePattern implements AttributePatternInterface
{
	/**
	 *
	 * @var string
	 */
	private $namePattern;

	/**
	 *
	 * @var string
	 */
	private $valuePattern;

	/**
	 * @return string
	 */
	public function getNamePattern()
	{
		return $this->namePattern;
	}

	/**
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setNamePattern($value)
	{
		$this->namePattern = (string)$value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getValuePattern()
	{
		return $this->valuePattern;
	}

	/**
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValuePattern($value)
	{
		$this->valuePattern = (string)$value;
		return $this;
	}
}