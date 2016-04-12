<?php
namespace Acl\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="acl_attribute")
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
		/*
		 * apply the standard string input filter to $value
		 */
		$this->name = $this->filterStringInput($value);
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
		/*
		 * apply the standard string input filter to $value
		 */
		$this->value = $this->filterStringInput($value);
		return $this;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 *
	 * @param User $user
	 *
	 * @return $this
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
		return $this;
	}

}