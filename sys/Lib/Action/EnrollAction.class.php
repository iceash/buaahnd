<?php
class EnrollAction extends Action {
	public function index() {
		$area = D("Area");
		$user = D("User");
		$province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
		$a = array();
		foreach($province as $key => $value) {
			$a[$value['region_name']] = $value['region_name'];
		} 
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
		$counselor = $user -> Field("username,truename") -> where("role like '%EnrollTea%'") -> select();
		$counselor_tag = array();
		foreach($counselor as $key => $value) {
			$counselor_tag[$value['username']] = $value['truename'];
		} 
		$this -> assign('a', $a);
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
		$this -> assign('counselor_tag', $counselor_tag);
		$this -> display();
	} 
	public function getSystem($name) {
		$system = D("System");
		$temp = explode(',', $system -> where("category='enroll' and name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		}         
		return $a;

	} 
	public function insert() {
		$dao = D("Enroll");
        $fill=$_POST['fill'];
		if ($dao -> create()) {
			$dao -> ctime = date("Y-m-d H:i:s") ;
			$dao -> abroad = implode(',', $_POST['abroad']);
			$dao -> infosource = implode(',', $_POST['infosource']);
			$dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
			$dao -> sourcenet = implode(',', $_POST['sourcenet']);
			if ($insertID = $dao -> add()) {
				$dao2 = D("Enrollrecord");
                $data['enrollid']=$insertID;
                $data['content']="新录入，填表人：".$fill;
                $data['counselor']="system";
                $data['counselorname']="系统自动记录";
                $data['ctime']=date("Y-m-d H:i:s");
                $result=$dao2->add($data);
                if($result>0){
                    $this -> success('已成功录入！');
                }
			} else{
                $this -> error('新建招生信息：数据写入错误！');
            }
		} else {
			$this -> error($dao -> getError());
		} 
	} 
	public function checkEnroll() {
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
		} elseif ((ismobile($_POST['mobile'])||($_POST['mobile']==='00000000000'))==false) {
			$this -> error('Part1 学生手机号校验失败');
		} 
        $dao = D("Enroll");
        $current = @date("Y-m-d",time());
        $current_year = @date("Y",time());
        $pyear = $current_year-1;
        if($current > $current_year.'-10-31'){
            $map['ctime'] = array('gt',$current_year.'-11-01 00:00:00');
        } else{
            $map['ctime'] = array('between',array($pyear.'-11-01 00:00:00',$current_year.'-10-31 23:59:59'));
        }
        if($_POST['mobile']!=='00000000000'){
            $map['_query'] = 'mobile='.$_POST['mobile'].'&truename='.$_POST['truename'].'&_logic=or';
            $isHava = $dao -> where($map) -> select();

            if ($isHava) {
                $this -> error("库中已有相同姓名或手机号，无法录入! 姓名:" . $isHava[0]['truename'] . " 手机号:" . $isHava[0]['mobile'] . " 时间：" . $isHava[0]['ctime'] . " 填表人：" . $isHava[0]['fill']);
            } 
        }else{
            $map['truename'] = $_POST['truename'];
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
        if (empty($_POST['schoolprovince'])||empty($_POST['schoolcity'])||empty($_POST['schoolname'])) {
			$this -> error('Part3 毕业学校必填');
		}
		if (empty($_POST['fill'])) {
			$this -> error('填表人不能为空');
		}
        if (empty($_POST['infosource']) && empty($_POST['sourcenewspaper']) && empty($_POST['sourcenet'])) {
			$this -> error('信息来源不能为空');
		} 
		$this -> success('数据校验成功');
	} 
	public function test() {
		$dao = D("Enroll");
		$map['truename'] = '李明';
		$map['mobile'] = '18951650388';
		$isHava = $dao -> where($map) -> select();
		if ($isHava) {
			echo 'yes';
		} 
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

    public function insertPlus() {
		$dao = D("Enroll");
		if ($dao -> create()) {
			$dao -> abroad = implode(',', $_POST['abroad']);
			$dao -> infosource = implode(',', $_POST['infosource']);
			$dao -> sourcenewspaper = implode(',', $_POST['sourcenewspaper']);
			$dao -> sourcenet = implode(',', $_POST['sourcenet']);
            $checked = $dao->save();
			if ($checked) {
                $this -> success('已成功保存');
			} else{
                $this -> error('没有更新任何数据');
            }
		} else {
			$this -> error($dao -> getError());
		} 
	}

    public function enroll() {
        $area = D("Area");
        $user = D("User");
        $province = $area -> where("parent_id = 1") -> Field("region_name") -> select();
        $a = array();
        foreach($province as $key => $value) {
            $a[$value['region_name']] = $value['region_name'];
        }
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
        $counselor = $user -> Field("username,truename") -> where("role like '%EnrollTea%'") -> select();
        $counselor_tag = array();
        foreach($counselor as $key => $value) {
            $counselor_tag[$value['username']] = $value['truename'];
        }
        $this -> assign('a', $a);
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
        $this -> assign('counselor_tag', $counselor_tag);
        $this -> display();
    }
}

?>