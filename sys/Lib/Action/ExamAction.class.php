<?php
class ExamAction extends Action {
	public function index() {
        if (!session('?code')) {
			$this -> redirect('Exam/login');
		} 
        $Exam=D('Exam');
        $map['name']=session('name');
        $map['mobile']=session('mobile');
        $map['code']=session('code');
        $result=$Exam->where($map)->find();
        if(empty($result['paper'])){
            $newQuestion=$this->getPaperRnd();
            $q=$this->getPaperExist($newQuestion);
            $data['id']=$result['id'];
            $data['ctime']=date('Y-m-d H:i:s');
            $data['paper']=$newQuestion;
            $Exam->save($data);
        }else{
            $q=$this->getPaperExist($result['paper']);
        }
        $select=$this->getSelect($q[1]);
        $fill=$this->getFill($q[2]);
        $read=$this->getRead($q[3]);
        $write=$this->getWrite($q[4]);
        $cloze=$this->getCloze($q[5]);
        $this->assign('result',$result);
        $this->assign('select',$select);
        $this->assign('fill',$fill);
        $this->assign('read',$read);
        $this->assign('write',$write);
        $this->assign('cloze',$cloze);
        $this->display();
	} 
    public function logout() {
		if (session('?code')) {
			session('code', null);
			session('name', null);
			session('mobile', null);
			session('ruleid', null);
			session('[destroy]');
		} 
		$this -> redirect('Exam/login');
	} 
    public function submitPaper() {
        $score=0;
        $myanswer1=array(); 
        $myanswer2=array();
        $result1=array();
        $result2=array();
        $result3=array();
        $result5=array();
        $Exam=D('Exam');
        $map['name']=session('name');
        $map['mobile']=session('mobile');
        $map['code']=session('code');
        $result=$Exam->where($map)->find();
        if($result['issub']=='1'){
            $this->error("已于".$result['subtime']."提交（客观题分数为：".$result['mscore']."分），不能再次提交。");
        }
        $q=$this->getPaperExist($result['paper']);
        
        $Examselect=D('Examselect');//select
        $map_a['id']=array('in',$q[1]);
        $select = $Examselect->where($map_a)->select();
        foreach($select as $key=>$value){
            $total1[$value['level']]+=1;
            $temp='q1_'.$value['id'];
            $myanswer = $_POST[$temp];
            $myanswer1[]=$temp.':'.$myanswer;
            if($value['answer']==$myanswer){
                $score++;
                $right1[$value['level']]+=1;
            }
        }
        foreach($total1 as $key=>$value){//将$result组成“难度:满分:得分”的形式 
            $result1[]=$key.":".$value.":".$right1[$key];
        }      
        $Examfill=D('Examfill');//fill
        $map_b['id']=array('in',$q[2]);
        $fill = $Examfill->where($map_b)->select();
        foreach($fill as $key=>$value){
            $total2[$value['level']]+=1;
            $temp='q2_'.$value['id'];
            $myanswer = $_POST[$temp];
            $myanswer1[]=$temp.':'.$myanswer;
            if(trim($value['answer'])==trim($myanswer)){
                $score++;
                $right2[$value['level']]+=1;
            }
        }
        foreach($total2 as $key=>$value){
            $result2[]=$key.":".$value.":".$right2[$key];
        } 
        $Examread=D('Examread');//read
        $Examreaditem=D('Examreaditem');
        $map_c['id']=array('in',$q[3]);
        $read = $Examread->where($map_c)->select();
        foreach($read as $key=>$value){
            $total3[$value['level']]+=10;//阅读题每题对应10分
            $map_k['articleid']=$value['id'];
            $my_k=$Examreaditem->where($map_k)->order('id asc')->select();
            $temp='';
            foreach($my_k as $key2=>$value2){
                $temp='q3_'.$value2['id'];
                $myanswer = $_POST[$temp];
                $myanswer1[]=$temp.':'.$myanswer;
                if($value2['answer']==$myanswer){
                    $score+=2;//阅读题每题2分
                    $right3[$value['level']]+=2;
                }
            }
        }
        foreach($total3 as $key=>$value){
            $result3[]=$key.":".$value.":".$right3[$key];
        }
        
        $Examcloze=D('Examcloze');//cloze
        $Examclozeitem=D('Examclozeitem');
        $map_d['id']=array('in',$q[5]);
        $cloze = $Examcloze->where($map_d)->select();
        
        foreach($cloze as $key=>$value){
            $total5[$value['level']]+=20;//完型填空每大题对应20分
            $map_g['articleid']=$value['id'];
            $my_g=$Examclozeitem->where($map_g)->order('id asc')->select();
            $temp='';
            foreach($my_g as $key2=>$value2){
                $temp='q5_'.$value2['id'];
                $myanswer = $_POST[$temp];
                $myanswer1[]=$temp.':'.$myanswer;
                if($value2['answer']==$myanswer){
                    $score+=$value['score'];
                    $right5[$value['level']]+=$value['score'];
                    
                }
            }
        }
        
        foreach($total5 as $key=>$value){
            $result5[]=$key.":".$value.":".$right5[$key];
        } 

        $Examwrite=D('Examwrite');//write
        $map_d['id']=array('in',$q[4]);
        $write = $Examwrite->where($map_d)->select();
        foreach($write as $key=>$value){
            $temp='q4_'.$value['id'];
            $myanswer = '[作文题目'.$temp.']'.$value['title'];
            $myanswer .= '<br />[答卷]'.$_POST[$temp];
            $myanswer2[]=$myanswer;
        }
        $data['id']=$result['id'];
        $data['issub']='1';
        $data['subtime']=date('Y-m-d H:i:s');
        $data['answer1']=implode(',',$myanswer1);
        $data['answer2']=implode('<br />',$myanswer2);
        $data['result1']=implode(',',$result1);
        $data['result2']=implode(',',$result2);
        $data['result3']=implode(',',$result3);
        $data['result5']=implode(',',$result5);
        $data['mscore']=$score;
        $affect=$Exam->save($data);
        if($affect>0){
            $this->success('已成功提交！您的客观题得分为：'.$score.'分（不含作文分）');
        }else{
            $this->error('提交失败');
        }
	} 
    public function getSystem($name) {//返回难度为key、题目数量为value的数组
		$system = D("System");
        $map['category']='exam_enroll';
        $map['name']=$name;
		$temp = explode(',', $system -> where($map) -> getField("content"));
        $a=array();
        foreach($temp as $key=>$value){
            $b=explode(':',$value);
            $a[$b[0]]=$b[1];
        }
		return $a;
	} 
    public function formatRule($rule,$name) {//入口：$rule为getRule返回的一条规则，$name为题型，返回：难度为key、题目数量为value的数组
		$a=array();
        $tixing='';
        switch ($name) {
            case 'select':
                $tixing='slct';
                break;
            case 'fill':
                $tixing='fill';
                break;
            case 'read':
                $tixing='rd';
                break;
            case 'write':
                $tixing='wrt';
                break;
            case 'cloze':
                $tixing='cloze';
                break;
        }
		$temp = explode(',', $rule[$tixing]);
        if(!empty($temp)){
            foreach($temp as $key=>$value){
                $b=explode(':',$value);
                $a[$b[0]]=$b[1];
            }
        }
		return $a;
	} 
    public function getRule($ruleid) {//返回难度为key、题目数量为value的数组
		$dao = D("Examrule");
        $map['category']='enrolltest';
        $map['id']=session('ruleid');
		$temp = $dao -> where($map) -> find();
		return $temp;
	} 
    public function getQuestionByhard($a,$question_type,$question_flag){//$a为难度为key、题目数量为value的数组
        $dao=D('Exam'.$question_type);
        $b=array();
        foreach($a as $key=>$value){
            $map['level']=$key;
            if($value>0){
                $result=$dao->where($map)->order('rand()')->limit($value)->select();
                foreach($result as $value2){
                    $b[]='q'.$question_flag.'_'.$value2['id'];
                }
            }
        }
        return $b;
    }
   
