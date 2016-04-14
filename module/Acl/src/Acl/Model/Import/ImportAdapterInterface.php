<?php
namespace Acl\Model\Import;


interface ImportAdapterInterface
{
/**
	 * import data from a path to a CSV file
	 *
	 * @param mixed $target
	 *
	 * @return array<A>
	 */
	public function import($target);
}