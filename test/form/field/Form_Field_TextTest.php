<?php
/**
 * Test class for Form_Field_Text.
 * Generated by PHPUnit on 2011-05-24 at 15:59:17.
 */
use Sfcms\Form\Form;
use Sfcms\Data\Field\Text;

class Form_Field_TextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Text
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $form   = new Form(array('name'=>'test','fields'=>array(
                'test'  => array(
                    'type'  => 'text',
                    'label' => 'test',
                    'value' => 'hello',
                ),
            )), App::getInstance()->getRequest() );

        $this->object = $form->getField('test');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset( $form );
    }

    public function testGetId()
    {
        $this->assertEquals('test_test', $this->object->getId());
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->object->getName());
    }

    public function testGetType()
    {
        $this->assertEquals('text', $this->object->getType());
    }

    public function testGetValue()
    {
        $this->assertEquals('hello', $this->object->getValue());
    }

    public function testGetStringValue()
    {
        $this->assertEquals('hello', $this->object->getStringValue());
    }

    public function testSetValue()
    {
        $this->object->setValue('123print');
        $this->assertEquals('123print', $this->object->getValue());
    }

    public function testGetLabel()
    {
        $this->assertEquals('test', $this->object->getLabel());
    }

    public function testSetLabel()
    {
        $this->object->setLabel('label');
        $this->assertEquals('label', $this->object->getLabel());
    }

    public function testValidate()
    {
        $this->assertTrue( $this->object->validate('ABC') );
    }

    public function testValidateRequired()
    {
        $this->object->setValue('');
        $this->object->setRequired();
        $this->assertFalse( $this->object->validate() );
        $this->object->setRequired(false);
        $this->assertTrue( $this->object->validate() );
    }

    public function testHtml()
    {
        $this->assertEquals("<div class=\"control-group\" data-field-name=\"test\">"
            ."<label for='test_test' class='control-label'>test</label>"
            ."<div class='controls field-text'>"
                ."<input id='test_test' type='text' class=\"input-xlarge\" name='test[test]' value='hello' />"
            ."</div></div>", $this->object->html());
    }

    public function testHtmlCustom()
    {
        $this->object->setLabel('Name');
        $this->object->setValue('Nikolay');
        $this->object->setRequired();

        $this->assertEquals("<div class=\"control-group\" data-field-name=\"test\">"
                ."<label for='test_test' class='control-label'>Name&nbsp;<b>*</b> </label>"
                ."<div class='controls field-text'>"
                    ."<input id='test_test' type='text' class=\"input-xlarge required\" name='test[test]' value='Nikolay' />"
                ."</div></div>", $this->object->html());
    }

    public function testHtmlHidden()
    {
        $this->object->hide();
        
        $this->assertEquals("<input type='hidden' name='test[test]' id='test_test' value='hello' />", $this->object->html());
    }
}
