<?php
namespace Acl\Entity;

use \DateTime;

interface EntityInterface
{
	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @return DateTime
	 */
	public function getAdded();

	/**
	 * @return DateTime
	 */
	public function getRemoved();

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
	public function setRemoved();

	/**
	 * clear the removed date
	 */
	public function clearRemoved();

	/**
	 * return the name of the class for
	 * use with an EntityManager when making queries
	 */
	static public function getEntityClass();
}