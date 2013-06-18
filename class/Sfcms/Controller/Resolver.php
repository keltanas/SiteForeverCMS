<?php
/**
 * Решает, какой выбрать контроллер
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */

namespace Sfcms\Controller;

use Sfcms\Controller;
use Sfcms\Component;
use ReflectionClass;
use RuntimeException;
use Sfcms\Request;
use Sfcms_Http_Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Resolver extends Component
{
    /** @var array */
    protected $_controllers;

    public function __construct()
    {
        $this->_controllers = $this->app()->getControllers();
    }

    /**
     * Берет данные из файла protected/controllers.php через метод $this->app()->getControllers() и на основе этого
     * конфига принимает решение о том, какой класс должен выполнять функции контроллера,
     * в каком файле и пространстве имен он находится.
     *
     * @param Request $request
     * @param $controller
     * @param $action
     * @param $moduleName
     *
     * @return array
     * @throws HttpException
     * @throws RuntimeException
     */
    public function resolveController(Request $request, $controller = null, $action = null, $moduleName = null)
    {
        if ( null === $controller ) {
            $controller = strtolower($request->getController());
        }
        if ( null === $action ) {
            $action = $request->getAction();
        }
        $actionMethod = strtolower($action) . 'Action';

        // Если не удалось определить контроллер, как известный, то инициировать ош. 404
        if (!isset($this->_controllers[$controller])) {
            throw new HttpException(404, 'Controller not found');
        }

        if (!is_array($this->_controllers[$controller])) {
            throw new RuntimeException(sprintf('Configuration of the controller "%s" should be an array', $controller));
        }

        $config = $this->_controllers[$controller];
        $moduleName = isset($config['module']) ? $config['module'] : $moduleName;

        if ( isset( $config['class'] ) ) {
            $controllerClass = $config['class'];
        } else {
            $controllerClass = 'Controller_' . ucfirst($controller);
        }

        if ( $moduleName ) {
            $module = $this->app()->getModule( $moduleName );
            $controllerClass = sprintf(
                '%s\\%sController',
                $module->getNs(),
                str_replace( '_', '\\', $controllerClass )
            );
        }

        return array('controller' => $controllerClass, 'action' => $actionMethod, 'module'=>$moduleName);
    }

    /**
     * Запуск контроллера
     * @param Request $request
     * @param array $command
     * @return null|string
     * @throws HttpException
     */
    public function dispatch(Request $request, array $command = array())
    {
        $result = null;

        if (!$command) {
            $command = $this->resolveController($request);
            if (!$command) {
                throw new HttpException(404, 'Controller not resolved');
            }
        }

        $this->log( $command, 'Command' );

        // если запрос является системным
        if ($this->app()->getRouter()->isSystem()) {
            if ($this->app()->getAuth()->currentUser()->hasPermission(USER_ADMIN)) {
                $this->setSystemPage($request);
            } else {
                throw new HttpException(403, $this->t('Access denied'));
            }
        }

        if (!class_exists($command['controller'])) {
            throw new HttpException(404, sprintf('Controller class "%s" not exists', $command['controller']));
        }

        $ref = new ReflectionClass($command['controller']);

        /** @var Controller $controller */
        $controller = $ref->newInstance($request);

        // Защита системных действий
        $access = $controller->access();

        $this->acl($request, $access, $command);

        try {
            $method = $ref->getMethod($command['action']);
        } catch( \ReflectionException $e ) {
            throw new HttpException(404, $e->getMessage());
        }
        $arguments = $this->prepareArguments($method, $request);
        $result = $method->invokeArgs($controller, $arguments);

        return $result;
    }


    /**
     * Подотавливает список аргументов для передачи в Action, на основе указанных параметров и проверяет типы
     * на основе правил, указанных в DocBlock
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function prepareArguments(\ReflectionMethod $method, Request $request)
    {
        $arguments    = array();
        $methodParams = $method->getParameters();
        $docComment   = $method->getDocComment();
        preg_match_all( '/@param (int|float|string|array) \$([\w_-]+)/', $docComment, $match );
        foreach( $methodParams as $param ) {
            $default = $param->isOptional() ? $param->getDefaultValue() : null;
            if ($request->query->has($param->name)) {
                // Фильтруем входные параметры
//                $arguments[$param->name] = $val;
                if (false !== ($key = array_search($param->name, $match[2]))) {
                    switch ($match[1][$key]) {
                        case 'int':
                            $arguments[$param->name] = $request->query->getDigits($param->name, $default);
                            break;
                        case 'float':
                            $arguments[$param->name] = $request->query->filter($param->name, $default, false, FILTER_VALIDATE_FLOAT);
                            break;
                        case 'string':
                            $arguments[$param->name] = $request->query->filter($param->name, $default, false, FILTER_SANITIZE_STRING);
                            break;
//                        case 'array':
//                            $arguments[$param->name] = $request->query->get($param->name);
                        default:
                            $arguments[$param->name] = $request->query->get($param->name, $default);
                    }
                }
            } else {
                $arguments[$param->name] = $default;
            }
        }
        return $arguments;
    }

    /**
     * Acl
     *
     * Проходит по массиву, предоставленному методом Access() контроллера
     *
     * Массив содержит в качестве ключей - группы пользователей, а в качестве значений - список методов
     * которые разрешены для этой группы
     */
    protected function acl(Request $request, array $access = null, array $command = array())
    {
        if ( $access && is_array($access) ) {
            foreach( $access as $perm => $ruleMethods ) {
                if ( 'system' == $perm ) {
                    $perm = USER_ADMIN;
                }
                $ruleMethods = is_string($ruleMethods) ? array_map( 'trim', explode(',',$ruleMethods) ) : $ruleMethods;
                if( ! is_array($ruleMethods) ) {
                    throw new RuntimeException('Expected string or array');
                }
                $ruleMethods = array_map( function($method){
                    if (false === stripos($method, 'action')) {
                        $method = strtolower($method) . 'Action';
                    }
                    return $method;
                }, $ruleMethods );
                if ( in_array( $command['action'], $ruleMethods ) ) {
                    if ( $this->app()->getAuth()->currentUser()->hasPermission( $perm ) ) {
                        if ( $perm == USER_ADMIN ) {
                            $this->setSystemPage($request);
                        }
                    } else {
                        throw new Sfcms_Http_Exception($this->t('Access denied'), 403);
                    }
                }
            }
        }
    }

    /**
     * Переводит систему на работу с админкой
     */
    private function setSystemPage(Request $request)
    {
        $request->setTemplate('index');
        $request->set('resource', 'system:');
        $request->set('modules', $this->app()->adminMenuModules());
    }
}
