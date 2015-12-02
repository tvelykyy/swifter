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

class PagesFixtures extends AbstractFixture implements FixtureInterface
{
    const MAIN_BLOCK = 'main-content-block';
    const TITLE_BLOCK = 'title-block';
    const FOOTER_BLOCK = 'footer-block';

    const MAIN_TEMPLATE = 'main-template';

    const PARENT_PAGE = 'main-page';
    const CHILD_PAGE = 'news-page';
    const GRAND_CHILD_PAGE = 'news-first-page';

    const PARENT_PAGE_MAIN_BLOCK = 'main-page-main-content-block';
    const CHILD_PAGE_MAIN_BLOCK = 'news-page-main-content-block';
    const PARENT_PAGE_TITLE_BLOCK = 'main-page-title-block';
    const GRAND_CHILD_PAGE_FOOTER_BLOCK = 'first-news-page-footer-block';
    const CHILD_PAGE_FOOTER_BLOCK = 'news-page-footer-block';

    public function load(ObjectManager $manager)
    {

        $mainContentBlock = $this->createBlockFixture($manager, 'MAIN_CONTENT');
        $this->setReference(static::MAIN_BLOCK, $mainContentBlock);

        $titleBlock = $this->createBlockFixture($manager, 'TITLE');
        $this->setReference(static::TITLE_BLOCK, $titleBlock);

        $footerBlock = $this->createBlockFixture($manager, 'FOOTER');
        $this->setReference(static::FOOTER_BLOCK, $footerBlock);
        $manager->flush();

        /* Templates. */
        $template1 = $this->createTemplateFixture($manager, 'SwifterFrontBundle:DevTest:index.html.twig', 'Main Template', true);
        $this->setReference(static::MAIN_TEMPLATE, $template1);

        $template2 = $this->createTemplateFixture($manager, 'SwifterFrontBundle:DevTest:pages.html.twig', 'Uris', false);

        $manager->flush();

        /* Snippets. */
        $this->createSnippetFixture($manager, $template2);
        $manager->flush();

        /* Pages. */
        $mainPage = $this->createPageFixture($manager, 'Main', '/', $template1);
        $this->setReference(static::PARENT_PAGE, $mainPage);

        $newsPage = $this->createPageFixture($manager, 'News', '/news', $template1, $mainPage);
        $this->setReference(static::CHILD_PAGE, $newsPage);

        $firstNewsPage = $this->createPageFixture($manager, 'First News', '/news/first', $template1, $newsPage);
        $this->setReference(static::GRAND_CHILD_PAGE, $firstNewsPage);

        $manager->flush();

        /* Blocks. */
        $this->createPageBlockFixture($manager, static::PARENT_PAGE_MAIN_BLOCK, $mainPage, $mainContentBlock, 'Yes-Yes. This is page content [[DEV_TEST_PAGES]] contained in CONTENT block.');
        $this->createPageBlockFixture($manager, static::PARENT_PAGE_TITLE_BLOCK, $mainPage, $titleBlock, 'Заголовок кирилиця і буква І!');
        $this->createPageBlockFixture($manager, static::CHILD_PAGE_MAIN_BLOCK, $newsPage, $mainContentBlock, 'This is a news page.');
        $this->createPageBlockFixture($manager, static::CHILD_PAGE_FOOTER_BLOCK, $newsPage, $footerBlock, 'Medium footer.');
        $this->createPageBlockFixture($manager, static::GRAND_CHILD_PAGE_FOOTER_BLOCK, $firstNewsPage, $footerBlock, 'This is super cool footer.');

        $manager->flush();
    }

    private function createBlockFixture(ObjectManager $manager, $title)
    {
        $block = new Block();
        $block->setTitle($title);

        $manager->persist($block);

        return $block;
    }

    private function createTemplateFixture(ObjectManager $manager, $path, $title, $isForPage)
    {
        $template = new Template();
        $template->setPath($path);
        $template->setTitle($title);
        $template->setForPage($isForPage);

        $manager->persist($template);

        return $template;
    }


    private function createSnippetFixture(ObjectManager $manager, $template)
    {
        $snippet = new Snippet();
        $snippet->setTitle('DEV_TEST_PAGES');
        $snippet->setService('front.service.devtest');
        $snippet->setMethod('getPages');
        $snippet->setTemplate($template);
        $snippet->setParams('{"offset":0, "limit":5}');

        $manager->persist($snippet);
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

    private function createPageBlockFixture(ObjectManager $manager, $reference, Page $page, Block $block, $content)
    {
        $pageBlock = new PageBlock();
        $pageBlock->setPage($page);
        $pageBlock->setBlock($block);
        $pageBlock->setContent($content);

        $manager->persist($pageBlock);

        $this->setReference($reference, $pageBlock);
    }
}