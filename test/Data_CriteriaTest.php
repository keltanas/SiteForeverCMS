<?php
/**
 * Test class for Data_Criteria.
 * Generated by PHPUnit on 2011-02-07 at 10:39:14.
 */
class Data_CriteriaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data_Criteria
     */
    protected $object;
    protected $table;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ( ! isset( $this->table ) ) {
            $this->table  = new Data_Table_Page();
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Проверка Criteria::getSQL
     */
    public function testGetSQL()
    {
        $criteria   = new Data_Criteria($this->table, array());

        $sql    = "SELECT * FROM `{$this->table}`";

        $this->assertEquals( $sql, $criteria->getSQL() );

        //print( "\n".preg_replace('/\s+/', ' ', $criteria->getSQL() )."\n" );

        $criteria   = new Data_Criteria($this->table, array(
            'cond'      => 'param1 = :param1 AND param2 = :param2 AND par3 = ? AND par4 = ?',
            'params'    => array(':param1'=>'foo1', ':param2'=>'foo2', 'foo3', 'foo4'),
            'order'     => 'pos DESC',
            'limit'     => '1, 2'
        ));

        $sql    = "SELECT * FROM `{$this->table}` ".
                "WHERE param1 = 'foo1' AND param2 = 'foo2' AND par3 = 'foo3' AND par4 = 'foo4' ".
                "ORDER BY pos DESC LIMIT 1, 2";

        $this->assertEquals( $sql, $criteria->getSQL() );

        $criteria   = new Data_Criteria( $this->table, array('cond'=>'deleted = 0','order' => 'pos',));

        $sql    = "SELECT * FROM `{$this->table}` WHERE deleted = 0 ORDER BY pos";
        $this->assertEquals($criteria->getSQL(), $sql);

        $criteria   = new Data_Criteria( $this->table, array(
            'cond'      => ' id IN (:list) ',
            'params'    => array(':list'=>array(1,2,3)),
        ));

        $sql    = "SELECT * FROM `{$this->table}` WHERE id IN ('1','2','3')";

        $this->assertEquals( $criteria->getSQL(), $sql );
    }

    /**
     * Проверка Criteria::getParams
     */
    public function testGetParams()
    {
        $criteria   = new Data_Criteria($this->table, array(
            'params'    => array('test', ':test'=>'test'),
        ));
        $this->assertEquals( $criteria->getParams(), array('test', ':test'=>'test'), 'Params not correspond' );
    }
}
