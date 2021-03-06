<?php
namespace Sepbin\System\Cache\Storage;

use Sepbin\System\Util\Factory;
use Sepbin\System\Core\Exception\ExtensionException;

class Redis extends ACache
{
    
    
    private $driver;
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Redis{
        
        return Factory::get(Redis::class, $config_namespace, $config_file, $config_path);
        
    }
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        if( !class_exists('\Redis') ){
            throw (new ExtensionException())->appendMsg('Redis');
        }
        
        $this->driver = new \Redis();
        
        $host = $config->getStr('host','localhost');
        $port = $config->getInt('port',6379);
        $pass = $config->getStr('pass');
        
        $pconnect = $config->getBool('pconnect',false);
        
        if(!$pconnect){
            $this->driver->connect( $host, $port );
        }else{
            $this->driver->pconnect( $host, $port );
        }
        
        if(!empty($pass)){
            $this->driver->auth($pass);
        }
        
        $this->driver->setOption(\Redis::OPT_SERIALIZER,\Redis::SERIALIZER_IGBINARY);
        
        
    }
    
    
    public function set( $key, $value, $expire ){
        
        
        $result = $this->driver->set( $key, $value );
        $this->driver->expire( $key, $expire );
        
        return $result;
        
    }
    
    
    public function call( $name, ...$params ){
        
        return $this->driver->$name( ...$params );
        
    }
    
    public function delete( $key ){
        
        $this->driver->del( $key );
        
    }
    
    public function get( $key ){
        
        return $this->driver->get( $key );
        
    }
    
    
    
}