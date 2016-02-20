<?php
namespace Acl\Entity;

use \Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * @ORM\MappedSuperclass
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
abstract class AbstractEntity
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 *
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @var DateTime
	 */
	private $added;

	/**
	 * @ORM\Column(type="datetime", nullable = true)
	 *
	 * @var DateTime
	 */
	private $removed;

	/**
	 * initialize new entity
	 */
	public function __construct()
	{
		$this->added = new DateTime();
		$this->removed = null;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return DateTime
	 */
	public function getAdded()
	{
		return $this->added;
	}

	/**
	 * @return DateTime
	 */
	public function getRemoved()
	{
		return $this->removed;
	}

	/**
	 * this will serve as a delete so that the data can audit itself
	 *
	 * with a thoughtful enough query you can string the history of an
	 * entity together without having to write separate code for event
	 * trapping and logging. one can just traverse all the records in
	 * the table and sort upon added/removed date pairs to generate an
	 * audit log
	 *
	 * normal queries of current entities should always exclude those with
	 * removed dates
	 *
	 * @param DateTime $value
	 *
	 * @return $this
	 */
	public function setRemoved(DateTime $value)
	{
		$this->removed = $value;
		return $this;
	}
}