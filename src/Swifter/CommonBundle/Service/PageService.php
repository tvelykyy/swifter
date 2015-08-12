<?php

namespace Swifter\CommonBundle\Service;

use Doctrine\ORM\EntityManager;

class PageService
{
    const PAGE_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Page';

    private $em;
    private $repo;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(static::PAGE_CLASS_BUNDLE_NOTATION);
    }

    public function getOneById($id)
    {
        $page = $this->repo->findOneById($id);

        return $page;
    }

    public function getOneByUri($uri)
    {
        $page = $this->repo->findOneByUri($uri);

        return $page;
    }

    public function getAll()
    {
        $pages = $this->repo->findAll();

        return $pages;
    }

    public function getByNameLike($name)
    {
        $pages = $this->repo
            ->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()
            ->getResult();

        return $pages;

    }

}