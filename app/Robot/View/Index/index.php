<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
</head>

<body>
<main class="main-page">
<fieldset>
	<legend>请求</legend>
	请求地址 <input id="url" name="" value="<?=$url?>" style="width:500px">
	<button type="button" onclick="start();" id="btn-request">开始</button>
	<button type="button" onclick="document.getElementById('item').value='';start();">更改</button> 
	<p>
	请求键名 <input id="item" name="" value="" style="width:200px"> 
	<button type="button" onclick="_continue(1);">恢复</button>
	<button type="button" onclick="removeItem();">清除</button>
	
	<p>
	请求记录 <select id="request_log" onchange="select_change(this)">
		<option></option>
	</select>
	<p>
	请求延时 <input id="timeout" name="" value="1" style="width:200px"> 
	<p>
	最大页码 <input id="max_page" name="" value="" style="width:200px"> 
</fieldset>
<fieldset>
	<legend>计划</legend>
	任务代码 <input id="code" name="" value="task()" style="width:500px"> 
	<button type="button" onclick="autoTask();" id="btn-task">开始</button>
	<p>间隔毫秒 <input id="millisec" name="" value="<?=$millisec ? : 1000?>" style="width:200px">
	<p>下次任务 <input id="next_task" name="" value="" style="width:500px"> 
</fieldset>
<fieldset>
	<legend>统计</legend>
	开始时间 <input id="start_time" name="" value="" style="width:200px"> 上次完成 <input id="last_time" name="" value="" style="width:200px"> 本次执行 <input id="execute_time" name="" value="" style="width:200px">
	<p>
	总计用时 <input id="use_time" name="" value="0" style="width:200px"> 
	<p>
	请求次数 <input id="requests" name="" value="0" style="width:200px"> 
	<p>
	平均用时 <input id="avg_time" name="" value="0" style="width:200px"> 上次用时 <input id="last_use" name="" value="0" style="width:200px"> s
</fieldset>
<fieldset>
	<legend>消息</legend>
	<div id="msg"></div>
</fieldset>
<script>
var d = new Date();
var XHR = [];
var t = 0;
var stop = 0;
var step = 0;
var lasttime = d.getTime();
var timeover = 0;
var firsttime = lasttime;
var start_time = d.getHours() +':'+ d.getMinutes() +':'+ d.getSeconds() +'.'+ d.getMilliseconds();
var offsettime = 0;
var interval = null;
var exec_task = 0;
REQ = []
timeout_id = null

//Local Storage 列表生成
var ele_request_log = document.getElementById('request_log');
for(var i=0; i<localStorage.length; i++){
    var key = localStorage.key(i);
    var val = localStorage.getItem(key);
    var option = document.createElement("option");
    option.setAttribute('value', val);
    option.innerHTML = key;
	try {
		ele_request_log.append(option);
	} catch (err) {
		console.log(err)
	}
}

//Local Storage 列表操作
function select_change(obj) {
    start(1);   
    document.getElementById('item').value = ele_request_log.options[ele_request_log.selectedIndex].text;
    document.getElementById('url').value = obj.value;
}

function api_reset(url, xhr) {
	if (!REQ[url]) {
		REQ[url] = 1
	} else {		
		REQ[url]++
	}
	
	if (5 > REQ[url]) {
		api()
	} else {
		message('Problem retrieving(' + XHR[xhr].status + '):' + XHR[xhr].statusText)
	}
}

//XHR 执行
function api(url)
{
	var d = new Date();
	var nowtime = d.getTime();
	lasttime = nowtime;
	var tm = d.getHours() +':'+ d.getMinutes() +':'+ d.getSeconds() +'.'+ d.getMilliseconds();
	document.getElementById('execute_time').value = tm;
			
    stop = 0;
    if (!url) {
        url = document.getElementById('url').value;
    }



    var xhr = nowtime;
    // clearTimeout( timeout_id )
    // timeout_id = setTimeout( "api_reset('" + url + "', " + xhr + ")", 300000 )
    XHR[xhr] = new XMLHttpRequest();
    XHR[xhr].onreadystatechange = function() {
        if (4 == XHR[xhr].readyState) {
        	// clearTimeout( timeout_id )
            if (200 == XHR[xhr].status) {
                var text = XHR[xhr].responseText;
                if (text) {
                    eval("var json = " + text + "; api_change(json, '" + xhr + "');");
                } else {
                    //message(24);
					api_reset(url, xhr)
                }

            } else if (0 == XHR[xhr].status || 504 == XHR[xhr].status || 502 == XHR[xhr].status) {
                api_reset(url, xhr)
            } else {
                message('Problem retrieving data(' + XHR[xhr].status + '):' + XHR[xhr].statusText);
            }
        } else if (1 != XHR[xhr].readyState && 2 != XHR[xhr].readyState && 3 != XHR[xhr].readyState) {
        	// clearTimeout( timeout_id )
            message('Problem(' + XHR[xhr].status + '):' + XHR[xhr].readyState);
        }
    };
    XHR[xhr].open('GET', url, true);
    XHR[xhr].timeout = 590000
    XHR[xhr].ontimeout = function (e) {
        api_reset(url, xhr)
    }
    XHR[xhr].send(null);
}

