<?php
class URLHandler {
	private static $instance = false;
	private static $handler = false;

	private function __construct(){
		add_action('parse_request', array($this, 'custom_url_handler'));
	}

	/**
	* Singleton pattern
	*/
	public static function get_instance(){
		if( !self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	/**
	* Sets the callback that handles the will handle the URL result
	* @param {Function} handler - Callback function
	*/
	public static function set_handler( $handler ){
		self::$handler = $handler;
	}

	/**
	* Handles the URL in case it is an expected URL it dispatches the data required
	* @param {String} REQUEST_URI - The given request uri
	* @return {Event}
	*/
	public function custom_url_handler(){
		preg_match('/movies.json/', $_SERVER['REQUEST_URI'], $match);
		if(count($match) > 0){
			call_user_func(self::$handler);
			exit();
		}
	}
}