<?php

namespace AppBundle\Entity;



interface TransformableEntityInterface
{
	public function getReverseTransform();
	public function setCost($cost);
}
