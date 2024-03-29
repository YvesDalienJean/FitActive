
function sp_titles_counters() {
	var meta_title_val = jQuery("#seopress_titles_title_meta").val();
	var meta_title_placeholder = jQuery("#seopress_titles_title_meta").attr("placeholder");

	jQuery("#seopress_titles_title_counters").after('<div id="seopress_titles_title_counters_val">/ 60</div>'), 
	meta_title_val.length > 0 ? (jQuery("#seopress_titles_title_counters").text(meta_title_val.length), 
	jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_val))) : meta_title_placeholder.length && (jQuery("#seopress_titles_title_counters").text(meta_title_placeholder.length), 
	jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_placeholder))),
	
	meta_title_val.length > 60 ? jQuery("#seopress_titles_title_counters").css("color", "red") : meta_title_placeholder.length > 60 && jQuery("#seopress_titles_title_counters").css("color", "red"), 
	pixelTitle(meta_title_val) > 568 ? jQuery("#seopress_titles_title_pixel").css("color", "red") : pixelTitle(meta_title_placeholder) > 568 && jQuery("#seopress_titles_title_pixel").css("color", "red");
	
	if (meta_title_val.length) {
		var progress = Math.round(pixelTitle(meta_title_val)/568*100);
	} else {
		var progress = Math.round(pixelTitle(meta_title_placeholder)/568*100);
	}

	if (progress >= 100) {
		progress = 100;
	}
	
	jQuery('#seopress_titles_title_counters_progress').attr('aria-valuenow',progress),
	jQuery('#seopress_titles_title_counters_progress').text(progress +'%'),
	jQuery('#seopress_titles_title_counters_progress').css('width',progress +'%'),
	
	jQuery("#seopress_titles_title_meta, #seopress-tag-single-title, #seopress-tag-single-site-title, #seopress-tag-single-sep").on('keyup paste change click', function(e) {
		var meta_title_val = jQuery("#seopress_titles_title_meta").val();
		var meta_title_placeholder = jQuery("#seopress_titles_title_meta").attr("placeholder");

		jQuery("#seopress_titles_title_counters").css("color", "inherit"),
		jQuery("#seopress_titles_title_pixel").css("color", "inherit"),
		
		meta_title_val.length > 60 && jQuery("#seopress_titles_title_counters").css("color", "red"),
		pixelTitle(meta_title_val) > 568 && jQuery("#seopress_titles_title_pixel").css("color", "red");

		if (meta_title_val.length == 0) { 
			meta_title_placeholder.length > 60 && jQuery("#seopress_titles_title_counters").css("color", "red"),
			pixelTitle(meta_title_placeholder) > 568 && jQuery("#seopress_titles_title_pixel").css("color", "red")
		}

		meta_title_val.length > 0 ? (jQuery("#seopress_titles_title_counters").text(meta_title_val.length),
		jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_val))) : meta_title_placeholder.length && (jQuery("#seopress_titles_title_counters").text(meta_title_placeholder.length),
		jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_placeholder))),
		
		meta_title_val.length > 0 ? (jQuery(".snippet-title-custom").text(e.target.value),
		jQuery(".snippet-title").css("display", "none"),
		jQuery(".snippet-title-custom").css("display", "block"),
		jQuery(".snippet-title-default").css("display", "none")) : 0 == meta_title_val.length && (jQuery(".snippet-title-default").css("display", "block"),
		jQuery(".snippet-title-custom").css("display", "none"), jQuery(".snippet-title").css("display", "none"));

		if (meta_title_val.length) {
			var progress = Math.round(pixelTitle(meta_title_val)/568*100);
		} else {
			var progress = Math.round(pixelTitle(meta_title_placeholder)/568*100);
		}

		if (progress >= 100) {
			progress = 100;
		}

		jQuery('#seopress_titles_title_counters_progress').attr('aria-valuenow',progress),
		jQuery('#seopress_titles_title_counters_progress').text(progress +'%'),
		jQuery('#seopress_titles_title_counters_progress').css('width',progress +'%')
    })
}

