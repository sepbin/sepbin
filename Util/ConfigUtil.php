<?php
namespace Sepbin\System\Util;

use Sepbin\System\Util\Data\DotName;
use Sepbin\System\Util\Traits\TGetType;
use Sepbin\System\Util\Data\ClassName;

class ConfigUtil
{
	
	use TGetType;
	
	private $config = array();
	
	private $loadedFilename = array();
	
	
	static public function getInstance() : ConfigUtil{
		
		static $instance = null;
		
		if( $instance == null ){
			$instance = new ConfigUtil();
		}
		
		return $instance;
		
	}
	
	
	public function addIniFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$content = $this->loadFile( $fullname );
		
		if(empty($content)) return ;
		
		$config = parse_ini_string($content,true);
		
		foreach ( $config as $key=>$val ){
			if( strpos($key, '.') ){
				DotName::set($this->config, $key, $val);
			}else{
				$this->config[$key] = $val;
			}
		
		}
		
	}
	
	
	
	
	public function addPhpFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$config = $this->loadFile($fullname);
		if(empty($config)) return ;
		
		foreach ( $config as $key=>$val ){
			
			$this->config[$key] = $val;
			
		}
		
	}
	
	public function addXmlFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$content = $this->loadFile( $fullname );
		
		if(empty($content)) return ;
		
		$content = \simplexml_load_string($content);
		
		$config = json_decode( json_encode( $content ), true );
		
		foreach ( $config as $key=>$val ){
			
			$this->config[$key] = $val;
			
		}
		
		
	}
	
	public function addJsonFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$content = $this->loadFile( $fullname );
		if(empty($content)) return ;
		$config = json_decode($content,true);
		
		foreach ( $config as $key=>$val ){
			
			$this->config[$key] = $val;
			
		}
		
	}
	
	public function addFile( string $filename, string $path = CONFIG_DIR ){
		
		$ext = FileUtil::getExtensionName($filename);
		
		if( $ext == 'php' ){
			$this->addPhpFile($filename,$path);
		}
		
		if( $ext == 'json' ){
			$this->addJsonFile($filename,$path);
		}
		
		if( $ext == 'xml' ){
			$this->addXmlFile($filename,$path);
		}
		
		if( $ext == 'ini' ){
			$this->addIniFile($filename,$path);
		}
		
	}
	
	
	private function loadFile( string $fullname ){
		
		
		if( !file_exists($fullname) ){
			//throw (new ConfigFileNotFindException())->appendMsg( $fullname );
			trigger_error('???????????????????????????'.$fullname, E_USER_WARNING);
			return null;
		}
		
		$this->loadedFilename[ $fullname ] = true;
		
		if( FileUtil::getExtensionName($fullname) == 'php' ){
			
			return include $fullname;
			
		}else{
			
			return file_get_contents($fullname);
			
		}
		
	}
	
	public function checkLoadedFile( string $fullname ) : bool{
		
		if( isset($this->loadedFilename[$fullname]) ){
			return true;
		}
		
		return false;
		
	}
	
	public function get( string $name, $default='' ){
		
		return DotName::get($this->config,$name,$default);
		
		
	}
	
	
	/**
	 * ??????????????????
	 * @param string $name ??????????????????
	 * @param mixed $value
	 */
	public function set( string $name, $value ){
		
		DotName::set($this->config,$name, $value);
		
	}
	
	
	/**
	 * ???????????????????????????
	 * @param string $name ?????????????????? ???????????? 'a' => ['b'=>[]]????????? a.b??????
	 * @return boolean  ??????true??????????????????false????????????
	 */
	public function check( string $name ) : bool{
		
		$d = DotName::get($this->config,$name,null);
		
		if($d != null) return true;
		
		return false;
		
	}
	
	/**
	 * ????????????????????????????????????????????????
	 * ??????????????????????????????????????????????????????????????????????????????
	 * ???????????????????????????????????????????????????????????????????????????
	 * @param string $name
	 * @return bool ??????false????????????????????????????????????????????????true????????????????????????
	 */
	public function checkPointer( string $name ) : bool{
	    
	    $d = DotName::get($this->config,$name,null);
	    if($d == null) return false;
	    
	    if( !is_string($d) ) return false;
	    return true;
	    
	}
	
	public function getSubConfName( string $option ,string $name ){
	    
	    
	    $config_name = substr($name, strrpos($name, '\\')+1);
	    $config_name = ClassName::camelToUnderline($config_name);
	    $config_name = $option.'_'.$config_name;
	    
	    return $config_name;
	    
	}
	
	
}