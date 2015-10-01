<?php
use NoahBuscher\Macaw\Macaw;
Macaw::get('', 'HomeController@home');

Macaw::get('fuck', function () {
	echo '成功';
});

//Macaw::get('(:all)', function ($fu) {
//	echo '匹配到路由<br>' . $fu;
//});

// 无匹配项页面
Macaw::$error_callback = function() {
	throw new Exception("404 Not Found");
};

Macaw::dispatch();