<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class SecurityControllerTest extends WebTestCase
{
    public function testShowLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html head title', 'Log in!');
    
        $this->assertContains(
            'type="hidden" name="_csrf_token"',
            $client->getResponse()->getContent()
        );
    }
}