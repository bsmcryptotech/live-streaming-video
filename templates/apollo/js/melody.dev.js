$(function() {
	var cc = $.cookie('list_grid');
	if (cc == 'g') {
		$('#pm-grid').addClass('pm-ul-browse-videos-list');
		$('#show-list').addClass('active');
	} else {
		$('#pm-grid').removeClass('pm-ul-browse-videos-list');
		$('#show-grid').addClass('active');
	}
});


$(document).ready(function() {
	$('#pm-addthis').scrollToFixed({
		preFixed: function() { 
			$('.addthis_floating_style').css({ 'opacity':'0.7', 'top' : '0px' }); 
			$(this).css({ 'opacity':'0.7', 'top' : '0px' }); 
		},
		postFixed: function() { 
			$('.addthis_floating_style').css({ 'opacity':'1.0', 'top' : '-140px' });
			$(this).css({ 'opacity':'1.0', 'top' : '-140px' }); 
		 }
  	});	

	$('.pm-ads-floating-left, .pm-ads-floating-right').scrollToFixed({
		bottom:0,
		limit: $('.container-footer').offset().top,
	});


	$('ul.nav li.dropdown').hover(function() {
		$(this).find('ul.dropdown-menu:first').stop(true, true).delay(100).fadeIn(50);
	}, function() {
		$(this).find('ul.dropdown-menu').stop(true, true).delay(100).fadeOut();
	});

	/* iOS touch fix for BootStrap */
	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });


	$('#show-list').click(function() {
		$('#pm-grid').fadeOut(200, function() {
			$(this).addClass('pm-ul-browse-videos-list').fadeIn(100);
			$.cookie('list_grid', 'g');
		});
		$(this).addClass('active');
		$('#show-grid').removeClass('active');
		return false;
	});
	
	$('#show-grid').click(function() {
		$('#pm-grid').fadeOut(200, function() {
			$(this).removeClass('pm-ul-browse-videos-list').fadeIn(100);
			$.cookie('list_grid', null);
		});
		$(this).addClass('active');
		$('#show-list').removeClass('active');
		return false;
	});


	$("#to_modal").live('click', function() {
		var url = $(this).attr('url');
		var modal_id = $(this).attr('data-controls-modal');
		$("#" + modal_id).load(url);
	});

	$('.ajax-modal').click(function(e) {
	    e.preventDefault();
	    var href = $(e.target).attr('href');
	    if (href.indexOf('#') == 0) {
	        $(href).modal('open');
	    } else {
	        $.get(href, function(data) {
	            $('<div class="modal" id="uploadForm">' + data + '</div>').modal({keyboard: true});
	        });
	    }
	});


	/*
	$('#tags_suggest, #tags_upload').tagsInput({
		'removeWithBackspace' : true,
		'height':'auto',
		'width':'auto',
		'defaultText':'',
		'minChars' : 3,
		'maxChars' : 30
	});
	*/

	/*BootStrap Stuff*/
	$('[rel=tooltip]').tooltip({container: 'body'});

	$('#myModal').modal({
	  keyboard: true,
	  show: false
	});

	/*Suggest Video Page*/
    $("#pm_sources").change(function () {
        var str = $("select option:selected").attr('value');
        $("#pm_sources_ex").text(str);
    }).change();

	var input = document.createElement( 'input' ),
	    comment = $( '#comment' );

	if ( 'placeholder' in input ) {
		comment.attr( 'placeholder', $( '.comment-textarea label' ).remove().text() );
	}

	$('#c_comment_txt').click(function() { $('#pm-comment-form').slideDown(); });

	/* Expando Mode: start small, then auto-resize on first click + text length */
	$('#comment-form-identity').hide();
	$('#commentform .form-submit').hide();

	comment.css( {'height':'120px'} ).one( 'focus', function() {
		$('#comment-form-identity').slideDown();
		$('#commentform .form-submit').slideDown();
	});


	/* language selector */
	$(".lang_selected").click(function() {
		var submenu = $(".lang_submenu");
		if( submenu.css("display") == "block" )
		{
			submenu.css( "display", "none" );
			$(this).removeClass();
			$(this).addClass("lang_selected");
		}
		else
		{
			submenu.css( "display", "block" );
			$(this).removeClass();
			$(this).addClass("lang_selected_onclick");
		}
	});

	$("a[id^='lang_select_']").each(
		function() {
		var id = parseInt( this.name );
		var lang = $('#lang_select_' + id);
		lang.click(
		function()
		{
		 $.post( MELODYURL2+"/index.php", { select_language: 1, lang_id: id }, function() { window.location.reload(); }, '');
		});
	});

	$("#register-form").validate({
		rules: {
			name: {
				required: true,
				minlength: 2
			},
			username: {
				required: true,
				minlength: 2
			},
			pass: {
				required: true,
				minlength: 5
			},
			confirm_pass: {
				required: true,
				minlength: 5,
				equalTo: "#register-form #pass"
			},
			imagetext: {
				required: true
			},
			email: {
				required: true,
				email: true	
			},
			agree: "required"
		},
		messages: {
			name: {
				required: pm_lang.validate_name,
				minlength: pm_lang.validate_name_long
			},
			username: {
				required: pm_lang.validate_username,
				minlength: pm_lang.validate_username_long
			},
			pass: {
				required: pm_lang.validate_pass, 
				minlength: pm_lang.validate_pass_long
			},
			confirm_pass: {
				required: pm_lang.validate_pass,
				minlength: pm_lang.validate_pass_long,
				equalTo: pm_lang.validate_confirm_pass_long
			},
			imagetext: {
				required: pm_lang.validate_captcha
			},
			email: pm_lang.validate_email,
			agree: pm_lang.validate_agree
		},
		errorClass: "has-error",
	});
});

