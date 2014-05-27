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
    public function getCompleteTemplateAction($id)
    {
        $template = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Template')
            ->find($id);

        $completeTemplate = $this->templateService->getCompleteTemplate($template->getPath());

        return $this->responseService->generateResponse($completeTemplate);
    }

}