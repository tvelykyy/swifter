<?php

namespace Swifter\CommonBundle\Tests\Service;

/**
 * All tests test :) templates with inheritance
 */
class TemplateServiceTest extends \PHPUnit_Framework_TestCase
{
    const CONTAINER_INTERFACE = 'Symfony\Component\DependencyInjection\ContainerInterface';

    public function testShouldCheckBasicTemplate()
    {
        /* Given. */
        $templateService = $this->initTemplateServiceWithMockedGetPath(
            array
            (
                array('parent', $this->getRealPath('parent.html.twig')),
                array('child', $this->getRealPath('child.html.twig'))
            )
        );

        /* When. */
        $contents = $templateService->getCompleteTemplate('child');

        /* Then. */
        $this->assertContains('child first', $contents);
        $this->assertContains('parent second', $contents);
        $this->assertTrue(strpos($contents, 'parent first') === false);
    }

    public function testShouldCheckTemplateWithParentPlaceholder()
    {
        /* Given. */
        $templateService = $this->initTemplateServiceWithMockedGetPath(
            array
            (
                array('parent', $this->getRealPath('parent.html.twig')),
                array('child', $this->getRealPath('child_with_parent_placeholder.html.twig'))
            )
        );

        /* When. */
        $contents = $templateService->getCompleteTemplate('child');

        /* Then. */
        $this->assertContains('child first', $contents);
        $this->assertTrue(strpos($contents, 'parent first') === false);
        $this->assertContains('child second', $contents);
        $this->assertContains('parent second', $contents);
    }

    private function initTemplateServiceWithMockedGetPath(array $getPathReturnMapping)
    {
        $container = $this->getMockBuilder(self::CONTAINER_INTERFACE)
            ->disableOriginalConstructor()
            ->getMock();
        $templateService = $this->getMock('Swifter\CommonBundle\Service\TemplateService', array('getPath'), array($container));
        $this->mockGetPath($templateService, $getPathReturnMapping);

        return $templateService;
    }

    private function mockGetPath($service, array $templatesMapping)
    {
        $service->method('getPath')
            ->will($this->returnValueMap($templatesMapping));
    }

    private function getRealPath($path)
    {
        return __DIR__.'/../Resources/views/'.$path;
    }
}