$(function() {
var input = document.createElement("input");
if(('placeholder' in input)==false) { 
	$('[placeholder]').focus(function() {
		var i = $(this);
		if(i.val() == i.attr('placeholder')) {
			i.val('').removeClass('placeholder');
			if(i.hasClass('password')) {
				i.removeClass('password');
				this.type='password';
			}			
		}
	}).blur(function() {
		var i = $(this);	
		if(i.val() == '' || i.val() == i.attr('placeholder')) {
			if(this.type=='password') {
				i.addClass('password');
				this.type='text';
			}
			i.addClass('placeholder').val(i.attr('placeholder'));
		}
	}).blur().parents('form').submit(function() {
		$(this).find('[placeholder]').each(function() {
			var i = $(this);
			if(i.val() == i.attr('placeholder'))
				i.val('');
		})
	});
}
});

function SelectAll(id)
{
	document.getElementById(id).focus();
	document.getElementById(id).select();
}

$(document).ready(function() {
	// Scaled to fit thumbnails
    $('.col-md-12 .pm-video-thumb img, .col-md-8 .pm-video-thumb img').each(function() {
        var maxWidth = 240; // Max width for the image
        var maxHeight = 136;    // Max height for the image
        var ratio = 0;  // Used for aspect ratio
        var width = $(this).width();    // Current image width
        var height = $(this).height();  // Current image height
        // Check if the current width is larger than the max
        if(width > height){
            height = ( height / width ) * maxHeight;

        } else if(height > width){
            maxWidth = (width/height)* maxWidth;
        }
        $(this).css("width", maxWidth); // Set new width
        $(this).css("height", maxHeight);  // Scale height based on ratio
        $(this).css("object-fit", "cover");
        $(this).css("object-position", "25% 55%");
    });

    $('.pm-ul-browse-playlists .pm-video-thumb img').each(function() {
        var maxWidth = 255; // Max width for the image
        var maxHeight = 137;    // Max height for the image
        var ratio = 0;  // Used for aspect ratio
        var width = $(this).width();    // Current image width
        var height = $(this).height();  // Current image height
        // Check if the current width is larger than the max
        if(width > height){
            height = ( height / width ) * maxHeight;

        } else if(height > width){
            maxWidth = (width/height)* maxWidth;
        }
        $(this).css("width", maxWidth); // Set new width
        $(this).css("height", maxHeight);  // Scale height based on ratio
        $(this).css("object-fit", "cover");
        $(this).css("object-position", "25% 55%");
    });
});