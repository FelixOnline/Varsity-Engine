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
        types: {
            twitter: '<div class="control-group"><label class="control-label" for="tweetid">Tweet URL</label><div class="controls"><input type="text" name="tweetid" id="tweetid" class="input-large"/></div></div>',
            picture: '<div class="control-group"><label class="control-label" for="picurl">Picture URL</label><div class="controls"><input type="text" name="picurl" id="picurl" class="input-large"/></div></div>',
            quote: '<div class="control-group"><label class="control-label" for="quote">Quote</label><div class="controls"><textarea id="quote" name="quote"></textarea></div></div>'
        },
        metacont: $('#meta'),
        addMeta: function(option) {
            this.metacont.append(this.types[option]);
        }
    }

    $('#type').change(function() {
        meta.metacont.empty();
        var option = $(this).find('option:selected').val();
        if(meta.types[option]) {
            meta.addMeta(option);
        }
    });
});




