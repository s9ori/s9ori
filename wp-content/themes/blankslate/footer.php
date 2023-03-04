</main>
<?php get_sidebar(); ?>
</div>
<footer id="footer" role="contentinfo">
<a class="footer-s9ori" href="https://github.com/">by s ♥︎</a>
</div>
</footer>
</div>
<?php wp_footer(); ?>
<script>
const tab = document.querySelector('.tab');
const comments = document.querySelector('#comments'); // Update the ID of the div element
let isOpen = false;

tab.addEventListener('click', () => {
  // Check if the open class is already applied to the comments element
  if (!comments.classList.contains('open')) {
    // If the open class is not applied, add it and set the isOpen flag to true
    comments.classList.add('open');
    isOpen = true;
  } else {
    // If the open class is already applied, toggle it and flip the isOpen flag
    comments.classList.toggle('open');
    isOpen = !isOpen;
  }

  // Indent this block of code correctly and enclose it within curly braces
  //if (isOpen) {
  //  sidebar.style.marginLeft = '575px';
 // } else {
  //  sidebar.style.marginLeft = '0px';
 // }
});



</script>
<script async defer src="//assets.pinterest.com/js/pinit.js"></script>

</body>
</html>