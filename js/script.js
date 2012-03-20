/* Author:

*/
$(function() {
    tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "simple",
        entity_encoding : "raw",

        // Example content CSS (should be your site CSS)
        content_css : "css/style.css"
    });

    var meta = {
        normal: '',
        twitter: '',
        picture: ''
    }

    $('#type').change(function() {
        console.log(this);
    });
});




