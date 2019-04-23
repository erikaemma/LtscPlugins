<?php


namespace LTSC\Plugin;


use LTSC\Plugin\Exception\PluginException;
use LTSC\Plugin\Helper\PluginInfo;
use LTSC\Plugin\Helper\Settings;

final class Plugins
{
    static private $_instance = null;

    static public function getInstance(Settings $settings) {
        if(is_null(self::$_instance))
            self::$_instance = new static($settings);
        return self::$_instance;
    }

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Suit[]
     */
    private $suits = [];

    private $closed = false;

    private function __construct(Settings $settings) {
        $this->settings = $settings;
    }

    public function getSettings() :Settings {
        return clone $this->settings;
    }

    public function addSuit(Suit $suit) {
        if(!$this->closed) {
            if(!key_exists($suit->getName(), $this->suits))
                $this->suits[$suit->getName()] = $suit;
            else
                throw new PluginException('addSuit', "Suit {$suit->getName()} already exists");
        } else {
            throw new PluginException('addSuit', 'Plugins is closed');
        }
        return $this;
    }

    public function close() {
        $this->closed = true;
    }

    public function register(string $eventName, int $order, PluginInfo $plugin) {
        if(key_exists($eventName, $this->suits)) {
            $this->suits[$eventName]->add($order, $plugin);
            return $this;
        } else {
            throw new PluginException('register', "$eventName is already exists");
        }
    }

    public function emit(string $eventName, ...$args) {
        if(!$this->closed)
            throw new PluginException("emit", "not close", "before your emit any events, you must close it firstly");
        if(key_exists($eventName, $this->suits))
            return $this->suits[$eventName]->callAll($args);
        else
            throw new PluginException('emit', "$eventName is not exsits");
    }
}