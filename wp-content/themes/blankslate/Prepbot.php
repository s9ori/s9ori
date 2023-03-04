<?php
/*
Template Name: Saori Uchida Prepbot EPR970
*/

get_header();
?>
<style>
  #content {
    max-width: 562px !important
  }
</style>
<div class="entry-content" itemprop="mainContentOfPage">
<div class="landing-container">
    <div class="landing">
    <div class="texted">
    <h1>hey nævis, write a prep on this subject:</h1>
    </div>
    <div class="landing-frame"><?php echo prep_openai_api_request_form(); ?></div>
    </div>
    <div class="openai-response-container">
    <div class="navis-calling" id="loading-container" style="display: none;">
    <h2>nævis calling</h2>
    </div>
    <img id="gif-container" style="display: none; width: 100%">
    <div class="openai-response" id="prep-response"></div>
    </div>
    </div>
<?php get_footer(); ?>
