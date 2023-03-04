<?php
/*
 * Plugin Name: OpenAI API Preps
 * Description: A WordPress plugin that makes requests to the OpenAI API and displays the response on screen
 * Version: 1.0
 * Author: Saori Uchida
 */

 add_action('wp_enqueue_scripts', 'prep_openai_api_request_form_enqueue_scripts');
function prep_openai_api_request_form_enqueue_scripts() {
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'prep', plugin_dir_url( __FILE__ ) . '/prep.js', array( 'jquery' ), '1.0', true );
  $data = array(
    'api_key' => getenv('API_KEY')
  );
  wp_localize_script( 'prep', 'openai_data', $data );
  $file_contents = file_get_contents( plugin_dir_path( __FILE__ ) . 'prep.json' );
  $prompt = json_decode( $file_contents );
  wp_localize_script( 'prep', 'file_data', array( 'messages' => $prompt ) );
}

// Display the form that will trigger the API request
function prep_openai_api_request_form() {
  ob_start();
  ?>
  <form class="prep-form" id="prep-form">
    <div>
      <label for="prompt">Prompt:</label>
      <textarea name="prompt" id="prompt"></textarea>
    </div>
    <div>
      <button type="submit">Make API Request</button>
    </div>
  </form>
  <?php
   return ob_get_clean();
}
add_shortcode('prep_openai_api_request_form', 'prep_openai_api_request_form');