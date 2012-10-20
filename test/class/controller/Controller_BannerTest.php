<?php

//require_once 'class/controller/banner.php';
use Module\Banner\Controller\BannerController;

/**
 * Test class for Controller_Banner.
 * Generated by PHPUnit on 2012-04-24 at 00:50:46.
 */
class Controller_BannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BannerController
     */
    protected $banner;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->banner = new BannerController( App::getInstance() );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Инициализация
     */
    public function testInit()
    {
    }

    /**
     * Главная страница
     */
    public function testIndexAction()
    {
    }

    /**
     * Админка
     */
    public function testAdminAction()
    {
    }

    /**
     * Редирект
     */
    public function testRedirectBannerAction()
    {
    }

    /**
     * Сохранить категорию
     */
    public function testSaveCatAction()
    {
    }

    /**
     * Удалить категорию
     */
    public function testDelCatAction()
    {
    }

    /**
     * Удалить баннер
     */
    public function testDelAction()
    {
    }

    /**
     * Категория
     */
    public function testCatAction()
    {
    }

    /**
     * Правка баннера
     */
    public function testEditAction()
    {
    }
}
