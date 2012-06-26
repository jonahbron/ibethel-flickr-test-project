<?php
/**
 * Locator.php
 * 
 * @license	http://www.opensource.org/licenses/bsd-license.php BSD
 * @link	http://skeletonframework.com/
 */

/**
 * A_Locator
 *
 * Registry plus Loader
 * 
 * @package A
 */
class A_Locator
{

	// standard repository object names
	const CONFIG = 'Config';
	const MAPPER = 'Mapper';
	const REQUEST = 'Request';
	const RESPONSE = 'Response';
	
	protected $_obj = array();
	protected $_reg = array();
	protected $_dir = array();
	protected $_dir_regexp = array();
	protected $_inject = array();
	protected $_extension = '.php';
	
	public function __construct($dir=false)
	{
		if ($dir) {
			if (is_array($dir)) {
				foreach($dir as $ns => $d) {
					$this->setDir($d, $ns);
				}
			} else {
				$this->setDir($dir);
			}
		}
		// if the location of the framework is not set then get it
		if (!isset($this->_dir['A'])) {
			$this->_dir['A'] = dirname(dirname(__FILE__)) . '/';
		}
	}
	
	/**
	 * Set a directory to used for class names that:
	 *      1. namespace '' the dir from which to load when do match is found
	 *      2. matched first part of PEAR class name 'Foo_*' or namespace '\Foo\'
	 *      3. match a perl regex like '/^Foo.*$/'
	 * @param string $dir
	 * @param string $namespace
	 * @return $this
	 */
	public function setDir($dir, $namespace='')
	{
		$dir = rtrim($dir, '/') . '/';
		if (substr($namespace, 0, 1) == '/') {	// perl regexp are in the form '/pattern/'
			$this->_dir_regexp[$namespace] = $dir;
		} else {
			$this->_dir[$namespace] = $dir;
		}
		return $this;
	}

	/**
	 * Regiser DI information to allow injecting via constructor or setters. 
	 * Calls to get() will then use this information to inject as specified. 
	 * 
	 * Example:
		$inject = array( 
			// Do: $foo = new Foo('Boo'); $foo->setBar('Bar'); $foo->setBaz('Baz', 'Jazz');
			'Foo' => array( 
				'__construct' => array('Boo'), 
				'setBar' => array('Bar'), 
				'setBaz' => array('Baz', 'Jazz'),
				), 
			// Do: $bar = new Bar($locator->get('Boo')); which in turn will create Foo as specified above
			'Bar' => array( 
				'__construct' => array(array('A_Locator'=>'get', 'name'=>'Boo'), 
				), 
			// Do: $bar = new Bar($locator->get('', 'Baz')); which in turn will create Foo as specified above
			'Bar' => array( 
				'__construct' => array(array('A_Locator'=>'get, 'name'=>'', 'class'=>'Baz'), 
				), 
			); 
	 * 
	 * @param string $dl
	 * @return $this
	 */
	public function register($dl)
	{
		if (is_string($dl)) {
			$params = func_get_params();
			array_shift($params);
			$dl = array($dl => $params);
		}
		if (is_array($dl)) {
			$this->_inject = array_merge($this->_inject, $dl);
		}
		return $this;
	}
	
	/**
	 * Load class using PEAR name to path rules. 
	 *
	 * @param string $class name
	 * @param string $dir from which to load class
	 * @param boolean $autoload triggered when class_exists() check done?
	 * @return unknown
	 */
	public function loadClass($class='', $dir='', $autoload=false)
	{
		$class = ltrim($class, '\\');
		// convert to dir separators
		$file = str_replace(array('_','\\','-'), array('/','/','_'), $class);
		//allow underscores that are not dir separators using dashes
		$class = str_replace('-', '_', $class);
		
		if (class_exists($class, $autoload)) {
			return true;
		}
		
		$pos = strripos($class, '\\');
		if ($pos !== false) {		// namespace found
			$class = substr($class, $pos + 1);
		}
		
		if ($dir) {
			$dir = rtrim($dir, '/') . '/';
		} else {
			$ns = '';
			$pos = strpos($file, '/');
			// find if in namespace
			if ($pos) {
				$ns = substr($file, 0, $pos);
				// don't use if namespace not registered
				if (! isset($this->_dir[$ns])) {
					$ns = '';
				}
			}
			if (isset($this->_dir[$ns])) {
				$dir = $this->_dir[$ns];
			} else {
				foreach ($this->_dir_regexp as $regexp => $dirstr) {
					if (preg_match($regexp, $class)) {
						$dir = $dirstr;
						break;
					}
				}
			}
		}
		$path = $dir . $file . (isset($this->_extension) ? $this->_extension : '.php');
		if (($dir == '') || file_exists($path)) {		// either in search path or absolute path exists
			$result = include($path);
			$result = $result !== false;
		} else {
			$result = false;
		}
		return $result && class_exists($class, $autoload);
	}
	
