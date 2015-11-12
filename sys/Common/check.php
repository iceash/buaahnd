<?php 
function ismobile($tel) {
	$result = false;
	$tel = trim($tel);
	if (strlen($tel) == "11") { // 判断长度是不是11位
		if (preg_match("/^13[0-9]{9}$|15[0-9]{9}$|18[0-9]{9}$/", $tel)) { // 判断以13，15，18开头，后面跟9位数字
			$result = true;
		} 
	} 
	return $result;
} 
function isemail($email) {
	$result = false;
	if (strstr($email, '@') && strstr($email, '.')) {
		$reg = '/[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*$/';
		if (preg_match($reg, $email)) {
            $result=true;
		} 
	} 
	return $result;
}
function isusername($username) {
	$result = false;
	$username = trim($username);
	if (strlen($username) == "11") { // 判断长度是不是11位
		if (preg_match("/^GJ[0-9]{9}$/", $username)) { // 判断以GJ开头，后面跟9位数字
			$result = true;
		} 
	} 
	// return $result;
	return true;
} 
function issex($sex) {
	$result = false;
	$sex = trim($sex);
	if (strlen($sex) == "3") { // 判断长度是不是汉字
		if ($sex=='男' || $sex=='女') { 
			$result = true;
		} 
	} 
	return $result;
} 
function isctime($ctime) {
	$result = false;
	$ctime = trim($ctime);
	if (strlen($ctime) == "2" || strlen($ctime) == "4") { // 长度是2位或4位都可以
			$result = true;
	} 
	return $result;
} 


?>
