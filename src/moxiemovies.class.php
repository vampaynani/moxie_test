<?php
class MoxieMovies {
	private static $instance = false;
	private static $loader = false;
	private static $twig = false;

	private function __construct(){
		add_action('init', array($this, 'create_moxie_movies_post_type'));
		add_action('add_meta_boxes', array($this, 'add_moxie_movies_metaboxes'));
	}

	/**
	* Singleton pattern
	*/
	public static function get_instance(){
		if( !self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	/**
	* Set the "movies" post type.
	*/
	public function create_moxie_movies_post_type(){
		register_post_type('movies', 
			array(
				'labels' => array(
					'name' => __( 'Movies' ),
					'singular_name' => __( 'Movie' )
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'capability_type' => 'post',
				'rewrite' => array('slug' => 'movies'),
				'register_meta_box_cb' => array($this, 'add_moxie_movies_metaboxes')
			)
		);
	}

	/**
	* Set metaboxes to the "movies" post type.
	*/
	public function add_moxie_movies_metaboxes(){
		add_meta_box('mx_movie_rating', 'Movie Rating', array($this, 'movie_rating_cb'), 'movies', 'side', 'default');
		add_meta_box('mx_movie_year', 'Movie Year', array($this, 'movie_year_cb'), 'movies', 'side', 'default');
	}

	/**
	* Generates HTML for the Metabox Movie Rating
	*/
	public function movie_rating_cb(){
		global $post;
		// Noncename needed to verify where the data originated
	    echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
	    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	    // Get the rating data if its already been entered
	    $rating = get_post_meta($post->ID, '_movie_rating', true);
	    // Echo the select field
	    echo '<select name="_movie_rating" value="' . $rating  . '" class="widefat">';
	    for($i = 0; $i <= 5; $i++){
	    	echo '<option>'. $i .'</option>';
	  	}
	  	echo '</select>';
	}

	/**
	* Generates HTML for the Metabox Movie Year
	*/
	public function movie_year_cb(){
		global $post;
		// Noncename needed to verify where the data originated
    echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    // Get the rating data if its already been entered
    $year = get_post_meta($post->ID, '_movie_year', true);
    // Echo the select field
    echo '<input type="number" name="_movie_year" value="' . $year  . '" class="widefat"/>';
	}
}