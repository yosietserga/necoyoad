(function() {
	var youtubeCmd = {
		exec: function(editor) {
			editor.openDialog('ntproducts');
			return
		}
	};
	CKEDITOR.plugins.add('ntproducts', {
		lang: ['es', 'en'],
		requires: ['dialog'],
		init: function(editor) {
			var commandName = 'ntproducts';
			editor.addCommand(commandName, youtubeCmd);
			editor.ui.addButton('Productos', {
				label: editor.customData,
				command: commandName,
				icon: this.path + "images/icon.png"
			});
			CKEDITOR.dialog.add(commandName, CKEDITOR.getUrl(this.path + 'dialogs/dialog.js'))
		}
	})
})();