<?php

namespace Swifter\AdminBundle\Tests\Controller;


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
        $template = $this->fixtures->getReference('main-template');

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