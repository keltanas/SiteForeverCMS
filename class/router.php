<?php
/**
 * Определяет, каким контроллером обрабатывать запрос
 * @author keltanas aka Ermin Nikolay
 * @link http://ermin.ru
 */

class Router
{
    private $route;

    private $route_table = array();

    private $controller = 'page';
    private $action = 'index';
    private $id;

    private $request;

    private $system = 0;

    private $template = 'index';

    /**
     * Создаем маршрутизатор
     * @param Request $request
     */
    public function __construct( Request $request )
    {
        $this->request = $request;
        $route = $this->request->get('route');

        if ( is_array( $route ) ) {
            $route = 'index';
        }

        $this->route = trim( $route, '/' );

        /*if ( $this->route == 'index' ) {
            header("location: /");
            exit();
        }*/

        if ( ! $this->route ) {
            $this->route = 'index';
        }

        if ( preg_match( '/[\w\d\/_-]+/i', $this->route ) ) {
            $this->route = trim( $this->route, ' /' );
        }

        // выделяем указатель на страницы
        if ( preg_match( '/\/page(\d+)/i', $this->route, $match_page ) ) {
            $this->request->set('page', $match_page[1]);
            $this->route = trim( str_replace( $match_page[0], '', $this->route ), '/' );
        }

        $params = explode('/', $this->route);

        foreach( $params as $key => $param ) {
            if ( preg_match( '/(\w+)=(.+)/xui', $param, $matches ) ) {
                $this->request->set( $matches[1], $matches[2] );
                unset( $params[$key] );
            }
        }

        $this->request->set('params', $params);

        $this->route = join('/', $params);

        //print $this->route;
        //printVar( App::$request->get('email') );
        //printVar( App::$request->get('code') );

        $this->request->set('route', $this->route);
    }

    /**
     * Вернет href для ссылки
     * @param string $url
     * @param array  $params
     * @return string
     */
    public function createLink( $url = '', $params = array() )
    {
        if ( null === $url && isset( $params['controller'] ) ) {
            return $this->createDirectRequest( $params );
        }

        $url = trim($url, '/');
        if ( '' === $url ) {
            $url = $this->request->get('route');
        }

        $par = array();

        if ( count($params) ) {
            foreach( $params as $k => $v ) {
                $par[] = $k.'='.$v;
            }
        }

        if ( 'index' == $url ) {
            $url = '';
        }

        if ( App::getInstance()->getConfig()->get('url.rewrite') ) {
            $url = '/'.$url.( count($par) ? '/'.join('/', $par) : '' );
        }
        else {
            $url = '/?route='.$url.( count($par) ? '&'.join('&', $par) : '' );
        }

        return $url;
    }

    /**
     * @param $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    public function createServiceLink( $controller, $action = 'index', $params = array() )
    {
        $result     = '';
        $parstring  = '';
        foreach ( $params as $key => $param ) {
            $parstring .= '/'.$key.'/'.$param;
        }

        $result .= '/'.$controller;
        if ('index' != $action || '' != $parstring ) {
            $result .= '/'.$action . $parstring;
        }

        if ( ! App::getInstance()->getConfig()->get('url.rewrite') ) {
            $result = '/?route='.trim( $result, '/' );
        }

        if ('index' == $action && 'index' == $controller && '' == $parstring ) {
            $result = '/';
        }

        return $result;
    }

    /**
     * @param array $params
     * @return string
     */
    private function createDirectRequest( $params )
    {
        $controller = $params['controller'];
        unset( $params['controller'] );
        if ( isset( $params['action'] ) ) {
            $action = $params['action'];
            unset( $params['action'] );
        } else {
            $action = 'index';
        }

        return $this->createServiceLink( $controller, $action, $params );
    }

