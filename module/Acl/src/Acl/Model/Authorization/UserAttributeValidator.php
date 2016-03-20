<?php
namespace Acl\Model\Authorization;

use Acl\Model\DependentObjectTrait;
use Doctrine\ORM\EntityManager;

/**
 * This class validates that the attributes of the user passed to
 * the validate(user, string) function match the dql expression
 * in the second parameter. Ideally the second parameter would look for
 * certain attributes or combinations of attributes. It could potentially
 * consider attributes of the user object as well.
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class UserAttributeValidator
{
	use DependentObjectTrait;

	/**
	 *
	 * @var EnitityManager
	 */
	private $entityManager;

	/**
	 *
	 * @var array
	 */
	private $cachedAttributes;

	/**
	 * initialize instance
	 */
	public function __construct()
	{
		$this->initializeCachedAttributes();
	}

	/**
	 *
	 * @param EntityManagerManager $em
	 * @return $this
	 */
	public function setEntityManager(EntityManager $em)
	{
		$this->entityManager = $em;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getCachedAttributes()
	{
		return $this->cachedAttributes;
	}

	/**
	 * sets the $cachedAttributes property
	 * to an empty array
	 *
	 * @return $this
	 */
	public function initializeCachedAttributes()
	{
		$this->cachedAttributes = array();
		return $this;
	}

	/**
	 * test to see if user associated with the ID passed has the attributes
	 * specified by the dql where clause for attributes
	 * passed. caches any found attributes in the $cachedAttributes
	 * property just in case the actual attributes are needed later
	 * for any additional logic
	 *
	 * @param string $userId
	 * @param string|array $attributesWhereClause
	 *
	 * @return boolean
	 */
	public function validate($userId, $attributesWhereClause)
	{

		/*
		 * reset the cached attributes
		 */
		$this->initializeCachedAttributes();

		return $this->testClauseGroup($userId, $attributesWhereClause);
	}

	/**
	 * get the dependency configuration array
	 *
	 * @return array
	 */
	protected function getDependenciesConfig()
	{
		return array(
			array(
				'name' => 'Doctrine\ORM\EntityManager',
				'object' => $this->entityManager,
			),
		);
	}

	private function testClauseGroup($userId, $clauseGroup)
	{
		$falseCount = 0;
		$trueCount = 0;
		$isOrCondition = false;
		/*
		 * if there is an array of  clauses,
		 * check each one to make sure they are all true
		 * as if to say the user has Attribute1 AND Attribute2 ... AND Attribute[N]
		 */
		if ( is_array($clauseGroup) ) {

			/*
			 * test each clause
			 * if one is false, return false
			 */
			foreach($clauseGroup as $clause) {
				if (is_array($clause)) {
					$isOrCondition = true;
					$result = $this->testClauseGroup($userId, $clause);

				} else {
					$result = $this->testClause($userId, $clause);
				}

				/*
				 * increment the true OR false counter depending on
				 * the result of this clause
				 */
				if ($result == true) {
					$trueCount++;
				} else {
					$falseCount++;
				}

			}

			/*
			 * if an array was hit in this group then these are
			 * evaluated as if there are OR conditions, any true
			 * value ends with granted access
			 */
			if ($isOrCondition) {
				return ($trueCount > 0) ? true : false;
			} else {
				return ($falseCount > 0) ? false : true;
			}
		} else {
			/*
			 * if this is not really a group of clauses, then pass
			 * to the single clause method handler
			 */
			return $this->testClause($userId, $clauseGroup);
		}
	}

	/**
	 * get the results of a single attribute where clause
	 *
	 * @param integer $userId
	 * @param string $clause
	 */
	private function testClause($userId, $clause)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$entityManager = $this->entityManager;

		/*
		 * build the dql expression to pass to the entity manager
		 */
		$dql = sprintf("SELECT a FROM Acl\Entity\Attribute a JOIN a.user u WHERE u.id = %s AND ( %s )",
				$userId,
				$clause
		);

		$query = $entityManager->createQuery($dql);
		$results = $query->getResult();

		/*
		 * append the results to the $cachedAttributes
		 */
		$this->cachedAttributes = array_merge($this->cachedAttributes, $results);

		return ( count($results) > 0);
	}

}