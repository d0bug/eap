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
            <div data-options="name:'name'">试卷名称</div>
        </div>
        <!--a href="/Vip/VipAddress/serviceExport" style="padding-left: 50px; color:blue;" data-options="plain:true, iconCls:'icon-remove'" >导出</a-->
    </span>
</div>

<table id="single_process_table"></table>

<div id="basic-index-knowledge-add-dlg" class="easyui-dialog" data-options="modal:true,closed:true,buttons:'#basic-index-knowledge-add-dlg-buttons'" style="width:550px;height:235px;padding:5px;"></div>

<script src="http://gosspublic.alicdn.com/aliyun-oss-sdk-4.4.4.min.js"></script>
<script language='javascript' type='text/javascript'>
    $(function(){
        $('#single_process_table').datagrid({
            url:'/Vip/VipManagement/getTestList',
            border:false,
            fitColumns:false,
            singleSelect:true,
            pagination:true,
            pageSize:10,
            pageList:[10,15,20,25,100],
            columns:[[
                {field:'id',title:'序号',width:50},
                {field:'lecture_file_name',title:'考核名称',width:200},
                {field:'kaohetime',title:'考核时间',width:280},
                {field:'kaohe',title:'考核状态',width:55},
                {field:'daochu',title:'操作',width:80},
                {field:'chakan',width:55},

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
            prompt:'请输入试卷名称'
        });
    });

    //查看
    function kaohe(id){
        var params = {
            height: 500,
            href:'/Vip/VipManagement/trainTestStatus?id='+id,
            title:'视频播放',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }

    //考核状态
    function kaohe(id){

        var params = {
            height: 500,
            href:'/Vip/vip_management/trainTestStatus?id='+id,
            title:'考核状态',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }
    function kaoheend(id){
        var params = {
            height: 500,
            href:'/Vip/vip_management/trainTestStatus?id='+id,
            title:'考核状态',
        };
        $('#basic-index-knowledge-add-dlg').dialog(params).dialog('open');
        form ='dict-add-form';
    }

    //导出成绩单
    function daochu(id,kh_status){
        if(kh_status != 2){
            alert("结束考核才可以导出成绩单");
            return false;
        }
        window.location.href='/Vip/vip_management/exportTestExcel?id=' + id;

        //导出本试卷的成绩
    }
    //查看试卷
    function shijuan(id) {
        var params = {
            height: 600,
            href:'/Vip/vip_management/checkPaper?id='+id,
            title:'查看试卷',
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