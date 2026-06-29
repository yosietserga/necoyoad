(function() {
	CKEDITOR.dialog.add('ntproducts', function(editor) {
		return {
			title: 'Productos',
			minWidth: CKEDITOR.env.ie && CKEDITOR.env.quirks ? 368 : 350,
			minHeight: 240,
			onShow: function() {
			 
			},
			onOk: function() {
				var width = this.getContentElement('general', 'width').getInputElement().getValue();
				var height = this.getContentElement('general', 'height').getInputElement().getValue();
				var url = this.getContentElement('general', 'url').getInputElement().getValue();
                
                
				var text = '<iframe title="YouTube video player" class="youtube-player" type="text/html" width="'+ width +'" height="'+ height +'" src="https://www.youtube.com/embed/v/'+ url +'?rel=0" frameborder="0"></iframe>';
                
				this.getParentEditor().insertHtml(text);
			},
			contents: [{
				label: editor.lang.common.generalTab,
				id: 'general',
				elements: [
                    {
    					type: 'html',
    					id: 'pasteMsg',
					    html: '<div style="white-space:normal;width:500px;"><img style="margin:5px auto;" src="' + CKEDITOR.config.baseHref + 'images/vimeo_large.png' + '"><br />Copia la Url del video YouTube (Ejemplo, https://www.youtube.com/watch?v=KVu3gS7iJu4) y pegalo en el campo <b>Url del video YouTube</b></div>'
    				},
                    {
    					type: 'text',
    					id: 'width',
    					label: 'Ancho:',
					    style: 'width:90px;height:30px;float:left;',
    				},
                    {
    					type: 'text',
    					id: 'height',
    					label: 'Alto:',
					    style: 'width:90px;height:30px;float:left;',
    				},
                    {
    					type: 'text',
    					id: 'url',
    					label: 'Url del video YouTube:',
                    	validate : CKEDITOR.dialog.validate.notEmpty( 'Debes igresar la url del video' ),
                    	required : true
    				}
                ]
			}]
		}
	})
})();