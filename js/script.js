/* Author:

*/
$(function() {
    $('textarea#content').tinymce({
        // Location of TinyMCE script
        script_url : 'js/tiny_mce/tiny_mce.js',

        // General options
        theme : "simple",

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