function sp_meta_desc_counters() {
	var meta_desc_val = jQuery("#seopress_titles_desc_meta").val();
	var meta_desc_placeholder = jQuery("#seopress_titles_desc_meta").attr("placeholder");
	
	jQuery("#seopress_titles_desc_counters").after('<div id="seopress_titles_desc_counters_val">/ 160</div>'), 
	
	meta_desc_val.length > 0 ? (jQuery("#seopress_titles_desc_counters").text(meta_desc_val.length), 
	jQuery("#seopress_titles_desc_pixel").text(pixelTitle(meta_desc_val))) : meta_desc_placeholder.length && (jQuery("#seopress_titles_desc_counters").text(meta_desc_placeholder.length), 
	jQuery("#seopress_titles_desc_pixel").text(pixelTitle(meta_desc_placeholder))), 
	
	meta_desc_val.length > 160 ? jQuery("#seopress_titles_desc_counters").css("color", "red") : meta_desc_placeholder.length > 160 && jQuery("#seopress_titles_desc_counters").css("color", "red"), 
	pixelTitle(meta_desc_val) > 940 ? jQuery("#seopress_titles_desc_pixel").css("color", "red") : pixelTitle(meta_desc_placeholder) > 940 && jQuery("#seopress_titles_desc_pixel").css("color", "red");
	
	if (meta_desc_val.length) {
		var progress = Math.round(pixelTitle(meta_desc_val)/940*100);
	} else {
		var progress = Math.round(pixelTitle(meta_desc_placeholder)/940*100);
	}

	if (progress >= 100) {
		progress = 100;
	}

	jQuery('#seopress_titles_desc_counters_progress').attr('aria-valuenow',progress),
	jQuery('#seopress_titles_desc_counters_progress').text(progress +'%'),
	jQuery('#seopress_titles_desc_counters_progress').css('width',progress +'%'),

	jQuery("#seopress_titles_desc_meta, #seopress-tag-single-excerpt").on('keyup paste change click', function(e) {
		var meta_desc_val = jQuery("#seopress_titles_desc_meta").val();
		var meta_desc_placeholder = jQuery("#seopress_titles_desc_meta").attr("placeholder");

		jQuery("#seopress_titles_desc_counters").css("color", "inherit"),
		jQuery('#seopress_titles_desc_pixel').css('color', 'inherit'),
		
		meta_desc_val.length > 160 && jQuery("#seopress_titles_desc_counters").css("color", "red"),
		pixelTitle(meta_desc_val) > 940 && jQuery("#seopress_titles_desc_pixel").css("color", "red");

		if (meta_desc_val.length == 0) { 
			meta_desc_placeholder.length > 160 && jQuery("#seopress_titles_desc_counters").css("color", "red"),
			pixelTitle(meta_desc_placeholder) > 940 && jQuery("#seopress_titles_desc_pixel").css("color", "red")
		}
		
		meta_desc_val.length > 0 ? (jQuery("#seopress_titles_desc_counters").text(meta_desc_val.length),
		jQuery("#seopress_titles_desc_pixel").text(pixelTitle(meta_desc_val))) : meta_desc_placeholder.length && (jQuery("#seopress_titles_desc_counters").text(meta_desc_placeholder.length),
		jQuery("#seopress_titles_desc_pixel").text(pixelTitle(meta_desc_placeholder))), meta_desc_val.length > 0 ? (jQuery(".snippet-description-custom").text(e.target.value),
		jQuery(".snippet-description").css("display", "none"), 
		jQuery(".snippet-description-custom").css("display", "inline"), 
		jQuery(".snippet-description-default").css("display", "none")) : 0 == meta_desc_val.length && (jQuery(".snippet-description-default").css("display", "inline"), 
		jQuery(".snippet-description-custom").css("display", "none"), 
		jQuery(".snippet-description").css("display", "none"));

		if (meta_desc_val.length) {
			var progress = Math.round(pixelTitle(meta_desc_val)/940*100);
		} else {
			var progress = Math.round(pixelTitle(meta_desc_placeholder)/940*100);
		}
	
		if (progress >= 100) {
			progress = 100;
		}

		jQuery('#seopress_titles_desc_counters_progress').attr('aria-valuenow',progress),
		jQuery('#seopress_titles_desc_counters_progress').text(progress +'%'),
		jQuery('#seopress_titles_desc_counters_progress').css('width',progress +'%')

    }), jQuery("#excerpt, .editor-post-excerpt textarea").keyup(function(e) {
		var meta_desc_val = jQuery("#seopress_titles_desc_meta").val();
		var meta_desc_placeholder = jQuery("#seopress_titles_desc_meta").attr("placeholder");

		0 == meta_desc_val.length && 0 == jQuery(".snippet-description-custom").val().length && (jQuery(".snippet-description-custom").text(e.target.value), 
		jQuery(".snippet-description").css("display", "none"), 
		jQuery(".snippet-description-custom").css("display", "inline"), 
		jQuery(".snippet-description-default").css("display", "none"));

		if (meta_desc_val.length) {
			var progress = meta_desc_val.length;
		} else {
			var progress = meta_desc_placeholder.length;
		}
		if (progress >= 100) {
			progress = 100;
		}

		jQuery('#seopress_titles_desc_counters_progress').attr('aria-valuenow',progress),
		jQuery('#seopress_titles_desc_counters_progress').text(progress +'%'),
		jQuery('#seopress_titles_desc_counters_progress').css('width',progress +'%')
    })
}

