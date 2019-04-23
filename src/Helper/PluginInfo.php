<?php


namespace LTSC\Plugin\Helper;


class PluginInfo
{
    protected $name;
    protected $classname;

    public function __construct(string $name, $classname) {
        $this->name = $name;
        $this->classname = $classname;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getClassname() {
        return $this->classname;
    }

    public function setClassname($classname) {
        $this->classname = $classname;
    }
}