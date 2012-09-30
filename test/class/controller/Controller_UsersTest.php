<?php

require_once 'controller/users.php';

/**
 * Test class for Controller_Users.
 * Generated by PHPUnit on 2012-04-20 at 20:50:17.
 */
class Controller_UsersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Controller_Users
     */
    protected $controller;

    /**
     * @var App
     */
    protected $app;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->app = App::getInstance();
        $this->controller = new Controller_Users( $this->app );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Инициализвция
     */
    public function testInit()
    {
        $this->assertEquals('inner', $this->app->getRequest()->get('template') );
    }

    /**
     * Права доступа
     */
    public function testAccess()
    {
        $access = $this->controller->access();
        $this->assertArrayHasKey('system', $access);
        $this->assertContains('admin', $access['system']);
        $this->assertContains('adminEdit', $access['system']);
        $this->assertContains('save', $access['system']);
    }

    /**
     * Действие по умолчанию
     */
    public function testIndexAction()
    {
        $return = $this->controller->indexAction( null, null );
        $result = $this->app->getRequest()->getContent();

//        var_dump( $return );
//        var_dump( $result );

        $this->assertStringStartsWith('<form action="/users/login" class="form-horizontal"', trim( $return ) );
        $this->assertEmpty( $result );
    }

    /**
     * Действие админа
     */
    public function testAdminAction()
    {
        $return = $this->controller->adminAction();
        $result = $this->app->getRequest()->getContent();
        $this->assertEmpty( $result );
        $this->assertStringStartsWith('<form action', $return);
    }

    /**
     * Редактирование пользователя в админке
     */
    public function testAdminEditAction()
    {
        $return = $this->controller->adminEditAction( null );
        $result = $this->app->getRequest()->getContent();
        $this->assertArrayHasKey('form', $return );
        $this->assertEmpty( $result );
    }

    /**
     * Сохранение
     */
    public function testSaveAction()
    {
        $return = $this->controller->saveAction();
        $result = $this->app->getRequest()->getContent();

        $this->assertEmpty($result);
        $this->assertEquals('Data not sent', $return);
    }

    /**
     * Вход
     */
    public function testLoginAction()
    {
        $return = $this->controller->indexAction( null, null );
        $result = $this->app->getRequest()->getContent();
        //var_dump( $return );
        $this->assertStringStartsWith('<form action="/users/login" class="form-horizontal"', trim( $return ) );
        $this->assertEmpty( $result );
    }

    /**
     * Кабинет
     */
    public function testCabinetAction()
    {
//        $return = $this->controller->cabinetAction();
//        $result = $this->app->getRequest()->getContent();
    }

    /**
     * Правка профиля
     */
    public function testEditAction()
    {
        $return = $this->controller->editAction( null );
        $result = $this->app->getRequest()->getContent();
        //var_dump( $return );
        $this->assertEmpty($result);
        $this->assertInternalType('array', $return);
        $this->arrayHasKey('form', $return);
        $this->assertInstanceOf( '\Forms\User\Profile', $return['form']);
    }

    /**
     * Регистрация
     */
    public function testRegisterAction()
    {
        $return = trim( $this->controller->registerAction() );
        $result = $this->app->getRequest()->getContent();
        $this->assertEmpty($result);
        $this->assertStringStartsWith('<form action="" class="standart" enctype="multipart/form-data" id="form_register"', $return);
    }

    /**
     * Восстановление
     */
    public function testRestoreAction()
    {
        $return = $this->controller->restoreAction();
        $result = $this->app->getRequest()->getContent();

//        var_dump( $return['form']->html() );
        $this->assertEmpty($result);

        $this->assertInternalType('array', $return);
        $this->arrayHasKey('form', $return);
        $this->assertInternalType('string', $return['form']->html());
        $this->assertStringStartsWith(
            '<form action="/users/restore" class="form-horizontal" '
                . 'enctype="multipart/form-data" id="form_restore" method="post" name="form_restore"',
            $return['form']->html()
        );
    }

    public function testRecoveryAction()
    {
        $return = $this->controller->recoveryAction(null,null);
        $result = $this->app->getRequest()->getContent();

        $this->assertEmpty( $result );

        $this->assertInternalType('array',$return);
        $this->assertArrayHasKey('error', $return);
        $this->assertEquals(1, $return['error']);
        $this->assertArrayHasKey('msg', $return);
        $this->assertEquals("Не указаны параметры восстановления",$return['msg']);

        $return = $this->controller->recoveryAction('sdsadsd@ermin.ru','123232afsdfsdfs');
        $this->assertInternalType('array',$return);
        $this->assertArrayHasKey('error', $return);
        $this->assertEquals(1, $return['error']);
        $this->assertArrayHasKey('msg', $return);
        $this->assertEquals("Ваш email не найден",$return['msg']);

        $return = $this->controller->recoveryAction('admin@ermin.ru','123232afsdfsdfs');
        $this->assertInternalType('array',$return);
        $this->assertArrayHasKey('error', $return);
        $this->assertEquals(1, $return['error']);
        $this->assertArrayHasKey('msg', $return);
        $this->assertEquals("Неверный код восстановления",$return['msg']);
    }

    /**
     * Пароль
     */
    public function testPasswordAction()
    {
        $return = $this->controller->passwordAction();
        $result = $this->app->getRequest()->getContent();

        $this->assertEmpty($result);
        $this->assertStringStartsWith(
            '<form action="" class="form-horizontal" enctype="multipart/form-data" id="form_password"',
            $return
        );
    }
}
