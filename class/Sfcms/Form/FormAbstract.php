<?php
namespace Sfcms\Form;

/**
 * Базовый класс формы
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://ermin.ru
 * @link   http://standart-electronics.ru
 */
use Sfcms\Form\Exception;
use Sfcms\Request;

abstract class FormAbstract
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
        $name,
        $method,
        $action,
        $class,
        /** Массив объектов полей формы */
        $fields    = array(),
        /** Массив кнопок */
        $buttons   = array(),
        /** Данные, полученные из _POST или _GET */
        $data      = array();

    protected $err_required  = 0;
    protected $err_untype    = 0;

    protected $feedback      = array();

    protected $errors = array();


    /**
     * Создает форму согласно конфигу
     * @param         $config
     *
     * @throws Exception
     */
    public function __construct($config)
    {
        if ( ! isset( $config['name'] ) ) {
            throw new Exception('Require argument "name"');
        }
        $this->name   = $config['name'];
        $this->method = isset($config['method']) ? $config['method'] : 'post';
        $this->action = isset($config['action']) ? $config['action'] : '';
        $this->class  = isset($config['class']) ? $config['class'] : 'form-horizontal';

        if (isset($config['fields'])) {
            foreach ($config['fields'] as $fname => $field) {
                // Обработка HTML
                if (is_string($field)) {
                    $this->fields[] = $field;
                    continue;
                }

                try {
                    $obj_field = $this->createField($fname, $field);
                } catch (Exception $e) {
                    $this->addFeedback('Field "' . $fname . '" has undefined type');
                    continue;
                }

                if (isset($field['type']) && in_array($field['type'], array('submit', 'reset', 'button'))) {
                    $this->addButton($obj_field);
                } else {
                    $this->addField($obj_field);
                }
            }
        }
    }

    /**
     * Зарегистрировать ошибку от поля
     * @param $field
     * @param $msg
     */
    public function addError( $field, $msg )
    {
        $this->errors[ $field ] = $msg;
    }


    /**
     * @param Field $field
     * @param string $after
     * @return void
     */
    public function addField( Field $field, $after = '' )
    {
        if ( ! $after ) {
            $this->fields[ $field->getId() ]   = $field;
            return;
        }

        $fields = $this->fields;
        $this->fields  = array();

        foreach ( $fields as $key => $field ) {
            $this->fields[ $key ]  = $field;
            if ( $key == $after ) {
                $this->fields[ $field->getId() ]   = $field;
            }
        }
        return;
    }

    /**
     * @param Field $field
     */
    public function addButton( Field $field ) {
        $this->buttons[ $field->getId() ]  = $field;
    }

    /**
     * Создаст поле для формы
     * @param $name
     * @param $field
     * @return Field
     */
    public function createField($name, $field)
    {
        $hidden = false;

        $field['type'] = isset($field['type']) ? $field['type'] : 'text';

        // тип hidden преобразовать в скрытый text
        if ($field['type'] == 'hidden' /*|| in_array( 'hidden', $field )*/) {
            $hidden          = true;
            $field['type']   = 'text';
            $field['hidden'] = 1;
        }

        // физический класс обработки поля
        $field_class = '\\Sfcms\\Form\\Field\\' . ucfirst(strtolower($field['type']));

        // экземпляр поля
        if (!class_exists($field_class)) {
            throw new Exception('Class not found ' . $field_class);
        }

        /** @var Field $obj_field */
        $obj_field = new $field_class($this, $name, $field);

        if ($hidden) {
            $obj_field->hide();
        }

        return $obj_field;
    }

    /**
     * Очищает значения полей формы
     */
    public function clear()
    {
        foreach( $this->fields as $field ) {
            /** @var $field Field */
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
        return $this->getField( $key )->getValue();
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
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Вернет поле формы по имени
     * @param $key
     * @return Field
     * @throws Exception
     */
    public function getField( $key )
    {
        $id = $this->name.'_'.$key;
        if ( isset( $this->fields[$id] ) )
        {
            return $this->fields[$id];
        }
        throw new Exception("Field '{$key}' not found");
    }

    /**
     * Дернет из запроса значения полей
     * @param Request $request
     *
     * @return bool
     */
    public function getPost(Request $request)
    {
        if ($this->isSent($request)) {
            $data = $this->data;

            /**
             * @var $field Field
             */
            foreach ($this->fields as $field) {
                if (is_object($field)) {
                    if (isset($data[$field->getName()])) {
                        $field->setValue($data[$field->getName()]);
                    }
                    if ($field->getType() == 'file') {
                        $field->setValue($request->files->get($field->getName()));
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Отправлена ли форма?
     * @param Request $request
     *
     * @return bool
     */
    public function isSent(Request $request)
    {
        if ($request->request->has($this->name)) {
            $this->data = $request->request->get($this->name);

            return true;
        }

        return false;
    }

    /**
     * Установит или вернет значение имени формы
     * @param string $name
     * @return string|void
     */
    public function name($name = '')
    {
        if ($name) {
            $this->name = $name;
        } else {
            return $this->name;
        }
        return $this;
    }

    /**
     * Вернет массив значений
     * Как правило, нужно для использования с базой данных
     * @param $toString
     * @return array
     */
    public function getData($toString = false)
    {
        $data = array();
        foreach ($this->fields as $field) {
            /** @var $field Field */
            if (is_object($field)) {
                if (!in_array($field->getType(), array('submit', 'separator', 'captcha'))) {
                    if ($toString) {
                        $value = $field->getStringValue();
                    } else {
                        $value = $field->getValue();
                        if (is_array($value) && count($value) == 1) {
                            $value = join('', $value);
                        }
                    }

                    $data[$field->getName()] = $value;
                }
            }
        }
        return $data;
    }

    /**
     * Settings array of values
     * Как правило, нужно для использования с базой данных
     *
     * @param array|\ArrayAccess $data
     * @return bool
     * @throws Exception
     */
    public function setData($data)
    {
        if (count($this->fields) == 0) {
            throw new Exception('Form has not fields');
        }

        if ($data) {
            foreach ($this->fields as $field) {
                /** @var $field Field */
                if (is_object($field) && !in_array($field->getType(), array('submit', 'separator'))) {
                    if (isset($data[$field->getName()])) {
                        $value = $data[$field->getName()];
                        $field->setValue($value);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Check for valid form values
     * @return bool
     */
    public function validate()
    {
        $valid = true;
        foreach( $this->fields as $field ) {
            if( is_object( $field ) ) {
                /** @var $field Field */
                $ret = $field->validate();
                $valid &= ($ret == 1) ? true : false;
            }
        }
        return $valid;
    }


    /**
     * Return error list as JSON
     * @return string
     */
    public function getJsonErrors()
    {
        return json_encode( $this->errors );
    }

    /**
     * Return all errors as array
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * Добавит сообщение обратной связи
     * @param  $msg
     * @return void
     */
    public function addFeedback( $msg )
    {
        array_push( $this->feedback, $msg );
    }

    /**
     * Вернет сообщения обратной связи как массив
     * @return array
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Вернет сообщения обратной связи как строку
     * @param string $sep
     * @return string
     */
    public function getFeedbackString( $sep = "<br />\n" )
    {
        return join( $sep, $this->feedback );
    }


    /**
     * Изменит тип поля
     * @param $type
     */
    public function changeFieldType( $type )
    {

    }

    public function setAction( $action )
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setClass( $class )
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setName( $name )
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setMethod( $method )
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
