<?php
/*
 * Plugin Name: saori API Request
 * Description: A WordPress plugin that makes requests to the saori API and displays the response on screen
 * Version: 1.0
 * Author: Saori Uchida
 */

 add_action('wp_enqueue_scripts', 'saori_api_request_form_enqueue_scripts');
 function saori_api_request_form_enqueue_scripts() {
     wp_enqueue_script( 'jquery' );
     wp_enqueue_script( 'saori-api-request1', plugin_dir_url(__FILE__) . '/saori-api-request.js', array( 'jquery' ), '1.0', true );
     $data = array(
         'api_key' => getenv('API_KEY')
     );
     wp_localize_script( 'saori-api-request1', 'saori_data1', $data );
     $file_contents2 = file_get_contents(plugin_dir_url(__FILE__) . "/context.txt");
     wp_localize_script( 'saori-api-request1', 'file_data2', array( 'file_contents2' => $file_contents2 ) );}
 

 function saori_api_request_form() {
     ob_start();
     ?>
     <form class="openai" action="#" method="post">
         <textarea name="prompt" id="prompt" placeholder="ask me anything"></textarea>

<div class="prompt-tuning">
<button id="past-tense-btn">Send</button>
 </div>
     </form>
     <?php
     return ob_get_clean();
 }
 
 add_shortcode('saori_api_request_form', 'saori_api_request_form');
 ?>