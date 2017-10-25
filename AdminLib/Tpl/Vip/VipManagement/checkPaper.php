<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
    <meta charset="utf-8" />
    <?php include TPL_INCLUDE_PATH . '/headerCommon.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyui.php'?>
    <?php include TPL_INCLUDE_PATH . '/easyuiMove.php'?>
    <link href="/static/css/uploadify.css" type="text/css" rel="stylesheet" />
    <link href="/static/css/vip.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div id="main">
        <h1 align="center"><?=$result['testName']?></h1>
        <div  style="padding-left: 400px;"><span style="font-size: 14px;">总分:<?=$result['testScore']?></span></div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
            <?php foreach($result['dati'] as $key=>$val):?>
            <!--tr>
                <td>
                   <span style="font-size: 16px;"><?=$val['title']?>(每小题<?=$val['quesScore']?>,共<?=$val['totalScore']?>分)</span>
                </td>
            </tr-->
                <tr style="border: 1px solid darkgray">
                    <span style="font-size: 16px; border-bottom: 1px solid slategray">
                     <?php if($val['code'] == 'QU1000'):?>
                           一、<?=$val['title']?>(每小题<?=$val['quesScore']?>,共<?=$val['totalScore']?>分)
                     <?php elseif($val['code'] == 'QU1001'):?>
                           二、<?=$val['title']?>(每小题<?=$val['quesScore']?>,共<?=$val['totalScore']?>分)
                         <?php elseif($val['code'] == 'QU1002'):?>
                            三、<?=$val['title']?>(每小题<?=$val['quesScore']?>,共<?=$val['totalScore']?>分)
                     <?php endif?>
                    </span>
                    <?php foreach ($val['timu'] as $k=>$v):
                        $xuhao++;?>
                        <?php if($val['code'] == 'QU1000' || $val['code'] == 'QU1001'):?>
                            <div style=" margin-bottom: 10px; margin-top: 10px; border-bottom: dashed 1px dimgray">
                                <ul>
                                    <li>
                                        <span><?=$xuhao?>、</span><span><?=$v['content_text']?></span>
                                    </li>
                                </ul>

                                <ul>
                                    <li>
                                    <div style="margin-left:15px;">
                                        <ul >
                                            <?php foreach($v['xuanxiang'] as $xke=>$xval):?>
                                                <?php foreach ($zimu as $zk=>$zv):?>
                                                    <li>
                                                        <?php if($xval['is_answer'] == 1):?>
                                                            <span style="color: green">
                                                            <?php else:?>
                                                            <span>
                                                            <?php endif?>
                                                                <?php if($xke ==$zk ):?>
                                                                    <?=$zv?>、<?=$xval['content_text']?>
                                                                <?php endif;?>
                                                             </span>

                                                    </li>
                                                <?php endforeach;?>
                                            <?php endforeach?>
                                        </ul>
                                    </div>
                                    </li>

                                </ul>
                                <ul>
                                    <li><span style="color: #0A6583">解析: <?=$v['analysis_text']?></span>
                                    </li>
                                </ul>
                            </div>
                        <?php endif;?>
                        <?php if($val['code'] == 'QU1002'):?>
                            <div style=" margin-bottom: 10px; margin-top: 10px; border-bottom: dashed 1px dimgray">
                                <ul>
                                    <li>
                                        <span><?=$xuhao?>、</span><span><?=$v['content_text']?></span>
                                    </li>
                                </ul>
                                <ul>
                                    <li><span style="color: #0A6583">解析: <?=$v['analysis_text']?></span></li>
                                </ul>
                            </div>
                        <?php endif;?>

                    <?php endforeach;?>





                </tr>


            <?php endforeach?>


        </table>
</div>


</body>


