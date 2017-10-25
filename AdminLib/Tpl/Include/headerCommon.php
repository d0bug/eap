<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/static/lhgdialog/lhgdialog.js"></script>
    <script type="text/javascript">
    //jQuery = top.jQuery;
    var dialogWindow = {drag:true,lock:false,focus:true,cache:false};
    var dialogAlert = {};
    var dialogConfirm = {};
    var dialogMessage = {};
    var dialogLock = {};
    jQuery(function(){
        jQuery('a.winLink').click(function(){
            return actWindow(this);
        })
    })
    function actWindow(link) {
        var title=jQuery(link).attr('dlgTitle');
        var url = 'url:' + jQuery(link).attr('href');
        var id=jQuery(link).attr('id');
        var winSetting = {id:id,title:title,content:url};
        var width = Math.abs(jQuery(link).attr('width'));
        var height = Math.abs(jQuery(link).attr('height'));
        var lock = jQuery(link).attr('lock') || false;
        if(width>0)winSetting['width'] = width;
        if(height>0)winSetting['height'] = height;
        winSetting['lock'] = lock;
        top.jQuery.dialog(jQuery.extend(dialogWindow, winSetting));
        return false;
    }
    </script>
<style type="text/css">
    .sorter{width:30px;text-align:center}
    select{background: none repeat scroll 0 0 #F9F9F9;
    border-color: #666666 #CCCCCC #CCCCCC #666666;
    border-style: solid;
    border-width: 1px;
    color: #333333;
    padding: 2px;
    vertical-align: middle;}
    </style>