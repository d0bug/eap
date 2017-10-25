<?php
class EditorWidget extends Widget {
    public function render($data){
        static $isFirst = true;
        $field = $data['id'];
		$layout = $data['layout'];
        $layout = $layout ? $layout : 'full';
        $readonly = $data['readonly'] ? 'true':'false';
        $resizeType = isset($data['resizeType']) ? abs($data['resizeType']): 1;
        $layouts = array(
            'simple'=>"items:['source', 'formatblock', 'fontname', 'fontsize', 'forecolor', 'hilightcolor', 'bold','italic', 'underline', 'strikethrough', 'removeformat', 'image', 'table', 'anchor', 'link', 'unlink', 'jmeditor', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull'],",
            'minimal'=>"items:['fontname', 'fontsize', 'forecolor', 'hilightcolor', 'bold','italic', 'underline', 'image', 'table', 'jmeditor', 'source'],",
        );
        $layoutOptions = $layouts[$layout];
        if($isFirst) {
            $isFirst = false;
            $options = "var keEditorOptions = {cssPath : ['/static/kindeditor/plugins/code/prettify.css','/static/kindeditor/plugins/jmeditor/extra/mathquill-0.9.1/mathquill.css'],
                resizeType:$resizeType,
                minWidth:350,
                minHeight:50,
    			uploadJson : '".U('Util/Editor/upload_json')."',
    			fileManagerJson : '".U('Util/Editor/file_manager_json')."',
    			allowFileManager : true,
    			afterBlur: function(){
    			 this.sync();
    			},
    			afterCreate : function() {
    			    try{
    			         if(undefined ===keEditors) {
    			             keEditors = {};
    			         }
    			    } catch(e) {
    			         keEditors = {};
    			    }
    				var self = this;
    				KindEditor.ctrl(document, 13, function() {
    					self.sync();
    				});
    				KindEditor.ctrl(self.edit.doc, 13, function() {
    					self.sync();
    				});
    				delete keEditors[this.options.id];
    				keEditors[this.options.id] = this;
    				if(this.options.readonly) {
    				    this.readonly(true);
    				}
    			}}
    			";
        }
        $createOptions = "jQuery.extend({" . $layoutOptions . "id:'$field', readonly:$readonly}, keEditorOptions)";
        $script  = '<script type="text/javascript">
            ' . $options . '
            jQuery(function(){
                setTimeout(function(){

                    KindEditor.create("#' . $field . '", ' . $createOptions . ');
                }, 50)

            })
        </script>';
        return $script;
    }
}
?>