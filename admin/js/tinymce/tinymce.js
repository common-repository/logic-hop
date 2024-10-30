(function() {
	tinymce.create('tinymce.plugins.logichop', {
        init : function(editor, url) {
           	editor.on( 'BeforeSetContent', function( e ) {
				if ( e.content ) {
					var match;
					var conditions = /{% if (.+?) %}/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '<logichop-if>if ' + match[1] + '</logichop-if> ');
					}
					
					var match;
					var conditions = /{% else %}/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], ' <logichop-else>else</logichop-else> ');
					}
					
					var match;
					var conditions = /{% elseif (.+?) %}/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], ' <logichop-elseif>elseif ' + match[1] + '</logichop-elseif> ');
					}
					
					var match;
					var conditions = /{% endif %}/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], ' <logichop-endif>endif</logichop-endif>');
					}
					
					var match;
					var conditions = /{{ (.+?) }}/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '<logichop-var>' + match[1] + '</logichop-var>');
					}
				}
			});
			
			editor.on( 'PostProcess', function( e ) {
				if ( e.content ) {
					var match;
					var conditions = /<logichop-if>(.+?)<\/logichop-if>/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '{% ' + match[1] + ' %}');
					}
					
					var match;
					var conditions = /<logichop-else>else<\/logichop-else>/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '{% else %}');
					}
					
					var match;
					var conditions = /<logichop-elseif>(.+?)<\/logichop-elseif>/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '{% ' + match[1] + ' %}');
					}
					
					var match;
					var conditions = /<logichop-endif>endif<\/logichop-endif>/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '{% endif %}');
					}
					
					var match;
					var conditions = /<logichop-var>(.+?)<\/logichop-var>/g;
					while (match = conditions.exec(e.content)) {
						e.content = e.content.replace(match[0], '{{ ' + match[1] + ' }}');
					}
				}
			});
        },
        createControl : function(n, cm) {
            return null;
        },
       	getInfo : function() {
            return {
                longname : 'Logic Hop',
                author : 'Logic Hop',
                authorurl : 'https://logichop.com',
                infourl : 'https://logichop.com',
                version : '1.0'
            };
        }
    });
    tinymce.PluginManager.add( 'logichop', tinymce.plugins.logichop );
})();