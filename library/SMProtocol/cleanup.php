<?php
/** Namespace */
namespace library\SMProtocol;
/**
 * Class cleanup
 * @author Damien Lasserre <damien.lasserre@gmail.com>
 * @package library\SMProtocol
 */
class cleanup
{
    /** @var  string $_class */
    private $_class;

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * cleanup objects
     * @param string $class
     */
    public function _cleanup($class = __CLASS__)
    {
        if(class_exists($class)) {
            /** @var \ReflectionClass $_reflection */
            $_reflection = new \ReflectionClass($class);
            /** @var string _class */
            $this->_class = $class;

            foreach ($_reflection->getProperties() as $_property) {
                if (!$_property->isStatic()) {
                    //SMProtocol::_print('[unset] ' . COLOR_RED . 'Unset ' . $_property->getName() . COLOR_WHITE . PHP_EOL);
                    if (is_object($this->{$_property->getName()})) {
                        if (method_exists($this->{$_property->getName()}, '_cleanup')) {
                            $this->{$_property->getName()}->_cleanup();
                        } else if (is_array($this->{$_property->getName()})) {
                            $this->_cleanupArray($this->{$_property->getName()});
                        }
                    } else {
                        unset($this->$_property);
                    }
                } else {
                    //SMProtocol::_print('[unset] ' . COLOR_RED . 'Unset ' . $_property->getName() . COLOR_WHITE . PHP_EOL);
                    /** @var string $name */
                    $name = $_reflection->getName();
                    if (is_array($name::${$_property->getName()})) {
                        //SMProtocol::_print('[unset] ' . COLOR_RED . 'Unset array ' . $_property->getName() . COLOR_WHITE . PHP_EOL);
                        foreach ($name::${$_property->getName()} as $_key => $_item) {
                            if (is_object($_item)) {
                                //SMProtocol::_print('[unset] ' . COLOR_RED . 'Unset key ' . $_key . COLOR_WHITE . PHP_EOL);
                                $this->_cleanup($_item);
                            } else if (is_array($_item)) {
                                $this->_cleanupArray($_item);
                            }
                            unset($name::${$_property->getName()}[$_key]);
                        }
                        $name::${$_property->getName()} = null;
                    }
                }
            }
        }
    }

    /**
     * @author Damien Lasserre <damien.lasserre@gmail.com>
     * @param array $array
     */
    public function _cleanupArray(array &$array)
    {
        echo PHP_EOL.COLOR_GREEN.'Cleanup array: '.COLOR_WHITE.PHP_EOL;
        foreach($array as $key => $value) {
            if(is_object($value)) {
                $this->_cleanup($value);
            } else {
                if(is_array($value)) {
                    $this->_cleanupArray($value);
                } else {
                    echo PHP_EOL.COLOR_ORANGE;
                    //xdebug_debug_zval('value');
                    echo COLOR_WHITE;
                    //SMProtocol::_print(COLOR_RED.'['.$this->_class.'] Unset variable "'.$key.'"'.COLOR_WHITE.PHP_EOL);
                    unset($array[$key]);
                    echo COLOR_ORANGE;
                    //xdebug_debug_zval('value');
                    echo COLOR_WHITE;
                    echo PHP_EOL;
                }
            }
        }
    }
} 