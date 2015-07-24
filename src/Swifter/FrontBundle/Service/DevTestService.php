<?php

namespace Swifter\FrontBundle\Service;

use Doctrine\ORM\EntityManager;

class DevTestService
{
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getPages($offset, $limit)
    {
        return $this->em->getRepository('SwifterCommonBundle:Page')->findBy(array(), array(), $limit, $offset);
    }

}