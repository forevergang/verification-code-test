<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>极验</title>
    <link href="{{ env('URL_PREFIX') }}/asset/css/common.css" rel="stylesheet">
    <style>
        #captcha1 {
            margin: 180px 0 10px 20px;
        }

        .captcha {
            margin: 10px 0 10px 20px;
        }
    </style>
</head>
<body>
<a href="../" class=>返回</a>
<h1>极验</h1>
<hr>
<span>方式1</span>
<div id="captcha1"></div>
<span>方式2</span>
<div class="captcha" id="captcha2"></div>
<span>方式3</span>
<div id="captcha3-1"></div>
<button class="captcha" id="captcha3-2">点击</button>

<script src="{{ env('URL_PREFIX') }}/asset/js/jquery-1.12.3.min.js"></script>
<script src="//static.geetest.com/static/tools/gt.js"></script>
<script>
    getPreProcess(function (data) {
        // 使用initGeetest接口
        // 参数1：配置参数，与创建Geetest实例时接受的参数一致
        // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
        initGeetest({
            gt: data.gt,
            challenge: data.challenge,
            product: "float", // 产品形式
            offline: !data.success
        }, function (captchaObj) {
            // 将验证码加到id为captcha的元素里
            captchaObj.appendTo("#captcha1");
            captchaObj.onSuccess(function () {
                onSuccess(captchaObj);
            });
            captchaObj.onFail(onFail);
            captchaObj.onError(onError);
            captchaObj.onRefresh(onRefresh);
            captchaObj.onReady(onReady);
        });
    });
    getPreProcess(function (data) {
        initGeetest({
            gt: data.gt,
            challenge: data.challenge,
            product: "embed", // 产品形式
            offline: !data.success
        }, function (captchaObj) {
            captchaObj.appendTo("#captcha2");
            captchaObj.onSuccess(function () {
                onSuccess(captchaObj);
            });
            captchaObj.onFail(onFail);
            captchaObj.onError(onError);
            captchaObj.onRefresh(onRefresh);
            captchaObj.onReady(onReady);
        });
    });
    getPreProcess(function (data) {
        initGeetest({
            gt: data.gt,
            challenge: data.challenge,
            product: "popup", // 产品形式
            offline: !data.success
        }, function (captchaObj) {
            captchaObj.appendTo("#captcha3-1");
            captchaObj.bindOn("#captcha3-2");
            captchaObj.onSuccess(function () {
                onSuccess(captchaObj);
            });
            captchaObj.onFail(onFail);
            captchaObj.onError(onError);
            captchaObj.onRefresh(onRefresh);
            captchaObj.onReady(onReady);

        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function getPreProcess(cb) {
        $.ajax({
            type: "GET",
            url: "{{ env('URL_PREFIX') }}/geetest/preProcess?rand=" + Math.round(Math.random() * 100),
            dataType: "json", // 使用jsonp格式
            success: function (data) {
                cb(data)
            }
        });
    }
    function validate(geetestChallenge, geetestValidate, geetestSeccode, cb) {
        $.ajax({
            // 获取id，challenge，success（是否启用failback）
            type: "POST",
            url: "{{ env('URL_PREFIX') }}/geetest/validate",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                geetestChallenge: geetestChallenge,
                geetestValidate: geetestValidate,
                geetestSeccode: geetestSeccode
            }),
            success: function (data) {
                cb(data)
            }
        });
    }

    function onSuccess(captchaObj) {
        alert('前端验证成功');

        var result = captchaObj.getValidate();
        var geetestChallenge = result.geetest_challenge;
        var geetestValidate = result.geetest_validate;
        var geetestSeccode = result.geetest_seccode;
        alert("即将发起后端验证，理论上在后台执行相关业务逻辑前应该验证，并不需要将这一步展现给用户");
        validate(geetestChallenge, geetestValidate, geetestSeccode, function (data) {
            alert("后端验证：" + data.message + "\n服务器状态：" + data.serverStatus);
        })
    }

    function onFail() {
        alert('前端验证失败');
    }

    function onError() {
        alert('网络错误');
    }

    function onRefresh() {
        console.info('正在刷新');
    }

    function onReady() {
        console.info('验证码已经准备好');
    }
</script>

@include('piwik')
</body>
</html>
