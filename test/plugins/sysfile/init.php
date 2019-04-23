<?php

class SysFile extends MyPluginParent {

    public function run($time = null)
    {
        echo "SysFile Plugin loaded.\n";
        echo $time . "\n";
        return "sysfile";
    }
}