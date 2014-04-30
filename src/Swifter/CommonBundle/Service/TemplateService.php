<?php

namespace Swifter\CommonBundle\Service;

use Symfony\Component\DependencyInjection\Container;

class TemplateService
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getCompleteTemplate()
    {
        $path = $this->getTemplatePath('SwifterFrontBundle:DevTest:pages.html.twig');
        $templateContents = file_get_contents($path);

        preg_match('/{%.?extends "(.+)".?%}/', $templateContents, $matchedParent);
        print_r($matchedParent);

        if (isset($matchedParent[1]))
        {
            $parentPath = $this->getTemplatePath($matchedParent[1]);
            $parentContents = file_get_contents($parentPath);
        }
    }

    protected function getTemplatePath($templateName)
    {
        $parser = $this->container->get('templating.name_parser');
        $locator = $this->container->get('templating.locator');

        return $locator->locate($parser->parse($templateName));
    }
}