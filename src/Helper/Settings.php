<?php


namespace LTSC\Plugin\Helper;


use LTSC\Plugin\Exception\SettingsException;

final class Settings
{
    private $loadPath = '';
    private $loadFile = '';
    private $constructArgs = [];
    private $callMethod = '';
    private $parentClass = null;

    public function __construct(string $loadPath, string $loadFile, string $callMethod, array $constructArgs = [], $parentClass = null) {
        $this->setLoadPath($loadPath);
        $this->setLoadFile($loadFile);
        $this->setConstructArgs($constructArgs);
        $this->setCallMethod($callMethod);
        $this->setParentClass($parentClass);
    }


    public function getLoadPath(): string {
        return $this->loadPath;
    }

    public function setLoadPath(string $loadPath): Settings {
        if(file_exists($loadPath) && is_dir($loadPath)) {
            $this->loadPath = $this->_fullPath($loadPath);
            return $this;
        } else {
            throw new SettingsException('setLoadPath', "$loadPath not exists or not a folder");
        }
    }

    public function getLoadFile(): string {
        return $this->loadFile;
    }

    public function setLoadFile(string $loadFile): Settings {
        if(is_null($loadFile))
            $this->loadFile = null;
        else
            $this->loadFile = $loadFile;
        return $this;
    }

    public function getConstructArgs(): array {
        return $this->constructArgs;
    }

    public function setConstructArgs(array $constructArgs): Settings {
        $this->constructArgs = $constructArgs;
        return $this;
    }

    public function addConstructArg($constractArg): Settings {
        $this->constructArgs[] = $constractArg;
        return $this;
    }

    public function getCallMethod(): string {
        return $this->callMethod;
    }

    public function setCallMethod(string $callMethod): Settings {
        $this->callMethod = $callMethod;
        return $this;
    }

    public function getParentClass() {
        return $this->parentClass;
    }

    public function setParentClass($parentClass) {
        if(!class_exists($parentClass))
            throw new SettingsException('setParentClass', "$parentClass not exists");
        $this->parentClass = $parentClass;
        return $this;
    }

    private function _fullPath($path) {
        if(substr($path, strlen($path) - 1, 1) != DIRECTORY_SEPARATOR)
            return $path . DIRECTORY_SEPARATOR;
        else
            return $path;
    }
}