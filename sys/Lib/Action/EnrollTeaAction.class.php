<?php
class EnrollTeaAction extends CommonAction {
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
	public function ampieData() {
		$enroll = D('Enroll');
		$username = session('username');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
		for($i = 0;$i < 5;$i++) {
            $map['status'] = $i;
            $map['counselor'] = $username;
                        
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
            }
            
			$a[$i] = $enroll -> Field('id') -> where($map) -> count();
		} 
        $EnrollChange=D('Enrollchange');
        $map_a['fromcounselor']=$username;
        if($current > $current_year.'-10-31'){
            $map_a['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
        }
        $a[5] = $EnrollChange-> Field('id') -> where($map_a) -> count();
		$this -> assign('a', $a);
		$this -> display();
	} 
	public function status() {
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
	public function alert() {
        $Enroll = D('Enroll');
        $map['counselor']=session('username');
        $map['status']='0';
        $new_student =$Enroll->where($map)->count();
        $EnrollRecord = D('EnrollRecordView');
        $map2['counselor']=session('username');
        $day=array();
        for($i=-1;$i<27;$i++){
            $day[$i]=date("Y-m-d",strtotime("$i day"));
            $map2['nexttime']=$day[$i];
            $my[$i]=$EnrollRecord->where($map2)->select();
            $j=$i==-1?"_1":"$i";            
            $this -> assign("day".$j, $day[$i]);
            $this -> assign("my".$j, $my[$i]);
        }
        $this -> assign('new_student', $new_student);
		$this -> display();
	} 
   
	public function addRecord() {
		$enrollid = $_GET['enrollid'];
		if (!isset($enrollid)) {
			$this -> error('参数缺失');
		} 
		$enroll = D('Enroll');
		$map['id'] = $enrollid;
		$map['counselor'] = session('username');
		$student_info = $enroll -> where($map) -> find();
		if (!$student_info) {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
		// dump($student_info);
		$map2['enrollid'] = $enrollid;
		$enroll_record = D('Enrollrecord') -> where($map2) -> order('id desc') -> select();
		$this -> assign('my', $student_info);
		$this -> assign('enroll_record', $enroll_record);
		$this -> display();
	} 
	public function insertRecord() {
		$record_content = $_POST['record_content'];
		if (empty($record_content)) {
			$this -> error('请选择咨询记录');
		} 
		$enrollid = $_POST['enrollid'];
		switch ($record_content) {
			case '已交报名费':
				$kk1 = $_POST['kk1'];
				$kk2 = $_POST['kk2'];
				$kk3 = $_POST['kk3'];
				if (empty($kk1)) {
					$this -> error('请填写报名费收据号');
				} 
                if (empty($kk2)) {
					$this -> error('请填写交费日期');
				}
                if (empty($kk3)) {
					$this -> error('请填写下次跟踪时间');
				}
				$record_content .= " 报名费收据号为：" . $kk1;
				$record_content .= " 交费日期为：" . $kk2;
				$record_content .= " 下次跟踪：" . $kk3;
				$data_record['nexttime'] = $kk3;
				$Enroll = D("Enroll");
				$data_enroll['id'] = $enrollid;
				$data_enroll['entryfees'] = $kk1;
				$data_enroll['entryfeesdate'] = $kk2;
				$data_enroll['status'] = '2';
				$data_enroll['statustext'] = '已交报名费';
				$Enroll -> data($data_enroll) -> save();
				break;
			case '等待分数':
				if (empty($_POST['a2content'])) {
					$this -> error('请填写下次跟踪时间');
				} 
                $record_content .= " 下次跟踪：" . $_POST['a2content'];
				$data_record['nexttime'] = $_POST['a2content'];
				break;
			case '等待录取结果':
				if (empty($_POST['a3content'])) {
					$this -> error('请填写下次跟踪时间');
				} 
                $record_content .= " 下次跟踪：" . $_POST['a3content'];
				$data_record['nexttime'] = $_POST['a3content'];
				break;
			case '设置为已录取':
				if (empty($_POST['b5'])) {
					$this -> error('请填写学费发票号');
				} 
				if (empty($_POST['b6'])) {
					$this -> error('请填写学费发票日期');
				} 
				$Enroll = D("Enroll");
				$data_enroll['id'] = $enrollid;
				$data_enroll['tuitionfeef'] = $_POST['b5'];
				$data_enroll['tuitionfeefdate'] = $_POST['b6'];
				$data_enroll['status'] = '3';
				$data_enroll['statustext'] = '已录取';
				$Enroll -> data($data_enroll) -> save();
				break;
			case '设置为不录取':
				$a5content = $_POST['a5content'];
				if (empty($a5content)) {
					$this -> error('请填写不录取原因');
				} 
				$record_content .= " 原因为：" . $a5content;
				$Enroll = D("Enroll");
				$data_enroll['id'] = $enrollid;
				$data_enroll['status'] = '4';
				$data_enroll['statustext'] = '不录取';
				$Enroll -> data($data_enroll) -> save();
				break;
			case '其它':
				$a6content = $_POST['a6content'];
				if (empty($a6content)) {
					$this -> error('请填写其它情况说明');
				} 
				if (empty($_POST['a6other'])) {
					$this -> error(' 请填写下次跟踪时间');
				} 
				$record_content = $a6content;
                 $record_content .= " 下次跟踪：" . $_POST['a6other'];
				$data_record['nexttime'] = $_POST['a6other'];
                
				break;
		} 
		$Enrollrecord = D("Enrollrecord");
		$data_record['enrollid'] = $_POST['enrollid'];
		$data_record['content'] = $record_content;
		$data_record['counselor'] = session('username');
		$data_record['counselorname'] = session('truename');
		$data_record['ctime'] = date("Y-m-d H:i:s");
		$result = $Enrollrecord -> data($data_record) -> add();
 		$this -> success('已成功新增');
	} 
	public function start() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Enroll');
		$data['status'] = '1';
		$data['statustext'] = '正在咨询';
		$result = $dao -> where("id='$id'") -> save($data);

		$dao2 = D("Enrollrecord");
		$data_record['enrollid'] = $id;
		$data_record['content'] = '启动咨询';
		$data_record['counselor'] = session('username');
		$data_record['counselorname'] = session('truename');
		$data_record['ctime'] = date("Y-m-d H:i:s");
		$result = $dao2 -> data($data_record) -> add();
		$this -> success('已成功启动咨询。该学生被放入“正在咨询”中');
	} 
    
    public function getStatus(){
        $a=array();
        $a['x']='新分配';
        $a['1']='正在咨询';
        $a['2']='已交报名费';
        $a['3']='已录取';
        $a['4']='不录取';
        $a['z']='新录入';
        return $a;
    }
    public function search() {
		$truename = $_GET['truename'];
		$mobile = $_GET['mobile'];
		$schoolname = $_GET['schoolname'];
		$school = $_GET['school'];
		$status = $_GET['status'];
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        
		if (isset($truename)) {
			$map['truename']=array('like',"%$truename%");
            $this -> assign("truename", $truename);
		} 
        if (isset($mobile)) {
			$map['mobile']=array('like',"%$mobile%");
            $this -> assign("mobile", $mobile);
		} 
        if (isset($schoolname)) {
			$map['schoolname']=array('like',"%$schoolname%");
            $this -> assign("schoolname", $schoolname);
		} 
        if (isset($school)) {
            $this -> assign("city_select", $school);
            switch ($school) {
                case '安徽': 
                    $map['schoolprovince']=$school;
                    break;
                case '浙江': 
                    $map['schoolprovince']=$school;
                    break; 
                case '其它省或信息不全': 
                    $map['schoolprovince']=array('not in','江苏,安徽,浙江');
                    break;
                default:
                    $map['schoolcity']=$school;
            }
		} 
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
        
        //显示状态
        $all_status=$this->getStatus();
        $this -> assign('all_status', $all_status);

        if (isset($status)) {
            if($status == 'x'){
                $map['status'] = '0';
            }else{
                $map['status'] = $status;
            }
            			
            $this -> assign("status_select", $status);
		} 
               
        $map['counselor'] = session('username');
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }

            $enroll = D('Enroll');
            $count = $enroll -> where($map) -> count();

            import("@.ORG.Page");
            $listRows = 10;
            $p = new Page($count, $listRows);
            $my = $enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);        
        $this -> display();
	} 
    public function toExcel(){
        $this -> display();
    }
	public function category() {
		$statusid = $_GET['statusid'];
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
		if (!isset($statusid)) {
			$this -> error('参数缺失');
		} 
		$username = session('username');
		if ($statusid == '5') {
            $EnrollChangeView=D('EnrollChangeView');
            import("@.ORG.Page");
            $map['fromcounselor']=session('username');
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
            }
            $count = $EnrollChangeView -> where($map) -> count();
			$p = new Page($count, 10);
			$my = $EnrollChangeView -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select(); //原来按照中文拼音排序无法显示
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
			$this -> display('category' . $statusid);
		} else {
			$enroll = D('Enroll');
			import("@.ORG.Page");
// 			$map = "status='$statusid' and counselor='$username'";
            $map['status'] = $statusid;
            $map['counselor'] = $username;
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
            }
			$count = $enroll -> where($map) -> count();

            import("@.ORG.Page");
            $listRows = 10;
            $p = new Page($count, $listRows);
            $my = $enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
			$this -> display('category' . $statusid);
		} 
	} 
     public function getSystem($name) {
		$system = D("System");
		$temp = explode(',', $system -> where("name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		} 
		return $a;
	} 
    public function student() {
        $area = D("Area");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
        $is_or_not=array('是'=>'是','否'=>'否');
		$education = $this -> getSystem("education");
		$entrancefull = $this -> getSystem("entrancefull");
		$englishfull = $this -> getSystem("englishfull");
		$mathfull = $this -> getSystem("mathfull");
		$abroad = $this -> getSystem("abroad");
		$coursewant = $this -> getSystem("coursewant");
		$englishtrain = $this -> getSystem("englishtrain");
		$sourcenewspaper = $this -> getSystem("sourcenewspaper");
		$sourcenet = $this -> getSystem("sourcenet");
		$nationality = $this -> getSystem("nationality");

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
        
        $id=$_GET['enrollid'];
        if(!isset($id)){
            $this->error('参数缺失');
        }
        $Enroll = D('Enroll');
        $map['id']=$id;
        $my =$Enroll->where($map)->find(); 
        if($my){
            $this -> assign('nativecity', $this->getBrotherCity($my['nativecity']));
            $this -> assign('fcity', $this->getBrotherCity($my['fcity']));
            $this -> assign('mcity', $this->getBrotherCity($my['mcity']));
            $this -> assign('ocity', $this->getBrotherCity($my['ocity']));
            $this -> assign('schoolcity', $this->getBrotherCity($my['schoolcity']));
            $this -> assign('abroad_selected', explode(',',$my['abroad']));
            $this -> assign('newspaper_selected', explode(',',$my['sourcenewspaper']));
            $this -> assign('net_selected', explode(',',$my['sourcenet']));
            $this -> assign('infosource_selected', explode(',',$my['infosource']));
            $this -> assign('try', $my['try']);
            $this -> assign('my', $my);
            $this -> display();
        }else{
            $this->error('未找到该学生或对该学生没有操作权限');
        }
	}
    public function insertPlus() {
		$dao = D("Enroll");
		if ($dao -> create()) {
			$dao -> abroad = implode(',', $_POST['abroad']);
			$dao -> infosource = implode(',', $_POST['infosource']);
			$dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
			$dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $checked = $dao->save();
			if ($checked>0) {
                $this -> success('已成功保存');
			} else{
                $this -> error('没有更新任何数据');
            }
		} else {
			$this -> error($dao -> getError());
		} 
	} 
	public function checkEnrollPlus() {
		load("@.idcard");
		load("@.check");
		if (empty($_POST['truename'])) {
			$this -> error('Part1 学生姓名不能为空');
		} 
		if (empty($_POST['sex'])) {
			$this -> error('Part1 性别不能为空');
		} 
		
		if (empty($_POST['mobile'])) {
			$this -> error('Part1 学生手机号不能为空');
		} elseif ((ismobile($_POST['mobile'])||($_POST['mobile']=='00000000000'))==false) {
			$this -> error('Part1 学生手机号校验失败');
		} 
        
        $dao = D("Enroll");
        if($_POST['mobile']!=='00000000000'){
            $map['_query'] = 'mobile='.$_POST['mobile'].'&truename='.$_POST['truename'].'&_logic=or';
            $map['id'] = array('neq',$_POST['id']);
            $current = @date("Y-m-d",time());
            $current_year = @date("Y",time());
            $pyear = $current_year-1;
            if($current > $current_year.'-10-31'){
                $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
            } else{
                $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
            }// 只需交验当年的记录
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("库中已有相同姓名或手机号，无法录入! 姓名:" . $isHava[0]['truename'] . " 手机号:" . $isHava[0]['mobile'] . " 时间：" . $isHava[0]['ctime'] . " 填表人：" . $isHava[0]['fill']);
            } 
        }else{
            $map['truename'] = $_POST['truename'];
            $map['id'] = array('neq',$_POST['id']);
            $map['ctime'] = array('between',array($year.'-01-01 00:00:00',$year.'-12-31 23:59:59'));
            $isHava = $dao -> where($map) -> select();
            if ($isHava) {
                $this -> error("库中已有相同姓名，无法录入! 姓名:" . $isHava[0]['truename'] . " 时间：" . $isHava[0]['ctime'] . " 填表人：" . $isHava[0]['fill']);
            } 
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
    public function city() {
		$bigid = $_GET["province"];
		if (isset($bigid)) {
			$area = D("Area");
			$bigid = $area -> where("parent_id = 1 and region_name = '$bigid'") -> getField('region_id');
			$province = $area -> where("parent_id = " . $bigid) -> Field("region_name") -> select();
			$this -> ajaxReturn($province, '成功', 1);
		} else {
			$this -> ajaxReturn(0, '获取信息失败', 0);
		} 
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
	public function test() {
		echo "今天:".date("Y-m-d")."<br>";     
echo "昨天:".date("Y-m-d",strtotime("-1 day")), "<br>";     
echo "明天:".date("Y-m-d",strtotime("1 day")). "<br>";  
echo "明天:".date("Y-m-d",strtotime("2 day")). "<br>";  
$map[-1]='0';
$map[0]='1';
dump($map);
	} 
    
    public function statusCommon() {
        $this -> display();
    }
    public function statusCommonLeft() {
        $Enroll = D("Enroll");
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        if($current > $current_year.'-10-31'){
            $dtree_year = $Enroll -> field( 'year(ctime) as year') -> group('year')-> order('year desc') -> select();
        } else{
            $dtree_year = $Enroll -> field( 'year(ctime) as year') -> group('year') ->where('year(ctime) != year(now())')-> order('year desc') -> select(); //只查看往年的记录，排除当年的
        }
        
        $this -> assign('dtree_year',$dtree_year);
        $this -> display();
    }
    public function menuCategory($year) {
        $menu['paststatus']='招生进程图';
        $menu['pastcategory/statusid/0']='新分配';
        $menu['pastcategory/statusid/1']='正在咨询';
        $menu['pastcategory/statusid/2']='已交报名费';
        $menu['pastcategory/statusid/3']='已录取';
        $menu['pastcategory/statusid/4']='不录取';
        $menu['pastcategory/statusid/5']='已转';
        $menu['pastsearch']='搜索';
        $menu['pasttoExcel']='导出数据';
        $this->assign('menu',$this -> cateMenu($menu,$year));       
	}
    public function cateMenu($menu,$year='') {
		$path = array();
		foreach($menu as $key => $value) {
			$is_on = ($key == ACTION_NAME)?' class="on" ':'';
            $url_plus=empty($year)?'':'/year/'.$year;
			$path[] = '<a href="__URL__/' . $key.$url_plus . '" ' . $is_on . '>' . $value . '</a>';
		} 
		return implode(' | ', $path);
	} 
    public function pastampieData() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$enroll = D('Enroll');
		$username = session('username');
		for($i = 0;$i < 5;$i++) {
            $map['status']=$i;
            $map['counselor']=$username;
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
			$a[$i] = $enroll -> Field('id') -> where($map) -> count();
		} 
        $EnrollChange=D('Enrollchange');
        $map_a['fromcounselor']=$username;
        $map_a['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
        $a[5] = $EnrollChange-> Field('id') -> where($map_a) -> count();
		$this -> assign('a', $a);
        $this -> assign('year',$year);
		$this -> display();
	} 
    public function paststatus() {
        $year = $_GET['year'];
        $this -> menuCategory($year);
        $this -> assign('year',$year);
		$this -> display();
	} 
    public function pastcategory() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$statusid = $_GET['statusid'];
		if (!isset($statusid)) {
			$this -> error('参数缺失');
		} 
		$username = session('username');
      
		if ($statusid == '5') {
            $EnrollChangeView=D('EnrollChangeView');
            import("@.ORG.Page");
            $map['fromcounselor']=session('username');
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
            $count = $EnrollChangeView -> where($map) -> count();
			$p = new Page($count, 10);
			$my = $EnrollChangeView -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows)  -> order('ctime desc') -> select(); 
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
			$this -> assign('year', $year);
            $this -> menuCategory($year);
			$this -> display('pastcategory' . $statusid);
		} else {
			$enroll = D('Enroll');
			import("@.ORG.Page");            
			$map['status']=$statusid;
			$map['counselor']=$username;
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
			$count = $enroll -> where($map) -> count();//"year(ctime)='$year' and ".
// 			$enrollRecord=D('EnrollRecordView');
//             $dao=D('enrollrecord');
//             $query='select max(id) as mid from u_enrollrecord group by enrollid';
//             $ids=$dao->query($query);            
//             $id_array=array();
//             foreach($ids as $v){
//                 $id_array[]= $v['mid'];
//             }
//                     
//             $map['rid'] =array('in',$id_array);
            import("@.ORG.Page");
            $listRows = 10;
            $p = new Page($count, $listRows);
            $my = $enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
            $this -> assign('year', $year);
            $this -> menuCategory($year);
			$this -> display('pastcategory' . $statusid);
		} 
	}
 public function pastsearch() {
        $year = $_GET['year'];
        $pyear = $year-1;
		$truename = $_GET['truename'];
		$school = $_GET['school'];
		if (isset($truename)) {
			$map['truename|schoolname']=array('like',"%$truename%");
            $this -> assign("truename", $truename);
		} 
        if (isset($school)) {
            $this -> assign("city_select", $school);
            switch ($school) {
                case '安徽': 
                    $map['schoolprovince']=$school;
                    break;
                case '浙江': 
                    $map['schoolprovince']=$school;
                    break; 
                case '其它省或信息不全': 
                    $map['schoolprovince']=array('not in','江苏,安徽,浙江');
                    break;
                default:
                    $map['schoolcity']=$school;
            }
		} 
        $city_fortag=array('南京'=>'南京',
        '苏州'=>'苏州',
        '无锡'=>'无锡',
        '常州'=>'常州',
        '淮安'=>'淮安',
        '连云港'=>'连云港',
        '南通'=>'南通',
        '宿迁'=>'宿迁',
        '泰州'=>'泰州',
        '徐州'=>'徐州',
        '盐城'=>'盐城',
        '扬州'=>'扬州',
        '镇江'=>'镇江',
        '安徽'=>'安徽',
        '浙江'=>'浙江',
        '其它省或信息不全'=>'其它省或信息不全');
        $this -> assign("city_fortag", $city_fortag);
       
            $map['counselor'] = session('username');
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
            $enroll = D('Enroll');
            import("@.ORG.Page");
            $count = $enroll -> where($map) -> count();

            import("@.ORG.Page");
            $listRows = 10;
            $p = new Page($count, $listRows);
            $my = $enroll -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
            $this -> assign('year',$year);
            $this -> menuCategory($year);

        
        $this -> display();
	}   
    public function pasttoExcel(){
        $year = $_GET['year'];
        $this -> assign('year',$year);
        $this -> menuCategory($year);
        $this -> display();
    }    
    public function record() {
        $year = $_GET['year'];
		$enrollid = $_GET['enrollid'];
        $statusid = $_GET['statusid'];
		if (!isset($enrollid)) {
			$this -> error('参数缺失');
		} 
		$enroll = D('Enroll');
		$map['id'] = $enrollid;
		$map['counselor'] = session('username');
		$student_info = $enroll -> where($map) -> find();
		if (!$student_info) {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
		// dump($student_info);
		$map2['enrollid'] = $enrollid;
		$enroll_record = D('Enrollrecord') -> where($map2) -> order('id desc') -> select();
		$this -> assign('my', $student_info);
		$this -> assign('enroll_record', $enroll_record);
        $this -> assign('year',$year);
        $this -> assign('statusid',$statusid);
		$this -> display();
	} 
    public function paststudent(){
        $year = $_GET['year'];
        $statusid = $_GET['statusid'];
        $id = $_GET['enrollid'];
        $area = D("Area");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
		$is_or_not = array('是' => '是', '否' => '否');
		$education = $this -> getSystem("education");
		$entrancefull = $this -> getSystem("entrancefull");
		$englishfull = $this -> getSystem("englishfull");
		$mathfull = $this -> getSystem("mathfull");
		$abroad = $this -> getSystem("abroad");
		$coursewant = $this -> getSystem("coursewant");
		$englishtrain = $this -> getSystem("englishtrain");
		$sourcenewspaper = $this -> getSystem("sourcenewspaper");
		$sourcenet = $this -> getSystem("sourcenet");
		$nationality = $this -> getSystem("nationality");

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
		$id = $_GET['enrollid'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$Enroll = D('Enroll');
		$map['id'] = $id;
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
			$this -> assign('my', $my);
            $this -> assign('year',$year);
            $this -> assign('statusid',$statusid);
			$this -> display();
		} else {
			$this -> error('未找到该学生或对该学生没有操作权限');
		} 
    }
    public function excel(){
		$enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        $map['counselor'] = session('username');
        $my = $enroll -> where($map) -> order('status asc,id desc') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:G'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '手机');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '状态');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '咨询者');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '录入者');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '录入时间');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['mobile']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['statustext']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['counselorname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['fill']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['ctime']);
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('全部');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}            
    public function excel2(){
		$enroll = D('Enroll');
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        $map['counselor'] = session('username');
        $map['status']='3';
        $my = $enroll -> where($map) -> order('CONVERT(schoolprovince USING gbk), CONVERT(schoolcity USING gbk), CONVERT(schoolname USING gbk)') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '身份证号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '高考成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '英语成绩');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '毕业高中所在省');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '毕业高中名称');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '报名费收据号');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '学费发票号');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '咨询教师');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['idcard']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['entrancescore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['englishscore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['schoolprovince']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['schoolcity'].$value['schoolname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H'.$k, " ".$value['entryfees']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('I'.$k, " ".$value['tuitionfeef']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('J'.$k, $value['counselorname']);
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('已录取');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
    public function pastexcel(){
		$enroll = D('Enroll');
        $year = $_GET['year'];
        $pyear = $year-1;
        $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
        $map['counselor'] = session('username');
        $my = $enroll -> where($map) -> order('status asc,id desc') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:G'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        // Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '手机');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '状态');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '咨询者');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '录入者');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '录入时间');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['mobile']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['statustext']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['counselorname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['fill']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['ctime']);		
            ++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('全部');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
    public function pastexcel2(){
		$enroll = D('Enroll');
        $year = $_GET['year'];
        $pyear = $year-1;
        $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$year.'-10-31 23:59:59'));
        $map['counselor'] = session('username');
        $map['status']='3';
        $my = $enroll -> where($map) -> order('CONVERT(schoolprovince USING gbk), CONVERT(schoolcity USING gbk), CONVERT(schoolname USING gbk)') -> select();
        $count = $enroll -> where($map) -> count();
        $count++;
        import("@.ORG.PHPExcel");
		$objPHPExcel = new PHPExcel();
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        // Add some data
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '姓名');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '性别');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '身份证号');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '高考成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '英语成绩');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '毕业高中所在省');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '毕业高中名称');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '报名费收据号');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', '学费发票号');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', '咨询教师');
		$k = 2;
		foreach($my as $value){
			//纯文本
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.$k, $value['truename']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('B'.$k, $value['sex']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('C'.$k, " ".$value['idcard']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('D'.$k, $value['entrancescore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('E'.$k, $value['englishscore']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('F'.$k, $value['schoolprovince']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('G'.$k, $value['schoolcity'].$value['schoolname']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('H'.$k, " ".$value['entryfees']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('I'.$k, " ".$value['tuitionfeef']);
			$objPHPExcel->getActiveSheet(0)->setCellValue('J'.$k, $value['counselorname']);
						
			++$k;
		}
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('已录取');
		$objPHPExcel->setActiveSheetIndex(0);
		$outFileName = 'data '.date("Y-m-d H_i_s");
		$outFileName = iconv('UTF-8','GBK',$outFileName);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$outFileName.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
  
} 

?>