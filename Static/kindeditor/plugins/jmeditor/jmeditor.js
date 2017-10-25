KindEditor.plugin('jmeditor', function(K) {
        var editor = this, name = 'jmeditor';
        // 点击图标时执行
        editor.clickToolbar(name, function() {
            var dlg = K.dialog({
                title:'公式编辑器',
                width:500,
                body:'<iframe id="jme" name="jme" frameborder="no" scrolling="no" width="350" height="300" src="' + K.basePath + '/plugins/jmeditor/dialogs/mathdialog.html"></iframe>',
                closeBtn : {
                        name : '关闭',
                        click : function(e) {
                                dlg.remove();
                        }
                },
                yesBtn : {
                        name : '确定',
                        click : function(e) {
                                var thedoc = document.frames ? document.frames['jme'] : document.getElementById('jme');
                                var thewindow = thedoc.contentWindow;
                                try{
                                    if(thewindow) {
                                        thewindow.setHtml(editor);
                                    } else {
                                        thedoc.setHtml(editor);
                                    }
                                } catch(e){}
                                dlg.remove();
                        }
                },
                noBtn : {
                        name : '取消',
                        click : function(e) {
                                dlg.remove();
                        }
                }
            })
                
        });
});