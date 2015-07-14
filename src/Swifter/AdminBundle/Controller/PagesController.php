<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\ResponseService;
use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Service\PageBlockService;

class PagesController extends CrudController
{
    const PAGE_CLASS = 'Swifter\CommonBundle\Entity\Page';
    const PAGE_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Page';

    protected $pageBlockService;

    public function __construct(ResponseService $responseService, SerializationService $serializationService,
                                PageBlockService $pageBlockService)
    {
        parent::__construct($responseService, $serializationService);
        $this->pageBlockService = $pageBlockService;
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
        $page = $this->getDoctrine()
            ->getRepository(static::PAGE_CLASS_BUNDLE_NOTATION)
            ->findOneBy(array('id' => $id));

        $pageJson = $this->serializationService->serializeToJsonByGroup($page, 'details');
        return $this->render('SwifterAdminBundle::page_form.html.twig', array('title' => 'Pages Form', 'page' => $pageJson));
    }

    public function getPagesAction()
    {
        $pages = $this->getDoctrine()
            ->getRepository(static::PAGE_CLASS_BUNDLE_NOTATION)
            ->findAll();

        $jsonPages = $this->serializationService->serializeToJsonByGroup($pages, 'list');

        return $this->responseService->generateJsonResponse($jsonPages);
    }

    public function savePageAction()
    {
        $page = $this->serializationService->deserializeFromJson($this->get('request')->getContent(), static::PAGE_CLASS);

        $errors = $this->validate($page);

        if (count($errors) > 0) {
            $response = $this->responseService->generateErrorsJsonResponse($errors);
        }
        else
        {
            $response = $this->saveAndGenerateResponse($page);
        }

        return $response;
    }

    public function getBlocksAction($id)
    {
        $page = $this->getDoctrine()
            ->getRepository(static::PAGE_CLASS_BUNDLE_NOTATION)
            ->find($id);

        $this->pageBlockService->mergePageBlocksWithParents($page);

        $json = $this->serializationService->serializeToJsonByGroup($page, 'page-no-parent-template');

        return $this->responseService->generateJsonResponse($json);
    }

    public function getPagesByNameLike($name)
    {
        $pages = $this->getDoctrine()
            ->getRepository(static::PAGE_CLASS_BUNDLE_NOTATION)
            ->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()
            ->getResult();

        $json = $this->serializationService->serializeToJsonByGroup($pages, 'basic');

        return $this->responseService->generateJsonResponse($json);
    }

}