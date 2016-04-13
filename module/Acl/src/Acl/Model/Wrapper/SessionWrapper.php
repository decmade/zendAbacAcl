<?php
namespace Acl\Model\Wrapper;

class SessionWrapper
{
	public function toArray()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$session = $this->entity;

		return array(
			'id' => $session->getId(),
			'added' => $session->getAdded(),
			'removed' => $session->getRemoved(),
			'ipAddress' => $session->getIpAddress(),
			'expires' => $session->getExpires(),
		);
	}
}