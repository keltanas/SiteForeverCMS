<?php
/**
 * Test class for Form_Field_Text.
 * Generated by PHPUnit on 2011-05-24 at 15:59:17.
 */
use Sfcms\Form\Form;
use Sfcms\Data\Field\Text;

class Form_Field_TextTest extends \Sfcms\Test\WebCase
{
    /**
     * @var Text
     */
    protected $field;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $form   = new Form(array('name'=>'test','fields'=>array(
                'test'  => array(
                    'type'  => 'text',
                    'label' => 'test',
                    'value' => 'hello',
                ),
            )), $this->request );

        $this->field = $form->getField('test');
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
        $this->assertEquals('test_test', $this->field->getId());
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->field->getName());
    }

    public function testGetType()
    {
        $this->assertEquals('text', $this->field->getType());
    }

    public function testGetValue()
    {
        $this->assertEquals('hello', $this->field->getValue());
    }

    public function testGetStringValue()
    {
        $this->assertEquals('hello', $this->field->getStringValue());
    }

    public function testSetValue()
    {
        $this->field->setValue('123print');
        $this->assertEquals('123print', $this->field->getValue());
    }

    public function testGetLabel()
    {
        $this->assertEquals('test', $this->field->getLabel());
    }

    public function testSetLabel()
    {
        $this->field->setLabel('label');
        $this->assertEquals('label', $this->field->getLabel());
    }

    public function testValidate()
    {
        $this->assertTrue( $this->field->validate('ABC') );
    }

    public function testValidateRequired()
    {
        $this->field->setValue('');
        $this->field->setRequired();
        $this->assertFalse($this->field->validate());
        $this->field->setRequired(false);
        $this->assertTrue($this->field->validate());
    }

    public function testHtml()
    {
        $this->assertEquals("<div class=\"control-group\" data-field-name=\"test\">"
            ."<label for='test_test' class='control-label'>test</label>"
            ."<div class='controls field-text'>"
                ."<input id='test_test' type='text' class=\"input-xlarge\" name='test[test]' value='hello' />"
            ."</div></div>", $this->field->html());
    }

    public function testHtmlCustom()
    {
        $this->field->setLabel('Name');
        $this->field->setValue('Nikolay');
        $this->field->setRequired();

        $this->assertEquals("<div class=\"control-group\" data-field-name=\"test\">"
                ."<label for='test_test' class='control-label'>Name&nbsp;<b>*</b> </label>"
                ."<div class='controls field-text'>"
                    ."<input id='test_test' type='text' class=\"input-xlarge required\" name='test[test]' value='Nikolay' />"
                ."</div></div>", $this->field->html());
    }

    public function testHtmlHidden()
    {
        $this->field->hide();

        $this->assertEquals("<input type='hidden' name='test[test]' id='test_test' value='hello' />", $this->field->html());
    }
}
