var file_contents = file_data.prompt;
var openai_data = window.openai_data || {};
var prompts = '';


jQuery(document).ready(function($) {
    // Listen for text input event on the prompt textarea
    $('#prompt').on('input', function() {
      var prompts = $(this).val();
    });

  $('form.prep-form').submit(function(event) {
    event.preventDefault();
    var prompt = JSON.stringify(file_contents);
    var api_key = openai_data.api_key;
      var model = "text-davinci-003";
      var max_tokens = 1000;
      var prompts = $('#prompt').val();
      var temperature = .7;
      var url = "https://api.openai.com/v1/completions";
    
      var data = {
        "model": model,
        "prompt": "Following the style and structure of the intro, listener questions, and interviewer questions for each subject here:/n/n" + prompt + "\n\n Here is an intro, a numbered list of listener questions, and a numbered list of interviewer questions based on this subject: " + prompts + ":\n",
        "max_tokens": max_tokens,
        "temperature": temperature
      };

    // Make the API request using the prompt
    $.ajax({
      type: "POST",
      url: url,
      data: JSON.stringify(data),
      contentType: "application/json",
      beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + api_key);
      $('.navis-calling').show();
      $('.texted').hide();
      $('#gif-container').show();
      $('#prompt').hide();
      $('.openai-response').css({
        "opacity": "0",
        "display": "none"
      });
        },
        success: function(response) {
          // Get the generated text from the API response
          var text = response.choices[0].text;
          
          // Split the generated text into sections
          var sections = text.split('\n\n');
          
          // Create a new div element to display the generated text
          var generatedText = document.createElement('div');
          
          // Loop through each section and format it
          for (var i = 0; i < sections.length; i++) {
            // Add a line break before each section (except the first one)
            if (i > 0) {
              generatedText.appendChild(document.createElement('br'));
            }
            
            // Check if the section is an intro, listener questions, or interviewer questions
            var header = '';
            if (sections[i].startsWith('Intro:')) {
              header = '<br><b>Intro:</b><br> ';
            } else if (sections[i].startsWith('Listener Questions:')) {
              header = '<br><b>Listener Questions:</b><br> ';
            } else if (sections[i].startsWith('Interviewer Questions:')) {
              header = '<br><b>Interviewer Questions:</b><br> ';
            }
            
            // Add the section to the generated text div with the header formatted in bold
            var sectionText = document.createTextNode(sections[i].replace(/Intro:|Listener Questions:|Interviewer Questions:/, ''));
            generatedText.innerHTML += header;
            generatedText.appendChild(sectionText);
          }
          
          // Append the new div element to the openai-response div on the page
          $('.openai-response').append(generatedText);
          $('.navis-calling').hide();
          $('.texted').show();
          $('#gif-container').hide();
          $('#prompt').show();
          $('.openai-response').css({
            "opacity": "1",
            "display": "flex"
          });
        },        
      error: function(error) {
        // Display the error message in the console
        console.error(error);
      }
    });
  });
});