<?php
class AbroadTea2Action extends CommonAction {
	public function index() {
        $User = D('User');
        $map['username'] = session('username');
        $photo = $User->where($map)->getField('photo');
        $this->assign('photo',$photo);
        $Notice = D("Notice");
        $map="readusername='".session('username')."' and readtime is NULL";
        $count = $Notice -> where($map) -> count();
        $this->assign('count',$count);
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
    public function notice() {
        if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
            $map['title|content'] =array('like','%'.$searchkey.'%');
            $this->assign('searchkey',$searchkey);
        }
        $Notice = D("NoticeView");
        $map['readusername']=session('username');
        $count = $Notice -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Notice -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
        $this -> display();
    }
    public function setNoticeRead(){
        if(!isset($_GET['id'])) {
            $this->error('参数缺失');
        }
        $id=$_GET['id'];
        $Notice = D("Notice");
        $map['readusername']=session('username');
        $map['id']=$id;
        $my=$Notice->where($map)->select();
        if($my){
            $data['id']=$id;
            $data['readtime']=date("Y-m-d H:i:s");
            $result=$Notice->save($data); 
            if($result>0){
                $this->success('已成功标记');
            }
        }  
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
    public function checkAdd(){
        $dao=D('Abroad');
        $map['struename']=$_POST['struename'];
        $my=$dao->where($map)->find();
        if($my){
            $this->success('该学生已有，是否继续添加？');
        }else{
            $this->error('0');
        }
    }
    public function getHND(){
        $truename=$_POST['struename'];
        if(!isset($truename)){
            $this->error('参数缺失');
        }
        // $dao=D('User');
        $dao=D("classstudent");
        // $map['truename']=$truename;
        $map["studentname"]=$truename;
        $my=$dao->where($map)->select();
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
        $map['username']=$id;
        $my=$Enroll->where($map)->select();
        if($my){
            $this->ajaxReturn($my,'',1);
        }else{
            $this->error('未找到记录');
        }
    }
    public function add(){
        $this->assign('status',$this->getStatus());
        $this->display();
    }
    public function addInsert(){
       $dao = D('Abroad');
        if ($dao -> create()) {
            if(empty($_POST['status'])){
                $this -> error('留学进程不能为空');
            }else{
                $status=implode(',',$_POST['status']);
                $dao->status=$this->fixStatusFull($status);
            }
            $dao->ctime=date('Y-m-d H:i:s');
            $dao->tusername4=session('username');
            $insertID = $dao -> add();
            if ($insertID) {
                $this -> ajaxReturn($insertID, '已成功保存！', 1);
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    }
    public function processCategory() {
        $this->assign('category_fortag',$this -> getStatus());
		if (isset($_GET['searchkey'])) {
			$map['struename'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        if (isset($_GET['category'])) {
			$map['status'] = $_GET['category'];
			$this -> assign('category_current', $_GET['category']);
            if($_GET['category'] == 7) {
                $map['year(ctime)']=date("Y",time());
            }
		}
        if(isset($_GET['status'])) {
            
        } else {
            if(isset($_GET['id'])){
                $map['year(ctime)'] = $_GET['id'];
                $this->assign('id',$_GET['id']);
            }else{
               $map['year(ctime)']=date("Y",time()); 
            }
        }
        
        $dao = D('Abroad');
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
                $map['year(ctime)']=date("Y",time());
            }
		}

        if(isset($_GET['year'])) {
            $this->assign('year', $_GET['year']);
        } else {
            if(isset($_GET['id'])){
                $map['year(ctime)'] = $_GET['id'];
                $this->assign('id',$_GET['id']);
            }else{
               $map['year(ctime)']=date("Y",time()); 
            }
        }
        $dao = D('Abroad');
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
        $all_status=$this->getStatus();
        $a=array();
        $map['year(ctime)']=date("Y",time());
        foreach($all_status as $key=>$value){
            $map['status']=$key;
            $count = $dao->Field('status')-> where($map) -> count();
            $a[]=array('status'=>$key,'statustext'=>$value,'count'=>$count);
        }
        $this->assign('my',$a);
        $this -> assign('all_status', $all_status);
        $this ->menuProcess();
        $this->display();
    }
    public function status(){
        $this->assign('mytime',date('YmdHis'));
        $dao = D('Abroad');
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
        $all_status=$this->getStatus();
        $a=array();

        foreach($all_status as $key=>$value){
            $map['status']=$key;
            $count = $dao->Field('status')-> where($map) -> count();
            $a[]=array('status'=>$key,'statustext'=>$value,'count'=>$count);
        }
        //状态7的只显示当年的
        $map['year(ctime)'] = date("Y",time());
        $map['status'] = 7;
        $count6 = $dao->Field('status')-> where($map) -> count();
        $a[6] = array('status'=>7,'statustext'=>'7、签证通过，等待开学','count'=>$count6);

        $this->assign('my',$a);
        $this -> assign('all_status', $all_status);
        $this ->menuProcess();
        $this->display();
    }
    public function search() {
        $truename = $_GET['truename'];
		$status = $_GET['status'];
        
        if (isset($truename)) {
			$map['struename']=array('like',"%".$truename."%");
            $this -> assign("truename", $truename);
		}
        if (isset($status)) {
			$map['status']=$status;
            $this -> assign("status_select", $status);
		}
        
        $this -> assign('all_status', $this->getStatus()); 
        
        if(isset($_GET['year'])) {
            if($status == 7) {
                $map['year(ctime)'] = date("Y",time()); 
            }
            $this->assign('year', $_GET['year']);
        } else {
            $map['year(ctime)'] = date("Y",time()); 
        }
        $dao = D('Abroad');
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
        
        $dao = D('Abroad');
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
        if(isset($_GET['status'])) {
            
        } else {
            if(isset($_GET['id'])){
                $map['year(ctime)'] = $_GET['id'];
            }else{
                $map['year(ctime)']=date("Y",time());
            }
        }
        
        $all_status=$this->getStatus();
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
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
            if (empty($_POST['bentime'])) {
                $dao -> bentime = NULL;   
            }
            if (empty($_POST['shuotime'])) {
                $dao -> shuotime = NULL;   
            }
            if (empty($_POST['botime'])) {
                $dao -> botime = NULL;   
            }
            $dao -> country = implode(',', $_POST['country']);
            $dao -> degree = implode(',', $_POST['degree']);
            if($_POST['quit'] == 1) {
                $dao -> quittime = date("Y-m-d");
            } else {
                $dao -> quittime = NULL;
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
                $info['ktime']=mb_substr($info['ktime'], 0, 10, 'utf-8');
				$this -> ajaxReturn($info,'已成功保存',1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
    public function excel(){
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
        $dao = D('Abroad');
        $school = D('Abroadschool');
        $my=$dao -> where($map)-> order('status asc,id desc') -> select();
        foreach($my as $key=>$vo){
            $my[$key]['school']=$school->where('abroadid = '.$vo['id'])->select();
            if(!empty($my[$key]['school'])){
               foreach($my[$key]['school'] as $keys=>$jo){
                   if($my[$key]['school'][$keys]['ktime']=='0000-00-00 00:00:00'){
                       $my[$key]['school'][$keys]['ktime']='';
                   }else{
                      $my[$key]['school'][$keys]['ktime']=substr($jo['ktime'],0,10); 
                   }
               }
            }
        }
        foreach($my as $key=>$li){
            if($my[$key]['visatime']=='0000-00-00 00:00:00'){
                $my[$key]['visatime']='';
            }else{
                $my[$key]['visatime']=substr($li['visatime'],0,10);
            }
            
        }
        $excel=D("Excel");
        $excel->output($my);
    }
     public function pastAbroadInfo(){
        $this->display();
    }
    public function pastAbroadInfoLeft(){
        $dao = D("abroad");
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
        $map['year(ctime)'] = array('lt',date('Y'));
        $dtree_year = $dao -> field( 'year(ctime) as year') -> group('year') ->where($map)-> order('year desc') -> select(); //只查看往年的记录，排除当年的
        $this -> assign('dtree_year',$dtree_year);
        $this -> display();
    }
    public function pastProcess(){
        $this->assign('id',$_GET['id']);
        $dao = D('Abroad');
        $all_status=$this->getStatus();
        $a=array();
        $map['year(ctime)'] = $_GET['id'];
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
                'name'=>'楷体',
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
       
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
       
        $map['tusername1|tusername2|tusername3|tusername4'] = session('username');
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
    public function stugraduationconfirm()
    {
        $truename = $_GET['stuname'];
        $A =D('ClassstudentView');
        $map['studentname']=$truename;
        $data = $A ->where($map)->select();

        if($data[0]['sex'] == '男'){
                $data[0]['ensex'] = 'Mr.';
                $data[0]['sexname'] = 'him';
            }
        if($data[0]['sex'] == '女'){
                $data[0]['ensex'] = 'Ms.';
                $data[0]['sexname'] = 'her';
            }
        $data[0]['graduteyear'] = $data[0]['year'] + 3;
        $data[0]['birthday']=date('j M Y',time($data[0]['birthday']));
        if($data[0]['sex']==''||$data[0]['birthday'] == ''||$data[0]['birthday'] == '0000-00-00'||$data[0]['majore'] == ''||$data[0]['year'] == ''){
            $tips='下划线处信息不全，请下载后补全!';
        }
        if($data[0]['sex']==''){
            $data[0]['ensex']='______'.'(Ms. or Mr.)';
            $data[0]['sexname']='______'.'(him or her)';
        }
        if($data[0]['birthday'] == '' || $data[0]['birthday'] == '0000-00-00'){
            $data[0]['birthday']='____________________'.'(birthday)';
        }
        if($data[0]['majore'] == ''){
            $data[0]['majore']='________________________'.'(major)';
        }
        if($data[0]['year'] == ''){
            $data[0]['year']='_______________';
            $data[0]['graduateyear']='_______________';
        }
        $this->assign('stuname',$truename);
        $this->assign('tips',$tips);        
        $this->assign('data',$data);
        $this->assign('current_time',date('M.j,Y',time()));
        $this->display(graduationconfirm);
    }
     public function downgraduateconfirm(){
        $name = $_GET['name'];
        if (!isset($name)) {
            $this -> error('参数缺失');
        }
        $A =D('ClassstudentView');
        $map['studentname']=$name;
        $data = $A ->where($map)->select();

        if($data[0]['sex'] == '男'){
                $data[0]['ensex'] = 'Mr.';
                $data[0]['sexname'] = 'him';
            }
        if($data[0]['sex'] == '女'){
                $data[0]['ensex'] = 'Ms.';
                $data[0]['sexname'] = 'her';
            }
        $data[0]['graduteyear'] = $data[0]['year'] + 3;
        $data[0]['birthday']=date('j M Y',time($data[0]['birthday']));
        $current_time=date('M j, Y',time());
        if($data[0]['sex']==''){
            $data[0]['ensex']='______'.'(Ms. or Mr.)';
            $data[0]['sexname']='______'.'(him or her)';
        }
        if($data[0]['birthday'] == '' || $data[0]['birthday'] == '0000-00-00'){
            $data[0]['birthday']='____________________'.'(birthday)';
        }
        if($data[0]['majore'] == ''){
            $data[0]['majore']='________________________'.'(major)';
        }
        if($data[0]['year'] == ''){
            $data[0]['year']='_______________';
            $data[0]['graduateyear']='_______________';
        }
        include dirname(__FILE__).'/../../Lib/ORG/PHPWord.php';
        $PHPWord = new PHPWord();
        $section = $PHPWord->createSection();
        $PHPWord->addFontStyle('rStyle', array('bold'=>true,'name'=>'Arial','size'=>'15'));
        $PHPWord->addFontStyle('cStyle', array('name'=>'Arial','size'=>'12'));
        $PHPWord->addFontStyle('aStyle', array('name'=>'Times New Roman','size'=>'12'));
        $PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>250));
        $PHPWord->addParagraphStyle('lStyle', array('align'=>'left', 'spaceAfter'=>250));
        $PHPWord->addParagraphStyle('YStyle', array('align'=>'right', 'spaceAfter'=>250));

        $section->addTextBreak(6);
        $section->addText('GRADUATION CERTIFICATE',array('name'=>'Arial','size'=>'15'), 'pStyle');
        $section->addTextBreak(3);
        $section->addText("$current_time",array('name'=>'Arial','size'=>'14'),'YStyle');
        $section->addTextBreak(2);
        $textrun = $section->createTextRun(array('spacing'=>250));
        $textrun->addText("This is to certify that ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['ensex'] ,array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['ename'],array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText(" (Full Name of the student), born on ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['birthday'],array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText(", has been a student of SQA HND programme at our university since September ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['year'],array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText(". ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['ensex'] ,array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['ename'],array('color'=>'blue','name'=>'Arial','size'=>'14'));        
        $textrun->addText(" has completed all the units in HND Courses of ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['majore'],array('italic'=>'true','color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText(" successfully. The HND Diploma from Scottish Qualifications Authority (SQA) is expected to be issued to ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['sexname'],array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText(" in June ",array('name'=>'Arial','size'=>'14'));
        $textrun->addText($data[0]['graduteyear'],array('color'=>'blue','name'=>'Arial','size'=>'14'));
        $textrun->addText(". ",array('name'=>'Arial','size'=>'14'));
        $section->addTextBreak(4);
        $section->addText('Entrepreneurship Management and Training School',array('name'=>'Arial','size'=>'14'), 'lStyle');
        $section->addText('Beihang University',array('name'=>'Arial','size'=>'14'), 'lStyle');
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $filename='HND'.''.'毕业证明-'.$name;
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition:attachment;filename=".$filename.".docx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }
    public function backcountryconfirm()
    {
        $truename = $_GET['stuname'];
        $A =D('ClassstudentView');
        $map['studentname']=$truename;
        $data = $A ->where($map)->select();
        $data[0]['graduateyear'] = $data[0]['year'] + 3;
        $data[0]['birthday']=date('Y年m月d日 ',time($data[0]['birthday']));
        $tips='下划线处信息不全，请下载后补全!';
        if($data[0]['sex']==''){
            $data[0]['sex']='______'.'(性别)';
        }
        if($data[0]['birthday'] == '' || $data[0]['birthday'] == '0000-00-00'){
            $data[0]['birthday']='____________________'.'(生日)';
        }
        if($data[0]['major'] == ''){
            $data[0]['major']='________________________'.'(专业)';
        }
        if($data[0]['year'] == ''){
            $data[0]['year']='_______________';
            $data[0]['graduateyear']='_______________';
        }
        if($data[0]['passport'] == ''){
            $data[0]['passport']='_______________';
        }
        if($data[0]['idcard'] == ''){
            $data[0]['idcard']='_______________';
        }
        $this->assign('stuname',$truename);
        $this->assign('tips',$tips);        
        $this->assign('data',$data);
        $this->assign('current_time',date('Y年m月d日 ',time()));
        $this->display(backconfirm);
    }
     public function downbackconfirm(){
        $name = $_GET['name'];
        if (!isset($name)) {
            $this -> error('参数缺失');
        }
        $A =D('ClassstudentView');
        $map['studentname']=$name;
        $data = $A ->where($map)->select();
        $data[0]['graduateyear'] = $data[0]['year'] + 3;
        $data[0]['birthday']=date('Y年m月d日',time($data[0]['birthday']));
        $current_time=date('Y年m月d日',time());
        if($data[0]['sex']==''){
            $data[0]['sex']='______'.'(性别)';
        }
        if($data[0]['birthday'] == '' || $data[0]['birthday'] == '0000-00-00'){
            $data[0]['birthday']='____________________'.'(生日)';
        }
        if($data[0]['major'] == ''){
            $data[0]['major']='________________________'.'(专业)';
        }
        if($data[0]['year'] == ''){
            $data[0]['year']='_______________';
            $data[0]['graduateyear']='_______________';
        }
        if($data[0]['passport'] == ''){
            $data[0]['passport']='_______________';
        }
        if($data[0]['idcard'] == ''){
            $data[0]['idcard']='_______________';
        }
        include dirname(__FILE__).'/../../Lib/ORG/PHPWord.php';
        $PHPWord = new PHPWord();
        $section = $PHPWord->createSection();
        $PHPWord->addFontStyle('rStyle', array('bold'=>true,'name'=>'楷体','size'=>'15'));
        $PHPWord->addFontStyle('cStyle', array('name'=>'楷体','size'=>'12'));
        $PHPWord->addFontStyle('aStyle', array('name'=>'Times New Roman','size'=>'12'));
        $PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>250));
        $PHPWord->addParagraphStyle('lStyle', array('align'=>'left', 'spaceAfter'=>250));
        $PHPWord->addParagraphStyle('YStyle', array('align'=>'right', 'spaceAfter'=>250));

        $section->addTextBreak(6);
        $section->addText('国内项目院校的证明函',array('name'=>'宋体','bold'=>'true','size'=>'22'), 'pStyle');
        $section->addTextBreak(3);
        $section->addText("中国留学服务中心：",array('name'=>'楷体','size'=>'14'),'lStyle');
        $textrun = $section->createTextRun(array('spacing'=>250));
        $textrun->addText("    ",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['studentname'] ,array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("，",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['sex'],array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("，",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['birthday'],array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("生，身份证：",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['idcard'],array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("，系我院英国高等教育文凭项目（SQA HND ) ",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['year'] ,array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("级",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['major'],array('color'=>'blue','name'=>'楷体','size'=>'14'));        
        $textrun->addText("专业学生[中留服注册号CHN为____________，注册号SCN为____________]。该生 ",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['year'],array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("年9月开始学习，",array('name'=>'楷体','size'=>'14'));
        $textrun->addText($data[0]['graduateyear'],array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun->addText("年5月获得英国苏格兰学历管理委员会（SQA）颁发的HND文凭。该项目在我校属于计划外招生。",array('name'=>'楷体','size'=>'14'));
        $textrun2 = $section->createTextRun(array('spacing'=>250));
        $textrun2->addText("    经核实",array('name'=>'楷体','size'=>'14'));
        $textrun2->addText($data[0]['studentname'] ,array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun2->addText("（护照号：",array('name'=>'楷体','size'=>'14'));
        $textrun2->addText($data[0]['passport'],array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun2->addText("）在____年__月至____年__月在_____大学_______专业学习大学本科第__年课程。____年__月，",array('name'=>'楷体','size'=>'14'));
        $textrun2->addText($data[0]['studentname'] ,array('color'=>'blue','name'=>'楷体','size'=>'14'));
        $textrun2->addText("获得_____大学___学位。" ,array('name'=>'楷体','size'=>'14'));
        $section->addTextBreak(4);
        $section->addText('北航创业管理培训学院',array('name'=>'楷体','size'=>'14'), 'YStyle');
        $section->addText($current_time,array('name'=>'楷体','size'=>'14'), 'YStyle');
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $filename='回国认证-'.$name;
        $filename=mb_convert_encoding($filename, "GB2312", "UTF-8");
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition:attachment;filename=".$filename.".docx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }
    public function stuCommon() {
        $this -> display();
    } 
    public function stuCommonMenu($id) {
        $menu['stuCommonInfo']='基本信息';
        $menu['stuCommonScore']=' 成绩单';
        $menu['stuCommonCertification']='在读证明';
        $menu['stuCommonAttend']='考勤记录';
        $menu['stuCommonReward']='奖惩记录';
        $menu['stuCommonProcess']='留学进程';
        $this->assign('menu',$this ->autoMenu($menu,$id));  
    }
    
    public function stuCommonScore() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $Score = D("prograde");
        $map['stunum']=$id;
        // $map['isvisible']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
        $term_num=count($term);
        // if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('id',$id);//这里的id指的是学号
            $this->assign('my',$my);
        // }
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function stuCommonCertification(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $student=D('classstudent');
        $enroll=D('enroll');
        $class=D('class');
        $map['student']=$id;
        $map1['username']=$id;
        $stu=$student->where($map)->select();

        $mystuename = $stu[0][ename];
        $mystuename_begin = ucfirst(strtolower($mystuename));
        $len = strlen($mystuename);
        $position = 0;
        for($i=0;$i<$len;$i++){
            if($mystuename[$i]!=$mystuename_begin[$i]){
                $position = $i;
                break;
            }
        }
        $my_xing = substr($mystuename_begin,0,$position);
        $my_ming = substr($mystuename_begin,$position);
        $mystuename = $my_xing." ".ucfirst(strtolower($my_ming));

        $map2[id]=$stu[0]['classid'];
        $zsex='';
        $year='';
        $month='';
        $day='';
        $major='';
        $sex='';
        $birthday='';
        $majore='';
        $classinfo=$class->where($map2)->select();
        $stuinfo=$enroll->where($map1)->select();
        $current_grade=$this->getGrade(date('Y-m-d',time()),$classinfo[0][year]);
        $current_egrade=$this->getEgrade(date('Y-m-d',time()),$classinfo[0][year]);
        if($stuinfo[0][sex]==''){
            $zsex='______'.'(性别)';$sex='______'.'(sex)';
        }else{
            $zsex=$stuinfo[0][sex];$sex=$this->getSex($stuinfo[0][sex]);
        }
        if($stuinfo[0][birthday] == '' || $stuinfo[0][birthday] == '0000-00-00 00:00:00'){
            $year='____';$month='____';$day='____';$birthday='____________________';
        }else{
            $year=substr($stuinfo[0][birthday],0,4);
            $month=substr($stuinfo[0][birthday],5,2);
            $day=substr($stuinfo[0][birthday],8,2);
            $birthday=date('M.j,Y',strtotime($stuinfo[0][birthday]));
        }
        if($classinfo[0][major] == ''){
            $major='______________________________________';
            $item = '_________________';
            $iteme = '_________________';
        }else{
            $major=$classinfo[0][major];
            $item = M("major")->where(array("major"=>$major))->find();
            if ($item["item"] == "HND") {
                $item = "英国高等教育文凭";
                $iteme = "SQA HND programme";
            }elseif ($item["item"] == "美国2+2") {
                $item = "美国高等教育项目";
                $iteme = "American Higher Education Project(AHEP)";
            }
        }
        if($classinfo[0][majore] == ''){
            $majore='_____________________________________';
        }else{
            $majore=$classinfo[0][majore];
        }
        $this->assign("item",$item);
        $this->assign("iteme",$iteme);
        $this->assign('zsex',$zsex);
        $this->assign('year',$year);
        $this->assign('month',$month);
        $this->assign('day',$day);
        $this->assign('major',$major);
        $this->assign('sex',$sex);
        $this->assign('majore',$majore);
        $this->assign('current_grade',$current_grade);
        $this->assign('now',date('Y-m-d',time()));
        $this->assign('current_time',date('M.j,Y',time()));
        $this->assign('birthday',$birthday);
        $this->assign('current_egrade',$current_egrade);
        $this->assign('stu',$stu);
        $this->assign('mystuename',$mystuename);
        $this->assign('stuinfo',$stuinfo);
        $this->assign('classinfo',$classinfo);
        $this->assign('id',$id);
        $this->stuCommonMenu($id);
        if($stuinfo[0][sex]==''||$stuinfo[0][birthday]=='0000-00-00 00:00:00'||$classinfo[0][major]==''||$classinfo[0][majore]==''){
            $tips='下划线处信息不全，请下载后补全!';
        }
        $this->assign('tips',$tips);
        $this -> display();
    }
    public function stuCommonAttend() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $Attend = D("Attend");
        $map['susername']=$id;
        $map1['susername']=$id;
        $count = $Attend -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Attend -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
         $rid=$Attend->where($map)->field('id')->order('ctime DESC') ->select();
            $rid_num=count($rid);
            $a=array();
            if($rid_num>0){
                foreach($rid as $key=>$value){
                $map['id']=$value['id'];
                $my[$key]=$Attend -> where($map)-> find();
                        if(!in_array(substr($my[$key]['timezone'],0,strripos($my[$key]['timezone'], '第')),$a)){
                         array_push($a,substr($my[$key]['timezone'],0,strripos($my[$key]['timezone'], '第')));
                     }
                }
            }
            $map1['timezone']=array('like',$a[0].'%');
            $truant_num=$Attend->where($map1)->sum('truant');
            $tvacate_num=$Attend->where($map1)->sum('tvacate');
            $svacate_num=$Attend->where($map1)->sum('svacate');
            $late_num=$Attend->where($map1)->sum('late');
            $this->assign('term',$a[0]);
            $this->assign('truant',$truant_num);
            $this->assign('tvacate',$tvacate_num);
            $this->assign('svacate',$svacate_num);
            $this->assign('late',$late_num);
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function stuCommonReward() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $Reward = D("Reward");
        $map['susername']=$id;
        $count = $Reward -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
            $p = new Page($count, $listRows);
            $my = $Reward -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);         
        } 
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function stuCommonGetSystem($category,$name) {
        $system = D("System");
        $temp = explode(',', $system -> where("category='" . $category . "' and name='" . $name . "'") -> getField("content"));
        $a = array();
        foreach($temp as $key => $value) {
            $a[$value] = $value;
        } 
        return $a;
    } 
    public function stuCommonGrade(){
        $this->assign('id',$_GET['id']);
        $this->assign('info',$this->students());
        $this->display();
    }
     public function stuCommonClass(){
        $this->assign('id',$_GET['id']);
        $this->assign('class',$_GET['class']);
        $this->assign('info',$this->students());
        $this->display();
    }
    public function stuCommonInfo() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        $area = D("Area");
        $province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
        $a = array();
        foreach($province as $key => $value) {
            $a[$value['region_name']] = $value['region_name'];
        }
        $is_or_not = array('是' => '是', '否' => '否');
        $education = $this -> stuCommonGetSystem("enroll","education");
        $entrancefull = $this -> stuCommonGetSystem("enroll","entrancefull");
        $englishfull = $this -> stuCommonGetSystem("enroll","englishfull");
        $mathfull = $this -> stuCommonGetSystem("enroll","mathfull");
        $abroad = $this -> stuCommonGetSystem("enroll","abroad");
        $coursewant = $this -> stuCommonGetSystem("enroll","coursewant");
        $englishtrain = $this -> stuCommonGetSystem("enroll","englishtrain");
        $sourcenewspaper = $this -> stuCommonGetSystem("enroll","sourcenewspaper");
        $sourcenet = $this -> stuCommonGetSystem("enroll","sourcenet");
        $nationality = $this -> stuCommonGetSystem("enroll","nationality");

        $this -> assign('a', $a);
        $this -> assign('is_or_not', $is_or_not);
        $this -> assign('education', $education);
        $this -> assign('entrancefull', $entrancefull);
        $this -> assign('englishfull', $englishfull);
        $this -> assign('mathfull', $mathfull);
        $this -> assign('abroad', $abroad);
        $this -> assign('coursewant', $coursewant);
        $this -> assign('englishtrain', $englishtrain);
        $this -> assign('sourcenewspaper', $sourcenewspaper);
        $this -> assign('sourcenet', $sourcenet);
        $this -> assign('nationality', $nationality);

        $Enroll = D('Enroll');
        $map['username'] = $id;
        $my = $Enroll -> where($map) -> find();
        if ($my) {
            $this -> assign('nativecity', $this -> getBrotherCity($my['nativecity']));
            $this -> assign('fcity', $this -> getBrotherCity($my['fcity']));
            $this -> assign('mcity', $this -> getBrotherCity($my['mcity']));
            $this -> assign('ocity', $this -> getBrotherCity($my['ocity']));
            $this -> assign('schoolcity', $this -> getBrotherCity($my['schoolcity']));
            $this -> assign('abroad_selected', explode(',', $my['abroad']));
            $this -> assign('newspaper_selected', explode(',', $my['sourcenewspaper']));
            $this -> assign('net_selected', explode(',', $my['sourcenet']));
            $this -> assign('infosource_selected', explode(',', $my['infosource']));
            $this -> assign('try', $my['try']);
            $this -> assign('paystatus',$my["paystatus"]);
        }
        $mbp["stunum"] = $id;
        $allpay = M("payment")->where($mbp)->select();
        $this->assign('allpay',$allpay);
        $allgrade = M("prograde")->where("stunum=$id")->select();
        foreach ($allgrade as $va) {
            $willgrade[$va["term"]][] = $va;
        }
        $this->assign("willgrade",$willgrade);
        $this -> assign('my', $my);
        $mapJ['susername']=$id;
        $list=M('judge')->where($mapJ)->select();
        $this->assign('list',$list);
        $this->stuCommonMenu($id);
        $this -> display();
    }
    public function checkEnrollPlus() {
        load("@.idcard");
        load("@.check");
        if (empty($_POST['truename'])) {
            $this -> error('Part1 学生姓名不能为空');
        }
        if (empty($_POST['username'])) {
            // $this -> error('Part1 学生学号不能为空');
        }elseif(!isusername($_POST['username'])){
          $this -> error('Part1 学生学号校验失败');  
        }
        if (empty($_POST['cyear'])) {
            // $this -> error('Part1 入学年份不能为空');
        }elseif (!isctime($_POST['cyear'])) {
            $this -> error('Part1 入学年份校验失败');
        }
        if (empty($_POST['sex'])) {
            // $this -> error('Part1 性别不能为空');
        }elseif (!issex($_POST['sex'])) {
            $this -> error('Part1 学生性别校验失败');
        }
        if (empty($_POST['bankcard'])) {
            // $this->error("Part1 学生银行卡号不能为空");
        }
        if (empty($_POST["onecard"])) {
            // $this->error("Part1 学生一卡通号不能为空");
        }
        if (empty($_POST['mobile'])) {
            // $this -> error('Part1 学生手机号不能为空');
        } elseif (!ismobile($_POST['mobile'])) {
            $this -> error('Part1 学生手机号校验失败');
        }
        if (!empty($_POST['qq'])) {
            if (!is_numeric($_POST['qq']))
                $this -> error('Part1 学生QQ号码校验失败');
        }
        if (!empty($_POST['email'])) {
            if (!isemail($_POST['email']))
                $this -> error('Part1 学生Email校验失败');
        }
        if (!empty($_POST['idcard'])) {
            if (!validation_filter_id_card($_POST['idcard']))
                $this -> error('Part1 身份证号码校验失败');
        }else{
            $this->error("Part1 身份证号码不能为空");
        }
        if (!empty($_POST['fmobile'])) {
            if (!ismobile($_POST['fmobile']))
                $this -> error('Part2 父亲手机号码校验失败');
        }
        if (!empty($_POST['mmobile'])) {
            if (!ismobile($_POST['mmobile']))
                $this -> error('Part2 母亲手机号码校验失败');
        }
        if (!empty($_POST['omobile'])) {
            if (!ismobile($_POST['omobile']))
                $this -> error('Part2 其他联系人手机号码校验失败');
        }         
        $this -> success('数据校验成功');
    }    

    public function insertPlus() {
        $dao = D("Enroll");
        $map["student"] = $dao->where(array("id"=>$_POST["id"]))->getField("username");
        if ($dao -> create()) {
            $dao -> abroad = implode(',', $_POST['abroad']);
            $dao -> infosource = implode(',', $_POST['infosource']);
            $dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
            $dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $checked = $dao->save();
            if ($checked>0) {
                if (!empty($_POST["username"])) {
                    M("classstudent")->where($map)->save(array("student"=>$_POST["username"]));
                }
                $this -> success('已成功保存');
            } else{
                $this -> error('没有更新任何数据');
            }
        } else {
            $this -> error($dao -> getError());
        }
    } 
    public function stuCommonProcess(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao=D('Abroad');
        $map['susername']=$id;
        $my=$dao->where($map)->find();
        $this->assign('my',$my);
        $school=D('Abroadschool')->where("abroadid=".$my['id'])->select();
        $this -> assign('school', $school);
        $this->stuCommonMenu($id);
        $this->display();

    }
    public function stuCommonLeft(){
        $searchkey = $_GET['searchkey'];
        if (isset($searchkey)) {
            $dao2 = D('ClassstudentView');
            $map2['studentname|ename|enamesimple']=array('like',"%$searchkey%");
            //$stu = $dao2->where($map2)->order('student asc')-> select();
            $stu = $dao2->query('SELECT u_classstudent.student, u_class.name, u_classstudent.studentname from u_classstudent, u_class where u_classstudent.classid=u_class.id and ( u_classstudent.enamesimple like "%'.$searchkey.'%" or u_classstudent.ename like "%'.$searchkey.'%" or  u_classstudent.studentname like "%'.$searchkey.'%" ) ORDER BY u_classstudent.student asc');
            $count=count($stu);
            $this -> assign('searchkey', $searchkey);
            $this -> assign('stu', $stu);
            $this -> assign('count', $count);
            $this->display();
        } else{
            $m["item"] = '美国2+2';//分项目的限制开始
            $major = M("major")->where($m)->select();
            foreach ($major as $vm) {
                $majors[$vm["major"]]=$vm["major"];
            }
            $map_class["major"] = array("in",$majors);//分项目的限制结束
            $grades = explode(",", $_SESSION["grade"]);
            $map_class["year"] = array("in",$grades);
            $dao_class = D('Class');
            $map_class['isbiye']=0;
            $dtree_class = $dao_class->where($map_class)->order('year desc,name asc')-> select();
            $dtree_year=$dao_class ->where($map_class)->field('year')-> group('year')->order('year desc')->select();
            $dao2 = D('ClassstudentView');
            $dtree_stu = $dao2->order('student asc')->group("id")-> select();
            $this->assign('dtree_year',$dtree_year);
            $this -> assign('dtree_class', $dtree_class);
            foreach ($dtree_stu as $k => $va) {
                if (empty($va["student"]) || empty($va["scnid"]) || empty($va["sex"]) || empty($va["bankcard"]) || empty($va["onecard"]) || empty($va["mobile"])) {
                    $dtree_stu[$k]["studentname"] .= '<span style="color:red;">*</span>';
                }
            }
            $this -> assign('dtree_stu', $dtree_stu);
            $this->display();
        }
    }
    public function stuCommonRight(){
        $test=$_GET['id'];
        $this -> assign('test', $test);
        $this->display();
    }
    public function students(){
        $a = array('name'=>'姓名','ename'=>'ename','birthday'=>'出生日期','sex'=>'性别','gender'=>'Gender','address'=>'家庭住址','HomeAddress'=>'HomeAddress','postaladdress'=>'通信地址','CorrespondenceAddress'=>'CorrespondenceAddress','phone'=>'固定电话','mobile'=>'手机','email'=>'Email1','Email2'=>'Email2','MSN'=>'MSN','qq'=>'OICQ','nativeprovince'=>'省份','Province'=>'Province','nativecity'=>'城市','City'=>'City','idcardpassport'=>'身份证护照号码','project'=>'就读国内项目名称','HNDCenter'=>'HNDCenter','year'=>'入学时间','grade'=>'所属年级','entrancescore'=>'高考成绩总分','englishscore'=>'英语单科成绩','entrancefull'=>'高考分数标准','major'=>'HND专业','drop'=>'是否退学','repeat'=>'是否留级','SCN'=>'SCN号','listeningscore'=>'听力得分','readingscore'=>'阅读得分','writingscore'=>'写作得分','speakingscore'=>'口语得分','testscore'=>'进入专业课英语成绩总分','score1'=>'最优有效雅思成绩','score1id'=>'雅思考试号','plus'=>'其他','HNDtime'=>'获得HND证书时间','quit'=>'是否留学','country'=>'留学国家','Country'=>'Country','school'=>'国外院校名称','ForeignUniversityApplied'=>'ForeignUniversityApplied','fmajor'=>'留学所学专业','together'=>'出国所经过中介名称','employ'=>'是否就业','enterprise'=>'就业企业名称','workaddress'=>'就业企业所在省市','enterprisenature'=>'就业企业性质','individualorientationandspecialty'=>'个人情况介绍及特长','professionalcertificate'=>'所获得职业资格证书','xuben'=>'续本','xubensch'=>'续本国内院校名称','degreesch'=>'将获得哪所院校颁发学位','xubenmajor'=>'续本专业');
        return $a;
    }
    public function getBrotherCity($cityname) {
        $Area = D("Area");
        $map['region_name']=$cityname;
        $map['region_type']=2;
        $parent = $Area ->field('parent_id')-> where($map) -> find();
        $map_a['parent_id']=$parent['parent_id'];
        $brother = $Area-> where($map_a) -> select();
        $a = array();
        foreach($brother as $key => $value) {
            $a[$value['region_name']] = $value['region_name'];
        }
        return $a;
    }
    public function downPreScore(){
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/prescore.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('B3', $stuinfo["studentname"]) 
            ->setCellValue('B4', $stuinfo["ename"]) ;//写上学生姓名
        $scores = M("pregrade")->where(array("stunum"=>$id))->select();
        $styleArray = array(  
            'borders' => array(  
                'allborders' => array(  
                    'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
                    'color' => array('argb' => '000'),  
                ),  
            ),  
        );  
        $line = 6;
        for ($i=0; $i < count($scores); $i++) { 
            $p  ->setActiveSheetIndex(0)
                ->setCellValue('A'.$line, $scores[$i]["examname"]) 
                ->setCellValue('A'.($line+1), ' 听 力 :') 
                ->setCellValue('B'.($line+1), $scores[$i]["listening"]."分")
                ->setCellValue('A'.($line+2), ' Listening:') 
                ->setCellValue('B'.($line+2), $scores[$i]["listening"])
                ->setCellValue('A'.($line+3), ' 口 语 :') 
                ->setCellValue('B'.($line+3), $scores[$i]["speaking"]."分")
                ->setCellValue('A'.($line+4), 'Speaking:') 
                ->setCellValue('B'.($line+4), $scores[$i]["speaking"])
                ->setCellValue('D'.($line+1), ' 阅 读 :') 
                ->setCellValue('E'.($line+1), $scores[$i]["reading"]."分") 
                ->setCellValue('D'.($line+2), 'Reading:') 
                ->setCellValue('E'.($line+2), $scores[$i]["reading"]) 
                ->setCellValue('D'.($line+3), ' 总 分 :') 
                ->setCellValue('E'.($line+3), $scores[$i]["total"]."分") 
                ->setCellValue('D'.($line+4), 'Total:') 
                ->setCellValue('E'.($line+4), $scores[$i]["total"]) 
                ->setCellValue('G'.($line+1), ' 写 作 :') 
                ->setCellValue('H'.($line+1), $scores[$i]["writing"]."分") 
                ->setCellValue('G'.($line+2), 'Writing:')
                ->setCellValue('H'.($line+2), $scores[$i]["writing"])
                ->setCellValue('A'.($line+5), 'Remark:Listening 9,Reading 9,Writing 9,Speaking 9,Total 9')
                ->getStyle('A'.($line+5).':I'.($line+5))->applyFromArray($styleArray);
            $p  ->getActiveSheet()->mergeCells( 'A'.$line.':I'.$line);
            $p  ->getActiveSheet()->mergeCells( 'A'.($line+5).':I'.($line+5));
            $p  ->getActiveSheet()->getStyle('A'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            for ($j=1; $j <= 4; $j++) { 
                $p  ->getActiveSheet()->getStyle('A'.($line+$j).':C'.($line+$j))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->getStyle('D'.($line+$j).':F'.($line+$j))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->getStyle('G'.($line+$j).':I'.($line+$j))->applyFromArray($styleArray);
                $p  ->getActiveSheet()->mergeCells( 'B'.($line+$j).':C'.($line+$j));
                $p  ->getActiveSheet()->mergeCells( 'E'.($line+$j).':F'.($line+$j));
                $p  ->getActiveSheet()->mergeCells( 'H'.($line+$j).':I'.($line+$j));
            }
            $line += 7;
        }
        
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$stuinfo["student"].'-'.$stuinfo["studentname"].'-预科成绩单.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function downProScoreA(){
        //导出字母制成绩
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/proscore.xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A2', 'SCN：'.$stuinfo["student"].'        Name：'.$stuinfo["ename"].'      Major：'.$stuinfo["majore"]);//写上名字
        $scores = M("prograde")->where(array("stunum"=>$id))->select();//选出所有考试的分数
        foreach ($scores as $vs) {//对数据进行初步处理
            if ($willwrite[$vs["term"]][$vs["course"]]["isrepair"] == "repair") {
                continue;
            }
            $willwrite[$vs["term"]][$vs["course"]]["count"]++;
            $willwrite[$vs["term"]][$vs["course"]]["hundred"] += $vs["hundred"];
            if ($vs["letter"] == "U" || $vs["hundred"] == 0) {//接下来判断这是重修或是导入时输入字母还是数字
                $willwrite[$vs["term"]][$vs["course"]]["isrepair"] = "repair";
                $willwrite[$vs["term"]][$vs["course"]]["hundred"] = 0;
            }else{
                if ($vs["letter"] == "P" || $vs["letter"] == "F") {
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "hundred";
                }else{
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "letter";
                }
            }
        }
        $line = 6;//从第7行开始写，每门课加1
        $row = 64;//从B列开始写，每学期加2
        $course = M("course");
        foreach ($willwrite as $termname => $vw) {
            $row = $row + 2;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue(chr($row)."5",$termname)
                ->mergeCells(chr($row)."5:".chr($row+1)."5")
                ->setCellValue(chr($row)."6","Marks")
                ->setCellValue(chr($row+1)."6","Credits");
            foreach ($vw as $coursename => $vs) {
                $line++;
                $map["classid"] = $stuinfo["classid"];
                $map["name|ename"] = $coursename;
                $credit = $course->where($map)->getField("credit");//获取学分
                if ($vs["hundred"] == 0) {//这里开始处理转化为字母的问题
                    $hundred = 0;
                    $letter = "U";
                }else{
                    $hundred = $vs["hundred"]/$vs["count"];
                    if ($vs["standard"] == "letter") {
                        switch (true) {
                            case $hundred > 70:
                                $letter = "A";
                                break;
                            case $hundred >60:
                                $letter = "B";
                                break;
                            case $hundred > 50:
                                $letter = "C";
                                break;
                            case $hundred > 1:
                                $letter = "D";
                                break;
                        }
                    }else{
                        if ($hundred >= 60) {
                            $letter = "P";
                        }else{
                            $letter = "F";
                        }
                    }
                }
                $p  ->setActiveSheetIndex(0)
                    ->setCellValue("A".$line,$coursename)
                    ->setCellValue(chr($row).$line,$letter)
                    ->setCellValue(chr($row+1).$line,$credit);
            }
        }
        $styleThinBlackBorderOutline = array( 
            'borders' => array ( 
                'allborders' => array ( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式 
                    'color' => array ('argb' => 'FF000000'), //设置border颜色 
                ), 
            ),
        );
        $p->getActiveSheet()->getStyle('A5:'.chr($row+1).$line)->applyFromArray($styleThinBlackBorderOutline);
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$stuinfo["student"].'-'.$stuinfo["studentname"].'-专业课成绩单（字母制）.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function downProScoreB(){
        //导出百分制成绩
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        }
        // Vendor('PHPExcel'); 
        $titlepic = '/buaahnd/sys/Tpl/Public/download/proscore(2+2).xls';
        $php_path = dirname(__FILE__) . '/';
        $excelurl = $php_path .'../../../..'.$titlepic;
        $stuinfo = D("ClassstudentView")->where(array("student"=>$id))->find();
        if (!$stuinfo) {
            $this -> error('无此学生'.$id);
        }
        include $php_path .'../../Lib/ORG/PHPExcel.class.php';
        $p = PHPExcel_IOFactory::load($excelurl);//载入Excel
        if ($stuinfo["sex"] == "男") {
            $stuinfo["sexe"] = "male";
        }elseif($stuinfo["sex"] == "女") {
            $stuinfo["sexe"] = "female";
        }//性别
        $tmpstart = substr($stuinfo["student"], 0,4);
        $tmpend = $tmpstart + 2;
        $stuinfo["date"] = "Sep.$tmpstart-July.$tmpend （$tmpstart.9-$tmpend.7）";
        //在校期间
        $p  ->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Name of Student（姓名）:'.$stuinfo["ename"].'('.$stuinfo["studentname"].')')
            ->setCellValue("A3","Gender（性别）:".$stuinfo["sexe"].'('.$stuinfo["sex"].')')
            ->setCellValue("A4","Date of Birth（生日）:".$stuinfo["birthday"])
            ->setCellValue("A5","Major（专业）: ".$stuinfo["majore"]."(".$stuinfo["major"].")")
            ->setCellValue("A6","Duration of School（在校期间）:".$stuinfo["date"]);
        $scores = M("prograde")->where(array("stunum"=>$id))->select();//选出所有考试的分数
        foreach ($scores as $vs) {//对数据进行初步处理
            if ($willwrite[$vs["term"]][$vs["course"]]["isrepair"] == "repair") {
                continue;
            }
            $willwrite[$vs["term"]][$vs["course"]]["count"]++;
            $willwrite[$vs["term"]][$vs["course"]]["hundred"] += $vs["hundred"];
            if ($vs["letter"] == "U" || $vs["hundred"] == 0) {//接下来判断这是重修或是导入时输入字母还是数字
                $willwrite[$vs["term"]][$vs["course"]]["isrepair"] = "repair";
                $willwrite[$vs["term"]][$vs["course"]]["hundred"] = 0;
            }else{
                if ($vs["letter"] == "P" || $vs["letter"] == "F") {
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "hundred";
                }else{
                    $willwrite[$vs["term"]][$vs["course"]]["standard"] = "letter";
                }
            }
        }
        $line = 7;//从第7行开始写，每门课加1
        $row = 66;//从B列开始写，每学期加2
        $course = M("course");
        foreach ($willwrite as $termname => $vw) {
            $allscore = 0;$allcredit = 0;
            $line++;
            $tmpTermYear = substr($termname, 0,4);
            $tmpTermNum = substr($termname, -10);
            if ($tmpTermNum == "第1学期") {
                $tmpName = "Fall $tmpTermYear"."（"."$tmpTermYear"."年秋季学期）";
                $tmpCredit = "Credits Fall $tmpTermYear"."（"."$tmpTermYear"."年秋季学期学分）";
            }elseif ($tmpTermNum == "第2学期") {
                $tmpName = "Spring $tmpTermYear"."（"."$tmpTermYear"."年春季学期）";
                $tmpCredit = "Credits Spring $tmpTermYear"."（"."$tmpTermYear"."年春季学期学分）";
            }
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("A".$line,$tmpName);
            $p  ->getActiveSheet()->getStyle('A'.$line)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $p  ->getActiveSheet()
                ->getStyle("A".$line)->getFont()->setBold(true);
            foreach ($vw as $coursename => $vs) {
                $map["classid"] = $stuinfo["classid"];
                $map["name|ename"] = $coursename;
                $courseinfo = $course->where($map)->find();
                $credit = $courseinfo["credit"]; //获取学分
                if (!$courseinfo) {
                    continue;
                }
                $line++;
                if ($vs["hundred"] == 0) {//这里开始处理转化为字母的问题
                    $hundred = 0;
                    $letter = "U";
                }else{
                    $hundred = $vs["hundred"]/$vs["count"];
                    $hundred = round($hundred,2);
                }
                $p  ->setActiveSheetIndex(0)
                    ->setCellValue("A".$line,$courseinfo["name"].'('.$courseinfo["ename"].')')
                    ->setCellValue("B".$line,$credit)
                    ->setCellValue("C".$line,$hundred);
                $allcredit += $credit;
            }
            $line++;
            $p  ->setActiveSheetIndex(0)
                ->setCellValue("A".($line),$tmpCredit)
                ->setCellValue("B".$line,$allcredit);
            $p->getActiveSheet()->getStyle('A'.($line))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }
        $p  ->setActiveSheetIndex(0)
            ->setCellValue("A".($line+1),"Note：Keys to Graded Unit:  4= 100%~90%  3=89%~80%   2=79%~70%  1=60%~69%   0= 59% and below.")
            ->setCellValue("A".($line+3),"Date:". date('Y-m-d',time()) )
            ->setCellValue("A".($line+4),"Place:Beihang University,Beijing,China（地点：中国，北京，北京航空航天大学）");
        $p->getActiveSheet()->getRowDimension($line+1)->setRowHeight(37);
        for ($i=1; $i <= 4; $i++) { 
            $p->getActiveSheet()->mergeCells('A'.($line+$i).':C'.($line+$i));
            $p->getActiveSheet()->getStyle('A'.($line+$i))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }
        $line+=1;
        $styleThinBlackBorderOutline = array( 
            'borders' => array ( 
                'allborders' => array ( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN, //设置border样式 
                    'color' => array ('argb' => 'FF000000'), //设置border颜色 
                ), 
            ),
        );
        $p->getActiveSheet()->getStyle('A7:'.chr($row+1).$line)->applyFromArray($styleThinBlackBorderOutline);
        
        ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma: no-cache");
        header("Content-Type:application/octet-stream");
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename='.$stuinfo["student"].'-'.$stuinfo["studentname"].'-专业课成绩单.xls');//设置文件的名称
        header("Content-Transfer-Encoding:binary");
        $objWriter = PHPExcel_IOFactory::createWriter($p, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    public function getGrade($now,$enrollyear){
        $grade=array('一','二','三','四');
        $a='';
        if((intval(substr($now,0,4))-intval($enrollyear)) == 0){
            $a=$grade[0];
        }elseif((intval(substr($now,5,2)) < 9)){
            $a=$grade[((intval(substr($now,0,4))-intval($enrollyear))-1)];
        }else{
            $a=$grade[(intval(substr($now,0,4))-intval($enrollyear))];    
        }
        return $a;
    }
    public function getSex($stusex){
        $allsex=array('male','female');
        $sex='';
        if($stusex == '男'){
            $sex=$allsex[0];
        }elseif($stusex == '女'){
            $sex=$allsex[1];
        }
        return $sex;
    }
    public function getEgrade($now,$enrollyear){
        $egrade=array('First','Second','Third','Fourth');
        $b='';
        if((intval(substr($now,0,4))-intval($enrollyear)) == 0){
            $b=$egrade[0];
        }elseif((intval(substr($now,5,2)) < 9)){
            $b=$egrade[((intval(substr($now,0,4))-intval($enrollyear))-1)];
        }else{
            $b=$egrade[(intval(substr($now,0,4))-intval($enrollyear))];    
        }
        return $b;
    }
} 

?>