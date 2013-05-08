<?php
/**
 * Абстрактный класс драйвера шаблона
 * @author KelTanas
 */
namespace Sfcms\Tpl;

use Sfcms\Component;
use Sfcms\View\Breadcrumbs;
use Sfcms\Exception;

abstract class Driver extends Component
{
    // движок шаблонизатора
    protected $engine = null;

    protected $_breacrumbs  = null;
    
    public function __call( $fname, $args )
    {
        throw new Exception(t("Interface TPL_Driver does not support the method {$fname}"));
    }
    
    abstract public function assign( $params, $value = null );
    
    abstract public function display( $tpl, $cache_id = null );

    abstract public function fetch( $tpl, $cache_id = null );
    
    abstract public function setTplDir( $dir );

    abstract public function getTplDir();

    abstract public function addTplDir($dir);

    abstract public function setCplDir( $dir );

    public function render($tpl, $params)
    {
        $this->assign($params);
        return $this->fetch($tpl);
    }

    public function set( $key, $value )
    {
        $this->assign($key, $value);
    }

    public function get( $key )
    {
        throw new Exception(t('Driver properties write only'));
    }

    /**
     * @return Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        if ( null === $this->_breacrumbs ) {
            $this->_breacrumbs  = new Breadcrumbs();
        }
        return $this->_breacrumbs;
    }
}