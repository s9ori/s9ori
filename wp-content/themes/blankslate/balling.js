const $bigBall = document.querySelector('.cursor__ball--big');
const $smallBall = document.querySelector('.cursor__ball--small');
const $bigBallCircle = document.querySelector('.shape--big');
const $smallBallCircle = document.querySelector('.shape--small');
const $hoverables = document.querySelectorAll('a[href]:not(.latest-post-block__title a[href])');
const $titleHoverables = document.querySelectorAll('.latest-post-block__title');
const $paragraphs = document.querySelectorAll('p');

document.body.addEventListener('mousemove', onMouseMove);
for (let i = 0; i < $paragraphs.length; i++) {
  $paragraphs[i].addEventListener('mouseenter', onTextMouseHover);
  $paragraphs[i].addEventListener('mouseleave', onTextMouseOut);
}

for (let i = 0; i < $hoverables.length; i++) {
  $hoverables[i].addEventListener('mouseenter', onMouseHover);
  $hoverables[i].addEventListener('mouseleave', onMouseHoverOut);
}

for (let i = 0; i < $titleHoverables.length; i++) {
  $titleHoverables[i].addEventListener('mouseenter', onTitleMouseHover);
  $titleHoverables[i].addEventListener('mouseleave', onTitleMouseHoverOut);
}

// Move the cursor
function onMouseMove(e) {
  const x = e.pageX - window.scrollX - 25;
  const y = e.pageY - window.scrollY - 25;
  TweenMax.to($bigBall, .4, {
    x: x,
    y: y
  });
  TweenMax.to($smallBall, 0, {
    x: e.pageX - window.scrollX - 5,
    y: e.pageY - window.scrollY - 7
  });
}


// Hover an element
function onMouseHover() {
  TweenMax.to($bigBall, .5, {
    scale: 2
  });
  $bigBallCircle.style.background = '#f7f8fa'
  document.querySelector('.cursor__ball--big').style.mixBlendMode = 'difference';
  $smallBallCircle.style.background = '#f7f8fa'
  document.querySelector('.cursor__ball--small').style.mixBlendMode = 'difference';
}

function onMouseHoverOut() {
  TweenMax.to($bigBall, .5, {
    scale: 1
  });
  $bigBallCircle.style.background = '#f7f8fa'
  document.querySelector('.cursor__ball--big').style.mixBlendMode = 'difference';
  $smallBallCircle.style.background = '#f7f8fa'
  document.querySelector('.cursor__ball--small').style.mixBlendMode = 'difference';
}

function onTitleMouseHover(e) {
  e.target.style.background = 'black';
  const tagLink = e.target.querySelector('.tag-link');
  if (tagLink) {
    tagLink.style.color = '#f2f2f2';
  }
  TweenMax.to($bigBall, .5, {
    fill: 'transparent'
  })
}

function onTitleMouseHoverOut(e) {
  e.target.style.background = 'transparent';
  const tagLink = e.target.querySelector('.tag-link');
  if (tagLink) {
    tagLink.style.color = 'black';
  }
  TweenMax.to($bigBall, .5, {
    fill: '#f7f8fa'
  });
}

function onTextMouseHover(e) {
  document.querySelector('.shape--big').classList.add('square');
  document.querySelector('.shape--small').classList.add('square');
  $bigBallCircle.style.opacity = '0';
}

function onTextMouseOut(e) {
  document.querySelector('.shape--big').classList.add('square');
  document.querySelector('.shape--small').classList.remove('square');
  $bigBallCircle.style.opacity = '1';
}