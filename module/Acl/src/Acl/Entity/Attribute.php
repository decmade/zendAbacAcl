<?php
namespace Acl\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class Attribute extends AbstractEntity
{
	/**
	 * @ORM\Column(type="string", length=30)
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=100)
	 *
	 * @var string
	 */
	private $value;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="attributes")
	 * @ORM\JoinColumn(name="userid", referencedColumnName="id")
	 *
	 * @var User
	 */
	private $user;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setName($value)
	{
		$this->name = (string)$value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = (string)$value;
		return $this;
	}


}