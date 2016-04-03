<?php
namespace Acl\Model\Authorization;

use Acl\Model\DependentObjectTrait;
use Doctrine\ORM\EntityManager;
use Acl\Model\StandardInputFiltersTrait;

/**
 * This class validates that the attributes of the user passed to
 * the evaluate(user, string) function match the dql expression
 * in the second parameter. Ideally the second parameter would look for
 * certain attributes or combinations of attributes. It could potentially
 * consider attributes of the user object as well.
 *
 * @author John W. Brown, Jr. <john.w.brown.jr@gmail.com>
 *
 */
class UserAttributeEvaluator
{
	use DependentObjectTrait;
	use StandardInputFiltersTrait;

	/**
	 *
	 * @var EnitityManager
	 */
	private $entityManager;

	/**
	 * used to cache the attributes found as
	 * the attribute conditions are applied during the
	 * evaluateSingleCluase($userId, $clause) function
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
		/*
		 * if $cachedAttribues already exits, unset them
		 */
		if ($this->cachedAttributes) {
			unset($this->cachedAttributes);
		}

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
	 * @param string|array $accessDqlConfig
	 *
	 * @return boolean
	 */
	public function evaluate($userId, $accessDqlConfig)
	{

		/*
		 * reset the cached attributes
		 */
		$this->initializeCachedAttributes();

		/*
		 * if the $accessDqlConfig is an array, then pass as is,
		 * otherwise filter it as a string
		 */
		if ( is_array($accessDqlConfig)) {
			$clauseGroup = $accessDqlConfig;
		} else {
			$clauseGroup = $this->filterStringInput($accessDqlConfig);
		}

		return $this->evaluateClauseGroup($userId, $clauseGroup);
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

	/**
	 * evaluates/queries for an array of DQL clauses against
	 * the current authenticated user's attributes
	 *
	 * @param int $userId
	 * @param array $clauseGroup
	 *
	 * @return boolean
	 */
	private function evaluateClauseGroup($userId, $clauseGroup)
	{
		$falseCount = 0; /* number of times a condition is evaluated as false */
		$trueCount = 0; /* number of time a condition is evaluated as ture */
		$isOrCondition = false; /* true = this group of conditions should be evaluated as if with an OR operator */

		/*
		 * if there is an array of  clauses,
		 * check each one to make sure they are all true
		 * as if to say the user has Attribute1 AND Attribute2 ... AND Attribute[N]
		 */
		if ( is_array($clauseGroup) ) {
			/*
			 * if a $clauseGroup is malformed with an empty nested array
			 * it will result in an evaluation of true without this
			 * condition
			 */
			if (count($clauseGroup) == 0) {
				$falseCount++;
			}

			foreach($clauseGroup as $clause) {
				if (is_array($clause)) {
					/*
					 * any time a nested array is found in a group,
					 * the elements of the group are evaluated
					 * as if with an OR operator, so the $isOrCondition is
					 * set to true
					 */
					$isOrCondition = true;
					$result = $this->evaluateClauseGroup($userId, $clause);

				} else {
					$result = $this->evaluateSingleClause($userId, $clause);
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
			 * evaluated as if there are OR conditions, if any attribute
			 * single clause is evaluated as true then return true
			 * otherwise false
			 */
			if ($isOrCondition) {
				return ($trueCount > 0) ? true : false;
			} else {
				/*
				 * else this is an AND gate condition and all clauses
				 * must be true, so any fale returns false otherwise
				 * true
				 */
				return ($falseCount > 0) ? false : true;
			}
		} else {
			/*
			 * if this is not really a group of clauses, then pass
			 * execution to the single clause handler
			 */
			return $this->evaluateSingleClause($userId, $clauseGroup);
		}
	}

	/**
	 * true if the clause returns results when applied
	 * to the attributes of the authenticated user
	 * false if not
	 *
	 * @param int $userId
	 * @param string $clause
	 *
	 * @return boolean
	 */
	private function evaluateSingleClause($userId, $clause)
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$entityManager = $this->entityManager;

		/*
		 * apply the standard integer input filter to $userId
		 */
		$userId = $this->filterIntegerInput($userId);

		/*
		 * apply the standard string input filter to the $clause
		 */
		$clause = $this->filterStringInput($clause);

		if (empty($clause)) {
			return true;
		} else {
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

}