<?php
/**
 * Модуль новостей
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */

namespace Module\News;

use Sfcms\Module as SfModule;

class Module extends SfModule
{
    public static function relatedModel()
    {
        return 'NewsCategory';
    }
}
