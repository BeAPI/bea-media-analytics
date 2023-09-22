<?php namespace BEA\Media_Analytics;

/**
 * Singleton base class for having singleton implementation
 * This allows you to have only one instance of the needed object
 * You can get the instance with
 *     $class = My_Class::get_instance();
 *
 * /!\ The get_instance method have to be implemented !
 *
 * Class Singleton
 * @package BEA\Media_Analytics
 */
trait Singleton {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @return self
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Constructor protected from the outside
	 */
	final private function __construct() {
		$this->init();
	}

	/**
	 * Add init function by default
	 * Implement this method in your child class
	 * If you want to have actions send at construct
	 */
	protected function init() {}

	/**
	 * prevent the instance from being cloned
	 *
	 * @throws \LogicException
	 */
    final public function __clone() {
        throw new \LogicException( 'A singleton must not be unserialized!' );
    }

	/**
	 * prevent from being unserialized
	 *
	 * @throws \LogicException
	 */
    final public function __wakeup() {
        throw new \LogicException( 'A singleton must not be unserialized!' );
    }
}