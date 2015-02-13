var VideoAnalytics = (function( window, document, $, undefined ) {
	'use strict';
	
	var app = { $videos: {} };
	
	app.init = function() {
		app.addYoutubeScript();
		app.onFrameReady();
		
		app._gaq = window._gaq || [];
	};
	
	app.addYoutubeScript = function() {
		window.tag = document.createElement('script');
		window.tag.src = "http://www.youtube.com/player_api";
	
		window.firstScriptTag = document.getElementsByTagName('script')[0];
		window.firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	};
	
	app.onFrameReady = function() {
		window.onYouTubeIframeAPIReady = function () {
			$('div[id^="yt-frame-"]').each(function(i, val) {
			
				// Initialize each YouTube video present
				app.$videos[i] = new YT.Player( $(this).attr('id'), {
					height: '449',
					width: '768',
					videoId: $(this).attr('data-key'),
					playerVars: {
						wmode: "opaque",
	                    listType:'playlist',
	                    list: $(this).attr('data-playlist-key')
					},
					events: {
						onReady: app.onPlayerReady,
	                    onStateChange: app.onPlayerStateChange,
	                    onError: app.onPlayerError
					}
				});
			});
		};
	};
	
	app.onPlayerReady = function(e) {
		// Do something like report back to Google Tag Manager
	};
	
	app.onPlayerStateChange = function(e) {
        e["data"] == YT.PlayerState.PLAYING && setTimeout(app.onPlayerPercent, 1000, e["target"]);
        if (e["data"] == YT.PlayerState.PLAYING && YT.gtmLastAction == "p") {
            console.log({
                event: "youtube",
                action: "play",
                label: e.target["getVideoUrl"]().match(/v=([^&]+)/)[1]
            });
            
            app._gaq.push(['_trackEvent', 'Youtube-' + e.target["getVideoUrl"]().match(/v=([^&]+)/)[1], 'play']); 
            YT.gtmLastAction = "";
        }
        if (e["data"] == YT.PlayerState.PAUSED) {
            console.log({
                event: "youtube",
                action: "pause",
                label: e.target["getVideoUrl"]().match(/v=([^&]+)/)[1]
            });
            
            app._gaq.push(['_trackEvent', 'Youtube-' + e.target["getVideoUrl"]().match(/v=([^&]+)/)[1], 'pause']); 
            YT.gtmLastAction = "p";
        }
    };
    
    app.onPlayerError = function(e) {
        dataLayer.push({
            event: "error",
            action: "GTM",
            label: "youtube:" + e["target"]["src"] + "-" + e["data"]
        });
    };
    
    app.onPlayerPercent = function(e) {
        if (e["getPlayerState"]() == YT.PlayerState.PLAYING) {
            var t = e["getDuration"]() - e["getCurrentTime"]() <= 1.5 ? 1 : (Math.floor(e["getCurrentTime"]() / e["getDuration"]() * 4) / 4).toFixed(2);
            if (!e["lastP"] || t > e["lastP"]) {
                e["lastP"] = t;
				
				console.log({
                    event: "youtube",
                    action: t * 100 + "%",
                    label: e["getVideoUrl"]().match(/v=([^&]+)/)[1]
                });
                
                // track with GA
				app._gaq.push(['_trackEvent', 'Youtube-' + e["getVideoUrl"]().match(/v=([^&]+)/)[1], t * 100 + "%"]); 
            }
            e["lastP"] != 1 && setTimeout(app.onPlayerPercent, 1000, e);
        }
    };
	
	$( document ).ready( app.init );
	
	return app;
	
})( window, document, jQuery );