    public function getPaperRnd(){
        $question_type=array(1=>'select',2=>'fill',3=>'read',4=>'write',5=>'cloze');
        $q=array();
        $rule=$this->getRule();
        foreach($question_type as $key=>$value){
            $hard=$this->formatRule($rule,$value);
            $question=$this->getQuestionByhard($hard,$value,$key);
            foreach($question as $value2){
                $q[]=$value2;
            }
        }
//         sort($q);
        $result=implode(',',$q); //变为由','连接的字符串
        return $result;
    }
    public function getPaperExist($paper){
        $q_id=explode(',',$paper);
        $q1=array();
        $q2=array();
        $q3=array();
        $q4=array();
        $q5=array();
        $q=array(1=>$q1,2=>$q2,3=>$q3,4=>$q4,5=>$q5);
        foreach($q_id as $key=>$value){
            $temp=substr($value,1,1);
            $temp2=substr($value,3,6);
            $q[$temp][]=$temp2;
        }
        return $q;
    }
    public function getSelect($question_id){
        $Examselect=D('Examselect');
        $map['id']=array('in',$question_id);
        $select=array();
        $select = $Examselect->where($map)->select();
        foreach($select as $key=>$value){
            $select[$key]['myitem']=$this->getItem($value['item'],'q1_'.$value['id']);
        }
        return $select;
    }
    public function getFill($question_id){
        $Examfill=D('Examfill');
        $map['id']=array('in',$question_id);
        $fill=array();
        $fill = $Examfill->where($map)->select();
        return $fill;
    }
    public function getRead($question_id){
        $Examread=D('Examread');
        $Examreaditem=D('Examreaditem');
        $map['id']=array('in',$question_id);
        $read=array();
        $read = $Examread->where($map)->select();
        foreach($read as $key=>$value){
            $map_a['articleid']=$value['id'];
            $my_a=$Examreaditem->where($map_a)->order('id asc')->select();
            $temp='';
            foreach($my_a as $key2=>$value2){
                $temp.=($key2+1).'. '.$value2['title'].'<br />';
                $temp.=$this->getItem($value2['item'],'q3_'.$value2['id'],'<br />');
            }
            $read[$key]['myitem']=$temp;
        }
        return $read;
    }
    public function getWrite($question_id){
        $Examwrite=D('Examwrite');
        $map['id']=array('in',$question_id);
        $write=array();
        $write = $Examwrite->where($map)->select();
        return $write;
    }
    public function getItem($item,$name,$separator='') {
		$a = array();
        $kkk = explode('<br />', nl2br($item));
		$j = 'A';
		$a = array();
        $parseStr   = '';
		foreach($kkk as $key => $value) {
            $parseStr .= '<label><input type="radio" name="'.$name.'" value="'.$j.'">'.'[' . $j . '] ' . $value.'</label>&nbsp;&nbsp;'.$separator;
			$j++;
		} 
        return $parseStr;
	} 
    public function getCloze($question_id){
        $Examcloze=D('Examcloze');
        $Examclozeitem=D('Examclozeitem');
        $map['id']=array('in',$question_id);
        $cloze=array();
        $cloze = $Examcloze->where($map)->select();
        foreach($cloze as $key=>$value){
            $map_a['articleid']=$value['id'];
            $my_a=$Examclozeitem->where($map_a)->order('id asc')->select();
            $temp='';
            foreach($my_a as $key2=>$value2){
                $temp.=$value2['title'].'. <br />';
                $temp.=$this->getItem($value2['item'],'q5_'.$value2['id'],'<br />');
            }
            $cloze[$key]['myitem']=$temp;
        }
        return $cloze;
    }
    public function checkLogin(){
		if (empty($_POST['name']) || empty($_POST['mobile'])|| empty($_POST['code'])) {
			$this -> error('必填字段不能留空');
		} 
		$name = $_POST['name'];
		$mobile = $_POST['mobile'];
		$code = $_POST['code'];
		$Exam = D('Exam');
        $map['name']=$name;
        $map['mobile']=$mobile;
        $map['code']=$code;
		$count = 0;
		$count = $Exam -> where($map) -> count();
		if ($count == 1) {
            session('name', $name);
            session('mobile', $mobile);
            session('code', $code);
            $my=$Exam -> where($map) -> find();
            session('ruleid', $my['ruleid']);
			$this -> success('');
		} else {
			$this -> error('无法通过验证，请检查输入是否有误');
		} 
    }
    
} 

?>