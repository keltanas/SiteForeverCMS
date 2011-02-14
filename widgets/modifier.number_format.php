<?php
/**
 * Smarty plugin
 * Модификатор чисел
 * -------------------------------------------------------------
 * Файл:     modifier.number_format.php
 * Тип:      modifier
 * Имя:      number_format
 * Назначение:  Форматировать число php-функцией number_format
 * -------------------------------------------------------------
 */
function smarty_modifier_number_format( $number, $decimal = 2, $dec_point = ',', $thousands_sep = '' )
{
    if ( $number === "" )
        return "";
    
    return number_format( $number, $decimal, $dec_point, $thousands_sep );
}