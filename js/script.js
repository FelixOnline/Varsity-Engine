/* Author:

*/
$(function() {
    //tinyMCE.init({
        //// General options
        //mode : "textareas",
        //theme : "simple",
        //entity_encoding : "raw",

        //// Example content CSS (should be your site CSS)
        //content_css : "css/style.css"
    //});

    var content = new wysihtml5.Editor("content", {
        toolbar:      "content-toolbar", // id of toolbar element
        parserRules:  wysihtml5ParserRules // defined in parser rules set 
    });

    var sticky = new wysihtml5.Editor("sticky", {
        toolbar:      "sticky-toolbar", // id of toolbar element
        parserRules:  wysihtml5ParserRules // defined in parser rules set 
    });

    var meta = {
        types: {
            twitter: '<div class="control-group"><label class="control-label" for="tweetid">Tweet URL</label><div class="controls"><input type="text" name="tweetid" id="tweetid" class="input-large"/></div></div>',
            picture: '<div class="control-group"><label class="control-label" for="picurl">Picture URL</label><div class="controls" id="picbox"><input type="text" name="picurl" id="picurl" class="input-large"/></div></div><div class="control-group"><div class="controls"><input id="imageupload" name="file_upload" type="file" /> (max size 50mb)</div></div>',
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

		addbutton();
    });

});

function addbutton() {
	if ($('#imageupload').length) {
		$('#imageupload').uploadify({
			'uploader'  : 'uploadify.swf',
			'script'    : 'uploadify.php',
			'cancelImg' : 'cancel.png',
			'folder'    : '/website/media/felix/img/upload',
			'auto'      : true,
			'fileExt'   : '*.jpg;*.gif;*.png;*.jpeg',
			'fileDesc'  : 'Image Files',
			'buttonText'  : 'Upload picture',
			'sizeLimit' : 52428800,
			'onComplete'  : function(event, ID, fileObj, response, data) {
				var returndata = response.split('+');
				$('#picbox').empty();
				$('#picbox').html('<input type="text" name="picurl" id="picurl" class="input-large" value="'+returndata[1]+'">');
			}
		});
	}
}



