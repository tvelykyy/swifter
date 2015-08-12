<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\ResponseService;
use Swifter\CommonBundle\Service\TemplateService;

class TemplatesController extends BaseController
{
    private $templateService;

    public function __construct(TemplateService $templateService, ResponseService $responseService)
    {
        parent::__construct($responseService);
        $this->templateService = $templateService;
    }
    public function getTemplateAction($id)
    {
        $templateFullContents = $this->templateService->getTemplateFullContents($id);

        return $this->responseService->generateResponse($templateFullContents);
    }

}