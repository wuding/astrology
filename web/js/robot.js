
// 时间
var dt = new Date()
firstTime = lastTime = dt.getTime()
startTime = timeFormat(dt)

// 整数定义
timeoutUrl = timeOver = offsetTime = stop = step = execTask = 0
timeoutMs = 300000
timeoutApi = 100
timeoutVal = 1
pageNo = 1
frequency = 30

// 可未定义
intervalTsk = timeoutId = lastUrl = null

// 数组定义，连等会合并键名
XHR = []
REQ = []

/* 通用 */

// 简化
function ele(id) {
    return document.getElementById(id)
}

function num(id) {
    return parseInt(ele(id).value)
}

// 列表操作
function selectChange(obj) {
    start(1)
    ele('item').value = itm = log.options[ log.selectedIndex ].text
    ele('url').value = obj.value
    recovery(0, itm)
}

// 格式化时间
function timeFormat(d) {
    return tm = d.getHours() +':'+ d.getMinutes() +':'+ d.getSeconds() +'.'+ d.getMilliseconds()
}

// 毫秒格式化为秒 直接除 1000 啊
function secondFormat(time) {
    time = new String(time)
    sec = 0
    ms = time
    if (3 < time.length) {
        len = time.length - 3
        sec = time.substring(0, len)
        ms = time.substring(len)
    }
    return sec + '.'+ ms
}


/* 项目 */

