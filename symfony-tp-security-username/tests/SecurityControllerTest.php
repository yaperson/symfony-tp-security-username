<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class SecurityControllerTest extends WebTestCase
{
    public function testShowLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html head title', 'Log in!');
    
        // $this->assertContains(
        //     'type="hidden" name="_csrf_token"',
        //     $client->getResponse()->getContent()
        // );
    }

    private function logIn($userName = 'user', $userRole = 'ROLE_USER' )
    {
        $session = self::$container->get('session');

        // somehow fetch the user (e.g. using the user repository)
        $user = $userName;

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'main';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken('admin', null, $firewallName, ['ROLE_ADMIN']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testSecuredHello()
    {
        $this->logIn('user', 'ROLE_USER');
        $crawler = $this->client->request('GET', '/category/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Category index', $crawler->filter('h1')->text());
    }
}