    /**
     * Маршрутизация
     * @return bool
     */
    public function routing()
    {
        // Если контроллер и действие указаны явно, то не производить маршрутизацию
        if ( $this->request->get('controller') ) {
            if ( ! $this->request->get('action') ) {
                $this->request->set('action', 'index');
            }
            return true;
        }

        if ( ! $this->findAlias() ) {
            if ( ! $this->findRoute() ) {
                if ( ! $this->findStructure() ) {

                    $route_pieces   = explode( '/', $this->route );

                    if ( count( $route_pieces ) == 1 ) {
                        $this->controller   = $route_pieces[0];
                        $this->action   = 'index';
                    }
                    elseif ( count( $route_pieces ) > 1 ) {
                        $this->controller   = $route_pieces[0];
                        $this->action       = $route_pieces[1];

                        $route_pieces       = array_slice( $route_pieces, 2 );

                        if ( 0 == count( $route_pieces ) % 2 ) {
                            $key    = '';
                            foreach ( $route_pieces as $i => $r ) {
                                if ( $i % 2 ) {
                                    $this->request->set( $key, $r );
                                } else {
                                    $key    = $r;
                                }
                            }
                        }
                    }
                    else {
                        $this->activateError();
                    }
                }
            }
        }

        $this->request->set('controller', $this->controller);
        $this->request->set('action',     $this->action);
        if ( $this->id )        $this->request->set('id',         $this->id);
        if ( $this->template )  $this->request->set('template',   $this->template);
        return true;
    }

    public function activateError( $error = '404' )
    {
        $this->controller   = 'page';
        $this->action       = 'error';
        $this->id           = $error;
        $this->template     = App::getInstance()->getConfig()->get('template.404');
        $this->system       = 0;
    }

    /**
     * @return bool
     */
    private function findAlias()
    {
        $model  = Model::getModel('Alias');
        $alias  = $model->find(
            array(
                'cond'  => 'alias = ?',
                'params'=> array($this->route),
            )
        );
        if ( $alias ) {
            $this->controller   = $alias->controller;
            $this->action       = $alias->action;
            $params = $alias->getParams();
            if ( $params && is_array( $params ) ) {
                foreach ( $params as $key => $val ) {
                    $this->request->set( $key, $val );
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Поиск по маршрутам
     * @return bool
     */
    private function findRoute()
    {
        if ( $this->findXMLRoute() ) {
            return true;
        } elseif ( $this->findTableRoute() ) {
            return true;
        }
        return false;
    }

    /**
     * Ищем маршрут в XML конфиге
     * @return bool
     */
    private function findXMLRoute()
    {
        $xml_routes_file    = SF_PATH.'/protected/routes.xml';
        if ( file_exists( $xml_routes_file ) ) {
            $xml_routes = new SimpleXMLIterator( file_get_contents( $xml_routes_file ) );
            if ( $xml_routes ) {
                foreach ( $xml_routes as $route ) {
                    if ( $route['active'] !== "0" && preg_match( '@^'.$route['alias'].'$@ui', $this->route ) ) {
                        $this->controller   = (string) $route->controller;
                        $this->action       = isset($route->action) ? (string) $route->action : 'index';
                        $this->id           = $route['id'];
                        $this->protected    = $route['protected'];
                        $this->system       = $route['system'];
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Поиск маршрута в таблице БД
     * @return bool
     */
    private function findTableRoute()
    {
        $routes = Model::getModel('Routes');

        $this->route_table = $routes->findAll( array(
            'cond' => 'active = 1',
        ));

        // индексируем маршруты
        foreach( $this->route_table as $route )
        {
            // если маршрут совпадает с алиасом, то сохраняем
            if ( preg_match( '@^'.$route['alias'].'$@ui', $this->route ) )
            {
                $this->controller   = $route['controller'];
                $this->action       = $route['action'];
                if ( isset($route['id']) )      $this->id       = $route['id'];
                if ( isset($route['system']) )  $this->system   = $route['system'];

                return true;
            }
        }
        return false;
    }

    /**
     * Поиск по структуре
     * @return bool
     */
    private function findStructure()
    {
        $model  = Model::getModel('Page');

        $data   = $model->find(array(
            'cond'  => 'alias = ? AND deleted = 0',
            'params'=> array($this->route),
        ));

        if ( $data )
        {
            $this->controller   = $data['controller'];
            $this->action       = $data['action'];
            $this->id           = $data['id'];
            $this->template     = $data['template'];
            $this->system       = $data['system'];
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function isSystem()
    {
        return $this->system;
    }

    /**
     * @param $route
     * @return Router
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }
}
