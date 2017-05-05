(function() {
	tinymce.PluginManager.requireLangPack('grpdocs_assembly');
	tinymce.create('tinymce.plugins.GrpdocsAssemblyPlugin', {
		init : function(ed,url) {
			ed.addCommand('mceGrpdocsAssembly', function() {
				ed.windowManager.open( {
					file : url + '/../grpdocs-dialog.php',
					width : 420 + parseInt(ed.getLang('grpdocs_assembly.delta_width',0)),
					height : 540 + parseInt(ed.getLang('grpdocs_assembly.delta_height',0)),
					inline : 1}, {
						plugin_url : url,
						some_custom_arg : 'custom arg'
					}
				)}
			);
			ed.addButton('grpdocs_assembly', {
				title : 'GroupDocs Assembly Embedder',
				cmd : 'mceGrpdocsAssembly',
				image : url + '/../images/grpdocs-assembly-button.png'
			});
			ed.onNodeChange.add
				(function(ed,cm,n) {
					cm.setActive('grpdocs_assembly',n.nodeName=='IMG')
				})
		},
		createControl : function(n,cm) {
			return null
		},
		getInfo : function() { 
			return { 
				longname : 'GroupDocs Assembly Embedder',
				author : 'Sergiy Osypov',
				authorurl : 'http://www.groupdocs.com',
				infourl : 'http://www.groupdocs.com',
				version : "1.0"}
		}
	});
	tinymce.PluginManager.add('grpdocs_assembly',tinymce.plugins.GrpdocsAssemblyPlugin)
})();
