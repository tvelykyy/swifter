<?php

namespace Swifter\CommonBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Swifter\CommonBundle\Entity\Block;
use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Entity\Snippet;
use Swifter\CommonBundle\Entity\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPagesData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $block1 = new Block();
        $block1->setTitle('MAIN_CONTENT');

        $block2 = new Block();
        $block2->setTitle('TITLE');

        $block3 = new Block();
        $block3->setTitle('FOOTER');

        $manager->persist($block1);
        $manager->persist($block2);
        $manager->persist($block3);
        $manager->flush();

        /* Templates. */
        $template1 = new Template();
        $template1->setPath('SwifterFrontBundle:DevTest:index.html.twig');
        $template1->setTitle('Main Template');

        $template2 = new Template();
        $template2->setPath('SwifterFrontBundle:DevTest:pages.html.twig');
        $template2->setTitle('Uris');

        $manager->persist($template1);
        $manager->persist($template2);
        $manager->flush();

        /* Snippets. */
        $snippet1 = new Snippet();
        $snippet1->setTitle('DEV_TEST_PAGES');
        $snippet1->setService('front.service.devtest');
        $snippet1->setMethod('getPages');
        $snippet1->setTemplate($template2);
        $snippet1->setParams('{"offset":0, "limit":5}');

        $manager->persist($snippet1);
        $manager->flush();

        /* Pages. */
        $page1 = new Page();
        $page1->setName('Main');
        $page1->setUri('/');
        $page1->setTemplate($template1);

        $page2 = new Page();
        $page2->setName('News');
        $page2->setUri('/news');
        $page2->setTemplate($template1);
        $page2->setParent($page1);

        $page3 = new Page();
        $page3->setName('First News');
        $page3->setUri('/news/first');
        $page3->setTemplate($template1);
        $page3->setParent($page2);

        $manager->persist($page1);
        $manager->persist($page2);
        $manager->persist($page3);
        $manager->flush();

        /* Blocks. */
        $pageBlock1 = new PageBlock();
        $pageBlock1->setPage($page1);
        $pageBlock1->setBlock($block1);
        $pageBlock1->setContent('Yes-Yes. This is page content [[DEV_TEST_PAGES]] contained in CONTENT block.');

        $pageBlock2 = new PageBlock();
        $pageBlock2->setPage($page2);
        $pageBlock2->setBlock($block1);
        $pageBlock2->setContent('This is a news page.');

        $pageBlock3 = new PageBlock();
        $pageBlock3->setPage($page1);
        $pageBlock3->setBlock($block2);
        $pageBlock3->setContent('Заголовок кирилиця і буква І!');

        $pageBlock4 = new PageBlock();
        $pageBlock4->setPage($page3);
        $pageBlock4->setBlock($block3);
        $pageBlock4->setContent('This is super cool footer.');

        $pageBlock5 = new PageBlock();
        $pageBlock5->setPage($page2);
        $pageBlock5->setBlock($block3);
        $pageBlock5->setContent('Medium footer.');

        $manager->persist($pageBlock1);
        $manager->persist($pageBlock2);
        $manager->persist($pageBlock3);
        $manager->persist($pageBlock4);
        $manager->persist($pageBlock5);
        $manager->flush();
    }
}