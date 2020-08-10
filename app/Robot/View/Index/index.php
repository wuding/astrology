<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
</head>

<body>
<main class="main-page">
<fieldset>
	<legend>请求</legend>
	请求地址 <input id="url" name="request_url" value="<?=$url?>" style="width:650px">
	<button type="button" onclick="start()" id="btn-request">开始</button>
	<button type="button" onclick="document.getElementById('item').value='';start();">更改</button>
	<p>
	请求键名 <input id="item" name="request_key" value="" style="width:650px">
	<button type="button" onclick="recovery(1)">恢复</button>
	<button type="button" onclick="removeItem()">清除</button>

	<p>
	请求记录 <select id="request_log" onchange="selectChange(this)">
		<option></option>
	</select>
	<p>
	请求延时 <input id="timeout" name="delay" value="1" style="width:200px">
	<p>
	最大页码 <input id="max_page" name="maximum" value="" style="width:200px">
</fieldset>
<fieldset>
	<legend>计划</legend>
	任务代码 <button type="button" onclick="autoTask()" id="btn-task">开始</button>
	<p>
	<textarea id="code" style="width:100%">testTask()</textarea>
	<p>间隔毫秒 <input id="millisec" name="interval" value="<?=$millisec ? : 1000?>" style="width:200px">
	<p>重复次数 <input id="repeat_time" name="repeat" value="" style="width:200px">
	   运行次数 <input id="run_times" value="" style="width:200px" disabled>
	<p>设置时间 <input id="config_time" value="" style="width:350px" disabled>
	   取消时间 <input id="cancel_time" value="" style="width:350px" disabled>
	<p>
	   最后运行 <input id="last_run" value="" style="width:350px" disabled>
	   下次任务 <input id="next_task" value="" style="width:350px" disabled>
</fieldset>
<fieldset>
	<legend>统计</legend>
	开始时间 <input id="start_time" value="" style="width:200px" disabled>
	上次完成 <input id="last_time" value="" style="width:200px" disabled>
	本次执行 <input id="execute_time" value="" style="width:200px" disabled>
	<p>
	总计用时 <input id="use_time" value="0" style="width:200px" disabled>
	上次用时 <input id="last_use" value="0" style="width:200px" disabled>
	实际总计 <input id="use_real" value="0" style="width:200px" disabled>
	<p>
	平均用时 <input id="avg_time" value="0" style="width:200px" disabled>
	请求次数 <input id="requests" value="0" style="width:200px" disabled>
	实际平均 <input id="avg_real" value="0" style="width:200px" disabled>
</fieldset>
<fieldset>
	<legend>消息</legend>
	<div id="msg"></div>
	<div id="note"></div>
</fieldset>
<script type="text/javascript" src="/js/robot.js?v=4"></script>
<script type="text/javascript">
<?php
if ($start) {
	echo 'start()';
}

if ($task) {
	echo 'autoTask()';
}
?>
</script>
</main>
</body>
</html>
