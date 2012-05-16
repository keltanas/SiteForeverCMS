<?php
/**
 * Базовый класс формы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */

abstract class Form_Abstract
{
    /*
     * 1. Получить значения из POST
     * 2. Обработать значения по шаблону, соответственно типам
     * 3. Проверить заполнение обязательных полей
     * 4. Выводить форму в шаблоны
     * 5. Динамически управлять типами и видимостью полей
     * 6. Возвращать данные запроса в виде проверенного массива для создания Domain классов
     */

    const DEBUG = false;

    protected
        $_name,
        $_method,
        $_action,
        $_class,
        /**
         * Массив объектов полей формы
         */
        $_fields    = array(),
        /**
         * Массив кнопок
         */
        $_buttons   = array(),
        /**
         * Данные, полученные из _POST или _GET
         */
        $_data      = array();

    protected $_err_required  = 0;
    protected $_err_untype    = 0;

    protected $_feedback      = array();

    /**
     * Создает форму согласно конфигу
     * @param $config
     * @param Request $request
     */
    public function __construct( $config, Request $request = null )
    {
        if ( isset( $request )  ) {
            $request->addScript( $request->get('path.misc').'/jquery/jquery.form.js' );
            //$request->addScript( $request->get('path.misc').'/jquery/jquery.blockUI.js' );
            //$request->addScript( $request->get('path.misc').'/forms.js' );
        }

        if ( ! isset( $config['name'] ) ) {
            throw new Form_Exception('Для формы нужно определить обязательный параметр name');
        }
//        if ( ! isset( $config['fields'] ) ) {
//            throw new Exception('Для формы нужно определить массив полей fields');
//        }
        $this->_name   = $config[ 'name' ];
        $this->_method = isset( $config[ 'method' ] ) ? $config[ 'method' ] : 'post';
        $this->_action = isset( $config[ 'action' ] ) ? $config[ 'action' ] : '';
        $this->_class  = isset( $config[ 'class' ] ) ? $config[ 'class' ] : 'standart ajax';

        if ( isset( $config['fields'] ) ) {
            foreach ( $config[ 'fields' ] as $fname => $field )
            {
                // Обработка HTML
                if ( is_string( $field ) ) {
                    $this->_fields[ ] = $field;
                    continue;
                }

                try {
                    $obj_field = $this->createField( $fname, $field );
                } catch ( Form_Exception $e ) {
                    $this->addFeedback('Field "'.$fname.'" has undefined type');
                    continue;
                }

                if ( isset($field['type']) && in_array( $field[ 'type' ], array( 'submit', 'reset', 'button' ) ) ) {
                    $this->addButton( $obj_field );
                }
                else {
                    $this->addField( $obj_field );
                }
            }
        }
    }

    /**
     * @param Form_Field $field
     * @param string $after
     * @return void
     */
    public function addField( Form_Field $field, $after = '' )
    {
        if ( ! $after ) {
            $this->_fields[ $field->getId() ]   = $field;
            return;
        }

        $fields = $this->_fields;
        $this->_fields  = array();

        foreach ( $fields as $key => $field ) {
            $this->_fields[ $key ]  = $field;
            if ( $key == $after ) {
                $this->_fields[ $field->getId() ]   = $field;
            }
        }
        return;
    }

    /**
     * @param Form_Field $field
     */
    public function addButton( Form_Field $field ) {
        $this->_buttons[ $field->getId() ]  = $field;
    }

