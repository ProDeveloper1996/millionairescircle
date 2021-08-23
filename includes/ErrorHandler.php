<?php

/*
Так надо вызвать, если надо вывести ошибку
trigger_error("какой то текст", E_USER_ERROR);
*/

// Обрабатываются все возможные ошибки
error_reporting(-1);
// Функция-обработчик
function myErrorHandler($type, $message, $file, $line) {
	static $titles = array(
		E_WARNING           => 'Warning(Предупреждение)',
		E_NOTICE            => 'Notice(Уведомление)',
		E_USER_ERROR        => 'Error, a user-defined(Ошибка, определенная пользователем)',
		E_USER_WARNING      => 'Warning, a user-defined(Предупреждение, определенное пользователем)',
		E_USER_NOTICE       => 'Notification of a user-defined(Уведомление, определенное пользователем)',
		E_STRICT            => 'compatibility problem in the code(Проблема совместимости в коде)',
		E_RECOVERABLE_ERROR => 'fixable error(Поправимая ошибка)'
	);
	if (!isset($titles[$type])) return true;

	$str_error = '<br>'.$message.'<br>'.'Source: ' . $file . ', line ' . $line;
	$mes=date("Y-m-d H:i:s",time()).'<br>Во время выполнения скрипта произошла ошибка:<br>';
	$mes.=$str_error.'<br><br>';
	$mes.='URL: http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].'<br><br>';
	$subject= "Error on the site ".$_SERVER['HTTP_HOST'];
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
	$headers .= "From: Errors on the site <noreply@".str_replace("www.",'',$_SERVER["HTTP_HOST"]).">\r\n";
	//mail('intertecpanel@gmail.com', $subject, $mes, $headers);

	//exit($str_error);
/*
	$mes=array();
	$mes['site']=str_replace("www.",'',$_SERVER["HTTP_HOST"]);
	$mes['type']='php_error';
	$mes['description']='<br>'.$message.'<br>'.'Source: ' . $file . ', line ' . $line;
	$mes['url_page']='http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$text=serialize($mes);
	$str=base64_encode($text);
	//$r=curl_send($str);
	//@file_get_contents(URL_SEND_ERROR.'/sysmespanel.php?message='.encode($text));
	//debug(encode($text));

	if (SHOW_ERROR_SITE=='on')
	{
		$tr = debug_backtrace()[0];
		$text='<blockquote style="text-align:left.padding:8px;background:#FFAEAE;border:1px solid #950909;border-radius:4px;text-align: center;font-weight: bold;color:#000000;font-size:13px;">'
			. '<h3>' . $titles[$type] . '</h3>'
			. '<p>' . $message . '<br />'
			. 'Источник: ' . $file . ', line ' . $line .'<br>'
			. '</p></blockquote>'; // basename($file)
		echo $text;
		//debug(debug_backtrace());
		exit();
	}
*/
	return true;
}
// Назначаем обработчик
//set_error_handler('myErrorHandler');

?>