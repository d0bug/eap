<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8" />
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    <link href="/static/css/question.css" type="text/css" rel="stylesheet" />
</head>
<body style="padding: 5px;">
<div class="it_model_top" style="height:auto;width:auto;padding:5px;border-bottom:1px solid #B4B4B4;">

    <span style="margin-left: 10px;margin-right:10px;">
        <input id="single_search_process_box" style="width:250px;height:26px">
        <div id="single_search_process_menu" fit="true">
            <div data-options="name:'name'">视频名称</div>

        </div>
        <!--a href="/Vip/VipAddress/serviceExport" style="padding-left: 50px; color:blue;" data-options="plain:true, iconCls:'icon-remove'" >导出</a-->
        <input style="margin-left: 10px;" type="button" name="addVideo" value="上传视频" onclick="addVideo()">
        <input style="margin-left: 10px;" type="button" name="delVideo" value="删除视频" onclick="delVideo()">
    </span>
</div>

<table id="single_process_table"></table>

<div id="basic-index-knowledge-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-knowledge-add-dlg-buttons'" style="width:550px;height:235px;padding:5px;"></div>

<script src="http://gosspublic.alicdn.com/aliyun-oss-sdk-4.4.4.min.js"></script>
<script language='javascript' type='text/javascript'>
    $(function(){
        $('#single_process_table').datagrid({
            url:'/Vip/VipManagement/getTrainList',
            border:false,
            fitColumns:false,
            singleSelect:true,
            pagination:true,
            pageSize:10,
            pageList:[10,15,20,25,100],
            columns:[[
                {field:'shanchu',width:60},
                {field:'id',title:'序号',width:50},
                {field:'video_type',title:'视频类型',width:200},
                {field:'title',title:'视频名称',width:200},
                {field:'create_name',title:'上传人',width:200},
                {field:'create_time',title:'上传日期',width:200},
                {field:'chakan',title:'操作',width:50},
                {field:'bianji',width:50},
                {field:'jilu',width:55},

            ]],
            onLoadError: function(){
                //$.messager.alert('提示','没有符合条件的搜索结果！');
                $.messager.show({
                    title:'提示',
                    msg:'暂时没有记录!',
                    timeout:5000,
                    showType:'slide'
                });
            }
        });

        //搜索
        $('#single_search_process_box').searchbox({
            searcher:function(value,name){
                //当value为空的时候，搜索所有邮箱
                $('#single_process_table').datagrid('load', {
                    search_value: value,
                    search_name: name
                });
            },
            menu:'#single_search_process_menu',
            prompt:'请输入视频名称'
        });
    });

    //详情
    function xiangqing(id){
        var params = {
            height: 500,
            href:'/Vip/VipManagement/trainVideoRow?id='+id,
            title:'视频播放',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }
    //浏览记录
    function jilu(id){
        var params = {
            height: 500,
            href:'/Vip/VipManagement/videoBrowse?id='+id,
            title:'浏览记录',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }

    //上传视频
    function addVideo(){
        var params = {
            height: 600,
            href:'/Vip/vip_management/addVideo',
            title:'上传视频',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }

    //编辑
    function bianji(id){
        var params = {
            height: 500,
            href:'/Vip/vip_management/trainVideoEdit?id='+id,
            title:'编辑',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }

    //删除
    function delVideo(){
        obj = document.getElementsByName("shanchu");
        check_val = [];
        for(k in obj){
            if(obj[k].checked)
                check_val.push(obj[k].value);
        }
        if(!confirm("您确定要删除吗？")){
            return false;
        }
        var id = check_val;
        var url = '/vip/vip_management/delManagement';
        $.ajax({
            type:'POST',
            url:url,
            data:{id:id},
            dataType: 'json',
            success:function(data) {
                if(data.status ==1 ){
                    alert(data.msg);
                    window.location.reload();
                }else if(data.status==0 || data.status==2){
                    alert(data.msg);
                } else{
                    alert('出现错误！');
                }
            },
            error : function() {
                alert("异常！");
            }
        });
    }

</script>
</body>
</html>