<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\BlockService;
use Swifter\AdminBundle\Service\ResponseService;
use Swifter\AdminBundle\Service\CrudService;
use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups;
use Swifter\CommonBundle\Service\PageBlockService;
use Swifter\CommonBundle\Service\PageService;
use Swifter\CommonBundle\Service\TemplateService;
use Symfony\Component\Validator\ConstraintViolationList;

class PageController extends CrudController
{
    const PAGE_CLASS = 'Swifter\CommonBundle\Entity\Page';
    const PAGE_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Page';

    private $pageBlockService;
    private $pageService;
    private $templateService;

    public function __construct(CrudService $crudService, ResponseService $responseService, SerializationService $serializationService,
                                PageBlockService $pageBlockService, PageService $pageService, TemplateService $templateService,
                                BlockService $blockService)
    {
        parent::__construct($crudService, $responseService, $serializationService);
        $this->pageBlockService = $pageBlockService;
        $this->pageService = $pageService;
        $this->templateService = $templateService;
        $this->blockService = $blockService;
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
        $pageJson = $this->serializationService->serializeToJsonByGroup($page, SerializationGroups::PAGE_DETAILS_GROUP);

        $templates = $this->templateService->getPageTemplates();
        $templatesJson = $this->serializationService->serializeToJsonByGroup($templates, SerializationGroups::LIST_GROUP);

        return $this->render('SwifterAdminBundle::page_form.html.twig',
            array(
                'title' => 'Pages Form',
                'page' => $pageJson,
                'templates' => $templatesJson
            )
        );
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

    protected function validate($page)
    {
        $validator = $this->get('validator');

        $errors = new ConstraintViolationList();
        foreach($page->getPageBlocks() as $pageBlock)
        {
            $block = $this->blockService->get($pageBlock->getBlock()->getId());
            $constraintName = $block->getType()->getConstraint();
            $constraintClass = 'Symfony\Component\Validator\Constraints\\'.$constraintName;
            $options = json_decode($block->getOptions(), true);
            $constraint = new $constraintClass($options);

            $errors->addAll($validator->validate($pageBlock->getContent(), $constraint));
        }

        return $errors;
    }

}