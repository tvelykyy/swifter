<?php

namespace Swifter\CommonBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Swifter\CommonBundle\Entity\Block;
use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Entity\Snippet;
use Swifter\CommonBundle\Entity\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPagesData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $block1 = $this->createBlockFixture($manager, 'MAIN_CONTENT');
        $block2 = $this->createBlockFixture($manager, 'TITLE');
        $block3 = $this->createBlockFixture($manager, 'FOOTER');
        $manager->flush();

        /* Templates. */
        $template1 = $this->createTemplateFixture($manager, 'SwifterFrontBundle:DevTest:index.html.twig', 'Main Template');
        $template2 = $this->createTemplateFixture($manager, 'SwifterFrontBundle:DevTest:pages.html.twig', 'Uris');
        $manager->flush();

        /* Snippets. */
        $this->createSnippetFixture($manager, $template2);
        $manager->flush();

        /* Pages. */
        $page1 = $this->createPageFixture($manager, 'Main', '/', $template1);
        $this->setReference("main-page", $page1);

        $page2 = $this->createPageFixture($manager, 'News', '/news', $template1, $page1);
        $this->setReference("news-page", $page2);

        $page3 = $this->createPageFixture($manager, 'First News', '/news/first', $template1, $page2);
        $this->setReference("news-first-page", $page3);

        $manager->flush();

        /* Blocks. */
        $this->createPageBlockFixture($manager, $page1, $block1, 'Yes-Yes. This is page content [[DEV_TEST_PAGES]] contained in CONTENT block.');
        $this->createPageBlockFixture($manager, $page2, $block1, 'This is a news page.');
        $this->createPageBlockFixture($manager, $page1, $block2, 'Заголовок кирилиця і буква І!');
        $this->createPageBlockFixture($manager, $page3, $block3, 'This is super cool footer.');
        $this->createPageBlockFixture($manager, $page2, $block3, 'Medium footer.');

        $manager->flush();
    }

    private function createBlockFixture(ObjectManager $manager, $title)
    {
        $block = new Block();
        $block->setTitle($title);

        $manager->persist($block);

        return $block;
    }

    private function createTemplateFixture(ObjectManager $manager, $path, $title)
    {
        $template = new Template();
        $template->setPath($path);
        $template->setTitle($title);

        $manager->persist($template);

        return $template;
    }


    private function createSnippetFixture(ObjectManager $manager, $template)
    {
        $snippet1 = new Snippet();
        $snippet1->setTitle('DEV_TEST_PAGES');
        $snippet1->setService('front.service.devtest');
        $snippet1->setMethod('getPages');
        $snippet1->setTemplate($template);
        $snippet1->setParams('{"offset":0, "limit":5}');

        $manager->persist($snippet1);
    }

    private function createPageFixture(ObjectManager $manager, $name, $uri, $template, $parent = null)
    {
        $page = new Page();
        $page->setName($name);
        $page->setUri($uri);
        $page->setTemplate($template);
        $page->setParent($parent);

        $manager->persist($page);

        return $page;
    }

    private function createPageBlockFixture(ObjectManager $manager, Page $page, Block $block, $content)
    {
        $pageBlock = new PageBlock();
        $pageBlock->setPage($page);
        $pageBlock->setBlock($block);
        $pageBlock->setContent($content);

        $manager->persist($pageBlock);
    }
}