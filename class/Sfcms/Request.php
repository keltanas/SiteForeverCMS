<?php
namespace Sfcms;

use App;
use Sfcms\Assets;
use Sfcms_Http_Exception as Exception;
use Sfcms\Kernel\KernelBase as Service;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Объект запроса
 */

class Request extends SymfonyRequest
{
    const TYPE_ANY  = '*/*';
    const TYPE_JSON = 'json';
    const TYPE_XML  = 'xml';

    private $_feedback = array();

    private $_error = 0;

    /** @var Response */
    private $_response;

    private $_title = '';
    private $_keywords = '';
    private $_description = '';

    /** @var App */
    private $_app;

    /**
     * @param \App $app
     */
    public function setApp($app)
    {
        $this->_app = $app;
    }

    /**
     * @return \App
     */
    public function app()
    {
        return $this->_app;
    }

    /**
     * Созание запроса
     */
//    public function __construct()
//    {
//        $this->request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
//        $this->request->setLocale($this->app()->getConfig('language'));
//        $this->set('resource', 'theme:');
//
//        if (in_array($this->request->getMimeType(static::TYPE_JSON), $this->request->getAcceptableContentTypes())) {
//            $this->request->setRequestFormat(static::TYPE_JSON, $this->request->getMimeType(static::TYPE_JSON));
//        }
//        if (in_array($this->request->getMimeType(static::TYPE_XML), $this->request->getAcceptableContentTypes())) {
//            $this->request->setRequestFormat(static::TYPE_XML, $this->request->getMimeType(static::TYPE_XML));
//        }
//
//        $this->_assets = new Assets();
//
//        if ($this->request->query->has('route')) {
//            $this->request->query->set('route', preg_replace('/\?.*/', '', $this->request->query->get('route')));
//        }
//

//    }
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        if ($this->getRequestUri()) {
            $q_pos = strrpos($this->getRequestUri(), '?');
            $req   = trim(substr($this->getRequestUri(), $q_pos + 1, strlen($this->getRequestUri())), '?&');
        }

        // дополняем request не учтенными значениями
        if (isset($req) && $opt_req = explode('&', $req)) {
            foreach ($opt_req as $opt_req_item) {
                $opt_req_item = explode('=', $opt_req_item);
                if (!$this->query->has($opt_req_item[0]) && isset($opt_req_item[1])) {
                    $this->query->set($opt_req_item[0], $opt_req_item[1]);
                }
            }
        }
    }



    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->get('_controller', $this->query->get('controller', null));
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->set('_controller', $controller);
    }


    /**
     * @return string
     */
    public function getModule()
    {
        return $this->get('_module', $this->query->get('module', null));
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->set('_module', $module);
    }


    /**
     * @return string
     */
    public function getAction()
    {
        return $this->get('_action', $this->query->get('action', 'index'));
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->set('_action', $action);
    }


    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->_keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->_keywords;
    }

    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function getAjax()
    {
        return $this->isAjax();
    }

    /**
     * Установить обработку аякс принудительно
     * @param bool   $ajax
     * @param string $type
     *
     * @return void
     */
    public function setAjax($ajax = false, $type = self::TYPE_ANY)
    {
//        $this->request->headers->set('Accept', $this->request->getMimeType($type));
        $this->setRequestFormat($type, $this->getMimeType($type));
        if ($ajax) {
            $this->headers->set('X-Requested-With', 'XMLHttpRequest');
        } else {
            $this->headers->set('X-Requested-With', null);
        }
    }

    /**
     * Является ли запрос аяксовым
     * @return bool
     */
    public function isAjax()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Тип запроса
     * @return string
     */
    public function getAjaxType()
    {
        return $this->getRequestFormat();
    }

    /**
     * Установить состояние ошибки
     * @param int $error
     *
     * @return void
     */
    public function setError($error)
    {
        $this->_error = $error;
    }

    /**
     * Вернуть состояние ошибки
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * Установить значение
     * @param $key
     * @param $val
     *
     * @return void
     */
    public function set($key, $val)
    {
        $this->attributes->set($key, $val);
    }

    /**
     * Установить контент страницы
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * Вернет заголовок страницы
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Установит имя шаблона для вида
     * @param  $tpl
     *
     * @return void
     */
    public function setTemplate($tpl)
    {
        $this->set('_template', $tpl);
    }

    /**
     * Вернет имя текущего шаблона для вида
     * @return array|string
     */
    public function getTemplate()
    {
        return $this->get('_template', $this->get('template', 'index'));
    }

    /**
     * Добавить сообщение
     * @param $msg
     *
     * @return void
     */
    public function addFeedback($msg)
    {
        if (is_string($msg)) {
            $this->_feedback[] = $msg;

            return;
        }
        if (is_array($msg)) {
            foreach ($msg as $m) {
                if (is_string($m)) {
                    $this->_feedback[] = $m;
                }
            }
        }
    }

    public function getFeedback()
    {
        return $this->_feedback;
    }

    public function getFeedbackString($sep = "<br />\n")
    {
        $ret = '';
        if (count($this->_feedback)) {
            $ret = join($sep, $this->_feedback);
        }

        return $ret;
    }

    /**
     * Добавить параметр в ответ
     * @param Response $response
     *
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
    }

    /**
     * Вернет респонс массивом
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}