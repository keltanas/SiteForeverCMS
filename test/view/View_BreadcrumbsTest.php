<?php

/**
 * Test class for View_Breadcrumbs.
 * Generated by PHPUnit on 2011-05-24 at 18:09:44.
 */
class View_BreadcrumbsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var View_Breadcrumbs
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new View_Breadcrumbs;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @return void
     */
    public function testRender()
    {
        $pathes = array(
            array(
                'name'  => 'Главная',
                'url'   => '/',
            ),
            array(
                'name'  => 'О компании',
                'url'   => '/about',
            ),
        );

        $ser    = serialize( $pathes );

        $this->object->fromSerialize( $ser );
        
        $this->assertEquals("<a href='/'>Главная</a>".$this->object->getSeparator()."<a href='/about'>О компании</a>", $this->object->render());
    }

    public function testRenderJson()
    {
        $pathes = array(
            array(
                'name'  => 'Главная',
                'url'   => '/',
            ),
            array(
                'name'  => 'О компании',
                'url'   => '/about',
            ),
        );

        $ser    = json_encode( $pathes );

        $this->object->fromJson( $ser );

        $this->assertEquals("<a href='/'>Главная</a>".$this->object->getSeparator()."<a href='/about'>О компании</a>", $this->object->render());
    }

    public function testAddPiece()
    {
        $this->object->addPiece( '', 'Главная' );
        $this->object->addPiece( '/about', 'О компании' );

        $this->assertEquals("<a href='/'>Главная</a>".$this->object->getSeparator()."<a href='/about'>О компании</a>", $this->object->render());
    }

}
