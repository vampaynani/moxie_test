<?php
class MoxieMovies {
	private static $instance = false;
	
	private function __construct(){
		add_action('init', array($this, 'create_moxie_movies_post_type'));
		add_action('add_meta_boxes', array($this, 'add_moxie_movies_metaboxes'));
		add_action('save_post', array($this, 'save_moxie_movie_metas'));
		add_action('save_post', array($this, 'caching_api_call'));
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
		add_meta_box('mx_movie_rating', 'Movie Metadata', array($this, 'movie_metadata_cb'), 'movies', 'side', 'default');
	}

	/**
	* Generates HTML for the Movie Metadata (Rating, Year)
	*/
	public function movie_metadata_cb(){
		global $post;
		// Noncename needed to verify where the data originated
	    echo '<input type="hidden" name="eventmeta_moxmovie" id="eventmeta_moxmovie" value="' .
	    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	    // Get the rating data if its already been entered
	    $rating = get_post_meta($post->ID, '_movie_rating', true);
	    // Echo the rating field
	    echo '<div class="misc-pub-section"><label>Movie Rating</label>';
	    echo '<select name="_movie_rating" class="widefat">';
	    for($i = 0; $i <= 5; $i++){
	    	echo $i == $rating ? '<option selected>'. $i .'</option>' : '<option>'. $i .'</option>';
	  	}
	  	echo '</select></div>';
	  	$year = get_post_meta($post->ID, '_movie_year', true);
    	// Echo the year field
    	echo '<div class="misc-pub-section"><label>Movie Year</label>';
    	echo '<input type="number" name="_movie_year" value="' . $year  . '" class="widefat"/></div>';
	}

	/**
	* Save metabox data from Moxie movie
 	*/
	public function save_moxie_movie_metas(){
		global $post;
		// verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['eventmeta_moxmovie'], plugin_basename(__FILE__) )) return $post->ID;
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) return $post->ID;

    // Add values of $events_meta as custom fields
    $events_meta = array( '_movie_rating' => $_POST['_movie_rating'], '_movie_year' => $_POST['_movie_year']);
    foreach ($events_meta as $key => $value) {
    	if( $post->post_type == 'revision' ) return;
      if(get_post_meta($post->ID, $key, FALSE)) {
      	// If the custom field already has a value
        update_post_meta($post->ID, $key, $value);
      } else { 
      	// If the custom field doesn't have a value
        add_post_meta($post->ID, $key, $value);
      }
      if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
	}

	/**
 	* API Simple Caching
 	*/
 	public function caching_api_call(){
 		if ( wp_verify_nonce( $_POST['eventmeta_moxmovie'], plugin_basename(__FILE__) )){
	 		$cache_file = dirname(__FILE__) . '/cache/api-cache.json';
	 		
	 		if( !file_exists($cache_file) ) die("Cache file is missing: $cache_file");

	 		$json_results = $this->get_json_data();
	 		if ( $json_results ){
	    	file_put_contents($cache_file, $json_results);
	    }else{
	    	unlink($cache_file);
	    }
	 	}
 	}

 	/**
	* Shows JSON Data previously cached of "movies" post type
	*/
	public function show_cached_json_data(){
		$cache_file = dirname(__FILE__) . '/cache/api-cache.json';

		// In case there's a clients callback, save it in a variable
		$jsonp_callback = isset($_GET['callback']) ? $_GET['callback'] : null;

		//Shows a valid JSON content and in case there's a jsonp callback, resolve it
		header("Content-type: application/json", false);
		$json = file_get_contents($cache_file);
		print $jsonp_callback ? "$jsonp_callback($json)" : $json;
	}

	/**
	* Generates JSON data of "movies" post type
	*/
	private function get_json_data(){
		// Get all posts from movies post type
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'movies',
			'post_status' => 'publish'
		);
		$posts_array = get_posts( $args );
		
		// Get all variables and set them on an associative array, then pass it to the $raw_json array
		$raw_json = array();
		foreach ($posts_array as $post) {
			array_push($raw_json, array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'poster_url' => wp_get_attachment_url( get_post_thumbnail_id($post->ID) ), 
				'rating' => get_post_meta($post->ID, '_movie_rating', FALSE)[0],
				'year' => get_post_meta($post->ID, '_movie_year', FALSE)[0],
				'short_description' => $post->post_content
			));
		};
		return $json = json_encode( $raw_json );
	}
}