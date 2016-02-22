<?php
namespace Acl\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class User extends AbstractEntity
{
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	/**
	 * @ORM\Column(type="string", unique=true, length=30)
	 *
	 * @var string
	 */
	private $identity;

	/**
	 * @ORM\Column(type="string", length=60)
	 *
	 * @var string
	 */
	private $credential;

	/**
	 * @ORM\Column(type="integer")
	 *
	 * @var int
	 */
	private $status;

	/**
	 * @ORM\OneToMany(targetEntity="Attribute", mappedBy="user")
	 *
	 * @var ArrayCollection
	 */
	private $attributes;

	/**
	 * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
	 *
	 * @var ArrayCollection
	 */
	private $sessions;

	/**
	 * initialize new entity
	 */
	public function __construct()
	{
		parent::__construct();

		$this->attributes = new ArrayCollection();
		$this->sessions = new ArrayCollection();
		$this->status = self::STATUS_ACTIVE;
	}

	/**
	 * @return string
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setIdentity($value)
	{
		$this->identity = (string)$value;
		return $this;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return boolean
	 */
	public function checkCredential($value)
	{
		return password_verify($value, $this->credential);
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function setCredential($value)
	{
		$rawCredential = (string)$value;
		$hash = password_hash($rawCredential,PASSWORD_BCRYPT);
		$this->credential = $hash;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 *
	 * @param int $value
	 *
	 * @return $this
	 */
	public function setStatus($value)
	{
		$this->status = (int)$value;
		return $this;
	}

	/**
	 *
	 * @param Attribute $attribute
	 *
	 * @return $this
	 */
	public function addAttribute(Attribute $attribute)
	{
		$this->attributes[] = $attribute;
		$attribute->setUser($this);
		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getAttributes()
	{
		return $this->attributes->toArray();
	}

// 	/**
// 	 *
// 	 * @param Session $session
// 	 *
// 	 * @return $this
// 	 */
// 	public function addSession(Session $session)
// 	{
// 		$this->sessions[] = $session;
// 		$session->setUser($this);
// 		return $this;
// 	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getSessions()
	{
		return $this->sessions->toArray();
	}

}