<?php

namespace Swifter\AdminBundle\Tests\Controller;


use Swifter\CommonBundle\DataFixtures\Test\PagesFixtures;

class TemplateControllerTest extends ControllerTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function testShouldReturnMainTemplate()
    {
        /* Given. */
        $template = $this->fixtures->getReference(PagesFixtures::MAIN_TEMPLATE);

        /* When. */
        $this->client->request('GET', $this->generateRoute('admin_get_template', ['id' => $template->getId()]));
        $response = $this->getResponse();
        $content = $response->getContent();

        /* Then. */
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('text/html', $response->headers->get('Content-Type'));
        $this->assertContains('TITLE', $content);
        $this->assertContains('MAIN_CONTENT', $content);
        $this->assertContains('FOOTER', $content);
    }


}