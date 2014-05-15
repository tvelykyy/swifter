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
        $contents = $this->getContents('SwifterFrontBundle:DevTest:pages.html.twig');
        $parent = $this->getParentTitle($contents);

        if (isset($parent))
        {
            $parentContents = $this->getContents($parent);

            $blocks = $this->getBlocks($contents);
            foreach($blocks as $blockTitle => $blockContents)
            {
                if (strpos($blockContents, static::PARENT_PATTERN) !== false)
                {
                    $parentBlockRegex = $this->getBlockRegexByTitle($blockTitle);
                    preg_match($parentBlockRegex, $parentContents, $parentBlock);
                    $mergedBlock = str_replace(static::PARENT_PATTERN, $parentBlock[1], $blockContents);
                    preg_replace($parentBlockRegex, '$1'.$mergedBlock.'$2$3', $parentContents);
                }
            }
        }
    }

    protected function getContents($template)
    {
        $path = $this->getPath($template);
        $contents = file_get_contents($path);

        return $contents;
    }

    protected function getPath($templateName)
    {
        $parser = $this->container->get('templating.name_parser');
        $locator = $this->container->get('templating.locator');

        return $locator->locate($parser->parse($templateName));
    }

    protected function getBlocks($contents)
    {
        preg_match_all(static::BLOCK_CONTENTS_REGEX, $contents, $matches);
        $blocks = array();
        foreach($matches[1] as $index => $title)
        {
            $blocks[$title] = $matches[2][$index];
        }
        return $blocks;
    }

    protected function getParentTitle($contents)
    {
        preg_match(static::EXTENDS_REGEX, $contents, $parent);

        return $parent[1];
    }

    protected function getBlockRegexByTitle($title)
    {
        return str_replace('_title_', $title, static::BLOCK_BY_TITLE_REGEX);
    }

}