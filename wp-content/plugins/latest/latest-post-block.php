<script>
function register_latest_post_block() {
  // Enqueue the block's assets (JS and CSS files)
  wp_register_script(
    'my-plugin-block-js',
    plugins_url( 'block.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element' )
  );
  wp_register_style(
    'my-plugin-block-css',
    plugins_url( 'block.css', __FILE__ ),
    array( 'wp-edit-blocks' )
  );

  // Register the block
  register_block_type( 'my-plugin/latest-post-block', array(
    'editor_script' => 'my-plugin-block-js',
    'editor_style' => 'my-plugin-block-css',
    'render_callback' => 'render_latest_post_block',
  ) );
}
add_action( 'init', 'register_latest_post_block' );

function render_latest_post_block() {
  // Retrieve the latest post
  $latest_post = get_posts( array(
    'numberposts' => 1,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish'
  ) );

  // Get the featured image URL
  $featured_image_url = get_the_post_thumbnail_url( $latest_post[0]->ID );
  // Get the post title
  $title = $latest_post[0]->post_title;

  // Return the block's HTML output
  return '
  <div class="latest-post-block">
    <div class="latest-post-block__featured-image-container">
      <img src="' . $featured_image_url . '" alt="Featured image" class="latest-post-block__featured-image">
    </div>
    <div class="latest-post-block__title-container">
      <h3 class="latest-post-block__title">' . $title . '</h3>
    </div>
  </div>';
}
</script>