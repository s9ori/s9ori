jQuery(document).ready(function($){
    // Check if the loading screen has been displayed before
    var showLoadingScreen = localStorage.getItem("showLoadingScreen");

    if (showLoadingScreen === null || showLoadingScreen === "true") {
        // Create a div to hold the loading screen
        var loading = $("<div>").addClass("loading-screen");
        // Add the spinner HTML code to the loading div
        loading.html(`
          <div class="spinner-container">
            <div class="spinner" id="spinner01"></div>
            <div class="spinner" id="spinner02"></div>
            <div class="spinner" id="spinner03"></div>
            <div class="spinner" id="spinner04"></div>
            <div class="spinner" id="spinner05"></div>
          </div>
          <h2 class="loading-text">Loading</h2>
        `);
        // Append the loading screen to the body of the page
        $("body").append(loading);

        // Set a timeout of 6 seconds
        var timeout = setTimeout(function(){
            // Remove the loading screen
            loading.fadeOut(500, function(){
                $(this).removeClass("loading-screen").addClass("loaded");
            });
        }, 4000);

        // Once the page is fully loaded
        $(window).on("load", function(){
            // Clear the timeout
            clearTimeout(timeout);
            // Remove the loading screen after a minimum of 3 seconds
            setTimeout(function(){
                loading.fadeOut(500, function(){
                    $(this).removeClass("loading-screen").addClass("loaded");
                });
            }, 2500);
        });
        // set the local storage value to false
        localStorage.setItem("showLoadingScreen", "false");
    }
});
