<?php
/*
Template Name: Saori Uchida Pet EPR970
*/

get_header();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<style>
  body {
    display: flex !important;
    align-items: center !important;
    flex-direction: column !important;
    background: #1d1d1d;
    color: #fff8f4;
    justify-content: center !important;
    touch-action: manipulation !important;
  }

  header {
    display: none !important;
    flex-direction: column !important;
    align-items: center !important;
    background-color: #1d1d1d;
    gap: 20px !important
  }

  .branding {
    display: none !important;
  }
  .pet-interactions {
    display: flex!important;
    flex-direction: row!important;
    gap: 10px!important;
  }

  .pet-stats {
    display: flex!important;
    flex-direction: column!important;
    align-items: center!important;
    justify-content: center!important;
    font-size: 14px!important;
    gap: 4px!important;
}

#site-description {
    font-family: "bitcount-mono-single-circle", sans-serif!important;
    font-weight: 500!important;
    font-style: normal!important;
    display: flex!important;
    flex-direction: column!important;
    gap: 20px!important;
    text-align: center!important;
    align-items: center!important;
}

  .pet-interactions button {
    padding: 10px!important;
    text-transform: uppercase!important;
    background: #feddde!important;
    font-weight: bold!important;
    transition: all 0.2s ease-in-out!important;
    font-family: 'bitcount-mono-single-circle'!important;
    font-size: 12px!important;

  }

  #foods {
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 20px;
    height: 100px;
    width: 100%;
    display: flex;
    align-content: center;
    overflow: hidden;
}

.food {
  display: flex;
    align-items: center;
    background: #00000069;
    min-width: 60px;
    min-height: 60px;
    justify-content: center;
    padding: 5px;
    padding-top: 5px !important;
    border: #feddde 2px solid;
    box-sizing: border-box;
}

#site-title {
  display: none;
}

  
  .pet-interactions button {
    padding: 10px!important;
    text-transform: uppercase!important;
    background: #feddde!important;
    font-weight: bold!important;
    transition: all 0.1s ease-in-out!important;
    font-family: 'bitcount-mono-single-circle'!important;
    font-size: 14px!important;
    color: black !important;
    border: none !important;

  }

  .pet-interactions button:active {
  transform: scale(0.9);
  transition: all 0.1s ease-in-out!important;
  }


  .pet-interactions button:hover {
    background: #5ECEFF !important;
    transition: all 0.1s ease-in-out!important;
  }

  #pet-box {
    display: flex!important;
    flex-direction: column-reverse!important;
    align-items: center!important;
    background-color: transparent!important;
    width: 100%!important;
    gap: 15px !important;
    height: 100%!important;
}

#monsters, .monster {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

#monsters {
  position: absolute;
    bottom: 110px;
}

#level {
  color: #b5ffff
}

#monsters p {
  font-family: 'bitcount-mono-single-circle'!important;
    font-size: 10px!important;
    font-weight: bold;
    color: #fff8b2;
    text-align: center;
}

.monster-level {
  font-family: 'bitcount-mono-single-circle'!important;
    font-size: 8px!important;
    font-weight: bold;
    text-align: center;
    color: #f2f2f2
}

#response {
    font-family: 'bitcount-mono-single-circle'!important;
    font-size: 12px!important;
    width: 250px!important;
    line-height: 1.25!important;
    text-align: center!important;
    position: absolute !important;
    top: 120px!important;
}

#pet {
  max-width: 185px;
  padding-left: 20px;
  padding-top: 0px;
  padding-bottom: 0px;
  padding-right: 20px
}
#drops {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: absolute;
    top: 308px;
}

.footer-s9ori, #forespe {
display: none
}

  </style>
<div id="drops"></div>
<div class="pet-stats">
    <div class="mood">
      <span>Mood: </span>
      <span id="mood-state"></span>
    </div>
    <div class="fitness">
      <span>Fitness: </span>
      <span id="fitness-state"></span>
    </div>
    <div class="power-level">
      <span>Power Level: </span>
      <span id="power-level"></span>
    </div>
  </div>
  <div class="drop-stats">
    <div class="moodRibbon">
      <span>Mood ribbon: </span>
      <span id="moodRibbon-state"></span>
    </div>
    <div class="fitnessRibbon">
      <span>Fitness ribbon: </span>
      <span id="fitnessRibbon-state"></span>
    </div>
    <div class="adventureRibbon">
      <span>Adventure ribbon: </span>
      <span id="adventureRibbon-state"></span>
    </div>
  </div>
  <div class="pet-interactions">
    <button id="play">Play</button>
    <button id="exercise">Train</button>
    <button id="adventure">Adventure</button>
  </div>
</div>
<div id="pet-box">
<div id="level"></div>
<img class="pet" src="https://lowfemme.com/wp-content/uploads/2023/02/tumblr_neqyicWGSs1u1nuzeo1_500.gif" alt="Pet Image">
</div>
<div id="response"></div>
</div>
<div id="monsters"></div>
<div id="foods"></div>

  </article>
<?php get_footer(); ?>