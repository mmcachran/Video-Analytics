(function() {
    tinymce.create('tinymce.plugins.video_analytics', {
        init : function(editor, url) {
        
        	editor.addCommand('video_analytics_cmd', function() {
				editor.windowManager.open({
					// Modal settings
					title: 'Insert Video',
					width: jQuery( window ).width() * 0.7,
					// minus head and foot of dialog box
					height: (jQuery( window ).height() - 36 - 50) * 0.4,
					inline: 1,
					id: 'video_analytics_cmd-insert-dialog',
					body: [{
						type: 'listbox', 
						name: 'type', 
						label: 'Type', 
						'values': [
							{text: 'Youtube', value: 'youtube'}
						]
					},{
						type: 'textbox', 
						name: 'video_id', 
						label: 'Video ID', 
					},
					{
						type: 'textbox', 
						name: 'playlist_id', 
						label: 'Playlist ID'
					}],
					onsubmit: function( e ) {
						editor.insertContent('[video_analytics type="'+e.data.type+'" video_id="' + e.data.video_id + '" playlist_id="' + e.data.playlist_id + '"]');
					},
				});
			});

            editor.addButton('video_analytics', {
                title : 'Insert Video',
                image : url + '/youtube.png',
                cmd: 'video_analytics_cmd'
            });
        },
		getInfo : function() {
			return {
				longname : "Video Analytics",
				author : 'mmcachran',
				version : "1.0"
			};
		}
    });
    tinymce.PluginManager.add('video_analytics', tinymce.plugins.video_analytics);
})();


