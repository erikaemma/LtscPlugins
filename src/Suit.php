<?php


namespace LTSC\Plugin;


use LTSC\Plugin\Exception\SuitException;
use LTSC\Plugin\Helper\PluginInfo;

final class Suit
{
    private $name;

    /**
     * @var Plugins
     */
    private $core;

    private $plugins = [];

    private $max = 0;
    private $count = 0;

    public function __construct(Plugins $core, string $name, int $max = 0) {
        $this->core = $core;
        $this->setName($name);
        $this->setMax($max);
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getMax(): int {
        return $this->max;
    }

    public function setMax(int $max) {
        $this->max = $max;
        return $this;
    }

    public function add(int $order, PluginInfo $plugin) {
        if(key_exists($order, $this->plugins)) {
            throw new SuitException("{$this->name}'s {$order} is exists", "order={$order}");
        } else {
            if($this->max != 0 && $this->count >= $this->max)
                throw new SuitException("{$this->name} is full while adding", "max={$this->max}");
            $this->plugins[$order] = $plugin;
            $this->count++;
            return $this;
        }
    }

    public function setOrder($old, $new) {
        if(key_exists($old, $this->plugins) && !key_exists($new, $this->plugins)) {
            $handle = $this->plugins[$old];
            $this->plugins[$new] = $handle;
            unset($this->plugins[$old]);
            return true;
        } else {
            return false;
        }
    }

    public function remove($order) {
        if(key_exists($order, $this->plugins)) {
            unset($this->plugins[$order]);
            return true;
        }
        return false;
    }

    public function callAll($args) {
        //$args = func_get_args();
        $result = [];
        krsort($this->plugins);
        $settings = $this->core->getSettings();

        $root = $settings->getLoadPath();
        $loadFile = $settings->getLoadFile();
        $parentClass = $settings->getParentClass();
        $cargs = $settings->getConstructArgs();
        $callMethod = $settings->getCallMethod();
        /**
         * @var  $id int
         * @var  $plugin PluginInfo
         */
        foreach($this->plugins as $id => $plugin) {
            $pluginPath = $root . $plugin->getName() . DIRECTORY_SEPARATOR . $loadFile;

            require_once $pluginPath;

            $classname = $plugin->getClassname();
            if(!class_exists($classname))
                throw new SuitException("{$this->name},{$classname} is not exists", 'while call');

            $refClass = new \ReflectionClass($classname);

            if(is_null($refClass->getConstructor()))
                $object = $refClass->newInstance();
            else
                $object = $refClass->newInstanceArgs($cargs);

            if(!is_null($parentClass)) {
                if(!($object instanceof $parentClass))
                    throw new SuitException("{$this->name}, {$classname}'s parent class is not {$parentClass}",
                                        'settings parentClass is not null');
            }

            if($refClass->hasMethod($callMethod) && $refClass->getMethod($callMethod)->isPublic())
                $result[$id] = $refClass->getMethod($callMethod)->invokeArgs($object, $args);
        }
        return $result;
    }
}