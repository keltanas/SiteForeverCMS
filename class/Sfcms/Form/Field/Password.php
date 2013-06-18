<?php
/**
 * Текстовое поле пароля
 * @author keltanas
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field;

class Password extends Field
{
    protected $_type      = 'password';

    public function htmlInput($field)
    {
        unset($field['value']);
        return parent::htmlInput($field);
    }


}
