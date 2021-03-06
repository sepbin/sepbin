<?php
namespace Sepbin\System\Db\Driver;

use Sepbin\System\Util\Factory;
use Sepbin\System\Core\Exception\ExtensionException;

class Mysql implements IDriver
{
	
	/**
	 * pdo对象
	 * @var \PDO
	 */
	private $pdo;
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Mysql{
		
		return Factory::get(Mysql::class,$config_namespace,$config_file,$config_path);
		
	}
	
	public function _init(\Sepbin\System\Util\FactoryConfig $config){
		
	    if( !class_exists('PDO') ){
	        throw (new ExtensionException())->appendMsg('php-pdo');
	    }
	    
		$host = $config->getStr('host');
		$dbname = $config->getStr('database');
		$user = $config->getStr('user');
		$pass = $config->getStr('pass');
		$port = $config->getInt('port',3306);
		$pconnect = $config->getBool('pconnect',false);
		$charset = $config->getStr('charset','utf8');
		
		if( $pconnect ){
			$this->pdo = new \PDO("mysql:host=$host;dbname=$dbname;$port;charset=$charset",$user,$pass,array(
					\PDO::ATTR_PERSISTENT => true
			));
		}else{
		    $this->pdo = new \PDO("mysql:host=$host;dbname=$dbname;$port;charset=$charset",$user,$pass);
		}
		
	}
	
	
	public function query( string $sql ){
		
		return $this->pdo->query($sql)->fetchAll( \PDO::FETCH_ASSOC );
		
	}
	
	public function exec( string $sql ){
	    
		return $this->pdo->exec($sql);
		
	}
	
	public function getError(){
		
		return $this->pdo->errorInfo()[2];
		
	}
	
	public function getLastInsertId(){
		
		return $this->pdo->lastInsertId();
	}
	
	public function beginTrans(){
		
		$this->pdo->beginTransaction();
		
	}
	
	public function commitTrans(){
		
		$this->pdo->commit();
		
	}
	
	public function rollBackTrans(){
		
		$this->pdo->rollBack();
		
	}
	
	
	public function close(){
		$this->pdo = null;
	}
	
}