<?php
namespace Acl\Model\Import;

use Zend\Validator\AbstractValidator;

/**
 * custom validator that keeps a cache of all the values it checks to
 * make certain that each value is unique
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class UniqueValue extends AbstractValidator
{
	const NOT_UNIQUE = "not-unique";

	/**
	 *
	 * @var array
	 */
	private $cache;

	/**
	 * overload base constructor to initialize arrays
	 *
	 * @param array $options
	 */
	public function __construct()
	{
		$options = array(
// 			'isTranslaterEnabled' => false,
			'messageTemplates' => array(
				self::NOT_UNIQUE => "the value '%value%' is duplicated in this input",
			),
		);

		parent::__construct($options);

		$this->clearCache();
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \Zend\Validator\ValidatorInterface::isValid()
	 */
	public function isValid($value)
	{
		$cache = $this->cache;
		$this->setValue(strtolower($value));

		if (in_array($value, $this->cache)) {
			$this->error(self::NOT_UNIQUE);
			return false;
		} else {
			$this->cache[] = $value;
			return true;
		}
	}

	/**
	 * provides an API to clear the cache so one instance
	 * can be used for subsequent domains of values
	 *
	 * @return self
	 */
	public function clearCache()
	{
		unset($this->cache);
		$this->cache = array();
		return $this;
	}

}