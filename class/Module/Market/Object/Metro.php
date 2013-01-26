<?php
/**
 * Domain object Metro
 * @author SiteForeverCMS Generator
 * @link http://siteforever.ru
 */

/**
 * @property int id
 * @property string name
 * @property int city_id
 * @property float lat
 * @property float lng
 */
namespace Module\Market\Object;

use Sfcms\Data\Object;
use Sfcms\Data\Field;

class Metro extends Object
{
    /**
     * Create field list
     * @return array
     */
    protected static function doGetFields()
    {
        return array(
            new Field\Int( 'id', 10, false, null, true ),
            new Field\Varchar( 'name', 50, true, null, false ),
            new Field\Int( 'city_id', 10, false, null, false ),
            new Field\Decimal( 'lat', 10, true, null, false ),
            new Field\Decimal( 'lng', 10, true, null, false ),
        );
    }

    /**
     * DB table name
     * @return string
     */
    public static function getTable()
    {
        return 'metro';
    }
}
