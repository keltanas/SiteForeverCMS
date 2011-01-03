<?php
/**
 * Модель маршрутов
 */
class model_Routes extends Model
{

    function createTables()
    {
        $this->table    = new Data_Table_Routes();

        if ( ! $this->isExistTable($this->table) ) {
            $this->db->query($this->table->getCreateTable());

            $this->db->insert($this->table, array(
                 'pos'      => '0',
                 'alias'    => 'rss',
                 'controller'=>'rss',
                 'action'   => 'index',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '1',
                 'alias'    => 'admin/users/add',
                 'controller'=>'users',
                 'action'   => 'adminEdit',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '2',
                 'alias'    => 'admin/users/edit',
                 'controller'=>'users',
                 'action'   => 'adminEdit',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '3',
                 'alias'    => 'admin/edit.*',
                 'controller'=>'admin',
                 'action'   => 'edit',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '4',
                 'alias'    => 'admin/add.*',
                 'controller'=>'admin',
                 'action'   => 'add',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '5',
                 'alias'    => 'admin/users',
                 'controller'=>'users',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '7',
                 'alias'    => 'admin/settings',
                 'controller'=>'settings',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '8',
                 'alias'    => 'admin/routes',
                 'controller'=>'routes',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '9',
                 'alias'    => 'elfinder',
                 'controller'=>'elfinder',
                 'action'   => 'index',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '10',
                 'alias'    => 'admin/order',
                 'controller'=>'order',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '11',
                 'alias'    => 'admin/catalog',
                 'controller'=>'catalog',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '12',
                 'alias'    => 'admin/news',
                 'controller'=>'news',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '13',
                 'alias'    => 'admin',
                 'controller'=>'admin',
                 'action'   => 'index',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '14',
                 'alias'    => 'users/logout',
                 'controller'=>'users',
                 'action'   => 'logout',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '15',
                 'alias'    => 'users/edit',
                 'controller'=>'users',
                 'action'   => 'edit',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '16',
                 'alias'    => 'users/restore',
                 'controller'=>'users',
                 'action'   => 'restore',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '17',
                 'alias'    => 'users/register',
                 'controller'=>'users',
                 'action'   => 'register',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '18',
                 'alias'    => 'users/login',
                 'controller'=>'users',
                 'action'   => 'login',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '19',
                 'alias'    => 'users',
                 'controller'=>'users',
                 'action'   => 'index',
                 'system'   => '0',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '20',
                 'alias'    => 'templates/edit',
                 'controller'=>'templates',
                 'action'   => 'edit',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '21',
                 'alias'    => 'templates',
                 'controller'=>'templates',
                 'action'   => 'index',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '22',
                 'alias'    => 'system',
                 'controller'=>'system',
                 'action'   => 'index',
                 'system'   => '1',
            ));
            $this->db->insert($this->table, array(
                 'pos'      => '23',
                 'alias'    => 'admin/gallery',
                 'controller'=>'gallery',
                 'action'   => 'admin',
                 'system'   => '1',
            ));
        }
    }

    /**
     * Поиск всех маршрутов
     * Здесь можно подключить кэширование
     * и не обращаться к БД лишний раз
     * @param $cond
     * @param $order
     */
    function findAll( $cond = '' )
    {
        $where = '';
    	if ( $cond ) {
    		$where = " WHERE {$cond} ";
    	}
    	$data_all = $this->db->fetchAll("SELECT * FROM {$this->table} $where ORDER BY pos");
    	return $data_all;
    }



}