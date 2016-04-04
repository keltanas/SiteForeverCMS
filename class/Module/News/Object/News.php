<?php
/**
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Module\News\Object;

use Module\Page\Object\Page;
use Sfcms;
use Module\Page\Model\PageModel;
use Sfcms\Data\Object;
use Sfcms\Data\Field;

/**
 * Объект Новостей
 *
 * @property $id
 * @property $cat_id
 * @property $author_id
 * @property $alias
 * @property $name
 * @property $notice
 * @property $text
 * @property $date
 * @property $note
 * @property $title
 * @property $keywords
 * @property $description
 * @property $priority
 * @property $hidden
 * @property $protected
 * @property $deleted
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property Category $Category
 */
class News extends Object
{
    public function getTitle()
    {
        if (!empty($this->data['title'])) {
            return $this->data['title'];
        }

        return $this->data['name'];
    }

    public function getUrl()
    {
        /** @var $pageModel PageModel */
        $pageModel = $this->getModel('Page');
        /** @var $page Page */
        $page = $pageModel->findByControllerLink('news', $this->cat_id);
        if ( null !== $page ) {
            return  $page->alias . '/' . $this->alias;
        } else {
            return $this->alias;
        }
    }


    /**
     * Вернет список полей
     * @return array
     */
    protected static function doFields()
    {
        return array(
            new Field\Int('id', 11, true, null, true),
            new Field\Int('cat_id'),
            new Field\Int('author_id'),

            new Field\Varchar('alias', 250,true,''),
            new Field\Varchar('name', 250),
            new Field\Varchar('image', 250, false, ''),
            new Field\Tinyint('main', 1, false, 0),
            new Field\Tinyint('priority', 1, false, 0),

            new Field\Int('date'),

            new Field\Text('notice'),
            new Field\Text('text'),

            new Field\Varchar('title', 250),
            new Field\Varchar('keywords', 250),
            new Field\Varchar('description', 250),

            new Field\Varchar('note', 250),

            new Field\Tinyint('hidden', 1, false, 0),
            new Field\Tinyint('protected', 1, false, 0),

            new Field\Datetime('created_at'),
            new Field\Datetime('updated_at'),

            new Field\Tinyint('deleted', 1, false, 0),
        );
    }

    /**
     * Вернет имя таблицы
     * @return string
     */
    public static function table()
    {
        return 'news';
    }
}
