<?php
namespace Acl\Model\Authorization;

/**
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
interface AttributePatternInterface
{
	/**
	 * @return string
	 */
	public function getNamePattern();

	/**
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setNamePattern($value);

	/**
	 * @return string
	 */
	public function getValuePattern();

	/**
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValuePattern($value);
}