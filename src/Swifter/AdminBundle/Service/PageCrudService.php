<?php

namespace Swifter\AdminBundle\Service;

use Doctrine\ORM\EntityManager;
use Swifter\CommonBundle\Service\PageBlockService;

class PageCrudService extends CrudService
{
    private $pageBlockService;

    public function __construct(ResponseService $responseService, PageBlockService $pageBlockService,
                                SerializationService $serializationService, EntityManager $em)
    {
        parent::__construct($responseService, $serializationService, $em);
        $this->pageBlockService = $pageBlockService;
    }

    public function editAndGenerateResponse($page)
    {
        $blocksIds = [];
        foreach ($page->getPageBlocks() as $block)
        {
            if ($block->getId())
            {
                $blocksIds[] = $block->getId();
            }
        }
        $this->pageBlockService->deleteForPageOtherBlocksThan($page->getId(), $blocksIds);

        return parent::editAndGenerateResponse($page);
    }

}