// 把网址转为名称
function setItemName(url) {
    url = url.trim()
    search = url.search(/\?/)
    itm = url.substring(0, search)
    itm = itm.replace(/^http:\/+/gi, '')
    itm = itm.replace(/^\/+/gi, '')
    itm = itm.replace(/:\/+/gi, '.')
    itm = itm.replace(/\//gi, '.')
    ele('item').value = itm
    return itm
}

function record(nm) {
    obj = {
        url: ele('url').value,
        delay: ele('timeout').value,
        maximum: ele('max_page').value,
        interval: ele('millisec').value,
        repeat: ele('repeat_time').value,
        code: ele('code').value
    }
    //console.log(obj)
    json = JSON.stringify(obj)
    localStorage.setItem(nm, json)
}

// 移除项目
function removeItem() {
    item = ele('item').value
    if (item) {
        localStorage.removeItem(item)
    } else if (confirm('Clear localStorage?')) {
        localStorage.clear()
    }
}

// Local Storage 列表生成
function localLog() {
    log = ele('request_log')
    for (i = 0; i < localStorage.length; i++) {
        opt = document.createElement("option")
        opt.innerHTML = key = localStorage.key(i)
        val = localStorage.getItem(key)
        obj = JSON.parse(val)
        url = obj.url
        opt.setAttribute('value', url)
        try {
            log.append(opt)
        } catch (err) {
            console.log(err)
        }
    }
}

// 从项目恢复
function recovery(c, itm) {
    item = itm || ele('item').value
    if (item) {
        json = localStorage.getItem(item)
        obj = JSON.parse(json)
        console.log(obj)
        wayback(obj)
    }
    if (c) {
        start()
    }
}

function wayback(obj) {
    ele('url').value = obj.url
    ele('timeout').value = obj.delay
    ele('max_page').value = obj.maximum
    ele('millisec').value = obj.interval
    ele('repeat_time').value = obj.repeat
    ele('code').value = obj.code
}

/* API */



// XHR 执行
function api(url) {
    d = new Date()
    xhr = lastTime = d.getTime()
    ele('execute_time').value = timeFormat(d)

    //stop = 0
    url = url || ele('url').value
    // 同一地址非连续请求无次数限制
    if (lastUrl != url) {
        REQ[url] = -1
        lastUrl = url
    }

    if (!REQ[url]) {
        REQ[url] = 1
    } else {
        REQ[url]++
    }

    if (frequency < REQ[url]) {
        message('max request times '+ frequency)
        // throw new Error('exit')
        return false
    } else {
        timeoutApi = 100 + REQ[url] * 100
    }

    clearTimeout(timeoutId)
    timeoutId = setTimeout("apiAgain('" + url + "', " + xhr + ")", timeoutMs)

    readyArr = [1, 2, 3]
    statusArr = [0, 502, 504]
    XHR[xhr] = x = new XMLHttpRequest()
    x.onreadystatechange = function() {
        clearTimeout(timeoutId)
        readySt = x.readyState
        if (4 == readySt) {
            if (200 == x.status) {
                text = x.responseText
                if (text) {
                    eval("json = " + text + "; apiCall(json, '" + xhr + "');")
                } else {
                    message('no response text ('+ xhr +') : '+  url)
                    apiAgain(url, xhr)
                }

            } else if (statusArr.includes(x.status)) {
                apiAgain(url, xhr)

            } else {
                statusMsg(x, 'Problem retrieving data')
            }

        } else if (!readyArr.includes(readySt)) {
            statusMsg(x, 'readyState: '+ readySt +' Problem status')
        }
    }
    x.open('GET', url, true)
    /*
    x.timeout = 590000
    x.ontimeout = function (e) {
        apiAgain(url, xhr)
    }
    */
    x.send(null)
}

// 重新请求 api
function apiAgain(url, xhr) {
    if (frequency > REQ[url]) {
        setTimeout("api()", timeoutApi)
    } else {
        statusMsg(XHR[xhr], 'Problem retrieving, try times: '+ REQ[url])
    }
}



// XHR 回调
function apiCall(json, func) {
    if (json) {
        type = Object.prototype.toString.call(json)
        if ("[object Object]" != type) {
            message('api json type: '+ type)

        } else {
            ele('requests').value = 1 + new Number(ele('requests').value)
            d = new Date()
            nowTime = d.getTime()
            offset = 0
            if (offsetTime) {
                offset = parseInt(nowTime) - parseInt(offsetTime)
                message('offset : '+ offset)
                if (!stop) {
                    offsetTime = 0
                }
            }
            useTime = parseInt(nowTime) - parseInt(firstTime) - offset
            uTime = useTime / 1000
            console.log([nowTime, firstTime, offset, uTime])
            ele('use_time').value = uTime
            ele('avg_time').value = uTime / num('requests')


            item = ele('item').value.trim()
            urlMsg = urlPre = url = ele('url').value
            if (json.code) {
                itm = item || setItemName(url)
                record(itm)
                message('api json msg: '+ json.msg)
                start()
            } else if (!stop) {
                //message(func +'_'+ json.msg)
                //JSON[func] = json
                //eval("api_" + func + "()")
                pageNo = 0
                urlJson = json.msg
                if (urlJson) {
                    search = urlJson.trim().search(/^(http|\/)/i)
                    if (-1 == search) {
                        urlJson = ''
                    }
                }
                urlMsg = urlJson || urlMsg
                search = urlMsg.search(/\?/)
                var searchStr = ''
                if (-1 == search) {
                    prefix = urlMsg
                } else {
                    prefix = urlMsg.substr(0, search) + '?'
                    substr = urlMsg.substr(search + 1);//message(substr)
                    split = substr.split(/&/)
                    for (i = 0; i < split.length; i++) {
                        srch = split[i].search(/=/)
                        name = split[i].substring(0, srch)
                        val = split[i].substr(srch + 1)
                        //message(name)
                        if ('page' == name) {
                            pageNo = parseInt(val)
                            ele('last_time').value = tm = timeFormat(d)
                            timeOver = parseInt(nowTime) - parseInt(lastTime)
                            //lastTime = d.getTime()
                            ele('last_use').value = sm = secondFormat(timeOver)
                            document.title = pageNo +'('+ sm +')'+ tm
                            to = timeOver / 1000
                            useReal = new Number(ele('use_real').value)
                            uReal = to + useReal
                            ele('use_real').value = uReal
                            ele('avg_real').value = uReal / num('requests')
                            // break
                            if (!urlJson) {
                                pageNo = val = pageNo + 1
                            }
                        }
                        if (name) {
                            searchStr += '&'+ name +'='+ val
                        }
                    }
                    searchStr = searchStr.replace(/^&/, '')
                }

                // 页数限制
                maxPage = num('max_page') || json.data.pageCount
                inPage = 1
                if (json.data.lastTime || (maxPage && pageNo > maxPage)) {
                    inPage = 0
                    start(1)
                }

                if (inPage) {
                    //urlInfo = 'http://localhost.urlnk.com' + json.msg
                    urlInf = urlInfo = json.msg || prefix + searchStr
                    if (urlPre == urlInfo) {
                        message('url previous')

                    } else if (urlInfo && 'final' != urlInfo) {
                        if (json.data.timeout) {
                            ele('timeout').value = json.data.timeout
                        }
                        timeoutVal = ele('timeout').value
                        if (!timeoutVal) {
                            timeoutVal = 100
                        }
                        timeoutVal = parseInt(timeoutVal) + timeoutApi
                        urlPrefix = urlInfo.trim().search(/^(http|\/)/i)
                        timeoutUrl = '-'
                        if (-1 == urlPrefix) {
                            urlInfo = urlInf
                        }
                        ele('url').value = urlInfo
                        timeoutUrl = setTimeout("api('"+ urlInfo +"')", timeoutVal)
                        message('t' + timeoutUrl + ' urlPrefix ' + urlPrefix + ' ' + urlInfo, 'note')
                        if (item) {
                            record(item)
                        } else {
                            setItemName(urlInfo)
                        }

                        /*
                        message(json.data.result)
                        if (json.data) {
                            if (json.data.result) {
                                if (json.data.result.update) {
                                    message(166)
                                    message(JSON.parse(json.data.result.update))
                                }
                            }
                        }*/
                    }

                } else {
                    message('max page! '+ json.data.lastTime)
                }

            } else {
                message('pause')
            }
        }

    } else {
        message('apiCall ERROR')
    }
}

/* 操作 */

// 开始
function start(s) {
    d = new Date()

    // 保持进行中
    if (0 === s) {
        step = 0
    }
    //console.log(JSON.stringify([s, step]))

    btn = ele('btn-request')
    if (s || 1 == step) {
        btn.innerHTML = '继续'
        pause()
        step = 0
        offsetTime = d.getTime()
    } else if (!step) {
        btn.innerHTML = '暂停'
        stop = 0
        api()
        step = 1
        ele('start_time').value = startTime
        //offsetTime = 0
    }
    document.title = timeFormat(d) +'('+ offsetTime +')'
}

// 暂停
function pause() {
    //clearTimeout(timeoutUrl)
    stop = 1
}



/* 任务 */

// 设置任务
function setTask() {
    ms = num('millisec')
    intervalTsk = window.setInterval(runTask, ms)
    ele('config_time').value = dt = new Date()
    time = dt.getTime() + ms
    dt.setTime(time)
    ele('next_task').value = dt
    ele('btn-task').innerHTML = "停止"
    ele('run_times').value = 0
}

// 停止任务
function stopTask() {
    window.clearInterval(intervalTsk)
    ele('btn-task').innerHTML = "开始"
    ele('cancel_time').value = dt = new Date()
}

// 切换任务执行
function autoTask() {
    if (execTask) {
        stopTask()
        execTask = 0
    } else {
        setTask()
        execTask = 1
    }
}

function runTask() {
    tms = 1 + num('run_times')
    rept = num('repeat_time')
    if (!isNaN(rept) && tms > rept) {
        console.log([tms, rept])
        stopTask()
        execTask = 0
        return false
    }
    ele('run_times').value = tms

    eval(ele('code').value)
    ele('last_run').value = dt = new Date()
    time = dt.getTime() + num('millisec')
    dt.setTime(time)
    ele('next_task').value = dt
}

function testTask() {
    d = new Date()
    console.log(timeFormat(d))
}

/* 信息 */

// 消息
function message(msg, id) {
    id = id || 'msg'
    ele(id).innerHTML = msg
}

// XHR 消息
function statusMsg(x, msg) {
    message(msg +' ('+ x.status +') : '+ x.statusText)
}

// 支持情况
storageType = typeof(Storage)
if ('function' !== storageType) {
    message("抱歉! 不支持 web 存储。"+ storageType)
} else {
    localLog()
}