    /**
     * Создаст поле для формы
     * @param $name
     * @param $field
     * @return Form_Field
     */
    public function createField( $name, $field )
    {
        $hidden = false;

        $field[ 'type' ] = isset( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';

        // тип hidden преобразовать в скрытый text
        if ( $field[ 'type' ] == 'hidden' /*|| in_array( 'hidden', $field )*/ ) {
            $hidden            = true;
            $field[ 'type' ]   = 'text';
            $field[ 'hidden' ] = 1;
        }

        // физический класс обработки поля
        $field_class = 'Form_Field_' . $field[ 'type' ];

        // экземпляр поля
        if ( !class_exists( $field_class ) ) {
            throw new  Form_Exception( 'Class not found ' . $field_class );
        }

        /** @var Form_Field $obj_field */
        $obj_field = new $field_class( $this, $name, $field );

        if ( $hidden ) {
            $obj_field->hide();
        }

        return $obj_field;
    }

    /**
     * Очищает значения полей формы
     */
    public function clear()
    {
        foreach( $this->_fields as $field ) {
            /** @var $field Form_Field */
            if ( is_object( $field ) )
                $field->clear();
        }
    }

    /**
     * Вернет значение поля формы по имени
     * @param $key
     * @return mixed
     */
    public function __get( $key )
    {
        return $this->getField( $key );
    }

    /**
     * Установит значение полю
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function __set( $key, $value )
    {
        try {
            $this->getField( $key )->setValue( $value );
            return true;
        } catch ( Form_Exception $e ) {
            return false;
        }
    }

    /**
     * Вернет поле формы по имени
     * @param $key
     * @return Form_Field
     */
    public function getField( $key )
    {
        $id = $this->_name.'_'.$key;
        if ( isset( $this->_fields[$id] ) )
        {
            return $this->_fields[$id];
        }
        return null;
//        throw new Form_Exception("Field '{$key}' not found");
    }

    /**
     * Дернет из запроса значения полей
     * @return bool
     */
    public function getPost()
    {
        if ( $this->isSent() )
        {
            $data = $this->_data;

            /**
             * @var $field form_Field
             */
            foreach ( $this->_fields as $field )
            {
                if ( is_object( $field ) ) {
                    if ( isset( $data[ $field->getName() ] ) )
                    {
                        $field->setValue( $data[ $field->getName() ] );
                    }
                    if ( $field->getType() == 'file' ) {
                        $field->setValue('');
                    }
                }
            }
            //reg::set('ajax', true);
            return true;
        }
        return false;
    }

    /**
     * Отправлена ли форма?
     * @return bool
     */
    public function isSent()
    {
        if ( isset( $_REQUEST[ $this->_name ] ) ) {
            $this->_data = $_REQUEST[ $this->_name ];
            return true;
        }
        return false;
    }

    /**
     * Установит или вернет значение имени формы
     * @param string $name
     * @return string|void
     */
    public function name( $name = '' )
    {
        if ( $name ) {
            $this->_name = $name;
        } else {
            return $this->_name;
        }
    }

    /**
     * Вернет массив значений
     * Как правило, нужно для использования с базой данных
     * @param $toString
     * @return array
     */
    public function getData( $toString = false )
    {
        $data = array();
        foreach( $this->_fields as $field ) {
            /** @var $field Form_Field */
            if ( is_object( $field ) ) {
                if ( ! in_array( $field->getType(), array('submit', 'separator', 'captcha') ) )
                {
                    if ( $toString ) {
                        $value = $field->getStringValue();
                    } else {
                        $value = $field->getValue();
                        if ( is_array( $value ) && count( $value ) == 1 ) {
                            $value = join( '', $value );
                        }
                    }

                    $data[ $field->getName() ] = $value;
                }
            }
        }
        return $data;
    }

    /**
     * Установит массив значений
     * Как правило, нужно для использования с базой данных
     * @param $data
     * @return array
     */
    public function setData( $data )
    {
        if ( count($this->_fields) == 0 ) {
            throw new Exception( 'Форма не содержит полей' );
        }

        foreach( $this->_fields as $field ) {
            /** @var $field Form_Field */
            if ( is_object( $field ) && ! in_array( $field->getType(), array('submit', 'separator') ) ) {
                if ( isset($data[ $field->getName() ]) ) {
                    $value = $data[ $field->getName() ];
                    $field->setValue( $value );
                }
            }
        }
        return true;
    }

    /**
     * Проверка валидности формы
     * @return bool
     */
    public function validate()
    {
        $valid = true;
        foreach( $this->_fields as $field ) {
            if( is_object( $field ) ) {
                /** @var $field Form_Field */
                $ret = $field->validate();
                $valid &= ($ret == 1) ? true : false;
                switch ( $ret ) {
                    case -1:
                        $this->_err_untype++;
                        break;
                    case -2:
                        $this->_err_required++;
                        break;
                }
            }
        }
        return $valid;
    }



    /**
     * Добавит сообщение обратной связи
     * @param  $msg
     * @return void
     */
    public function addFeedback( $msg )
    {
        array_push( $this->_feedback, $msg );
    }

    /**
     * Вернет сообщения обратной связи как массив
     * @return array
     */
    public function getFeedback()
    {
        return $this->_feedback;
    }

    /**
     * Вернет сообщения обратной связи как строку
     * @param string $sep
     * @return string
     */
    public function getFeedbackString( $sep = "<br />\n" )
    {
        return join( $sep, $this->_feedback );
    }


    /**
     * Изменит тип поля
     * @param $type
     */
    public function changeFieldType( $type )
    {

    }
}
