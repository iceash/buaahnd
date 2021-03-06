<?php
class AbroadAdmAction extends CommonAction {
	public function index() {
        $User = D('User');
        $map['username'] = session('username');
        $photo = $User->where($map)->getField('photo');
        $this->assign('photo',$photo);
        $roles=explode(',',session('role'));
        if(count($roles)>1){
            $all_role=R('Index/getRole');
            $my_role=array();
            foreach($roles as $key=>$value){
                $my_role[$value]=$all_role[$value];
            }
            $this->assign('my_role',$my_role);
            $this->assign('select_role',$this -> getActionName());
        }
		$this -> display();
	} 
    public function menuProcess() {        
        $menu['status']='按状态查看';
        $menu['process']='今年的留学进程';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    public function menuPastProcess($id) {
        $menu['pastProcess']='留学进程图';
        $menu['downPastAbroadInfo']='导出数据';
        $this->assign('menu',$this ->autoMenu($menu,$id));  
	}
    public function getStatus(){
        $a=array();
        $a['1']='1、咨询中';
        $a['2']='2、已签约，选校中';
        $a['3']='3、已定校，材料制作中';
        $a['4']='4、材料全部递交，等待录取中';
        $a['5']='5、已录取，签证准备中';
        $a['6']='6、签证已递交';
        $a['7']='7、签证通过，等待开学';
        return $a;
    }
    public function getEnroll(){
        $truename=$_POST['struename'];
        if(!isset($truename)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $map['truename']=$truename;
        $map['status']='3';
        $my=$Enroll->where($map)->select();
        $this->assign('my',$my);
        $this->assign('count',count($my));
        $this->display();
    }
    public function fixStatusFull($j){//只保留最大值
        $j=substr($j,-1,1);
        return $j;
    }
    public function getStuInfo(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $Enroll=D('Enroll');
        $map['id']=$id;
        $my=$Enroll->where($map)->select();
        if($my){
            $this->ajaxReturn($my,'',1);
        }else{
            $this->error('未找到记录');
        }
    }
    public function processCategory() {
        $dao = D('AbroadteacherView');
        $this->assign('category_fortag',$this -> getStatus());
        $this->assign('all_teacher',$this -> getAbroadTea());

		if (isset($_GET['searchkey'])) {
			$map['struename'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        if (isset($_GET['category'])) {
			$map['status'] = $_GET['category'];
			$this -> assign('category_current', $_GET['category']);
            if($_GET['category'] == 7) {
                $map['year(abroad.ctime)']=date("Y",time());
            }
		}
        if (isset($_GET['tusername'])) {
            $map['tusername1|tusername2|tusername3|tusername4'] = $_GET['tusername'];
            $this->assign('tusername',$_GET['tusername']);
            $this->assign('ttruename',$_GET['ttruename']);    
		} 
        if(isset($_GET['year'])) {

        } else {
            if(isset($_GET['id'])){
                $map['year(abroad.ctime)'] = $_GET['id'];
                $this->assign('id',$_GET['id']);
            }else{
               $map['year(abroad.ctime)']=date("Y",time()); 
            }
        }
        
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this ->menuProcess();
        $this -> display(); 
	}
    public function statusCategory() {
        $this->assign('all_status',$this -> getStatus());
        $this->assign('all_teacher',$this -> getAbroadTea());

        if (isset($_GET['status'])) {
			$map['status'] = $_GET['status'];
			$this -> assign('status_select', $_GET['status']);
            if($_GET['status'] == 7) {
                $map['year(abroad.finishtime)']=date("Y",time());
            }
		}
        if (isset($_GET['tusername'])) {
            $map['tusername1|tusername2|tusername3|tusername4'] = $_GET['tusername'];
            $this->assign('tusername',$_GET['tusername']);
            $this->assign('ttruename',$_GET['ttruename']);    
		} 
        if(isset($_GET['year'])) {
            $this->assign('year', $_GET['year']);
        } else {
            if(isset($_GET['id'])){
                $map['year(abroad.ctime)'] = $_GET['id'];
                $this->assign('id',$_GET['id']);
            }else{
               $map['year(abroad.ctime)']=date("Y",time()); 
            }
        }
        $dao = D('AbroadteacherView');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this ->menuProcess();
        $this -> display(); 
	}
    public function processCategoryDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $dao = D('Abroad');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $ids=explode(',',$id);
            $dao1=D('Abroadschool');
            foreach($ids as $key=>$value){
                $map1['abroadid']=$value;
                $count = $dao1 -> where($map1) -> delete();
            }
            $this -> success('已成功删除');
        } else {
            $this -> error('该记录不存在或权限不足');
        } 
    } 
    public function process(){
        $this->assign('mytime',date('YmdHis'));
        $dao = D('Abroad');
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);
        $this->assign('all_teacher',$this -> getAbroadTea());
        $a=array();
        if (isset($_GET['tusername'])) {
                if(substr($_GET['tusername'],0,1)=='T'){
                    $map['tusername1|tusername2|tusername3|tusername4'] = $_GET['tusername'];
                    $this->assign('tusername',$_GET['tusername']);
                    $this->assign('ttruename',$_GET['ttruename']); 
                }
		} 
        $map['year(ctime)']=date("Y",time());
        foreach($all_status as $key=>$value){
            $map['status']=$key;
            $count = $dao->Field('status')-> where($map) -> count();
            $a[]=array('status'=>$key,'statustext'=>$value,'count'=>$count);
        }
        $this->assign('my',$a);
        $this ->menuProcess();
        $this->display();
    }
    public function status(){
        $this->assign('mytime',date('YmdHis'));
        $dao = D('Abroad');
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);
        $this->assign('all_teacher',$this -> getAbroadTea());
        $a=array();
        if (isset($_GET['tusername'])) {
                if(substr($_GET['tusername'],0,1)=='T'){
                    $map['tusername1|tusername2|tusername3|tusername4'] = $_GET['tusername'];
                    $this->assign('tusername',$_GET['tusername']);
                    $this->assign('ttruename',$_GET['ttruename']); 
                }
		} 
        foreach($all_status as $key=>$value){
            $map['status']=$key;
            $count = $dao->Field('status')-> where($map) -> count();
            $a[]=array('status'=>$key,'statustext'=>$value,'count'=>$count);
        }
         //状态7的只显示当年的
        $map['year(finishtime)'] = date("Y",time());
        $map['status'] = 7;
        $count6 = $dao->Field('status')-> where($map) -> count();
        $a[6] = array('status'=>7,'statustext'=>'7、签证通过，等待开学','count'=>$count6);
        
        $this->assign('my',$a);
        $this ->menuProcess();
        $this->display();
    }
    public function search() {
        $truename = $_GET['truename'];
		$status = $_GET['status'];
        $tusername = $_GET['tusername'];
        
        if (isset($truename)) {
			$map['struename']=array('like',"%".$truename."%");
            $this -> assign("truename", $truename);
		}
        if (isset($status)) {
			$map['status']=$status;
            $this -> assign("status_select", $status);
		}
        if(isset($tusername)) {
            $map['tusername1|tusername2|tusername3|tusername4'] = $tusername;
            $this->assign('tusername', $tusername);
            $this->assign('ttruename', $_GET['ttruename']);
        }
        $this -> assign('all_status', $this->getStatus()); 
        
        if(isset($_GET['year'])) {
            if($status == 7) {
                $map['year(abroad.ctime)'] = date("Y",time()); 
            }
            $this->assign('year', $_GET['year']);
        } else {
            $map['year(abroad.ctime)'] = date("Y",time()); 
        }
        $dao = D('AbroadteacherView');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this ->menuProcess();
        $this->display();
    }
    public function pastSearch() {
        $truename = $_GET['truename'];
        
        if (isset($truename)) {
			$map['struename']=array('like',"%".$truename."%");
            $this -> assign("truename", $truename);
		}
        
        $dao = D('AbroadteacherView');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this ->menuProcess();
        $this->display();
    }
    public function processData(){
        $dao = D('Abroad');
        $all_status=$this->getStatus();
        if (isset($_GET['tusername'])) {
            if(substr($_GET['tusername'],0,1)=='T'){
                $map['tusername1|tusername2|tusername3|tusername4'] = $_GET['tusername'];
            }
		} 
        if(isset($_GET['status'])) {
            
        } else {
            if(isset($_GET['id'])){
                $map['year(ctime)'] = $_GET['id'];
            }else{
                $map['year(ctime)']=date("Y",time());
            }
        }
        $a=array();
        foreach($all_status as $key=>$value){
            $map['status']=$key;
            $count = $dao->Field('status')-> where($map) -> count();
            $a[]=$count;
        }
        //状态7的只显示当年的
        $map['year(ctime)'] = date("Y",time());
        $map['status'] = 7;
        $count6 = $dao->Field('status')-> where($map) -> count();
        $a[6] = $count6;

        $this->assign('data',implode(',',$a));
        $this->assign('max',intval(max($a)*1.5)+1);
        $this->assign('y_steps',intval(max($a)*1.5/10));
        $this->assign('mytitle','留学进程图');
        $this->display();
    }
    public function detail(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Abroad');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        $leave=array('0'=>'否','1'=>'是');
        $enrollStatus=array('0'=>'不录取','1'=>'录取但不是最终学校','2'=>'录取且是最终学校');
        if($my){
            $this->assign('my',$my);
            $this->assign('leave',$leave);
            $this->assign('enrollStatus',$enrollStatus);
            $this->assign('method',$this -> getSystem("method"));
            $this->assign('status',$this->getStatus());
            $this->assign('from',$this -> getSystem("from"));
            $this->assign('country',$this -> getSystem("country"));
            $this->assign('degree',$this -> getSystem("degree"));
            $this->assign('AbroadTea',$this -> getAbroadTea());
            $this -> assign('status_selected', explode(',', $my['status']));
            $this -> assign('from_selected', explode(',', $my['from']));
            $this -> assign('country_selected', explode(',', $my['country']));
            $this -> assign('degree_selected', explode(',', $my['degree']));
            $school=D('Abroadschool')->where("abroadid=$id")->select();
            $this -> assign('school', $school);
            $this ->menuProcess();
            $this->display();
        }else{
            $this->error('未找到记录或权限不足');
        }
    }
      public function stuScore(){
        $susername=$_GET['susername'];
         $dao = D("Score");
		$map['susername']=$susername;
		$map['isvisible']=1;
        $score=$dao->where($map)->order('term asc')->select();
        $my=array();
        foreach($score as $v){
            $my[$v['term']][]=$v;
        }
        $this->assign('my',$my);
        $this->menuProcess();
        $this -> display();
    }
    public function school(){
        $id=$_GET['id'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $dao=D('Abroad');
        $map['id']=$id;
        $my=$dao->where($map)->find();
        if($my){
            $this->assign('my',$my);
            $this->assign('method',$this -> getSystem("method"));
            $school=D('Abroadschool')->where("abroadid=$id")->select();
            $this -> assign('school', $school);
            $this ->menuProcess();
            $this->display();
        }else{
            $this->error('未找到记录或权限不足');
        }
    }
    public function getSystem($name) {
		$system = D("System");
		$temp = explode(',', $system -> where("category='abroad' and name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		} 
		return $a;
	} 
    public function getAbroadTea() {
		$dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%AbroadTea%');
        $my=$dao->Field('username,truename')->where($map)->select();
		$a = array();
		foreach($my as $key => $value) {
			$a[$value['username']] = $value['truename'];
		} 
		return $a;
	} 
    public function teacher() {
		$dao = D("User");
        $map['ispermit']=1;
        $map['role']=array('like','%AbroadTea%');
        $my=$dao->Field('username,truename')->where($map)->select();
		$this->assign('my',$my);
        $this->display();
	} 
    public function detailUpdate() {
        $dao = D('Abroad');
        if ($dao -> create()) {
            $dao ->from=implode(',',$_POST['from']);
            if(empty($_POST['status'])){
                $this -> error('留学进程不能为空');
            }else{
                $status=implode(',',$_POST['status']);
                $dao->status=$this->fixStatusFull($status);

                if($this->fixStatusFull($status) == 7) {
                    $dao -> finishtime = date('Y-m-d');
                } else {
                    $dao -> finishtime = NULL;
                }
            }
            $dao -> country = implode(',', $_POST['country']);
            $dao -> degree = implode(',', $_POST['degree']);
            if($_POST['quit'] == 1) {
                $dao -> quittime = date("Y-m-d");
            } else{
                $dao -> quittime = NULL;
            }
            if (empty($_POST['bentime'])) {
                $dao -> bentime = NULL;   
            }
            if (empty($_POST['shuotime'])) {
                $dao -> shuotime = NULL;   
            }
            if (empty($_POST['botime'])) {
                $dao -> botime = NULL;   
            }
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error('插入数据出错');
        } 
    } 
    public function schoolInsert() {
		$dao = D("Abroadschool");
		if ($dao -> create()) {
			$dao -> method = implode(',', $_POST['method']);
			$checked = $dao -> add();
			if ($checked > 0) {
                $info=$dao->where('id = '.$checked)->find();
                $info['ktime']=mb_substr($info['ktime'], 0, 10, 'utf-8');
				$this -> ajaxReturn($info,'已成功保存',1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error($dao -> getError());
		} 
	} 
    public function schoolDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = $id;
        $dao = D('Abroadschool');
        $count = $dao -> where($map) -> delete();
        if ($count > 0) {
            $this -> ajaxReturn($id,'已成功删除',1);
        } else {
            $this -> error('该记录不存在');
        } 
    }  
    public function schoolUpdate() { 
		$dao = D('Abroadschool');
        $id=$_POST['id'];
		if ($dao -> create()) {
			$checked = $dao -> save();
			if ($checked>0) {
                $info=$dao->where('id = '.$id)->find();
                if(strlen($info['ktime']) && $info['ktime'] !='0000-00-00 00:00:00')
                $info['ktime']=mb_substr($info['ktime'], 0, 10, 'utf-8');
                else
                $info['ktime']=toDate($info['ktime']);
				$this -> ajaxReturn($info,'已成功保存',1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function excel(){
        import("@.ORG.XmlExcel");
		$xls = new XmlExcel;
		$xls -> setDefaultAlign("left");   //居中设置
		$xls -> setDefaultHeight(18);   //设置表格高度
		$xls -> addHead(array("姓名", "留学进程","咨询负责人", "文案负责人", "签证负责人"), "output");
        $xls -> setColumnWidth("output",array(1=>80, 2=>150,3=>80, 4=>80, 5=>80));
        $dao = D('Abroad');
        $my = $dao -> where($map)  -> order('status asc,id desc') -> select();
        //dump($list);        
        $a=$this->getStatus();
		foreach($my as $key=>$value){
            $xls -> addRow(array($value['struename'], $a[$value['status']], $value['tusername1'], $value['tusername2'], $value['tusername3']), "output");
        }
		$xls -> export('data'.date("Y-m-d H:i:s"));
    }
    public function sys() {
		$Sys = D("System");
		$map['category'] = 'abroad';
		$count = $Sys -> where($map) -> count();
		if ($count > 0) {
			$my = $Sys -> where($map) -> order('id desc') -> select();
			$this -> assign('my', $my);			
		} 
        $this -> display();
	} 
    public function saveSys(){
        $id=$_POST['id'];
        $content=$_POST['content'];
        if(empty($id)||empty($content)){
            $this->error('必填项不能为空');
        }
        $Sys = D("System");
        $data['id']=$id;
        $data['content']=$content;
        $result=$Sys->save($data);
        if($result>0){
            $this->success('已成功保存');
        }
    }	
    public function pastAbroadInfo(){
        $this->display();
    }
    public function pastAbroadInfoLeft(){
        $dao = D("abroad");
        $dtree_year = $dao -> field( 'year(ctime) as year') -> group('year') ->where('year(ctime) < year(now())')-> order('year desc') -> select(); //只查看往年的记录，排除当年的
        $this -> assign('dtree_year',$dtree_year);
        $this -> display();
    }
    public function pastProcess(){
        $this->assign('id',$_GET['id']);
        $dao = D('Abroad');
        $all_status=$this->getStatus();
        $a=array();
        if (isset($_GET['tusername'])) {
            $map['tusername1|tusername2|tusername3|tusername4'] = $_GET['tusername'];
            $this->assign('tusername',$_GET['tusername']);
            $this->assign('ttruename',$_GET['ttruename']);    
		} 
        $map['year(ctime)'] = $_GET['id'];
        foreach($all_status as $key=>$value){
            $map['status']=$key;
            $count = $dao->Field('status')-> where($map) -> count();
            $a[]=array('status'=>$key,'statustext'=>$value,'count'=>$count);
        }
        $this->assign('my',$a);
        $this ->menuPastProcess($_GET['id']);
        $this->display();
    }
    public function downPastAbroadInfo(){
        $this->assign('id',$_GET['id']);
        $this ->menuPastProcess($_GET['id']);
        $this->display();
        }
    public function downPassStu(){
        if(isset($_GET['id'])) {
            $year = $_GET['id'];
            $map['year(ctime)'] = $year;
        }
        
        if(isset($_GET['finishyear'])) {
            $year = $_GET['finishyear'];
            $map['year(finishtime)'] = $year;
        }

        $map['status']=7;
        $country=$this->getSystem('country');
        $map['country'] = array('in',$country);
        $map['isenroll']=2;
        $dao=D('AbroadschoolView');
        $my=$dao->where($map)->select();
        $temp=array();
        $score=array('score1'=>'IELTS','score2'=>'IBT','score3'=>'PTE','score4'=>'GRE','score5'=>'GMAT');
        foreach($my as $k=>$v){
            $temp[$v['country']][]=$v;
        }
        //由于老师录成绩的不规范做的特殊处理
        foreach($temp as $k1=>$v1){
            foreach($v1 as $k2=>$v2){
                for($i=1;$i<6;$i++){
                $a=explode('：',$v2['score'.$i]);
                if(count($a) < 2){
                    $b=explode('；',$a[0]);
                    $temp[$k1][$k2][$score['score'.$i]]=$b[0];
                }else{
                  $temp[$k1][$k2][$score['score'.$i]]=floatval($a[1]);  
                }
                if($temp[$k1][$k2][$score['score'.$i]]){
                    $temp[$k1][$k2]['language'].=$score['score'.$i].' '.$temp[$k1][$k2][$score['score'.$i]]." ";
                }else{
                    $temp[$k1][$k2]['language'].='';
                }
            }
            }
        }
        $now=0;
        $country_num=count($temp);
        $temp_key=array_keys($temp);
        $abroadTea=$this->getAbroadTea();
        include dirname(__FILE__).'/../../Lib/ORG/PHPExcel.class.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle("NJU-HND auto generated Document");
        $styleHead= array(
            'font' => array(
                'name'=>'楷体_GB2312',
                'bold' => true,
                'size' => 18
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $style2= array(
            'font' => array(
                'name'=>'宋体',
                'bold'=>true,
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleA=
        array(
            'font' => array(
                'name'=>'Times New Roman',
                'size' => 10
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        $styleB=array(
            'font' => array(
                'name'=>'宋体',
                'size' => 10,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
               'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
        );
        
        for($i=0;$i<$country_num;$i++){
            $now=3;//第三行开始写
            $objPHPExcel->createSheet();
            $actSheet=$objPHPExcel->getSheet($i);
            $actSheet->setTitle($temp_key[$i]);
            $actSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                //ORIENTATION_LANDSCAPE 页面横向，ORIENTATION_PORTRAIT 页面竖向
            $actSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $actSheet->getPageSetup()->setHorizontalCentered(true);
            $actSheet-> getColumnDimension('A') -> setwidth(3.88);
            $actSheet-> getColumnDimension('B') -> setwidth(7.3);
            $actSheet-> getColumnDimension('C') -> setwidth(3.5);
            $actSheet-> getColumnDimension('D') -> setwidth(13.63);
            $actSheet-> getColumnDimension('E') -> setwidth(8);
            $actSheet-> getColumnDimension('F') -> setwidth(8.75);
            $actSheet-> getColumnDimension('G') -> setwidth(8.88);
            $actSheet-> getColumnDimension('H') -> setwidth(20.5);
            $actSheet-> getColumnDimension('I') -> setwidth(22.13);
            $actSheet-> getColumnDimension('J') -> setwidth(7);
            $actSheet-> getColumnDimension('K') -> setwidth(7.25);
            $actSheet->getRowDimension('1')->setRowHeight(32);
            $actSheet->getRowDimension('2')->setRowHeight(18);
            $actSheet->mergeCells('A1:K1');
            $actSheet->setCellValue('A1',$year.'年'.$temp_key[$i].'留学学生统计');
            $actSheet->setCellValue('A2',"序号");
            $actSheet->setCellValue('B2',"姓名");
            $actSheet->setCellValue('C2',"性别");
            $actSheet->setCellValue('D2',"班级/来源");
            $actSheet->setCellValue('E2',"护照号码");
            $actSheet->setCellValue('F2',"出生年月日");
            $actSheet->setCellValue('G2',"语言");
            $actSheet->setCellValue('H2',"就读国外学校");
            $actSheet->setCellValue('I2',"就读专业");
            $actSheet->setCellValue('J2',"咨询老师");
            $actSheet->setCellValue('K2',"入学时间");
            $actSheet->getStyle('A1:K1')->applyFromArray($styleHead);
            $actSheet->getStyle('A2:K2')->applyFromArray($style2);
            $actSheet->getStyle('A2:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) // 填充模式  
                 ->getStartColor()->setARGB('FFC0C0C0'); // 背景颜色  
                foreach($temp[$temp_key[$i]] as $k=>$v){
                    $actSheet->setCellValue('A'.$now, $k+1)
                            ->setCellValue('B'.$now,$v['struename'])
                            ->setCellValue('C'.$now,'')
                            ->setCellValue('D'.$now,$v['sfrom'])
                            ->setCellValue('E'.$now,$v['passport'])
                            ->setCellValue('F'.$now,$v['birth']?substr($v['birth'],0,10):'')
                            ->setCellValue('G'.$now,$v['language'])
                            ->setCellValue('H'.$now,$v['school'])
                            ->setCellValue('I'.$now,$v['major'])
                            ->setCellValue('J'.$now,$abroadTea[$v['tusername1']])
                            ->setCellValue('K'.$now,$v['ktime']?substr($v['ktime'],0,10):'');
                            $actSheet ->getStyle('A'.$now)->getAlignment()->setWrapText(true);//自动换行 
                            $actSheet ->getStyle('B'.$now)->getAlignment()->setWrapText(true);
                            $actSheet ->getStyle('C'.$now)->getAlignment()->setWrapText(true);
                            $actSheet ->getStyle('D'.$now)->getAlignment()->setWrapText(true);
                            $actSheet ->getStyle('E'.$now)->getAlignment()->setWrapText(true); 
                            $actSheet ->getStyle('F'.$now)->getAlignment()->setWrapText(true); 
                            $actSheet ->getStyle('G'.$now)->getAlignment()->setWrapText(true); 
                            $actSheet ->getStyle('H'.$now)->getAlignment()->setWrapText(true); 
                            $actSheet ->getStyle('I'.$now)->getAlignment()->setWrapText(true); 
                            $actSheet ->getStyle('J'.$now)->getAlignment()->setWrapText(true); 
                            $actSheet ->getStyle('K'.$now)->getAlignment()->setWrapText(true); 
                            $now++;
                }
                $actSheet->getStyle('A3:A'.($now-1))->applyFromArray($styleA);
                $actSheet->getStyle('E3:J'.($now-1))->applyFromArray($styleA);
                $actSheet->getStyle('B3:D'.($now-1))->applyFromArray($styleB);
                $actSheet->getStyle('K3:K'.($now-1))->applyFromArray($styleB);
        }
        $filename=$year.'年学生去向表-按国家分类.xls';
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");    
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit; 
        
    }
    public function finish() {
        $dao = D('Abroad');
        if (isset($_GET['searchkey'])) {
			$map['struename'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        $year = $_GET['year'];
        $map['status'] = '7';
        $map['year(finishtime)'] = $year;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        
        $this -> assign('year', $year);
        $this -> display(); 
    }
     public function quit() {
        $dao = D('Abroad');
        if (isset($_GET['searchkey'])) {
			$map['struename'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        $year = $_GET['year'];
        $map['quit'] = 1;
        $map['year(quittime)'] = $year;
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        
        $this -> assign('year', $year);
        $this -> display(); 
    }
    public function finishdownload() {
        $this->assign('year', $_GET['year']);
        $this->display();
    }
} 

?>