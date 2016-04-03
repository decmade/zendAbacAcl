<?php
namespace Acl\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\Entity
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class Session extends AbstractEntity
{
	/**
	 * @ORM\Column(type="string", length=15, nullable=true)
	 *
	 * @var string
	 */
	private $ipAddress;

	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @var DateTime
	 */
	private $expires;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="sessions")
	 * @ORM\JoinColumn(name="userid", referencedColumnName="id")
	 *
	 * @var User
	 */
	private $user;

	/**
	 * @return string
	 */
	public function getIpAddress()
	{
		return $this->ipAddress;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setIpAddress($value)
	{
		/*
		 * apply the standard string input filter to $value
		 */
		$this->ipAddress = $this->filterStringInput($value);
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getExpires()
	{
		return $this->expires;
	}

	/**
	 *
	 * @param DateTime $value
	 *
	 * @return $this
	 */
	public function setExpires(DateTime $value)
	{
		$this->expires = $value;
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