<?php
/** Namespace */
namespace protocol\plugins;
/**
 * Class stat
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package protocol\plugins
 */
class stat
{
    /** @var  object[] $_objects */
    protected static $_objects;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param $_object
     * @return bool
     */
    public static function create($_object)
    {
        if(is_object($_object)) {
            if (self::$_objects[get_class($_object)]) {
                /** return */
                return (self::$_objects[get_class($_object)]);
            }
            /** Return */
            return (self::$_objects[get_class($_object)] = $_object);
        }
        /** Return */
        return ( False );
    }

    public static function update($_object)
    {
        $_name = get_class($_object);

        $_database = \noSql::getInstance()->selectDB('download');
        if($_database) {
            $_collection = $_database->selectCollection($_name);
            if($_collection > 0) {
                $_collection->findOne(array('_id' => new \MongoId($_object->getMongoId())));
            }
        }
    }
}