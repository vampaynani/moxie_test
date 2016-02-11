<?php
class PageManager {
	private static $instance = false;
	
	private function __construct(){
		add_action('init', array($this, 'frontpage_is_enabled'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
		add_shortcode('list-movies', array($this, 'list_movies'));
	}

	/**
	* Singleton pattern
	*/
	public static function get_instance(){
		if( !self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	/**
	* List the "movies" post type called by a shortcode.
	* [list-movies]
	*/
	public function list_movies(){
		return '<div id="vp-moxie-movies"></div>';
	}

	/**
	* Insert necessary assets for SAP on [list-movies]
	* [list-movies]
	*/
	public function enqueue_assets(){
		wp_enqueue_style( 'ionicons', plugin_dir_url( __FILE__ ) . '../assets/lib/Ionicons/css/ionicons.css' );
		wp_enqueue_style( 'vp-moxie-movies-styles', plugin_dir_url( __FILE__ ) . '../assets/css/vp-moxie-movies.css' );
		wp_enqueue_script( 'gsap', plugin_dir_url( __FILE__ ) . '../assets/lib/gsap/src/uncompressed/TweenMax.js', null, '1.0', false );
		wp_enqueue_script( 'vue', plugin_dir_url( __FILE__ ) . '../assets/lib/vue/dist/vue.js', null, '1.0', false );
		wp_enqueue_script( 'vue-resource', plugin_dir_url( __FILE__ ) . '../assets/lib/vue-resource/dist/vue-resource.js', null, '1.0', false );
		wp_enqueue_script( 'vp-moxie-movies', plugin_dir_url( __FILE__ ) . '../assets/js/vp-moxie-movies.js', null, '1.0', true );
	}

	/**
	* Check if the moxie-videos00 page is available
	*/
	public function frontpage_is_enabled(){
		$page_name = 'movies';
		$this->create_page_if_not_exist($page_name);
	}

	/**
	* Assign the created page as frontpage
	* @param {string} page_name - name of the page that will be evaluated
	*/
	private function assign_frontpage($page_name){
		$homepage = get_page_by_title($page_name);
		if( $homepage ){
			update_option( 'page_on_front', $homepage->ID );
			update_option( 'show_on_front', 'page' );
		}
	}

	/**
	* Create a page if it not exist in the stack
	* @param {string} page_name - name of the page that will be evaluated
	*/
	private function create_page_if_not_exist($page_name){
		if( get_page_by_title($page_name) == NULL ) $this->create_page( $page_name );
		$this->assign_frontpage( $page_name );
	}

	/**
	* Create a post of type page with the name asigned
	* @param {string} page_name - name of the page that will be created
	*/
	private function create_page($page_name){
		$new_page = array(
			'post_title' => $page_name,
			'post_content' => '[list-movies]',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'page',
			'post_name' => $page_name
		);

		//Insert the post into the database
		wp_insert_post( $new_page );
	}
}	