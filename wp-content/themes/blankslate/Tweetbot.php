<?php
/*
Template Name: Saori Uchida Twitbot EPR970
*/

get_header();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="entry-basics">
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

<div class="landing-container">
  <div class="landing">
    <div class="texted">
      <h1 class="landing-text">Hi! I'm Saori Uchida 内田沙織, a data analyst and web developer based in New York City.</h1>
    </div>
    <div class="work-projects">
      <div class="work-container">
        <h2 class="wp-caption">Work</h2>
        <div class="work">
          <img src="https://s9ori.com/wp-content/uploads/2023/03/EQUINOX-fb-logo.jpeg">
          <img src="https://s9ori.com/wp-content/uploads/2023/03/EQUINOX-fb-logo.jpeg">
        </div>
      </div>
      <div class="projects-container">
        <h2 class="wp-caption">Projects</h2>
      </div>
    </div>
    <div>[openai_api_request_form]</div>
  </div>
</div>
<div class="openai-response-container">
  <div class="openai-response"></div>
</div>
