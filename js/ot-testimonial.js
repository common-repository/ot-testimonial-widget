/* OT Testimonial JAVASCRIPT */
jQuery.noConflict();
jQuery(document).ready(function($) {
	// Fix Bootstrap 3 tooltip conflicting with mootools-more
	if(window.MooTools && window.MooTools.More){
		$('.hasTooltip, [rel=tooltip], [data-toggle="tooltip"], [data-toggle="collapse"], [data-toggle="tab"], [data-toggle="popover"]').each(function(){
			this.show = null; this.hide = null
		});
		
		// $('.carousel').each(function(index, element) {
			// $(this)[index].slide = null;
		// });
		var mSlide = Element.prototype.slide;
		var mHide = Element.prototype.hide;
		var mShow = Element.prototype.show;
		Element.implement({
			// slide: function(how, mode){
				// return this;
			// },
			slide: function() {
				if (this.hasClass("noconflict")) {
					return this;
				}
				mSlide.apply(this, arguments);
			},
			hide: function() {
				if (this.hasClass("noconflict")) {
					return this;
				}
				mHide.apply(this, arguments);
			},
			show: function() {
				if (this.hasClass("noconflict")) {
					return this;
				}
				mShow.apply(this, arguments);
			}
		});		
	}

	var custom_uploader;
    $('#add_testi #client_avtar_button').click(function(e) {
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: true
        });
        custom_uploader.on('select', function() {   
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#add_testi #client_avtar').val(attachment.url);
        });
        custom_uploader.open();

    });
});