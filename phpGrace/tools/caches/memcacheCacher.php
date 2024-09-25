<?php
/**
 * phpGrace 缓存类 [memcache]
 * 作者 : 刘海君 5213606@qq.com
 */
namespace phpGrace\tools\caches;

class memcacheCacher{
	
	private static $cacher = null;
	private $memcacher;
	private $type          = '';
	
	private function __construct($conf){
		// 优先使用 memcached
		if(class_exists('\Memcached')){
			$this->memcacher = new \Memcached();
			$this->memcacher->addServer($conf['host'], $conf['port']);
			$this->type      = 'memcached';
		}else{
			if(!class_exists('\Memcache')){throw new \graceException('请开启 php 的 Memcached 或 Memcache 扩展'); }
			$this->memcacher = new \Memcache();
			$this->memcacher->connect($conf['host'], $conf['port']);
			$this->type      = 'memcache';
		}
		$this->pre = $conf['pre'];
	}
	
	public static function getInstance($conf){
		if(self::$cacher == null){self::$cacher = new memcacheCacher($conf);}
		return self::$cacher;
	}
	
	public function get($name){
		return $this->memcacher->get($name);
	}
	
	public function set($name, $val, $expire = 3600){
		if($expire > 2592000){$expire = 2592000;}
		if($this->type == 'memcached'){
			$this->memcacher->set($name, $val, $expire);
		}else{
			$this->memcacher->set($name, $val, MEMCACHE_COMPRESSED, $expire);
		}
	}
	
	public function removeCache($name){
		$this->memcacher->delete($name);
	}
	
	public function clearCache(){
		$this->memcacher->flush();
	}
	
	public function close(){
		$this->memcacher->close();
	}
}