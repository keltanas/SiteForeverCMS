<?php
/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */

namespace Sfcms\Test;

use PHPUnit_Framework_TestCase;
use Sfcms\Request;
use Sfcms\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\DomCrawler\Crawler;

class WebCase extends PHPUnit_Framework_TestCase
{
    /** @var Request */
    protected $request;

    /** @var Session */
    protected $session;

    /** @var Router */
    protected $router;

    protected $serverAjax = array(
        'HTTP_X_Requested_With' => 'XMLHttpRequest'
    );

    protected $serverJson = array(
        'HTTP_ACCEPT' => 'application/json',
    );

    protected $serverXml = array(
        'HTTP_ACCEPT' => 'application/xml',
    );


    protected function setUp()
    {
        $_POST = array();
        $_GET = array();
        $_FILES = array();
        $this->request = Request::create('/');
        $this->session = new Session(new MockArraySessionStorage());
        $this->session->set('user_id', null);
        $this->request->setSession($this->session);
        $this->session->start();
        \App::cms()->getRouter()->setRequest($this->request);
    }

    protected function createCrawler(Response $response)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getContent());
        return $crawler;
    }

    /**
     * @param        $controller
     * @param string $action
     *
     * @return Response
     */
    protected function runController($controller, $action = 'index')
    {
        $this->request->clearFeedback();
        $this->request->setController($controller);
        $this->request->setAction($action);
        $_GET && $this->request->query->replace($_GET);
        $_POST && $this->request->request->replace($_POST);
        if (!$this->request->headers->has('Accept')) {
            $this->request->headers->add(array('Accept' => 'text/html'));
        }
        return \App::cms()->handleRequest($this->request);
    }

    /**
     * @param        $uri
     * @param string $method
     * @param array  $parameters
     * @param array  $cookies
     * @param array  $files
     * @param array  $server
     * @param null   $content
     *
     * @return Response
     */
    protected function runRequest($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $server['HTTP_USER_AGENT'] = 'SiteForeverCMS';
        $this->request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);
        $this->request->setSession($this->session);
        return \App::cms()->handleRequest($this->request);
    }

    /**
     * Click by link and get response
     * @param Crawler $crawlerLink
     *
     * @return null|Response
     */
    protected function click(Crawler $crawlerLink, $method = 'GET', $ajax = false)
    {
        $server = array();
        if ($ajax) {
            $server += $this->serverAjax;
        }
        return $this->runRequest($crawlerLink->attr('href'), $method, array(), array(), array(), $server);
    }

    /**
     * Following to redirect header
     * @param RedirectResponse $response
     *
     * @return Response
     */
    protected function followRedirect(RedirectResponse $response)
    {
        $this->assertTrue($response->isRedirection());
        return $this->runRequest($response->getTargetUrl());
    }
}
