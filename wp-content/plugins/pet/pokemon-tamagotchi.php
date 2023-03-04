<?php
/*
Plugin Name: Pokemon Tamagotchi
Plugin URI: https://example.com/
Description: A tamagotchi-style game with Pokemon.
Version: 1.0
Author: Your Name
Author URI: https://example.com/
License: GPLv2 or later
Text Domain: pokemon-tamagotchi
*/

function pokemon_tamagotchi_enqueue_scripts() {
    wp_enqueue_script('jquery');
    // Enqueue the main stylesheet and JavaScript file
    wp_enqueue_style('pokemon-tamagotchi-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('pokemon-tamagotchi-script', plugins_url('main.js', __FILE__), array('jquery'), '1.0', true);
    $data = array(
        'api_key' => getenv('API_KEY')
    );
    wp_localize_script('pokemon-tamagotchi-script', 'openai_data2', $data );
    $file_contents2 = file_get_contents(plugin_dir_url(__FILE__) . "/b99.txt");
    wp_localize_script( 'pokemon-tamagotchi-script', 'file_data2', array( 'file_contents2' => $file_contents2 ) );}

add_action('wp_enqueue_scripts', 'pokemon_tamagotchi_enqueue_scripts');
