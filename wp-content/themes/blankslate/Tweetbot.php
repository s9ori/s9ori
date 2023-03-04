<?php
/*
Template Name: Saori Uchida Twitbot EPR970
*/

get_header();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="entry-basics">
<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1> <?php edit_post_link(); ?>
</div>
<style>
  #content {
    max-width: 562px !important
  }
  </style>
<div class="entry-content" itemprop="mainContentOfPage">
<div class="landing-container">
    <div class="landing">
    <div class="texted">
    <h1>hey nævis, write some hit tweets about:</h1>
    </div>
    <div class="landing-frame"><?php echo openai_api_request_form(); ?></div>
    </div>
    <div class="openai-response-container">
    <div class="navis-calling" id="loading-container" style="display: none;">
    <h2>nævis calling</h2>
    </div>
    <img id="gif-container" style="display: none; width: 100%">
    <div class="openai-response"></div>
    <div class="tuning">
<h2 class="rewrites" style="display: none;">make these more:<h2>
    <button id="creative-btn" class="input-btn" style="display: none">Creative</button>
    <button id="serious-btn" class="input-btn" style="display: none">Objective</button>
    <button id="longer-btn" class="input-btn" style="display: none">Detailed</button>
</div>
    </div>
    </div>
</div>
</article>
<?php get_footer(); ?>