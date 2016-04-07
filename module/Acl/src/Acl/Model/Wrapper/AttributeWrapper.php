<?php
namespace Acl\Model\Wrapper;

class AttributeWrapper extends AbstractEntityWrapper
{
	public function toArray()
	{
		/*
		 * run dependency check
		 */
		$this->checkDependencies();

		$attribute = $this->entity;

		return array(
			'id' => $attribute->getId(),
			'added' => $attribute->getAdded(),
			'removed' => $attribute->getRemoved(),
			'name' => $attribute->getName(),
			'value' => $attribute->getValue(),
		);
	}
}