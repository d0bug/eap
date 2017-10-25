<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/showdate.js"></script>
<script type="text/javascript" src="/static/js/vote.js"></script>
投票模块化后台——添加新项目
<form method="post" action="<?php echo U('Modular/ModularVote/add')?>" onsubmit="return mysubmit();">
<input type="hidden" name="listcount" value="1" id="listcount"/>
	<ul>
	  <li>
	    <label>投票项目:</label>
	    <label><input type="text" name="vote_name" class="hidd"/>*</label>
	  </li>
	  <li><hr/></li>
	  <li>
	    <label>第1组配置:</label>
	    <label>单选<input type="radio" name="vote[No1][type]" value="1" checked="checked" /></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>多选<input type="radio" name="vote[No1][type]" value="2"/></label>
	  </li>
	  <li>
	    <label>标题:</label>
	    <label><input type="text" name="vote[No1][title]" class="hidd"/>*</label>
	  </li>
	  <li>
	    <label>选项:</label>
	    <label><input type="text" name="vote[No1][voptions]" class="hidd"/>*请将选项用逗号隔开</label>
	  </li>
	  <div id="vote"></div>
	  <li>
	    <td colspan="2"><input type="button" value="添加投票组" id="addbtn"/></label>
	  </li>
	   <li><hr/></li>
	  <li>
	    <label>上线时间:</label>
	    <label><input type="text" id="begintime" readonly="readonly" name="begintime" onclick="return Calendar('begintime')"/></label>
	  </li>
	  <li>
	    <label>下线时间:</label>
	    <label><input type="text"id="endtime" readonly="readonly" name="endtime" onclick="return Calendar('endtime')"/></label>
	  </li>
	  <li>
	    <label>投票介绍:</label>
	    <label><textarea name="description"></textarea></label>
	  </li>
	  <li>
	    <label>查看投票结果:</label>
	    <label>允许<input type="radio" name="rel" value="1" checked="checked" /></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>不允许<input type="radio" name="rel" value="0"/></label>
	  </li>
	  <li>
	    <label>投票时间间隔:</label>
	    <label><input type="text" name="spacing" size="5" value="0"/>N天后可再次投票,0表示此IP地址只能投一次</label>
	  </li>
	  <li>
	    <label><input type="submit"  colspan="2" value="提交" name="submit"/></label>
	  </li>
	</ul>
</form>