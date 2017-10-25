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
            <div data-options="name:'name'">学生姓名</div>

        </div>
        <a href="/Vip/VipAddress/serviceExport" style="padding-left: 50px; color:blue;" data-options="plain:true, iconCls:'icon-remove'" >导出</a>
    </span>
</div>

<table id="single_process_table"></table>

<div id="basic-index-knowledge-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-knowledge-add-dlg-buttons'" style="width:550px;height:235px;padding:5px;"></div>


<script language='javascript' type='text/javascript'>
    $(function(){
        $('#single_process_table').datagrid({
            url:'/Vip/VipAddress/serviceReviewInfo',
            border:false,
            fitColumns:false,
            singleSelect:true,
            pagination:true,
            pageSize:10,
            pageList:[10,15,20,25,100],
            columns:[[
                {field:'id',title:'序号',width:50},
                {field:'ucode',title:'学号',width:200},
                {field:'uname',title:'姓名',width:200},
                {field:'phone',title:'电话',width:200},
                {field:'create_time',title:'评价时间',width:200},
                {field:'chakan',title:'详情',width:200},

            ]],
            onLoadError: function(){
                $.messager.alert('提示','没有符合条件的搜索结果！');
            }
        });

        /***************************查询邮箱*********************************/
        $('#single_search_process_box').searchbox({
            searcher:function(value,name){
                //当value为空的时候，搜索所有邮箱
                $('#single_process_table').datagrid('load', {
                    search_value: value,
                    search_name: name
                });
            },
            menu:'#single_search_process_menu',
            prompt:'请输入学生姓名'
        });
    });

    //详情
    function xiangqing(id){
        var params = {
            height: 600,
            href:'/Vip/VipAddress/serviceReviewRow?id='+id,
            title:'详情',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }


</script>
</body>
</html>