<?php
namespace Module\Page\Form;

use App;
use SimpleXMLElement;
use Sfcms\Model;

/**
 * Форма структуры сайта
 * @author keltanas
 * @link   http://ermin.ru
 * @link   http://siteforever.ru
 */
class PageForm extends \Sfcms\Form\Form
{
    public function __construct()
    {
        parent::__construct(
            array(
                'name'      => 'structure',
                'action'    => App::cms()->getRouter()->createServiceLink( 'page', 'save' ),
                'fields'    => array(
                    'id'         => array(
                        'type' => 'hidden',
                        'label'=> 'ID',
                        'value'=> null,
                    ),
                    'parent' => array(
                        'type' => 'select',
                        'label' => 'Parent',
                        'variants' => \App::cms()->getDataManager()->getModel('Page')->getSelectOptions(),
                        'value' => '0',
                    ),
                    'name'       => array(
                        'type' => 'text',
                        'label'=> 'Наименование',
                        'required'
                    ),
                    'template'   => array(
                        'type' => 'select',
                        'label'=> 'Шаблон',
                        'value'=> 'inner',
                        'variants' => $this->getTemplatesList(),
                        'required'
                    ),
                    //'uri'       => array('type'=>'text','label'=>'Псевдоним', 'value='=>'', 'hidden'),
                    'alias'      => array(
                        'type' => 'text',
                        'label'=> 'Адрес',
                        'required'
                    ),

                    'date'       => array(
                        'type' => 'date',
                        'label'=> 'Дата создания',
                        'value'=> time(),
                        'hidden'
                    ),
                    'update'     => array(
                        'type' => 'date',
                        'label'=> 'Дата обновления',
                        'value'=> time(),
                        'hidden'
                    ),

                    'pos'        => array(
                        'type' => 'int',
                        'label'=> 'Порядок сортировки',
                        'value'=> '0',
                        'readonly',
                        'hidden',
                    ),

                    'controller' => array(
                        'type'      => 'select',
                        'label'     => 'Контроллер',
                        'variants'  => App::cms()->getModel('Page')->getAvaibleModules(),
                        'required',
                    ),
                    'link'       => array(
                        'type' => 'int',
                        'label'=> 'Ссылка на раздел',
                        'value'=> '0',
//                        'hidden',
                    ),
                    'action'     => array(
                        'type' => 'text',
                        'label'=> 'Действие',
                        'required', 'readonly', 'hidden'
                    ),

                    'sort'       => array(
                        'type' => 'text',
                        'label'=> 'Сортировка',
                        'required', 'hidden'
                    ),

                    'title'      => array(
                        'type' => 'text',
                        'label'=> 'Заголовок'
                    ),
                    'keywords'   => array(
                        'type' => 'text',
                        'label'=> 'Ключевые слова'
                    ),
                    'description'=> array(
                        'type' => 'textarea',
                        'label'=> 'Описание',
                        'class' => 'plain',
                    ),
                    'nofollow'     => array(
                        'type'      => 'checkbox',
                        'label'     => 'No Follow',
                        'value'     => '0',
                    ),

                    'notice'     => array(
                        'type' => 'textarea',
                        'label'=> 'Вступление',
                        'value'=> '',
//                        'hidden'
                    ),
                    'content'    => array(
                        'type' => 'textarea',
                        'label'=> 'Текст',
                    ),

                    'thumb'      => array(
                        'type' => 'text',
                        'label'=> 'Иконка',
                        'class'=> 'image',
                    ),
                    'image'      => array(
                        'type' => 'text',
                        'label'=> 'Изображение',
                        'class'=> 'image',
                    ),


                    'author'     => array(
                        'type' => 'hidden',
                        'label'=> 'Автор',
                        'value'=> '1'
                    ),

                    'hidden'     => array(
                        'type'      => 'checkbox',
                        'label'     => 'Скрытое',
                        'value'     => '0',
                    ),
                    'protected'  => array(
                        'type'      => 'select',
                        'label'     => 'Защита страницы',
                        'value'     => USER_GUEST,
                        'variants'  => array(),
                    ),
                    'system'     => array(
                        'type'      => 'checkbox',
                        'label'     => 'Системный',
                        'value'     => '0',
                    ),
                ),
            )
        );
    }

    /**
     * Список шаблонов для нужной темы
     * @return array
     */
    protected function getTemplatesList()
    {
        $templates = array('index'=>'Main', 'inner'=>'Inner');

        $config = App::cms()->getContainer()->getParameter('template');
        $theme = $config['theme'];

        $themePath = ROOT . '/themes/' . $theme;
        $themeXMLFile = $themePath . '/theme.xml';

        if (file_exists($themeXMLFile)) {
            $themeXML = new SimpleXMLElement(file_get_contents($themeXMLFile));
            if (isset($themeXML->templates)) {
                $templates = array();
                /** @var $tpl SimpleXMLElement */
                foreach ($themeXML->templates->template as $tpl) {
                    $templates[(string) $tpl['value']] = (string) $tpl;
                }
            }
        }

        return $templates;
    }
}
