// Check if a theme preference is already stored in localStorage
const currentTheme = localStorage.getItem('theme');
const form = document.querySelector('.openai');
const texts = document.querySelector('.texted');
if (currentTheme === 'dark') {
  document.body.classList.add('dark');
  texts.classList.remove('dark');
  form.classList.add('dark');
  localStorage.setItem('theme', 'dark');
} else if(currentTheme === 'light') {
  document.body.classList.add('light');
  localStorage.setItem('theme', 'light');
  form.classList.add('light');
  texts.classList.add('light');
} else {
  // Set a default theme
  document.body.classList.add('light');
  localStorage.setItem('theme', 'light');
  form.classList.add('light');
  texts.classList.add('light');
}

const toggleButton = document.getElementById('theme-toggle');

// Add an event listener to the toggle button
toggleButton.addEventListener('click', () => {
  if (document.body.classList.contains('light')) {
    document.body.classList.remove('light');
    document.body.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.body.classList.remove('dark');
    document.body.classList.add('light');
    localStorage.setItem('theme', 'light');
  }

  // Get the <img> element inside the button
  const img = toggleButton.querySelector('.colored');

  // Toggle the src attribute of the <img> element
  if (img.getAttribute('src') === 'wp-content/themes/go/dist/images/sun1.svg') {
    img.setAttribute('src', 'wp-content/themes/go/dist/images/moon.svg');
  } else {
    img.setAttribute('src', 'wp-content/themes/go/dist/images/sun1.svg');
  }

  const form = document.querySelector('.openai');
  const texts = document.querySelector('.texted');

  if (document.body.classList.contains('light')) {
    form.classList.remove('dark');
    form.classList.add('light');
    texts.classList.remove('dark');
    texts.classList.add('light');
  } else {
    form.classList.remove('light');
    form.classList.add('dark');
    texts.classList.remove('light');
    texts.classList.add('dark');
  }
 
  const label = document.querySelector('.openai-prompt');

  if (document.body.classList.contains('light')) {
    label.classList.remove('dark');
    label.classList.add('light');
  } else {
    label.classList.remove('light');
    label.classList.add('dark');
  }
  
 const input = document.querySelector('.openai-input');
const response = document.querySelector('.openai-response');

if (document.body.classList.contains('light')) {
  input.classList.remove('dark');
  input.classList.add('light');
  response.classList.remove('dark');
  response.classList.add('light');
} else {
  input.classList.remove('light');
  input.classList.add('dark');
  response.classList.remove('light');
  response.classList.add('dark');
}


  });