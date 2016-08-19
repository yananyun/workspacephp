url = window.location.href;
$.post('/index.php/wapIndex/bindTag', {url:url}, function(){});