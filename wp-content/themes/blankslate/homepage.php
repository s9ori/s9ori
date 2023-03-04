<?php
/*
Template Name: Saori Uchida Homepage EPR970
*/

get_header();
?>

<div class="latest-post-block-container">
  <?php
    // Retrieve the most recent post
    $latest_post = get_posts( array(
      'numberposts' => 3,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'post_type' => 'post',
      'post_status' => 'publish'
    ) );

    // Loop through the posts
    foreach ( $latest_post as $post ) {
      // Get the featured image URL
      $featured_image_url = get_the_post_thumbnail_url( $post->ID );
      // Get the post title
      $title = $post->post_title;
      // Get the post URL
      $post_url = get_permalink( $post->ID );

      // Output the HTML structure for the post
      ?>
      <div class="latest-post-block">
  <div class="latest-post-block__featured-image-container">
    <a href="<?php echo $post_url; ?>" class="latest-post-block-link">
      <img src="<?php echo $featured_image_url; ?>" alt="Featured image" class="latest-post-block__featured-image">
    </a>
  </div>
  <div class="latest-post-block__title-container">
  <?php
$tags = get_the_tags();
if ($tags) {
  foreach($tags as $tag) {
    $tag_link = get_tag_link($tag->term_id);
    echo '<h3 class="latest-post-block__title"><a href="' . $tag_link . '" class="tag-link">' . $tag->name . '</a></h3>';
  }
}
?>
</div>
</div>

    <?php } ?>
</div>
<!-- /wp:html -->

<?php
get_footer();