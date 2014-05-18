<?php

namespace Swifter\CommonBundle\Service;

use Symfony\Component\DependencyInjection\Container;

class TemplateService
{
    protected $container;
    const EXTENDS_REGEX = '/{%.?extends "(.+)".?%}/';
    const BLOCK_CONTENTS_REGEX = '/{% block ([a-zA-Z]+) %}((\n|.)+?){% endblock %}/m';
    const BLOCK_BY_TITLE_REGEX = '/({% block _title_ %})((\n|.)+?)({% endblock %})/m';
    const PARENT_PATTERN = '{{ parent() }}';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getCompleteTemplate()
    {
        $contents = $this->getContents('SwifterFrontBundle:DevTest:pages.html.twig');
        return $this->mergeWithParent($contents);
    }

    protected function getContents($template)
    {
        $path = $this->getPath($template);
        $contents = file_get_contents($path);

        return $contents;
    }

    protected function mergeWithParent($contents)
    {
        $parent = $this->getParentTitle($contents);

        if ($parent) {
            $parentContents = $this->getContents($parent);
            $blocks = $this->getBlocks($contents);

            foreach ($blocks as $blockTitle => $blockContents) {
                $parentBlockRegex = $this->getBlockRegexByTitle($blockTitle);
                if (strpos($blockContents, static::PARENT_PATTERN) !== false) {
                    preg_match($parentBlockRegex, $parentContents, $parentBlock);
                    $blockContents = str_replace(static::PARENT_PATTERN, $parentBlock[2], $blockContents);
                }
                $parentContents = preg_replace($parentBlockRegex, '$1' . $blockContents . '$4', $parentContents);
            }
            return $this->mergeWithParent($parentContents);
        } else {
            return $contents;
        }
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

        return empty($parent) ? null : $parent[1];
    }

    protected function getBlockRegexByTitle($title)
    {
        return str_replace('_title_', $title, static::BLOCK_BY_TITLE_REGEX);
    }

}