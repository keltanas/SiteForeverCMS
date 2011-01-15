<?php
/**
 * Модель новостей
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */


class model_news extends Model
{
    private $form = null;

    /**
     * @var model_NewsCategory
     */
    public $category;

    function Init()
    {
        $this->category = self::getModel( 'NewsCategory' );
    }

    function findAllWithLinks($crit = array())
    {
        $data_all   = $this->findAll( $crit );

        $list_id    = array();
        foreach ( $data_all as $news ) {
            /**
             * @var Data_Object_News $news
             */
            $list_id[]  = $news->cat_id;
        }

        $structure  = self::getModel('Structure');

        //printVar( Data_Watcher::instance()->dumpDirty() );
        $page_data_all = $structure->findAll(array(
              'select'  => 'id, link, alias',
              'cond'    => "deleted = 0 AND alias != 'index' AND controller = 'news' AND link IN (".join(',', $list_id).")"
          ));

        foreach( $data_all as $news ) {
            /**
             * @var Data_Object_Page $page
             */
            foreach ( $page_data_all as $page ) {
                if ( $news['cat_id'] == $page['link'] ) {
                    $news->alias   = $page->alias;
                    $news->markClean();
                    break;
                }
            }
        }

        //printVar( Data_Watcher::instance()->dumpNew() );

        return $data_all;
    }

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_news_Edit();
        }
        return $this->form;
    }

    /**
     * @return string
     */
    public function tableClass()
    {
        return 'Data_Table_News';
    }

    /**
     * Класс для контейнера данных
     * @return string
     */
    public function objectClass()
    {
        return 'Data_Object_News';
    }
}
