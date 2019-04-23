<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'MyPluginParent.php';

class MainTest extends \PHPUnit\Framework\TestCase
{
    public function testMain() {
        $plugins = LTSC\Plugin\Plugins::getInstance(new \LTSC\Plugin\Helper\Settings(
            __DIR__ . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR,
            'init.php',
            'run',
            [],
            MyPluginParent::class
        ));

        $suit1 = new \LTSC\Plugin\Suit($plugins, 'core', 1);
        $suit2 = new \LTSC\Plugin\Suit($plugins, 'home', 0);

        $plugins->addSuit($suit1)->addSuit($suit2);

        $plugins->register('core', 0, new \LTSC\Plugin\Helper\PluginInfo('sysfile', SysFile::class));
        try {
            $plugins->register('core', 1, new \LTSC\Plugin\Helper\PluginInfo('sysfile', SysFile::class));
        } catch (\LTSC\Plugin\Exception\SuitException $pe) {
            $this->assertEquals('max=1', $pe->getWhy());
        }
        $plugins->close();
        $results = $plugins->emit('core', time());
        var_dump($results);
    }
}