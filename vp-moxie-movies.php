<?php
/**
 * Plugin Name: Moxie Movies
 * Description: This plugin adds a JSON API from a custom post type(movies) and displays it as a frontpage.
 * Version: 1.0.0
 * Author: Wenceslao Negrete
 * Author URI: http://github.com/vampaynani
 */

include_once('src/moxiemovies.class.php');
include_once('src/urlhandler.class.php');

$mox_movies = MoxieMovies::get_instance();