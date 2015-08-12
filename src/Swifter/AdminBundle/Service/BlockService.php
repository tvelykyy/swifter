<?php

namespace Swifter\AdminBundle\Service;

use Doctrine\ORM\EntityManager;

class BlockService {
    const BLOCK_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Block';

    private $em;
    private $repo;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(static::BLOCK_CLASS_BUNDLE_NOTATION);
    }

    public function getAll()
    {
        $blocks = $this->repo->findAll();

        return $blocks;
    }

    public function getOneById($id)
    {
        $block = $this->repo->find($id);

        return $block;
    }

    public function getAllByTitles($titles)
    {
        $qb = $this->repo->createQueryBuilder('b');

        $blocks = $qb->where($qb->expr()->in('b.title', $titles))
            ->getQuery()
            ->getResult();

        return $blocks;
    }
}