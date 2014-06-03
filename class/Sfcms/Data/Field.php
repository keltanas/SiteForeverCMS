<?php
/**
 * Поле таблицы
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Data;

abstract class Field
{
    protected $name;
    protected $length;
    protected $null;
    protected $default;
    protected $autoincrement;

    public static $types = [
        'Sfcms\Data\Field\Datetime' => 'datetime',
        'Sfcms\Data\Field\Decimal' => 'decimal',
        'Sfcms\Data\Field\Int' => 'integer',
        'Sfcms\Data\Field\Tinyint' => 'integer',
        'Sfcms\Data\Field\Text' => 'text',
        'Sfcms\Data\Field\Blob' => 'blob',
        'Sfcms\Data\Field\Varchar' => 'string',
    ];

    /**
     * Создает поле
     * @param string $name
     * @param int $length
     * @param bool $notnull
     * @param string|null $default
     * @param bool $autoincrement
     */
    public function __construct($name, $length = 11, $notnull = false, $default = null, $autoincrement = false)
    {
        $this->name     = $name;
        $this->length   = $length;
        $this->null     = ! $notnull;
        $this->default  = $default;
        $this->autoincrement    = $autoincrement;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param null|string $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return null|string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param boolean $null
     */
    public function setNull($null)
    {
        $this->null = $null;
    }

    /**
     * @return boolean
     */
    public function isNull()
    {
        return $this->null;
    }

    /**
     * Имя поля
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement()
    {
        return $this->autoincrement;
    }

    /**
     * Вернет строку для вставки в SQL запрос
     * @abstract
     * @return string
     */
    abstract function toString();

    /**
     * Проверит значение на правильность
     * @abstract
     * @var mixed $value Значение
     * @return mixed
     */
    abstract function validate( $value );

}

