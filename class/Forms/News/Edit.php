<?php
/**
 * Форма редактирования новости
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

use Sfcms\Model;

/**
 * Class Forms_News_Edit
 *
 * @property $id
 * @property $cat_id
 * @property $author_id
 * ...
 */
class Forms_News_Edit extends \Sfcms\Form\Form
{

    function __construct()
    {
        $app    = App::cms();

        $category   = Model::getModel('NewsCategory');
        $cats_data = $category->findAll();

        $cats   = array(0=>'Ничего не выбрано');
        foreach ( $cats_data as $_cd ) {
            $cats[$_cd['id']] = $_cd['name'];
        }

        parent::__construct(array(
            'name'      => 'news',
            'action'    => App::cms()->getRouter()->createServiceLink('news','edit'),
            'fields'    => array(
                'id'        => array('type'=>'int', 'value'=>null, 'hidden',),
                'cat_id'    => array(
                    'type'      =>  'select',
                    'value'     =>  '0',
                    'variants'  =>  $cats,
                    'label'     =>  'Категория',
                    //'hidden',
                ),
                'author_id' => array('type'=>'text', 'value'=>$app->getAuth()->getId(), 'label'=>'','hidden',),
                'name'      => array('type'=>'text', 'value'=>'', 'label'=>'Название', 'required',),
                'main'      => array(
                    'type'=>'checkbox',
                    'label'=>'Показывать на главной',
                    'value'=>'0',
                    'variants' => array('0' => 'Нет', '1' => 'Да'),
                ),
                'priority'  => array(
                    'type'=>'select',
                    'label'=>'Приоритет',
                    'value'=>'0',
                    'variants' => array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),
                ),

                'notice'    => array('type'=>'textarea', 'value'=>'', 'label'=>'Вступление',),
                'text'      => array('type'=>'textarea', 'value'=>'', 'label'=>'Текст',),
                'date'      => array('type'=>'date', 'label'=>'Дата',),
                'image'     => array('type'=>'text', 'class'=>'image', 'label' => 'Изображение'),
                'title'     => array('type'=>'text', 'value'=>'', 'label'=>'Заголовок',),
                'keywords'  => array('type'=>'text', 'value'=>'', 'label'=>'Ключевые слова',),
                'description'=> array('type'=>'text', 'value'=>'','label'=>'Описание',),
                'hidden'    => array(
                    'type'      => 'checkbox',
                    'label'     => 'Скрытое',
                    'value'     => '0',
                    'variants'  => array('Нет', 'Да'),
                ),
                'protected' => array(
                    'type'      => 'radio',
                    'label'     => 'Защита страницы',
                    'value'     => USER_GUEST,
                    'variants'  => Model::getModel('User')->getGroups(),
                ),

                'deleted'   => array('type'=>'int', 'value'=>'0', 'hidden'),

                'submit'    => array('type'=>'submit', 'value'=>'Сохранить'),
            ),
        ));
    }
}
