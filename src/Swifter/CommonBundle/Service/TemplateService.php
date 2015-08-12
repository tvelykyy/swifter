<?php

namespace Swifter\CommonBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class TemplateService
{
    const EXTENDS_REGEX = '/{%.?extends "(.+)".?%}/';
    const BLOCK_CONTENTS_REGEX = '/{% block ([a-zA-Z]+) %}((\n|.)+?){% endblock %}/m';
    const BLOCK_BY_TITLE_REGEX = '/({% block _title_ %})((\n|.)+?)({% endblock %})/m';
    const PARENT_PLACEHOLDER = '{{ parent() }}';
    const TITLE_PLACEHOLDER = '_title_';

    const TEMPLATE_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Template';

    private $container;
    private $em;
    private $repo;

    public function __construct(Container $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
        $this->repo = $em->getRepository(static::TEMPLATE_CLASS_BUNDLE_NOTATION);
    }

    public function get($id)
    {
        $template = $this->repo->find($id);

        return $template;
    }

    public function getTemplateFullContents($id)
    {
        $template = $this->get($id);
        $contents = $this->getContents($template->getPath());
        return $this->mergeWithParentIfHas($contents);
    }

    private function getContents($bundlePath)
    {
        $physicalPath = $this->getPhysicalPath($bundlePath);
        $contents = file_get_contents($physicalPath);

        return $contents;
    }

    public function getPhysicalPath($bundlePath)
    {
        $parser = $this->container->get('templating.name_parser');
        $locator = $this->container->get('templating.locator');

        return $locator->locate($parser->parse($bundlePath));
    }

    private function mergeWithParentIfHas($contents)
    {
        $parent = $this->getParentTitle($contents);
        if ($parent) {

            $contents = $this->mergeWithParent($contents, $parent);
        }

        return $contents;
    }

    private function mergeWithParent($contents, $parent)
    {
        $parentContents = $this->getContents($parent);
        $parentContents = $this->mergeWithParentIfHas($parentContents);

        $blocks = $this->getBlocks($contents);
        $contents = $this->mergeAllBlockWithParentOnes($blocks, $parentContents);

        return $contents;
    }

    private function mergeAllBlockWithParentOnes($blocks, $parentContents)
    {
        foreach ($blocks as $blockTitle => $blockContents) {
            $blockContents = $this->mergeBlockWithParentIfNecessary($blockTitle, $blockContents, $parentContents);
            $parentContents = $this->updateBlockInTemplate($blockTitle, $blockContents, $parentContents);
        }

        return $parentContents;
    }

    private function mergeBlockWithParentIfNecessary($blockTitle, $blockContents, $parentContents)
    {
        if ($this->isBlockUsesParentPlaceholder($blockContents)) {
            $blockRegex = $this->getBlockRegexByTitle($blockTitle);
            $blockContents = $this->mergeBlockWithParent($blockRegex, $parentContents, $blockContents);
        }
        return $blockContents;
    }

    private function updateBlockInTemplate($blockTitle, $blockContents, $contents)
    {
        $blockRegex = $this->getBlockRegexByTitle($blockTitle);
        return preg_replace($blockRegex, '$1' . $blockContents . '$4', $contents);
    }

    private function isBlockUsesParentPlaceholder($blockContents)
    {
        return strpos($blockContents, static::PARENT_PLACEHOLDER) !== false;
    }

    private function mergeBlockWithParent($parentBlockRegex, $parentContents, $blockContents)
    {
        preg_match($parentBlockRegex, $parentContents, $parentBlock);
        $blockContents = str_replace(static::PARENT_PLACEHOLDER, $parentBlock[2], $blockContents);

        return $blockContents;
    }

    private function getParentTitle($contents)
    {
        preg_match(static::EXTENDS_REGEX, $contents, $parent);

        return empty($parent) ? null : $parent[1];
    }

    private function getBlocks($contents)
    {
        preg_match_all(static::BLOCK_CONTENTS_REGEX, $contents, $matches);
        $blocks = array();
        foreach($matches[1] as $index => $title)
        {
            $blocks[$title] = $matches[2][$index];
        }
        return $blocks;
    }

    private function getBlockRegexByTitle($title)
    {
        return str_replace(static::TITLE_PLACEHOLDER, $title, static::BLOCK_BY_TITLE_REGEX);
    }

}