function pixelTitle(e) {
    for (var s = [5, 6, 6, 18, 10, 10, 10, 10, 10, 12, 12, 12, 12, 10, 10, 10, 5, 5, 6, 10, 10, 16, 12, 3, 6, 6, 7, 11, 5, 6, 5, 5, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 5, 5, 11, 11, 11, 10, 18, 12, 12, 13, 13, 12, 11, 14, 13, 5, 9, 12, 10, 15, 13, 14, 12, 14, 13, 12, 11, 13, 12, 17, 12, 12, 11, 5, 5, 5, 8, 10, 6, 10, 10, 9, 10, 10, 5, 10, 10, 4, 4, 9, 4, 15, 10, 10, 10, 10, 6, 9, 9, 5, 10, 9, 13, 9, 9, 9, 6, 5, 6, 11, 10, 0, 0, 4, 4, 10, 10], t = 0, l = 0; l < e.length; l++) t += s[" ·˙・«»àô€ÀÈÊÉéèê !\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuüvwxyz{|}~–\n\r‘’£…".indexOf(e.substring(l, l + 1))];
    return t
}
jQuery(document).ready(function(e) {
    function s() {
        e.ajax({
            method: "GET",
            url: seopressAjaxRealPreview.seopress_real_preview,
            data: {
                action: "seopress_do_real_preview",
                post_id: e("#seopress-tabs").attr("data_id"),
                tax_name: e("#seopress-tabs").attr("data_tax"),
                origin: e("#seopress-tabs").attr("data_origin"),
                post_type: e("#seopress_launch_analysis").attr("data_post_type"),
                seopress_analysis_target_kw: e("#seopress_analysis_target_kw_meta").val(),
                _ajax_nonce: seopressAjaxRealPreview.seopress_nonce
            },
            success: function(s) {
                Object.keys(s.data).forEach(e => {
                    let t = document.createElement("textarea");
                    t.innerHTML = s.data[e], s.data[e] = t.textContent
				}), 
				e("#seopress_cpt .google-snippet-preview .snippet-title").html(s.data.title),
				e("#seopress_cpt .google-snippet-preview .snippet-title-default").html(s.data.title),
				e("#seopress_titles_title_meta").attr("placeholder", s.data.title),
				e("#seopress_cpt .google-snippet-preview .snippet-description").html(s.data.meta_desc),
				e("#seopress_cpt .google-snippet-preview .snippet-description-default").html(s.data.meta_desc),
				e("#seopress_titles_desc_meta").attr("placeholder", s.data.meta_desc),
				e("#seopress_cpt #seopress_social_fb_title_meta").attr("placeholder", s.data.og_title),
				e("#seopress_cpt #seopress_social_fb_desc_meta").attr("placeholder", s.data.og_desc),
				e("#seopress_cpt #seopress_social_fb_img_meta").attr("placeholder", s.data.og_img),
				e("#seopress_cpt #seopress_social_twitter_title_meta").attr("placeholder", s.data.tw_title),
				e("#seopress_cpt #seopress_social_twitter_desc_meta").attr("placeholder", s.data.tw_desc),
				e("#seopress_cpt #seopress_social_twitter_img_meta").attr("placeholder", s.data.tw_img),
				e("#seopress_cpt #seopress_robots_canonical_meta").attr("placeholder", s.data.canonical),
				e("#seopress_analysis_results_state").fadeIn().css("display", "inline-block"),
				e("#seopress_analysis_results_state").delay(3500).fadeOut(),
				e("#seopress-analysis-tabs-1").load(" #seopress-analysis-tabs-1"),
				e("#seopress-analysis-tabs-2").load(" #seopress-analysis-tabs-2"),
				e("#seopress-analysis-tabs-3").load(" #seopress-analysis-tabs-3"),
				e("#seopress-analysis-tabs-4").load(" #seopress-analysis-tabs-4"),
				e(" #seopress_titles_title_counters_val").remove(),
				e(" #seopress_titles_desc_counters_val").remove(),
				sp_titles_counters(), 
				sp_meta_desc_counters()
            }
        })
    }
	e("#seopress-analysis-tabs .hidden").removeClass("hidden"), 
	e("#seopress-analysis-tabs").tabs(), 
	s(), 
	e("#seopress_launch_analysis").on("click", function() {
        s()
    })
});