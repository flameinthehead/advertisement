<?php

use App\Exceptions\AppException;
use App\Router;

class App
{
    const CONFIG = [
        'storage' => __DIR__.'/config/storage.php',
    ];

    private $config;
    private $router;
    private static $storage;

    public function __construct(Router $router)
    {
        $this->router = $router;

        $this->loadConfig();
        $this->configureStorage();
    }

    public static function storage()
    {
        return self::$storage;
    }

    public function run()
    {
        $response = $this->router->resolve();
    }

    private function loadConfig()
    {
        foreach(self::CONFIG as $alias => $configPath){
            if(!file_exists($configPath)){
                throw new AppException('There is no config file '.$configPath);
            }
            $this->config[$alias] = include $configPath;
        }
    }

    private function configureStorage()
    {
        if(!isset($this->config['storage'])){
            throw new AppException('Cannot found storage config data');
        }

        $default = $this->config['storage']['default'];
        $connectionSettings = $this->config['storage']['connections'][$default];
        if(empty($default) || empty($connectionSettings)){
            throw new AppException('Connection data failed');
        }

        switch ($default) {
            case 'redis':
                self::$storage = new Predis\Client([
                    'scheme' => $connectionSettings['scheme'],
                    'host'   => $connectionSettings['host'],
                    'port'   => $connectionSettings['port'],
                ]);
        }
    }
}
