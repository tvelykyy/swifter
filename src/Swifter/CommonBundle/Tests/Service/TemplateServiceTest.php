<?php

namespace Swifter\CommonBundle\Tests\Service;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Swifter\CommonBundle\DataFixtures\Test\PagesFixtures;
use Swifter\CommonBundle\Entity\Template;
use Swifter\CommonBundle\Service\TemplateService;

/**
 * All tests test :) templates with inheritance.
 *
 * Test Matrix
 *      A   B   C
 * 1.   +   +   x
 * 2.   +   ++  x
 * 3.   +   -   x
 * 4.   +   +   +
 * 5.   +   ++  ++
 * 6.   +   -   +
 * 7.   +   -   ++
 * 8.   +   +   ++
 * 9.   +   +   -
 *
 * + goes for current block content.
 * ++ goes for current block content and parent block content.
 * - means that current block doesn't override parent block.
 * x means that template isn't used in test.
 */
class TemplateServiceTest extends WebTestCase
{
    const CONTAINER_INTERFACE = 'Symfony\Component\DependencyInjection\ContainerInterface';

    private $em;

    public function __construct()
    {
        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
    }

    public function testGet()
    {
        /* Given. */
        $templateService = $this->initMockedTemplateService();
        $fixtures = $this->initFixtures();
        $expected = $fixtures->getReference(PagesFixtures::MAIN_TEMPLATE);

        /* When. */
        $actual = $templateService->get($expected->getId());

        /* Then. */
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getTitle(), $actual->getTitle());
        $this->assertEquals($expected->getPath(), $actual->getPath());
    }

    public function testGetPageTemplatesOnly()
    {
        /* Given. */
        $templateService = $this->initMockedTemplateService();
        $fixtures = $this->initFixtures();

        /* When. */
        $templates = $templateService->getPageTemplates();

        /* Then. */
        foreach($templates as $template)
        {
            $this->assertTrue($template->isForPage());
        }
    }

    public function testCase1()
    {
        $contents = $this->getCompleteTemplate('case1', 'case1.html.twig');

        /* Then. */
        $this->assertContains('child 1', $contents);
        $this->assertTrue(strpos($contents, 'parent') === false);
    }

    public function testCase2()
    {
        $contents = $this->getCompleteTemplate('case2', 'case2.html.twig');

        /* Then. */
        $this->assertContains('parent', $contents);
        $this->assertContains('child 2', $contents);
    }

    public function testCase3()
    {
        $contents = $this->getCompleteTemplate('case3', 'case3.html.twig');

        /* Then. */
        $this->assertContains('parent', $contents);
    }

    public function testCase4()
    {
        $contents = $this->getCompleteTemplate('case4_child', 'case4_child.html.twig',
            'case4_grandchild', 'case4_grandchild.html.twig');

        /* Then. */
        $this->assertContains('child grand 4', $contents);
        $this->assertTrue(strpos($contents, 'child 4') === false);
        $this->assertTrue(strpos($contents, 'parent') === false);
    }

    public function testCase5()
    {
        $contents = $this->getCompleteTemplate('case5_child', 'case5_child.html.twig',
            'case5_grandchild', 'case5_grandchild.html.twig');

        /* Then. */
        $this->assertContains('child grand 5', $contents);
        $this->assertContains('child 5', $contents);
        $this->assertContains('parent', $contents);
    }

    public function testCase6()
    {
        $contents = $this->getCompleteTemplate('case6_child', 'case6_child.html.twig',
            'case6_grandchild', 'case6_grandchild.html.twig');

        /* Then. */
        $this->assertContains('child grand 6', $contents);
        $this->assertTrue(strpos($contents, 'child 6') === false);
        $this->assertTrue(strpos($contents, 'parent') === false);
    }

    public function testCase7()
    {
        $contents = $this->getCompleteTemplate('case7_child', 'case7_child.html.twig',
            'case7_grandchild', 'case7_grandchild.html.twig');

        /* Then. */
        $this->assertContains('child grand 7', $contents);
        $this->assertContains('parent', $contents);
        $this->assertTrue(strpos($contents, 'child 7') === false);
    }

    public function testCase8()
    {
        $contents = $this->getCompleteTemplate('case8_child', 'case8_child.html.twig',
            'case8_grandchild', 'case8_grandchild.html.twig');

        /* Then. */
        $this->assertContains('child grand 8', $contents);
        $this->assertContains('child 8', $contents);
        $this->assertTrue(strpos($contents, 'parent') === false);
    }

    public function testCase9()
    {
        $contents = $this->getCompleteTemplate('case9_child', 'case9_child.html.twig',
            'case9_grandchild', 'case9_grandchild.html.twig');

        /* Then. */
        $this->assertContains('child 9', $contents);
        $this->assertTrue(strpos($contents, 'parent') === false);
        $this->assertTrue(strpos($contents, 'child grand 9') === false);
    }

    private function getCompleteTemplate($bundlePath1, $filename1, $bundlePath2 = null, $filename2 = null)
    {
        /* Given. */
        $templateService = $this->initTemplateServiceWithMockedGetPhysicalPath(
            array
            (
                array('parent', $this->getRealPath('parent.html.twig')),
                array($bundlePath1, $this->getRealPath($filename1)),
                array($bundlePath2, $this->getRealPath($filename2)),
            )
        );

        $bundlePath = ($bundlePath2 == null ? $bundlePath1 : $bundlePath2);
        $this->mockGet($templateService, $bundlePath);

        /* When. */
        return $templateService->getTemplateFullContents($bundlePath);
    }

    private function initTemplateServiceWithMockedGetPhysicalPath(array $getPathReturnMapping)
    {
        $container = $this->getMockBuilder(self::CONTAINER_INTERFACE)
            ->disableOriginalConstructor()
            ->getMock();
        $templateService = $this->getMock('Swifter\CommonBundle\Service\TemplateService', array('getPhysicalPath', 'get'), array($container, $this->em));
        $this->mockGetPath($templateService, $getPathReturnMapping);

        return $templateService;
    }

    private function mockGetPath($service, array $templatesMapping)
    {
        $service->method('getPhysicalPath')
            ->will($this->returnValueMap($templatesMapping));
    }

    private function mockGet($service, $bundlePath)
    {
        $template = new Template();
        $template->setPath($bundlePath);
        $service->method('get')
            ->will($this->returnValue($template));
    }

    private function getRealPath($path)
    {
        return __DIR__.'/../Resources/views/'.$path;
    }

    private function initMockedTemplateService()
    {
        $container = $this->getMockBuilder(self::CONTAINER_INTERFACE)
            ->disableOriginalConstructor()
            ->getMock();
        $templateService = new TemplateService($container, $this->em);
        return $templateService;
    }

    private function initFixtures()
    {
        $classes = [
            'Swifter\CommonBundle\DataFixtures\Test\PagesFixtures'
        ];
        $fixtures = $this->loadFixtures($classes)->getReferenceRepository();
        return $fixtures;
    }

}