//XHR 回调
function api_change(json, func)
{
	if (json) {
		var type = Object.prototype.toString.call(json);
		
		if ("[object Object]" != type) {
			message(type);
		} else {
			document.getElementById('requests').value = 1 + new Number(document.getElementById('requests').value);
			var d = new Date();
			var nowtime = d.getTime();
			var offset = 0;
			if (offsettime) {
				offset = parseInt(nowtime) - parseInt(offsettime);
				message(offset);
				if (!stop) {
					offsettime = 0;
				}
			}
			document.getElementById('use_time').value = parseInt(nowtime) - parseInt(firsttime) - offset;
			document.getElementById('avg_time').value = parseInt(document.getElementById('use_time').value) / parseInt(document.getElementById('requests').value);
		
			var item = document.getElementById('item').value;
			var url = document.getElementById('url').value;
			if (json.code) {
				
				
				if (item) {
					localStorage.setItem(item, url);
				} else {
					search = url.search(/\?/);
					itm = url.substring(1, search);
					document.getElementById('item').value = itm.replace(/\//gi, '.');
					localStorage.setItem(document.getElementById('item').value, url);
				}
					
				message(json.msg);
				start();
				
			} else if (!stop) {
				//message(func +'_'+ json.msg);
				//JSON[func] = json;
				//eval("api_" + func + "()");
				var str = 0;
				search = json.msg.search(/\?/);
					substr = json.msg.substr(search + 1);//message(substr);
					split = substr.split(/&/);
					for (i=0; i<split.length; i++) {
						srch = split[i].search(/=/);
						substring = split[i].substring(0, srch);
						//message(substring);
						if ('page' == substring) {
							str = parseInt(split[i].substr(srch + 1));
							
							
							var tm = d.getHours() +':'+ d.getMinutes() +':'+ d.getSeconds() +'.'+ d.getMilliseconds();
							timeover = parseInt(d.getTime()) - parseInt(lasttime);
							
							//lasttime = d.getTime();
							var sec_milli = format_second(timeover);
							document.title = str +'('+ sec_milli +')'+ tm;
							document.getElementById('last_time').value = tm;
							//document.getElementById('last_use').value = timeover;
							document.getElementById('last_use').value = sec_milli;
							break;
						}
					}
				
				var max_page = parseInt(document.getElementById('max_page').value);
				var in_page = 1;
				if (json.data.lastTime) {
					in_page = 0;
					start(1);
					
				} else if (max_page) {
							if (str > max_page) {//message(str +'>'+ max_page);
								in_page = 0;
								start(1);
							}
				}
				
				if (in_page) {
					//var url2 = 'http://localhost.urlnk.com' + json.msg;
					var url2 = json.msg;
					if ('final' != url2) {
						document.getElementById('url').value = url2;
						
						var timeout = document.getElementById('timeout').value;
						if (!timeout) {
							timeout = 500;
						} else {
							//document.title = timeout;
						}
						t = setTimeout("api('"+ url2 +"')", timeout);
										//message(t);
						if (item) {
							localStorage.setItem(item, json.msg);
							//console.log(localStorage.length);
						} else {
							search = json.msg.search(/\?/);
							itm = json.msg.substring(1, search);
							document.getElementById('item').value = itm.replace(/\//gi, '.');
						}
						
						/*
						message(json.data.result);
						if (json.data) {
							if (json.data.result) {
								if (json.data.result.update) {
									message(166);
									message(JSON.parse(json.data.result.update));
								}
							}
						}*/
					}
					
				} else {
					message('max page! ' + json.data.lastTime);
				}
				
			} else {
				message('pause');
			}
		}
		
	} else {
		message('api_change ERROR');
	}
}



//开始
function start(s) {
	// 保持进行中
	if (0 === s) {
		step = 0;
	}
	//console.log(JSON.stringify([s, step]));
	
    btn = document.getElementById('btn-request');
    if (s || 1 == step) {
        btn.innerHTML = '继续';
        pause();
        step = 0;
		var d = new Date();
		offsettime = d.getTime();
    } else if (!step) {
        btn.innerHTML = '暂停';
        api();
        step = 1;
		document.getElementById('start_time').value = start_time;
		//offsettime = 0;
    } else {
		
    }
    document.title = offsettime;
}

//暂停
function pause() {
    //clearTimeout(t);
    stop = 1;
}

//恢复
function _continue(c)
{
    var item = document.getElementById('item').value;
    if (item) {
        var val = localStorage.getItem(item);
        document.getElementById('url').value = val;
    }
    if (c) {
        start();
    }
}

//移除
function removeItem() {
    var item = document.getElementById('item').value;
    if (item) {
        localStorage.removeItem(item);
    } else if (confirm('Clear localStorage?')) {
        localStorage.clear();
    }
}

//任务
function setTask() {
	var millisec = document.getElementById('millisec').value;
	var d = new Date()
	var time = d.getTime() + parseInt(millisec);
	d.setTime(time);
	//message(time);
	document.getElementById('next_task').value = d;
	interval = window.setInterval(document.getElementById('code').value, millisec);
	document.getElementById('btn-task').innerHTML = "停止";
}
function stopTask() {
	window.clearInterval(interval);
	document.getElementById('btn-task').innerHTML = "开始";
}
function task() {
	location.reload();
	var d = new Date();
	var start_time = d.getHours() +':'+ d.getMinutes() +':'+ d.getSeconds() +'.'+ d.getMilliseconds();
	document.title = d.getSeconds() +'.'+ d.getMilliseconds();
	
}
function autoTask() {
	if (exec_task) {
		stopTask();
		exec_task = 0;
	} else {
		setTask();
		exec_task = 1;
	}
}

function format_second(timeover) {
	timeover = new String(timeover);
	var second = 0;
	var ms = timeover;
	if (3 < timeover.length) {
		var sec = timeover.length - 3;
		second = timeover.substring(0, sec);
		ms = timeover.substring(sec);
	}
	return second + '.'+ ms;
}

//消息
function message(msg) {
	document.getElementById('msg').innerHTML = msg;
}

if (typeof(Storage) !== 'function') {
    message("抱歉! 不支持 web 存储。" + typeof(Storage));
}

<?php
if ($start) {
	echo 'start();';
}
if ($task) {
	echo 'autoTask();';
}
?>
</script>
</main>
</body>
</html>
