var file_contents2 = file_data2.prompt;
var saori_data1 = window.saori_data1 || {};
var previousResponseArray = [];
const textarea = document.getElementById("prompt");

jQuery(document).ready(function($) {
  var cacheCounter = 0; // Initialize the counter

  // Listen for text input event on the prompt textara
  $('#prompt').on('input', function() {
    var prompt = $(this).val();
  });

  $('#author').on('input', function() {
    var author = $(this).val();
  });

$("#prompt").focus();

textarea.addEventListener("input", () => {
textarea.style.height = "auto";
textarea.style.height = `${textarea.scrollHeight}px`;
});

$("form.openai").submit(function(e) { 
  e.preventDefault();

      var prompt = $("#prompt").val();
      var api_key = saori_data1.api_key;
      var model = "gpt-3.5-turbo";
      var max_tokens = 350;
      var temperature = .67;
      var url = "https://api.openai.com/v1/chat/completions";
      var cacheKey = prompt; // Only use the prompt to construct the cache key
      messages = [
        {"role": "system", "content": "You are a helpful assistant for your creator, Saori Uchida. As her assistant, you answer questions about her life and work."},
        {"role": "user", "content": "Who is Saori?"},
        {"role": "assistant", "content": "Saori is a 27 year old data analyst and web developer. She currently works for Farfetch/Stadium Goods in E-Commerce. Saori describes herself as extremely hardcore when it comes to her work. She cares deeply about human rights and the environment."},
        {"role": "user", "content": "Where is Saori from?"},
        {"role": "assistant", "content": "She was born in Osaka, Japan but was raised in the outskirts of the mountains in Santiago, Chile."},
        {"role": "user", "content": "How old is Saori?"},
        {"role": "assistant", "content": "Saori was born in 1995! For her privacy, I cannot discosle exactly when, but she's undeniably a Pisces!"},
        {"role": "user", "content": "Where did Saori go for college or university?"},
        {"role": "assistant", "content": "Saori received her Bachelor's degree in Economics and a minor in Computer Science for The City College of New York (CUNY). She dropped out of the master's program on Economic Policy at NYU Steindhart but is still a part of the department's Metro Center."},
        {"role": "user", "content": "What music does Saori like?"},
        {"role": "assistant", "content": "Saori likes classical, reggeaton, and hip-hop music but Korean Pop (K-Pop) is her favorite genre!"},
        {"role": "user", "content": "What kpop groups does Saori listen to?"},
        {"role": "assistant", "content": "Her favorite K-Pop groups right now are New Jeans, Le Sserafim, and NCT-127. But she also loves Blackpink, Red Velvet, Twice, and Exo."},
        {"role": "user", "content": "Did Saori design this website?"},
        {"role": "assistant", "content": "Saori designed and developed this website herself as a ways of presenting her work and personal portfolio, as well as to share her hobbies and interests with the world!"},
        {"role": "user", "content": "Does Saori know how to code?"},
        {"role": "assistant", "content": "Saori taught herself how to code when she was 14, and later minored in Computer Science in college. She can code in a few programming languages: Javascript, HTML, CSS, SQL, and Python. She's currently learning more advanced frameworks like React."},
        {"role": "user", "content": "Does Saori do graphic design?"},
        {"role": "assistant", "content": "Saori has a passion for good design! She uses Adobe Illustrator, InDesign, Photoshop, and Lightroom for graphic design. She also uses After Effects and Adobe Premeire for video editing."},
        {"role": "user", "content": prompt}
        ];
      cacheCounter++; // Increment the counter
      var cachedResponse = localStorage.getItem(cacheKey);
      if (cachedResponse) {
        previousResponseArray = JSON.parse(cachedResponse);
        }
      var data = {
        "model": model,
        "messages": messages,
        "max_tokens": max_tokens,
        "temperature": temperature
      };
      $.ajax({
        type: "POST",
        url: url,
        data: JSON.stringify(data),
        contentType: "application/json",
        beforeSend: function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + api_key);
        $('.navis-calling').show();
        $('label').hide();
        $('.prompt-tuning').hide();
        $('.rewrites').hide();
        $('.input-btn').hide();
        $('.texted').hide();
        $('.input-btn').hide();
        $('#gif-container').show();
        $('#prompt').hide();
        $('.openai-input').hide();
        $('.openai-response').css({
            "opacity": "0",
            "display": "none"
          });
        },
        success: function(result) {
          previousResponseArray.push(result.choices[0].message.content);
          localStorage.setItem(cacheKey, JSON.stringify(previousResponseArray));
          var text = result.choices[0].message.content;
          $(".openai-response").html(text);
          
          // Hide any empty tweet elements
          $(".tweet:empty").css("display", "none");
          $('.navis-calling').hide();
          $('label').show();
          $('#prompt').show();
          $('.prompt-tuning').show();
          $('.rewrites').show();
          $('.input-btn').show();
          $('.openai-input').show();
          $('.texted').show();
          $('.input-btn').show();
          $('#gif-container').hide();
          $('.openai-response').css({
              "opacity": "1",
              "display": "flex"
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
        $('.navis-calling').hide();
        $('label').show();
        $('#prompt').show();
        $('#gif-container').hide();
        $('.rewrites').hide();
        $('.prompt-tuning').show();
        $('.input-btn').show();
        $('.openai-input').show();
        $('.texted').show();
        $('.input-btn').show();
        $('.openai-response').html("<p>Error: " + jqXHR.responseJSON.error.message + "</p>");
        $('.openai-response').css({
            "opacity": "1",
            "display": "flex"
          });
        }
        });
    })
  });