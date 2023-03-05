var openai_data = window.openai_data || {};
var file_contents = file_data.file_contents;
var tense = "live segment"; // Default tense
var whatTense = "present tense";
var previousResponseArray = [];
const textarea = document.getElementById("prompt");

jQuery(document).ready(function($) {
  var cacheCounter = 0; // Initialize the counter
  var buttons = $('#past-tense-btn, #present-tense-btn, #future-tense-btn');
buttons.prop('disabled', true);

// Listen for text input event on the prompt textarea
$('#prompt').on('input', function() {
  var prompt = $(this).val();
  
  // Enable/disable buttons based on the prompt length
  if (prompt.trim().length > 15) {
    buttons.prop('disabled', false);
  } else {
    buttons.prop('disabled', true);
  }
});

$("#prompt").focus();

textarea.addEventListener("input", () => {
  textarea.style.height = "auto";
  textarea.style.height = `${textarea.scrollHeight}px`;
});


$("form.openai").submit(function(e) {
  $('#past-tense-btn').click(function() {
    tense = "past segment";
    whatTense = "past tense";
  });
  
  $('#present-tense-btn').click(function() {
    tense = "live segment";
    whatTense = "present tense";
  });

  $('#future-tense-btn').click(function() {
    tense = "upcoming segment";
    whatTense = "future tense";
  });
  
e.preventDefault();
var prompt = $("#prompt").val();
var api_key = openai_data.api_key;
var model = "text-davinci-003";
var max_tokens = 420;
var temperature = .88;
var url = "https://api.openai.com/v1/completions";
var cacheKey = prompt; // Only use the prompt to construct the cache key

cacheCounter++; // Increment the counter
var cachedResponse = localStorage.getItem(cacheKey);
if (cachedResponse) {
previousResponseArray = JSON.parse(cachedResponse);
}
var data = {
"model": model,
"prompt": "Inspired by the voice and tone of these tweets from The Brian Lehrer Show: " + file_contents + ", here is a numbered list of five original Tweets in " + whatTense + " with less than 280 characters and no hashtags about this " + tense + ":" + prompt + ":",
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
  previousResponseArray.push(result.choices[0].text);
  localStorage.setItem(cacheKey, JSON.stringify(previousResponseArray));
  var text = result.choices[0].text;
  // Split the response into separate tweets by looking for instances of "\n\n"
  var tweets = text.split("\n");

  // Create a div element for each tweet
  var tweetDivs = tweets.map(function(tweet) {
    if (tweet.trim() === '') {
      // If the tweet is empty, return an empty string
      return '';
    }
    // Otherwise, create a div for the tweet
    return "<div class='tweet'>" + tweet + "</div>";
  });

  // Join the tweet divs together and insert them into the DOM
  var formattedText = tweetDivs.join("");
  $(".openai-response").html(formattedText);
  
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
});
$('#creative-btn').click(function() {
  var inputVariable = " more creative:";
  runRewrite(inputVariable);
});

$('#serious-btn').click(function() {
  var inputVariable = " more serious:";
  runRewrite(inputVariable);
});

$('#longer-btn').click(function() {
  var prompt3 = $("#prompt").val();
  var inputVariable = " more descriptive, more detailed, and longer (with a maximum length of 280 characters) using this reference" + prompt3 + ":";
  runRewrite(inputVariable);
});

function runRewrite(inputVariable) {
    var cacheKey = $('#prompt').val() + '-' + (cacheCounter - 1); // Get the cache key from the previous request
    var api_key = openai_data.api_key;
    var model = "text-davinci-003";
    var max_tokens = 420;
    var temperature = .7;
    var url = "https://api.openai.com/v1/completions";
    var prompt2 = previousResponseArray[previousResponseArray.length - 1]; // Get the last response from the array
    var prompt3 = $("#prompt").val();


    var data2 = {
      "model": model,
      "prompt": "Make the tweets in this list\n " + prompt2 + "\n" + inputVariable,
      "max_tokens": max_tokens,
      "temperature": temperature
    };

    $.ajax({
      type: "POST",
      url: url,
      data: JSON.stringify(data2),
      contentType: "application/json",
      beforeSend: function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + api_key);
        $('.navis-calling').show();
        $('label').hide();
        $('.rewrites').hide();
        $('.prompt-tuning').hide();       
        $('.input-btn').hide();
        $('.texted').hide();
        $('.input-btn').hide();
        $('#gif-container').show();
        $('#prompt').hide();
        $('.openai-input').hide();
        $('.openai-response').css({
          "opacity": "0",
          "display": "flex"
        });
      },
      success: function(result) {
        previousResponseArray.push(result.choices[0].text);
        localStorage.setItem(cacheKey, JSON.stringify(previousResponseArray));
        var text = result.choices[0].text;

       // Split the response into separate tweets by looking for instances of "\n\n"
        var tweets = text.split("\n");

       // Create a div element for each tweet
      var tweetDivs = tweets.map(function(tweet) {
      if (tweet.trim() === '') {
      // If the tweet is empty, return an empty string
      return '';
    }
    // Otherwise, create a div for the tweet
    return "<div class='tweet'>" + tweet + "</div>";
   });

  // Join the tweet divs together and insert them into the DOM
  var formattedText = tweetDivs.join("");
  $(".openai-response").html(formattedText);
  
  // Hide any empty tweet elements
  $(".tweet:empty").css("display", "none");
  $('.navis-calling').hide();
        $('label').show();
        $('.rewrites').show();
        $('#prompt').show();
        $('.prompt-tuning').show();
        $('.texted').show();
        $('.input-btn').show();
        $('.openai-input').show();
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
        $('.texted').show();
        $('.input-btn').show();
        $('#gif-container').hide();
        $('.rewrites').hide();
        $('.prompt-tuning').show();
        $('.input-btn').show();
        $('.openai-input').show();
        $('.openai-response').html("<p>Error: " + jqXHR.responseJSON.error.message + "</p>");
        $('.openai-response').css({
          "opacity": "1",
          "display": "flex"
        });
      }
    });
  };

  $('#summarizeArticle').click(function() {
    var api_key = openai_data.api_key;
    var model = "text-davinci-003";
    var max_tokens = 880;
    var temperature = .7;
    var url = "https://api.openai.com/v1/completions";
    var prompt = $("#prompt").val();
  
    var data3 = {
      "model": model,
      "prompt": "Here is a detailed summary of this article:\n " + prompt,
      "max_tokens": max_tokens,
      "temperature": temperature
    };
  
    $.ajax({
      type: "POST",
      url: url,
      data: JSON.stringify(data3),
      contentType: "application/json",
      beforeSend: function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + api_key);
        $('.navis-calling').show();
        $('label').hide();
        $('.rewrites').hide();
        $('.prompt-tuning').hide();       
        $('.input-btn').hide();
        $('.texted').hide();
        $('.input-btn').hide();
        $('#gif-container').show();
        $('#prompt').hide();
        $('.openai-input').hide();
        $('.openai-response').css({
          "opacity": "0",
          "display": "flex"
        });
      },
      success: function(result) {
        var text1 = result.choices[0].text;
        var api_key = openai_data.api_key;
        var model = "text-davinci-003";
        var max_tokens = 420;
        var temperature = .88;
        var url = "https://api.openai.com/v1/completions";
        var cacheKey = prompt; // Only use the prompt to construct the cache key
        cacheCounter++; // Increment the counter
        var cachedResponse = localStorage.getItem(cacheKey);
        if (cachedResponse) {
          previousResponseArray = JSON.parse(cachedResponse);
        }
        var data4 = {
          "model": model,
          "prompt": "Inspired by the voice and tone of these tweets from The Brian Lehrer Show: " + file_contents + ", here is a numbered list of five original Tweets with less than 280 characters and no hashtags about this "  + text1 + ":",
          "max_tokens": max_tokens,
          "temperature": temperature
        };
  
        $.ajax({
          type: "POST",
          url: url,
          data: JSON.stringify(data4),
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
            previousResponseArray.push(result.choices[0].text);
            localStorage.setItem(cacheKey, JSON.stringify(previousResponseArray));
            var text = result.choices[0].text;
            // Split the response into separate tweets by looking for instances of "\n\n"
            var tweets = text.split("\n");
  
            // Create a div element for each tweet
            var tweetDivs = tweets.map(function(tweet) {
              if (tweet.trim() === '') {
                // If the tweet is empty, return an empty string
                return '';
              }
    // Otherwise, create a div for the tweet
    return "<div class='tweet'>" + tweet + "</div>";
  });

  // Join the tweet divs together and insert them into the DOM
  var formattedText = tweetDivs.join("");
  $(".openai-response").html(formattedText);
  
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
})
    // Hide any empty tweet elements
    $(".tweet:empty").css("display", "none");
    $('.navis-calling').hide();
          $('label').show();
          $('.rewrites').show();
          $('#prompt').show();
          $('.prompt-tuning').show();
          $('.texted').show();
          $('.input-btn').show();
          $('.openai-input').show();
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
          $('.texted').show();
          $('.input-btn').show();
          $('#gif-container').hide();
          $('.rewrites').hide();
          $('.prompt-tuning').show();
          $('.input-btn').show();
          $('.openai-input').show();
          $('.openai-response').html("<p>Error: " + jqXHR.responseJSON.error.message + "</p>");
          $('.openai-response').css({
            "opacity": "1",
            "display": "flex"
          });
        }}
    )
      })
    });