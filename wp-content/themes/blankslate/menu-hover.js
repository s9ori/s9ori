jQuery(document).ready(function($){
    $(document).ready(function() {
        // create a div element to overlay on top of the video
        var overlay = $("<div>").addClass("overlay");
        $(".landing-frame").append(overlay);
        overlay.css({
            "position": "absolute",
            "top": "0",
            "left": "0",
            "z-index": "1",
            "width": "100%",
            "height": "100%",
            "opacity": "0.5",

        });
        $(".landing-video").css({
        });

        $(".real-integration").hover(function() {
            // stop any running animations before starting a new one
            overlay.stop();
            // change the background color of the overlay div
            overlay.css("background-color", "red");
            // add the title of the <a> element to the overlay div
            overlay.html($(this).attr("title"));
            // fade in the overlay div
            overlay.fadeIn();
        }, function() {
            // stop any running animations before starting a new one
            overlay.stop();
            // fade out the overlay div
            overlay.fadeOut();
        });

        $(".programs").hover(function() {
            overlay.stop();
            overlay.css("background-color", "blue");
            overlay.html($(this).attr("title"));
            overlay.fadeIn();
        }, function() {
            overlay.stop();
            overlay.fadeOut();
        });

        $(".resources").hover(function() {
            overlay.stop();
            overlay.css("background-color", "green");
            overlay.html($(this).attr("title"));
            overlay.fadeIn();
        }, function() {
            overlay.stop();
            overlay.fadeOut();
        });

        $(".get-involved").hover(function() {
            overlay.stop();
            overlay.css("background-color", "yellow");
            overlay.html($(this).attr("title"));
            overlay.fadeIn();
        }, function() {
            overlay.stop();
            overlay.fadeOut();
        });
    });
});
