<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * ����:     modifier.number_format.php
 * ���:      modifier
 * ���:      number_format
 * ����������:  ������������� ����� php-�������� number_format
 * -------------------------------------------------------------
 */
function smarty_modifier_number_format( $number, $decimal = 2, $dec_point = ',', $thousands_sep = '' )
{
    return number_format( $number, $decimal, $dec_point, $thousands_sep );
}