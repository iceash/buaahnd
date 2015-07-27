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
/**********
*将该学生在该收费项目的所有收费记录相加覆盖已交金额和判断付费状态
**需要传入参数isRefund,feeid和idcard
***$isRefund为1时状态固定为“退费”
****函数返回值为$checkU
*********/
function updatePaymentStatus($isRefund,$feeid,$idcard){
    $payment=M('payment');$deal=M('deal');
    $mapU['feeid']=$feeid;
    $mapU['idcard']=$idcard;
    $mapU['period']=0;
    $allPay=$deal->where($mapU)->field('money')->select();
    $sum=0;
    for ($i=0; $i < count($allPay); $i++) { 
        $sum+=$allPay[$i]['money'];
    }
    $dataU['paid']=$sum;
    $standard=$payment->where($mapU)->getField('standard');
    if (!($isRefund)) {
        if ($sum>=$standard) {
            $dataU['status']=2;
        }elseif ($sum<=0) {
            $dataU['status']=0;
        }else{
            $dataU['status']=1;
        }
    }else{
        $dataU['status']=3;
    }
     return $checkU=$payment->where($mapU)->save($dataU);
}
function isDate($date){
  if($date == date('Y-m-d',strtotime($date))){
    return true;
  }else{
    return false;
  }
}
/****************************************************************
*****************************************************************
****************************************************************/
function downloads(){
      $mapEn=$_GET['searchkey'];
      $mapFn=$_GET['searchtype'];
      $item =$_GET['item'];
      $grade =$_GET['grade'];
      $classes =$_GET['classes'];
      $majorname =$_GET['majorname'];
      $period =$_GET['period'];
      $status1 =$_GET['status1'];
      $D =D('ClassstudentView');
      if($item){$where['item']  = array('like','%'.$item.'%');}
      if($grade){
        $mapccd['year']=$grade;
         $mapin2=  $D ->where($mapccd) ->Field('idcard')->select();
         for ($ll=0; $ll <count($mapin2); $ll++) { 
             $mapi2[]=$mapin2[$ll]['idcard'];
         }
         $where['idcard'] = array('in',$mapi2);
     }
      if($classes){
        $mapcc['name']=$classes;
         $mapin1 =  $D ->where($mapcc) ->Field('studentname')->select();
         for ($ll2=0; $ll2 <count($mapin1); $ll2++) { 
             $mapi1[]=$mapin1[$ll2]['studentname'];
         }
          $where['truename'] = array('in',$mapi1);

        }
      if($majorname){$where['majorname']  = array('like','%'.$majorname.'%');}
      if($_GET['datefrom']&&$_GET['dateto']){$where['date']=array(array('egt',$_GET['datefrom']),array('elt',$_GET['dateto']));}
      if($_GET['sbfrom']&&$_GET['sbto']){$where['submitdate']=array(array('egt',$_GET['sbfrom']),array('elt',$_GET['sbto']));}
      $where['period']=0;
      if($period){$where['period']  = $period;}
      if($status1){
        if($status1=='交费'){
            $where['money'] = array('gt',0);
            
        }
        if($status1=='退费'){
            $where['money'] = array('lt',0);
            
        }
      }
      $Form  =  M('statistics');

      if(!$_GET['searchkey']){
      import("ORG.Util.Page");
      $count=  $Form ->where($where) ->count();
      $Page= new Page($count,20);
        $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
        $show= $Page->show();
        $Model =  $Form ->where($where)->order('date desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 

        for ($i=0; $i <count($Model);$i++) {
          if($Model[$i]['money']>0){
              $statusname='交费';
          }
          if($Model[$i]['money']<0){
            $statusname='退费';
          }
     
        $mapa['studentname']=$Model[$i]['truename'];
        $stugrade= $D ->where($mapa)->select();
        $Model[$i]['grade']=$stugrade[0]['year'];
        $Model[$i]['class']=$stugrade[0]['name'];
        $Model[$i]['statusname']=$statusname;

       }
        
        return($Model);


      }
      else{
        switch ($mapFn) {
          case '1':
          import("ORG.Util.Page");
          $count=  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND enroll.truename ="'.$mapEn.'"')->order('date desc')->count();
          $Page= new Page($count,20);
          $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
          $show= $Page->show();
          $Model =  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND enroll.truename ="'.$mapEn.'"')->order('date desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            break;
          case '2':
          import("ORG.Util.Page");
          $count=  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.stunum='.$mapEn)->order('date desc')->count();
          $Page= new Page($count,20);
          $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
          $show= $Page->show();
          $Model =  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.stunum='.$mapEn)->order('date desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            break;
          case '3':
          import("ORG.Util.Page");
          $count=  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.idcard='.$mapEn)->order('date desc')->count();
          $Page= new Page($count,20);
          $Page->setConfig('theme','%first% %upPage% %linkPage% %downPage% %end%');
          $show= $Page->show();
          $Model =  $Form ->Table(array('u_enroll'=>'enroll','u_payment'=>'payment','u_fee'=>'fee','u_deal'=>'deal'))->where('enroll.id=payment.stunum AND fee.name=payment.feename AND deal.idcard=payment.idcard AND deal.feename=fee.name AND deal.period=0 AND deal.idcard='.$mapEn)->order('date desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            break;
        }
      
      
      }
      //dump($Model);
        for ($i=0; $i <count($Model);$i++) {
          if($Model[$i]['money']>0){
              $statusname='交费';
          }
          if($Model[$i]['money']<0){
            $statusname='退费';
          }
     
        $mapa['studentname']=$Model[$i]['truename'];
        $stugrade= $D ->where($mapa)->select();
        $Model[$i]['grade']=$stugrade[0]['year'];
        $Model[$i]['class']=$stugrade[0]['name'];
        $Model[$i]['statusname']=$statusname;

       }
      
      if($Model[0]["idcard"]==0){
        $this->assign('thead','该同学不存在!');
      }
        else{        
        return($Model);
        }// 模板变量赋值
}

     function excelwarning($excelurl,$errorarr,$sheetnum=0){
       Vendor('PHPExcel'); 
        $p = PHPExcel_IOFactory::load($excelurl);
        $p -> setActiveSheetIndex($sheetnum);
        for ($errornum=0; $errornum <count($errorarr) ; $errornum++) { 
        $p->getActiveSheet()->getStyle($errorarr[$errornum])->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $p->getActiveSheet()->getStyle($errorarr[$errornum])->getFill()->getStartColor()->setARGB('FFFF7F50'); 
        }
        
          header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
          header("Pragma: no-cache");
          header("Content-Type:application/octet-stream");
          header('content-Type:application/vnd.ms-excel;charset=utf-8');
          header('Content-Disposition:attachment;filename=表.xls');//设置文件的名称
          header("Content-Transfer-Encoding:binary");
          $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
          $objWriter->save($excelurl);
          return true;
          exit;

        //PHPExcel_IOFactory::createWriter($p, 'Excel5') //根据需要也可以是 Excel2007
          //->save($excelurl)
       
      
    //   $objPHPExcel = new PHPExcel();   
    //   $PHPReader = new PHPExcel_Reader_Excel5(); 
    //   $filePath=$excelurl;
    //   if(!$PHPReader->canRead($filePath)){   
    //       echo 'no Excel';   
    //       return ;   
    //   }   
    //    $PHPExcel = $PHPReader->load($filePath);  
    //    $sheetData = $PHPExcel->getActiveSheet()->toArray(null,true,true,true);
    //    dump($sheetData);
    //    $currentSheet = $PHPExcel->getSheet(0);  
       
    //     $allColumn = $currentSheet->getHighestColumn();    //取得一共有多少列   
         
    //     $allRow = $currentSheet->getHighestRow(); //取得一共有多少行  
    //     //循环读取数据,默认是utf-8输出  
    //     $data=array();
    //      for ($ia = 0; $ia <= $allRow; $ia++){
    //         $item['A']=$sheetData[$ia]['A'];
    //         $item['B']=$sheetData[$ia]['B'];
    //         $item['C']=$sheetData[$ia]['C'];
    //         $item['D']=$sheetData[$ia]['D'];
    //         $item['E']=$sheetData[$ia]['E'];
    //         $item['F']=$sheetData[$ia]['F'];
    //         $item['G']=$sheetData[$ia]['G'];
    //         $item['H']=$sheetData[$ia]['H'];
    //         $item['I']=$sheetData[$ia]['I'];
    //         $item['J']=$sheetData[$ia]['J'];
    //         $item['K']=$sheetData[$ia]['K'];
    //         $item['L']=$sheetData[$ia]['L'];
    //         $item['M']=$sheetData[$ia]['M'];
    //         $item['N']=$sheetData[$ia]['N'];
    //         $item['O']=$sheetData[$ia]['O'];
    //         $item['P']=$sheetData[$ia]['P'];
    //         $item['Q']=$sheetData[$ia]['Q'];
    //         $item['R']=$sheetData[$ia]['R'];
    //         $item['S']=$sheetData[$ia]['S'];
    //         $item['T']=$sheetData[$ia]['T'];
    //         $item['U']=$sheetData[$ia]['U'];
    //         $item['V']=$sheetData[$ia]['V'];
    //         $item['W']=$sheetData[$ia]['W'];
    //         $item['X']=$sheetData[$ia]['X'];
    //         $item['Y']=$sheetData[$ia]['Y'];
    //         $item['Z']=$sheetData[$ia]['Z'];
    //         $data[]=$item;
    //     }   
    //     $objPHPExcelnew = new PHPExcel();
    //     $i =0;
    //     foreach($data as $k=>$v){
    // $objPHPExcelnew->setActiveSheetIndex(0)
    //             ->setCellValue('A'.$i, $v['A'])
    //             ->setCellValue('B'.$i, $v['B'])
    //             ->setCellValue('C'.$i, $v['C'])
    //             ->setCellValue('D'.$i, $v['D'])
    //             ->setCellValue('E'.$i, $v['E'])
    //             ->setCellValue('F'.$i, $v['F'])
    //             ->setCellValue('G'.$i, $v['G'])
    //             ->setCellValue('H'.$i, $v['H'])
    //             ->setCellValue('I'.$i, $v['I'])
    //             ->setCellValue('J'.$i, $v['J'])
    //             ->setCellValue('K'.$i, $v['K'])
    //             ->setCellValue('L'.$i, $v['L'])
    //             ->setCellValue('M'.$i, $v['M'])
    //             ->setCellValue('N'.$i, $v['N'])
    //             ->setCellValue('O'.$i, $v['O'])
    //             ->setCellValue('P'.$i, $v['P'])
    //             ->setCellValue('Q'.$i, $v['Q'])
    //             ->setCellValue('R'.$i, $v['R'])
    //             ->setCellValue('S'.$i, $v['S'])
    //             ->setCellValue('T'.$i, $v['T'])
    //             ->setCellValue('U'.$i, $v['U'])
    //             ->setCellValue('V'.$i, $v['V'])
    //             ->setCellValue('W'.$i, $v['W'])
    //             ->setCellValue('X'.$i, $v['X'])
    //             ->setCellValue('Y'.$i, $v['Y'])
    //             ->setCellValue('Z'.$i, $v['Z']);
    // $i++;
    // }
    // $objPHPExcelnew->getActiveSheet()->setTitle('sheet1');//设置sheet标签的名称
    // $objPHPExcelnew->setActiveSheetIndex(0);
    // ob_end_clean();  //清空缓存 
    // header("Pragma: public");
    // header("Expires: 0");
    // header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    // header("Content-Type:application/force-download");
    // header("Content-Type:application/vnd.ms-execl");
    // header("Content-Type:application/octet-stream");
    // header("Content-Type:application/download");
    // header('Content-Disposition:attachment;filename=表.xls');//设置文件的名称
    // header("Content-Transfer-Encoding:binary");
    // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelnew, 'Excel5');
    // $objWriter->save('php://output');
    // exit;

    }
?>