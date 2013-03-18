<?php
use Sfcms\Model;
use Sfcms\db;
use Sfcms\Kernel\Base as Kernel;
use Module\System\Object\Test as TestObject;
use Sfcms\Data\Watcher;
use Sfcms\Data\Collection;

/**
 * Test class for Model.
 * Generated by PHPUnit on 2011-02-07 at 18:49:00.
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $model = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ( null === $this->model ) {
            $this->model = Model::getModel('\\Module\\System\\Model\\TestModel');
        }
        Watcher::instance()->clear();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     *
     */
    public function testGetDB()
    {
        $this->assertTrue( $this->model->getDB() instanceof db );
    }

    /**
     *
     */
    public function testApp()
    {
        $this->assertTrue( $this->model->app() instanceof Kernel );
    }

    /**
     *
     */
    public function testGetModel()
    {
        $this->assertTrue(
            $this->model->getModel('\\Module\\System\\Model\\TestModel') instanceof \Module\System\Model\TestModel
        );
    }

    /**
     *
     */
    public function testCreateObject()
    {
        /** @var $obj TestObject */
        $obj = $this->model->createObject();
        if ( ! $obj ) {
            $this->fail('Created object '.var_export($obj, true));
        }
        $this->assertTrue( $obj instanceof TestObject );
        $obj->markClean();
    }

    /**
     *
     */
    public function testObjectClass()
    {
        $this->assertEquals($this->model->objectClass(), '\Module\System\Object\Test');
    }

    /**
     *
     */
    public function testTableClass()
    {
        $this->assertEquals($this->model->tableClass(), '\Module\System\Object\Test');
    }

    /**
     *
     */
    public function testGetTable()
    {
        $this->assertEquals( 'test', $this->model->getTable() );
    }

    /**
     *
     */
    public function testTable()
    {
        $object_class = $this->model->objectClass();
        $this->assertEquals( $object_class::table(), DBPREFIX.'test' );
    }

    /**
     *
     */
    public function testSave()
    {
        $obj1   = $this->model->createObject();
        $obj1->value = 'val1';
        $obj2   = $this->model->createObject();
        $obj2->value = 'val2';
        $this->assertNotNull( $this->model->save($obj1) );
        $this->assertNotNull( $this->model->save($obj2) );
    }

    /**
     *
     */
    public function testCount()
    {
        //$this->assertEquals($this->object->count(), 2);
    }

    /**
     *
     */
    public function testFind()
    {
        $obj    = $this->model->find(2);
        $this->assertNotNull($obj);
        $this->assertEquals($obj->getId(), 2);
        $this->assertEquals($obj->value, 'val2');

        $obj    = $this->model->find(1);
        $this->assertNotNull($obj);
        $this->assertEquals($obj->getId(), 1);
        $this->assertEquals($obj->value, 'val1');
    }

    /**
     *
     */
    public function testFindAll()
    {
        /** @var $all Collection */
        $all    = $this->model->findAll();
        $this->assertEquals( $all->count(), 2 );
        $this->assertEquals( $all->getData(), array(
            array('id'=>1,'value'=>'val1'),
            array('id'=>2,'value'=>'val2'),
        ));
    }

    /**
     *
     */
    public function testDelete()
    {
        $this->model->delete(1);
        $this->assertNull( $this->model->find(1) );

        $this->model->delete(2);
        $this->assertNull( $this->model->find(2) );
        
        $this->assertEquals($this->model->count(), 0);

        $pdo    = $this->model->getDB()->getResource();
        $pdo->exec("DROP TABLE `test`");
    }

//
//    public function testMigration()
//    {
//        $reflection = new ReflectionMethod( get_class( $this->object ), 'migration' );
//        $reflection->invoke( $this->object );
//    }
}
