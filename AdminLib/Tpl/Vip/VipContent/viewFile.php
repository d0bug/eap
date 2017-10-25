<!doctype html>
<html>
<head>
    <title>FlexPaper</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyui.php'?>
	<?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width" />
    <style type="text/css" media="screen">
        html, body	{ height:100%;font-size:12px; }
        body { margin:0; padding:0; overflow:auto; }
        #flashContent { display:none; }
    </style>

    <link rel="stylesheet" type="text/css" href="/static/css/flexpaper.css" />
    <script type="text/javascript" src="/static/js/flexpaper/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/flexpaper/flexpaper.js"></script>
    <script type="text/javascript" src="/static/js/flexpaper/flexpaper_handlers.js"></script>
</head>
<body>
<div region="center">
<?php if($is_exists == 0):?><br><br>&nbsp;&nbsp;&nbsp;&nbsp;
	<font size="3">抱歉，没有找到预览版文档！</font>
<?php else:?><br>&nbsp;
	<font size="3">文档标题：<?php echo $title;?></font>
	<div style="position:absolute;left:10px;top:40px;">
	<div id="documentViewer" class="flexpaper_viewer" style="width:950px;height:750px"></div>
<?php endif;?>
<script type="text/javascript">
function getDocumentUrl(document){
	return "php/services/view.php?doc={doc}&format={format}&page={page}".replace("{doc}",document);
}

var startDocument = "Paper";

$('#documentViewer').FlexPaperViewer(
{ config : {
	SWFFile : '<?php echo $swf_url?>',
	Scale : 4,
	ZoomTransition : 'easeOut',
	ZoomTime : 0.1,
	ZoomInterval : 0.2,
	FitPageOnLoad : true,
	FitWidthOnLoad : true,
	FullScreenAsMaxWindow : false,
	ProgressiveLoading : true,
	MinZoomSize : 0.2,
	MaxZoomSize : 5,
	SearchMatchAll : false,
	InitViewMode : 'Portrait',
	RenderingOrder : 'flash',
	StartAtPage : '0',

	ViewModeToolsVisible : true,
	ZoomToolsVisible : true,
	NavToolsVisible : true,
	CursorToolsVisible : true,
	SearchToolsVisible : true,
	WMode : 'window',
	localeChain: 'en_US'
}}
);
</script>
<script type="text/javascript">
var url = window.location.href.toString();

if(location.length==0){
	url = document.URL.toString();
}

if(url.indexOf("file:")>=0){
	jQuery('#documentViewer').html("<div style='position:relative;background-color:#ffffff;width:420px;font-family:Verdana;font-size:10pt;left:22%;top:20%;padding: 10px 10px 10px 10px;border-style:solid;border-width:5px;'><img src='http://flexpaper.devaldi.com/resources/warning_icon.gif'>&nbsp;<b>You are trying to use FlexPaper from a local directory.</b><br/><br/> FlexPaper needs to be copied to a web server before the viewer can display its document properlty.<br/><br/>Please copy the FlexPaper files to a web server and access the viewer through a http:// url.</div>");
}
</script>
</div>
</body>
</html>