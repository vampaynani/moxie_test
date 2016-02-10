<?php
/**
 * Plugin Name: Moxie Movies
 * Description: This plugin adds a JSON API from a custom post type(movies) and displays it as a frontpage.
 * Version: 1.0.0
 * Author: Wenceslao Negrete
 * Author URI: http://github.com/vampaynani
 */

include_once('vendor/autoload.php');
include_once('src/moxiemovies.class.php');
include_once('src/urlhandler.class.php');

$url_handler = URLHandler::get_instance();
$mox_movies = MoxieMovies::get_instance();

$url_handler->set_callback(array($mox_movies, 'show_json_data'));