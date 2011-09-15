<?php
/**
 * Модель для изображений галереи
 */

class Model_Gallery extends Model
{
    protected $form;

    /**
     * Получает следующую позицию для сортировки
     * @param $category_id
     * @return string
     */
    function getNextPosition( $category_id )
    {
        return $this->db->fetchOne(
             "SELECT MAX(pos)+1 "
            ."FROM `{$this->getTable()}` "
            ."WHERE category_id = ? "
            ."LIMIT 1",
            array($category_id)
        );
    }

    /**
     * @param Data_Object_Gallery $obj
     * @return bool
     */
    public function onSaveStart($obj = null)
    {
//        print "Проверить алиас страницы";
        /**
         * @var Model_Alias $alias_model
         */
        $alias_model    = $this->getModel('Alias');
        $alias          = $alias_model->findByAlias( $obj->getAlias() );
        if ( null !== $alias ) {
            if ( null === $obj ) {
                // если наш объект еще не создан, значит у кого-то уже есть такой алиас
                throw new ModelException('Такой алиас уже существует');
            } else {
                $route  = $obj->createUrl();
                if ( $alias->url != $route ) {
                    // если адреса не соответствуют
                    throw new ModelException('Такой алиас уже существует');
                }
            }
        }
        return true;
    }

    /**
     * @param Data_Object_Page $obj
     * @return bool
     */
    public function onSaveSuccess( $obj = null )
    {
        /**
         * @var Model_Alias $alias_model
         */
        $alias_model    = $this->getModel('Alias');

        $alias  = $alias_model->findByUrl($obj->createUrl());

        if ( null === $alias ) {
            $alias  = $alias_model->createObject();
        }

        $data    = $obj->getAttributes();
        if( $data['alias']!=''){
            $alias->alias   = $data['alias'];
        } else {
            $alias->alias   = $obj->getAlias();
        }

        $alias->url         = $obj->createUrl();
        $alias->controller  = 'gallery';
        $alias->action      = 'index';
        $alias->params      = serialize(array('id'=>$obj->getId()));
        $alias->save();

        try {
            if ( $obj->alias_id != $alias->getId() ) {
                $obj->alias_id  = $alias->getId();
                $obj->save();
            }
        } catch ( Exception $e ) {
            print $e->getMessage();
        }

        return true;
    }

    /**
     * Удалить изображения перед удаление объекта
     * @param int $id
     * @return void
     */
    public function onDeleteStart( $id )
    {
        $data = $this->find( $id );
        if ( $data ) {
            if ( $data['thumb'] && file_exists(ROOT.$data['thumb']) ) {
                @unlink ( ROOT.$data['thumb'] );
            }
            if ( $data['middle'] && file_exists(ROOT.$data['middle']) ) {
                @unlink ( ROOT.$data['middle'] );
            }
            if ( $data['image'] && file_exists(ROOT.$data['image']) ) {
                @unlink ( ROOT.$data['image'] );
            }
            return true;
        }
        return false;
    }

    /**
     * Пересортировка изображений
     * @return int
     */
    function reposition()
    {
        $positions = $this->request->get('positions');
        $new_pos = array();
        foreach ( $positions as $pos => $id ) {
            $new_pos[] = array('id'=>$id, 'pos'=>$pos);
        }
        return $this->db->insertUpdateMulti($this->getTable(), $new_pos);
    }

    /**
     * Переключение активности изображения
     * @param int $id
     * @return bool|int
     */
    function hideSwitch( $id )
    {
        if( ! $obj = $this->find( $id ) ) {
            return false;
        }
        if ( $obj['hidden'] ) {
            $obj['hidden'] = '0';
        }
        else {
            $obj['hidden'] = '1';
        }
        return $obj['hidden'] ? 1 : 2;
    }

    /**
     * @return form_Form
     */
    function getForm()
    {
        if ( is_null( $this->form ) ) {
            $this->form = new forms_gallery_image();
        }
        return $this->form;
    }
}