	/**
	 * Get object from registery by name. If name does not exist and class given then will attempt to load/instatiate
	 * baseclass is used to lookup DI information, if baseclass is '*' then it will search for info by parent classes/interfaces 
	 *
	 * @param string $name
	 * @param string $class
	 * @param string $baseclass
	 * @return unknown
	 */
	public function get($name='', $class='', $baseclass='')
	{
		$param = null;
		if (func_num_args() > 3) {
			$param = array_slice(func_get_args(), 3);	// get params after name/clas/dir
			// if only one param then pass the param rather than an array of params
			if (count($param) == 1) {
				$param = $param[0];
			}
		}
		if ($name) {
			if (isset($this->_obj[$name])) {
				return $this->_obj[$name];		// return registered object
			} elseif ($class) {
				$obj = $this->newInstance($class, $baseclass, $param);
				if ($obj) {
					$this->_obj[$name] = $obj;
				}
				return $obj;		// return registered object
			}
		} elseif ($class) {
			return $this->newInstance($class, $baseclass, $param);
		}
	}
	
	/**
	 * load class and create instance
	 *
	 * @param string $class
	 * @param string $baseclass is the name to lookup in DI registry, or '*' to search parent classes/interfaces
	 * @return object instantiated
	 */
	public function newInstance($class='', $baseclass='')
	{
		$obj = null;
		// get dir and clear
		if ($class) {
			$param = null;
			if (func_num_args() > 2) {
				$param = array_slice(func_get_args(), 2);	// get params after $class
				// if only one param then pass the param rather than an array of params
				if (count($param) == 1) {
					$param = $param[0];
				}
			}

			if ($this->loadClass($class)) {
				if (! $baseclass) {					// no base class then lookup by class
					$baseclass = $class;
				} elseif ($baseclass == '*') {		// wildcard the search for class TODO: use regexp here? 
					$baseclass = $class;
					$classes = array_merge(class_parents($class), class_implements($class));
					foreach ($classes as $c) {
						if (isset($this->_inject[$c])) {
							$baseclass = $c;
						}
					}
				}
				// do constructor injection here
				if (isset($this->_inject[$baseclass])) {
					$inject = array();
					foreach ($this->_inject[$baseclass] as $method => $params) {
						foreach ($params as $key => $param) {
							if (is_array($param) && isset($param['A_Locator'])) {
								switch ($param['A_Locator']) {
								// get/create new object by name/class using get() 
								case 'get':
									$inject[$method][$key] = $this->get($param['name'], $param['class']);
									break;
								// get container from locator
								case 'container':
									$container = $this->get($param['name'], $param['class']);
									if ($container) {
										$inject[$method][$key] = $container->get($param['key']);
									}
									break;
								}
							} else {
								$inject[$method][$key] = $param;
							}
						}
					}
					if (isset($inject['__construct'])) {
						$reflector = new ReflectionClass($class);
						$obj = $reflector->newInstanceArgs($inject['__construct']);
						unset($inject['__construct']);
					} else {
						$obj = new $class($param);
					}
					// do setter injection
					if ($inject) {
						foreach ($inject as $method => $params) {
							call_user_func_array(array($obj, $method), $params);
						}
					}
				} else {
					$obj = new $class($param);
				}
			}
		}
		return $obj;
	}
	
	public function set($name, $value)
	{
		if ($value !== null) {
			$this->_obj[$name] = $value;
		} else {
			unset($this->_obj[$name]);
		}
		return $this;
	}
	
	public function has($name)
	{
		return isset($this->_obj[$name]);
	}
	
	public function autoload()
	{
		return spl_autoload_register(array($this, 'loadClass'));
	}

}
