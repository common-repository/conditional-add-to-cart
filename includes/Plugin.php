<?php

namespace ConditionalAddToCart;

class Plugin {
	/**
	 * Plugin instance.
	 * @var \ConditionalAddToCart\Plugin $instance
	 */
	protected static $instance = null;

	/**
	 * Plugin URI
	 * @var string $uri
	 */
	protected $uri;

	/**
	 * Plugin path.
	 * @var string $path
	 */
	protected $path;

	/**
	 * Plugin version.
	 * @var string version
	 */
	protected $version;


	protected $baseName;
	/**
	 * Return plugin instance.
	 * @return \ConditionalAddToCart\Plugin
	 */
	public static function instance() {

		if ( static::$instance === null ) {
			static::$instance = new Plugin;
		}

		return static::$instance;
	}

	
	public function __construct() {
		$this->version = '0.1.1';
		$this->path    = plugin_dir_path( __DIR__ );
		$this->uri     = plugin_dir_url( __DIR__ );
		$this->baseName  = plugin_basename(dirname(__DIR__). '/conditional-add-to-cart.php');

	}

	/**
	 * @return string
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}
	/**
	 * @return string
	 */
	public function getBaseName() {
		return $this->baseName;
	}


	/**
	 * Load plugin.
	 */
	public function run() {
		Admin::instance()->run();
		Front::instance()->run();
	}
}
