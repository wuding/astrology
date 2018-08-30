<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
textarea {
	width: 100%;
}
	</style>
</head>

<body>
<fieldset>
	<legend>Cookie</legend>
	<form method="post" action="/robot/alimama/cookie" target="_cookie">
		<textarea name="cookie" rows="5"><?=htmlspecialchars($cookie)?></textarea>
		<button type="submit" id="btn_cookie">Set</button>
	</form>
</fieldset>

<fieldset>
	<legend>登录</legend>
	<form method="post" action="https://login.taobao.com/member/login.jhtml?style=mini&newMini2=true" target="taobao_login">
		<p>
			<input name="TPL_username" value="" placeholder="TPL_username">
			<input name="TPL_password" value="" type="password" placeholder="TPL_password">
			<button type="submit" id="btn_login">Login</button>
		</p>
		<p>
			<input name="TPL_redirect_url" value="http://login.taobao.com/member/taobaoke/login.htm?is_login=1" placeholder="TPL_redirect_url" title="http://www.alimama.com">
			<input name="from" value="alimama" placeholder="from">
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>任务</legend>
	<form onsubmit="return false">
		任务地址 <input id="task_url" value="/robot?task=0&millisec=1000000&start=1&url=/robot/alimama/download/excel?debug%26type=json%26bill=3" style="width:800px">
		<p>
			任务名称 <input id="task_name" value="下载excel" style="width:200px"> 
			<button type="button" onclick="setTaskInfo()">Set</button> 
			<button type="reset">Reset</button>
		</p>
		<p>
			任务链接 
			<a href="" 
			   target="_robot" id="btn_task">
				
			</a>
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>计划</legend>
	<form onsubmit="return false">
		计划代码 <input id="interval_code" value="task()" style="width:500px"> 		
		<button type="button" onclick="execCode()" id="interval_exec">Exec</button>
		<p>间隔毫秒 <input id="interval_ms" value="<?=$millisec ? : 1000?>" style="width:200px">
		<button type="button" onclick="autoTasks()" id="interval_task">Set</button>
		<p>下次时间 <input id="interval_next" value="" style="width:500px" disabled> 
	</form>
</fieldset>

<fieldset>
	<legend>设定</legend>
	设定代码 <input id="timeout_code" value="autoTasks();execCode()" style="width:500px">
	<p>剩余秒数 <input id="timeout_ms" value="5" style="width:200px"> 
	<button type="button" onclick="autoTasks(1)" id="timeout_task">Set</button>	
	<p>设定时间 <input id="timeout_next" value="" style="width:500px" disabled> 
</fieldset>

倒计时
登录
<a href="" target="robot">下载excel</a>
	发送邮件
	复制cookie
转为csv
解析csv
	检查数据
	优化表
备份

<script>
interval = null
timeout = null
win = null
task_once = 0
exec_tasks = [0, 0]
set_tasks = [ intervalSet, timeoutSet ]
stop_tasks = [ stopInterval, stopTimeout ]

/**
 * 批量执行
 */
function task() {
	// location.reload();
	
	start_time = getTime( 1 )
	document.title = start_time;
	console.log(start_time)
	
	if ( !task_once ) {
		interval_next.value = getDate( interval_ms.value )
	}
	
	btn_cookie.click()	
	setTimeout("win = window.open('about:blank', 'taobao_login')", 1000)
	setTimeout("btn_login.click()", 3000)
	setTimeout("win.close()", 19000)
	setTimeout("btn_task.click()", 20000)
}


/**
 * 立即运行代码一次
 */
function execCode() {
	task_once = 1
	setTimeout( interval_code.value, 0 )
}

/**
 * 设定或取消间隔任务
 */
function autoTasks( no ) {
	no = no || 0
	if ( exec_tasks[ no ] ) {
		stop_tasks[ no ]()
		exec_tasks[ no ] = 0
	} else {
		set_tasks[ no ]()
		exec_tasks[ no ] = 1
	}
}

/**
 * 设定间隔任务
 */
function intervalSet() {
	task_once = 0
	ms = interval_ms.value
	interval = window.setInterval( interval_code.value, ms )
	interval_task.innerHTML = 'Cancel'
	interval_next.value = getDate( ms )
}

function stopInterval() {
	window.clearInterval( interval )
	interval_task.innerHTML = 'Set'
	interval_next.value = ''
}

function getDate( ms, unit ) {
	unit = unit || 1
	d = new Date()
	time = d.getTime() + parseInt( ms ) * unit
	d.setTime( time )
	return d
}

function getTime( ms ) {
	d = new Date()
	time = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds()
	if ( ms ) {
		time += '.' + d.getMilliseconds()
	}
	return time
}



/**
 * 设置倒计时
 */
function timeoutSet() {
	timeout = window.setInterval( "countDown();", 1000 )
	timeout_task.innerHTML = 'Cancel'
	timeout_next.value = getDate( timeout_ms.value, 1000 )
}

/**
 * 取消倒计时
 */
function stopTimeout() {
	window.clearInterval( timeout )
	timeout_task.innerHTML = 'Set'
	timeout_next.value = ''
}

/**
 * 倒计时函数
 */
function countDown() {
	sec = parseInt( timeout_ms.value ) - 1
	timeout_ms.value = sec
	if (1 > sec) {
		stopTimeout()
		window.setTimeout( timeout_code.value, 100 )
	}	
}



/**
 * 设置目标任务地址和名称
 */
function setTaskInfo() {
	btn_task.innerHTML = task_name.value
	btn_task.href = task_url.value
}

<?php
if ($task) {
	echo 'autoTasks();';
}
?>
</script>

</body>
</html>
