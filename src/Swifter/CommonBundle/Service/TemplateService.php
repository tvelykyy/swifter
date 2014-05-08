<?php

namespace Swifter\CommonBundle\Service;

use Symfony\Component\DependencyInjection\Container;

class TemplateService
{
    protected $container;
    const EXTENDS_REGEX = '/{%.?extends "(.+)".?%}/';
    const BLOCK_CONTENTS_REGEX = '/{% block ([a-zA-Z]+) %}\n((\n|.)+?)\n{% endblock %}/m';
    const BLOCK_BY_TITLE_REGEX = '/{% block _title_ %}\n((\n|.)+?)\n{% endblock %}/m';
    const PARENT_PATTERN = '{{ parent() }}';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getCompleteTemplate()
    {
        $path = $this->getTemplatePath('SwifterFrontBundle:DevTest:pages.html.twig');
        $templateContents = file_get_contents($path);

        preg_match(static::EXTENDS_REGEX, $templateContents, $matchedParent);

        if (isset($matchedParent[1]))
        {
            $parentPath = $this->getTemplatePath($matchedParent[1]);
            $parentContents = file_get_contents($parentPath);

            preg_match_all(static::BLOCK_CONTENTS_REGEX, $templateContents, $blocks);
            foreach($blocks[1] as $index => $blockTitle)
            {
                if (strpos($blocks[2][$index], static::PARENT_PATTERN) !== false)
                {
                    $parentBlockRegex = $this->getBlockRegexByTitle($blockTitle);
                    preg_match($parentBlockRegex, $parentContents, $parentBlock);
                    $mergedBlock = str_replace(static::PARENT_PATTERN, $parentBlock[1], $blocks[2][$index]);
                    preg_replace($parentBlockRegex, '$1'.$mergedBlock.'$2$3', $parentContents);
                }
            }
        }
    }

    protected function getTemplatePath($templateName)
    {
        $parser = $this->container->get('templating.name_parser');
        $locator = $this->container->get('templating.locator');

        return $locator->locate($parser->parse($templateName));
    }

    protected function getBlockRegexByTitle($title)
    {
        return str_replace('_title_', $title, static::BLOCK_BY_TITLE_REGEX);
    }
}