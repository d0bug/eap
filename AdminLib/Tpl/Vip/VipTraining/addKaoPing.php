<script type="text/javascript" src="/static/js/training.js"></script>
<script type="text/javascript">
/*function fenshu(obj){
    //console.log(obj.value);
    //alert(obj.value);
    
   	var text = $(this).val();
    alert(text);exit;
    $("input[name='n']").val(text);


}
*/

$(function(){
    
    $("input[name='fenshu']").change(function(){
        var ping1 = $("textarea[name='pingyu']").val();
        if(ping1 != '' ){
            alert('评语和考试评语只能填写一个！！！')
            exit;
        }       
        var text = $(this).val();
        if(parseInt(text) < parseInt(60)){
            var msg = '今天是你的第X次考试，得了'+text+'分 ，获得D级！帅锅（美妞），这个成绩可不行哦，不要把你的“洒脱”和“大气”都用在此处啦！相信你是有实力的哈！';
        }else if(parseInt(text) >= parseInt(60) && parseInt(text) <= parseInt(70)){
            var msg='今天是你的第X次考试，得了'+text+'分 ，获得C级！今后得努力加油了哈，不然会被甩掉一大截儿的！相信你是有实力的！！！';
        }else if(parseInt(text) >= parseInt(70) && parseInt(text) <= parseInt(80)){            
            var msg='今天是你的第X次考试，得了'+text+'分 ，获得B级！亲，再勤奋一点，再细心一点，你绝对属于第一梯队的那一群。看好你哦！！！';
        }else if(parseInt(text) >= parseInt(80) && parseInt(text) <= parseInt(90)){
            var msg='今天是你的第X次考试，得了'+text+'分 ，获得A级！小鲜肉，相信聪明的你，成绩不会止步于此的，FIGHTING!!!';
        }else if(parseInt(text) >= parseInt(90) && parseInt(text) <= parseInt(100)){
            var msg='今天是你的第X次考试，得了'+text+'分 ，获得S级！大学霸，今后还望多多指教，么么哒！';
        }else if(parseInt(text) == parseInt(100)){
            var msg='今天是你的第X次考试，得了'+text+'分 ，获得SSS级！哇塞，你是偶滴男神女神！约不？！';
        }        
        //alert(msg);
        $("textarea[name='fen_pingyu']").val(msg);
    
    });

})

function myCheck(){
     pingyu = document.getElementById("pingyu").value;
     fen_pingyu = document.getElementById("fen_pingyu").value;
     if(pingyu != false && fen_pingyu != false){
        alert("评语和考试评语只能填写一个！！！");
        return false;            
     }
     if(pingyu == false && fen_pingyu == false){
         alert("请填写评语！");
         return false;
     }
}

</script>

<div class="easyui-layout" data-options="fit: true">
	<div region="north" data-options="fit: true, collapsible: false, border: false">
		<form id="dict-add-form" method="post" novalidate action="/Vip/VipTraining/dict_add_kaoping" onSubmit="return myCheck()">
        <input type="hidden" name="id" value="<?php echo $te_id;?>" />
        <input type="hidden" name="tr_id" value="<?php echo $teInfo['tr_id']?>"/>
			<table width="90%" border="0" cellpadding="0" cellspacing="0" class="tableInfo">
                
				 <tr>
					<td class="alt right">姓名：</td>
					<td>
					<span><?php echo $teInfo['te_name'];?></span>
					</td>
				</tr>
			
				<tr>
					<td class="alt right">学科：</td>
					<td>
						<?php echo $xuekeList;?>
					</td>
				</tr>
               
               	<tr>
					<td class="alt right">培训期：</td>
					<td>
						<?php echo $teInfo['tr_name'];?>
					</td>
				</tr>
                <tr>
					<td class="alt right">评语：</td>
					<td>
						<textarea name="pingyu" id="pingyu" rows="5" cols="50" value=''></textarea>
					</td>
				</tr>
              
                
                <tr>
                    <td class="alt right">考试时间：</td>
                    <td>
            		
                        <select name="time">
                        <?php 
                	       if(!empty($levelList)){
                				foreach ($levelList as $key=>$level){
                				?>     
                                           			
                               <option  id="span_level_<?php echo $key+1;?>"  value="<?php echo $level['time']?>_<?php echo $key+1;?>" ><?php echo $level['time']?></option>                               
                			<?php
                				}
                			}
                			?>
                         </select>
                        <!--/div-->
                    </td>
                </tr>
                <tr>
                    <td class="alt right">考试分数</td>
                    <td><input type="text" name="fenshu" id="fenshu" value="" width="20px;"  />&nbsp;&nbsp;<span class="alt right">排名</span>
                    <span></span><input type="text" name="paiming" value="" width="20px;" /></td>                    
                </tr>
                <tr>
                    <td class="alt right">考试评语</td>
                    <td>
                    <ul>
                    <li><span style="color: red;">* 请注意修改评语中的考试次数！！（今天是你第X考试....）</span></li>
                    <li><textarea name="fen_pingyu" id="fen_pingyu" style="width: 300px;"></textarea></li>
                    </ul>
                    <!--input type="text" style="height: 50px; width: 300px;" name="fen_pingyu"  value="" /-->
                    </td>
                </tr>
			
            </table>
            
            <span style="padding-left: 200px; margin-top: 5px;"><input style="width: 60px; height: 30px;" type="submit" value="提交"></span>

		</form>
	</div>

</div>
