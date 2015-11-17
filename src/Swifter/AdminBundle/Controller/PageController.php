<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\ResponseService;
use Swifter\AdminBundle\Service\CrudService;
use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups;
use Swifter\CommonBundle\Service\PageBlockService;
use Swifter\CommonBundle\Service\PageService;

class PageController extends CrudController
{
    const PAGE_CLASS = 'Swifter\CommonBundle\Entity\Page';
    const PAGE_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Page';

    private $pageBlockService;
    private $pageService;

    public function __construct(CrudService $crudService, ResponseService $responseService, SerializationService $serializationService,
                                PageBlockService $pageBlockService, PageService $pageService)
    {
        parent::__construct($crudService, $responseService, $serializationService);
        $this->pageBlockService = $pageBlockService;
        $this->pageService = $pageService;
    }

    public function renderPagesAction()
    {
        return $this->render('SwifterAdminBundle::pages_list.html.twig', array('title' => 'Pages Management'));
    }

    public function renderPagesAddAction()
    {
        return $this->render('SwifterAdminBundle::page_form.html.twig', array('title' => 'Pages Form'));
    }

    public function renderPagesEditAction($id)
    {
        $page = $this->pageService->get($id);
        $pageJson = $this->serializationService->serializeToJsonByGroup($page, SerializationGroups::DETAILS_GROUP);

        return $this->render('SwifterAdminBundle::page_form.html.twig', array('title' => 'Pages Form', 'page' => $pageJson));
    }

    public function getPagesAction()
    {
        $pages = $this->pageService->getAll();
        $jsonPages = $this->serializationService->serializeToJsonByGroup($pages, SerializationGroups::LIST_GROUP);

        return $this->responseService->generateJsonResponse($jsonPages);
    }

    public function createPageAction()
    {
        return $this->create(static::PAGE_CLASS);
    }

    public function editPageAction()
    {
        return $this->edit(static::PAGE_CLASS);
    }

    public function deletePageAction($id)
    {
        $pageToDelete = $this->pageService->get($id);

        return $this->crudService->deleteAndGenerateResponse($pageToDelete);
    }

    public function getBlocksAction($id)
    {
        $page = $this->pageService->get($id);

        $this->pageBlockService->mergePageBlocksWithParents($page);
        $json = $this->serializationService->serializeToJsonByGroup($page, SerializationGroups::PAGE_BASIC_GROUP);

        return $this->responseService->generateJsonResponse($json);
    }

    public function getPagesByNameLike($name)
    {
        $pages = $this->pageService->getByNameLike($name);
        $json = $this->serializationService->serializeToJsonByGroup($pages, SerializationGroups::BASIC_GROUP);

        return $this->responseService->generateJsonResponse($json);
    }

}