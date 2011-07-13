<?php

/**
 * Test class for Siteforever_Html.
 * Generated by PHPUnit on 2011-07-08 at 13:30:47.
 */
class Siteforever_HtmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Siteforever_Html
     */
    protected $html;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->html = new Siteforever_Html;
        App::getInstance()->getConfig()->set('url.rewrite', true);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testHref()
    {
        $this->assertEquals(
            'href="/elcatalog/metaProduct/prodid/15"',
            $this->html->href( null, array("controller"=>"elcatalog", "action"=>"metaProduct", "prodid"=>15) )
        );
    }
}
