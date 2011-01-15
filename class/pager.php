<?php
/**
 * 
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
class Pager implements ArrayAccess
{

    public  $page   = 1;
    public  $from   = 0;
    public  $to     = 0;
    public  $offset = 0;
    public  $perpage= 20;
    public  $count  = 0;
    public  $html   = '';
    public  $limit  = '';

    function __construct( $count, $perpage, $link = '' )
    {
        $pages  = ceil( $count / $perpage );

        if ( $pages < 2 ) {
            return;
        }

        $p      = array();

        $page   = App::$request->get('page');
        $page   = $page ? $page : 1;

        $link   = preg_replace('/\/page=\d+|\/page\d+/', '', $link);

        if ( $page > 1 ) {
            $p[]    = Siteforever::html()->link('&lt; пред', $link, array('page'=>$page-1));
        }

        $radius = 2;

        $from   = $page - $radius;
        if ( $from < 1 ) {
            $from = 1;
        }
        $to     = $page + $radius;
        if ( $to > $pages ) {
            $to = $pages;
        }

        if ( $page - $radius > 1 ) {
            $p[]    = Siteforever::html()->link('1', $link, array('page'=>1));
            if ( $page - $radius - 1 > 1 ) {
                $p[]    = '...';
            }
        }

        for ( $i = $from; $i <= $to; $i++ ) {
            if ( $i == $page ) {
                $p[]    = $page;
            }
            else {
                $p[]    = Siteforever::html()->link($i, $link, array('page'=>$i));
            }
        }

        if ( $page + $radius < $pages ) {
            if ( $page + $radius + 1 < $pages ) {
                $p[]    = '...';
            }
            $p[]    = Siteforever::html()->link($pages, $link, array('page'=>$pages));
        }

        if ( $page < $pages ) {
            $p[]    = Siteforever::html()->link('след &gt;', $link, array('page'=>$page+1));
        }

        $this->html     = count($p) > 1 ? 'Страницы: '.join(' - ',$p) : '';
        $this->from     = ($page - 1) * $perpage;
        $this->to       = $this->from + $perpage;
        $this->offset   = $this->to;
        $this->perpage  = $perpage;
        $this->limit    = ($pages > 1) ? $this->from.','.$this->perpage : '';

        $this->page     = $page;
        $this->count    = $count;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean Returns true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset ( $this->$offset );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset  = $value;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset( $this->$offset );
    }
}
