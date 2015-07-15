<?php
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		if ($suffix && strlen($str) > $length)
			return mb_substr($str, $start, $length, $charset) . "...";
		else
			return mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		if ($suffix && strlen($str) > $length)
			return iconv_substr($str, $start, $length, $charset) . "...";
		else
			return iconv_substr($str, $start, $length, $charset);
	} 
	$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("", array_slice($match[0], $start, $length));
	if ($suffix) return $slice . "…";
	return $slice;
}
function toDate($time, $format = 'Y年m月d日 H:i:s') {
	if (empty($time) || $time == '0000-00-00 00:00:00') {
		return '';
	} 
	date_Default_TimeZone_set("PRC");
	return date ($format, strtotime($time));
} 
function mb_sub($title,$stop){
    $length=mb_strlen ($title,'utf-8');
    $newtitle='';
    if($length>$stop){
        $stop-=2;
        $newtitle=mb_substr($title,0,$stop,'utf-8').'...';
    }else{
        $newtitle=$title;
    }
    return $newtitle;
}
function explainResult($result,$seperate='<br />') {//入口为1:8:5,2:7:1,...难度:满分:得分,返回文字描述
    $s='未设定该题型';
    if(!empty($result)){
        $temp = explode(',', $result);
        $a=array();
        if(isset($result)){
            foreach($temp as $key=>$value){
                $b=explode(':',$value);
                if($b[2]=='') $b[2]=0;
                $a[]="难度$b[0] 共$b[1]分，得$b[2]分";
            }  
        }
        $s=implode($seperate,$a);
    }
    return $s;
} 
function formatFileSize($s) {
	foreach (array('', 'K', 'M', 'G') as $i => $k) {
		if ($s < 1024) break;
		$s /= 1024;
	} 
	$data = round($s, 2) . $k . "B";
	return $data;
} 
function getFlag($s) {
    $img='';
	for($i=0;$i<$s;$i++) {
		$img.='<img src="../Public/images/flag.gif" />';
	} 
	return $img;
} 
function getAbroadStatus($i) {
    $a=array();
        $a['1']='1、咨询中';
        $a['2']='2、已签约，选校中';
        $a['3']='3、已定校，材料制作中';
        $a['4']='4、材料全部递交，等待录取中';
        $a['5']='5、已录取，签证准备中';
        $a['6']='6、签证已递交';
        $a['7']='7、签证通过，等待开学';
        return $a[$i];
}
function stuToParent($s) {//入口参数：学生学号，返回值：家长帐号
    $temp='JZ'.substr($s,2);
    return $temp;
}
?>