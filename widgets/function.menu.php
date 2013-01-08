<?php

use Sfcms\Model;
use Module\Page\Model\PageModel;

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.menu.php
 * Type:     function
 * Name:     menu
 * Purpose:  Выведет меню на сайте
 * -------------------------------------------------------------
 */
function smarty_function_menu($params, Smarty_Internal_Template $smarty)
{
    $parent   = isset( $params[ 'parent' ] ) ? $params[ 'parent' ] : 0;
    $level    = isset( $params[ 'level' ] ) ? $params[ 'level' ] : 0;
    $template = isset( $params[ 'template' ] ) ? $params[ 'template' ] : 'menu';
    $source   = isset( $params[ 'source' ] ) ? $params[ 'source' ] : 'widget';

    /** @var $model PageModel */
    $model  = Model::getModel('Page');

    if ( ! count( $model->parents ) ) {
        $model->createParentsIndex();
    }

    $smarty->assign(
        array(
            'parent'    => $parent,
            'level'     => $level,
            'currentId' => App::getInstance()->getRequest()->get( 'id' ),
            'parents'   => $model->parents,
        )
    );

    return $smarty->fetch("{$source}:{$template}.tpl");
}
