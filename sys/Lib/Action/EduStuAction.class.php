<?php
class EduStuAction extends CommonAction {
    
    public function getStuName() {
		return session('username');
	} 
	public function index() {
        $User = D('User');
        $map['username'] = session('username');
        $photo = $User->where($map)->getField('photo');
        $this->assign('photo',$photo);
        $Notice = D("Notice");
        $map="readusername='".session('username')."' and readtime is NULL";
		$count = $Notice -> where($map) -> count();
        $this->assign('count',$count);
        $map2['username']=session('username');
        $mysession= D("session")->where($map2)->find();
        $video_url='http://hndsys.nju.edu.cn/video/html/index.php/Index/index/';
        $video_url.='username/'.$mysession['username'].'/session_id/'.$mysession['session_id'];
        $this->assign('video_url',$video_url);
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
        //获得近两周更新的内容个数
        $predate = date("Y-m-d",strtotime("-2 week"));
        //最近两周接受的新邮件
        $mail = D("Mail");
        $rec_user = session('username');
        $count_mail = $mail -> where("a1 = '" .$rec_user . "' and ctime >='" . $predate . "' and isdela=0 and isdelb=0 and isread=0") -> count();
        $this -> assign("count_mail", $count_mail);
        //大学排名'
        $ranking = D("Ranking");
        $count_1 = $ranking -> where("ctime >='". $predate . "'") -> count();
        if($count_1>0) {
            $message .= "<a href='__URL__/ranking'>有". $count_1 . "篇大学排名更新</a><br/>";
        }
        //音乐专辑
        $musicalbum = D("Musicalbum");
        $count_2 = $musicalbum -> where("ctime >='". $predate . "'") -> count();
        if($count_2>0) {
            $message .= "<a href='__URL__/music'>有" . $count_2 . "个音乐专辑更新</a><br/>";
        }
        //电子书
        $ebook = D("Ebook");
        $count_3 = $ebook -> where("ctime >='". $predate . "'") -> count();
        if($count_3>0) {
            $message .= "<a href='__URL__/ebook'>有" . $count_3 . "本电子书更新</a><br/>";
        }
        //软件
        $software = D("Software");
        $count_4 = $software -> where("ctime >='". $predate . "'") -> count();
        if($count_4>0) {
            $message .= "<a href='__URL__/software'>有" . $count_4 . "个软件更新</a><br/>";
        }
        //课件
        $courseware = D("Courseware");
        $count_5 = $courseware -> where("ctime >='". $predate . "'") -> count();
        if($count_5>0) {
            $message .= "<a href='__URL__/courseware'>有" . $count_5 . "个课件更新</a><br/>";
        }
        //文库
        $document = D("Document");
        $count_6 = $document -> where("ctime >='". $predate . "'") -> count();
        if($count_6>0) {
            $message .= "<a href='__URL__/document'>有" . $count_6 . "篇文库文章更新</a><br/>";
        }
        //心灵家园
        $consulting = D("Consulting");
        $count_7 = $consulting -> where("ctime >='". $predate . "'") -> count();
        if($count_7>0) {
            $message .= "<a href='__URL__/consulting'>有" . $count_7 . "篇心灵家园文章更新</a><br/>";
        }
        $this -> assign("message", $message);
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
			$my = $Notice -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('istop desc,ctime desc') -> select();
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
        $Notice = D("NoticeView");
        $map['readusername']=session('username');
        $map['id']=$id;
        $my=$Notice->where($map)->find();
        if($my){
            $dao = D("Notice");
            $data['id']=$id;
            $data['readtime']=date("Y-m-d H:i:s");
            $result=$dao->save($data); 
        }
        $this -> assign('my', $my);
        $this -> display();
        
    }
    public function video() {
		$this -> display();
	}
    public function score() {
        $Score = D("Score");
		$map['susername']=$this->getStuName();
		$map['isvisible']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
		$term_num=count($term);
        if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('my',$my);
        } 
        $this -> display();
	}
    public function gpa() {
        $Score = D("Score");
		$map['susername']=$this->getStuName();
		$map['ispublic']=1;
        $term=$Score -> where($map) ->field('term')->group('term')->order('term asc')-> select();
		$term_num=count($term);
        if($term_num>0){
            foreach($term as $key=>$value){
                $map['term']=$value['term'];
                $my[$key]=$Score -> where($map) -> select();
            }
            $this->assign('my',$my);
        } 
        $this -> display();
	}
    public function homework() {
		if(isset($_GET['searchkey'])) {
            $searchkey = $_GET['searchkey'];
			$map['title|coursename|ttruename'] =array('like','%'.$searchkey.'%');
			$this->assign('searchkey',$searchkey);
        }
		$HomeworkView = D("HomeworkView");
		$map['susername']=$this->getStuName();
		$count = $HomeworkView -> where($map) -> count();
		if ($count > 0) {
            import("@.ORG.Page");
            $listRows=10;
			$p = new Page($count, $listRows);
			$my = $HomeworkView -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);			
		} 
        $this -> display();
	}
    public function homeworkView() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('HomeworkView');
        $map['id'] = $id;
        $map['susername']=$this->getStuName();
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function homeworkSub() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('HomeworkView');
        $map['id'] = $id;
        $map['susername']=$this->getStuName();
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            $this -> display();
        } else {
            $this -> error('该记录不存在');
        } 
    } 
    public function homeworkInsert() {
        $content = $_POST['content'];
        if (empty($content)) {
            $this -> error('必填项不能为空');
        } 
        $dao = D('Homework');
        if ($dao -> create()) {
            $dao ->ctime=date("Y-m-d H:i:s");
            $checked = $dao -> save();
            if ($checked > 0) {
                $this -> success('已成功保存');
            } else {
                $this -> error('没有更新任何数据');
            } 
        } else {
            $this -> error($dao->getError());
        } 
    }   
    public function tv() {
        $this -> display();
	}
    public function attend() {
        $Attend = D("Attend");
		$map['susername']=$this->getStuName();
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
        $this -> display();
	}
    public function reward() {
        $Reward = D("Reward");
		$map['susername']=$this->getStuName();
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
        $this -> display();
	}
    public function translator(){
        $this -> display();
    }
    public function translatorGet(){
        $client_id='t2zOnWGW8RR7qlwPG0wielpR';
        $q=$_POST['q'];
        $post_url = "http://openapi.baidu.com/public/2.0/bmt/translate"; //登录地址
        $post = "client_id=$client_id&q=$q&from=auto&to=auto"; //提交字符串
        $login = curl_init($post_url); //创建CURL对象
        curl_setopt($login, CURLOPT_HEADER, 0); //返回头部
        curl_setopt($login, CURLOPT_RETURNTRANSFER, 1); //返回信息
        curl_setopt($login, CURLOPT_POST, 1); //设置POST提交
        curl_setopt($login, CURLOPT_POSTFIELDS, $post); //提交POST数据
        $data = curl_exec($login); //执行已经定义的设置
        curl_close($login); //关闭
        $data=json_decode($data);
        $a=$data->trans_result;
        if(isset($a)){
            $this->ajaxReturn($a,'success',1);
        }else{
            $this->error('网络故障，请重试一次');
        }
        
    }
    public function rate() {
		$a=array(
            'CNY'=>'CNY 人民币元',
            'USD'=>'USD 美元',
            'AUD'=>'AUD 澳元',
            'BRL'=>'BRL 巴西雷亚尔',
            'CAD'=>'CAD 加元',
            'CHF'=>'CHF 瑞郎',
            'CLP'=>'CLP 智利比索',
            'CZK'=>'CZK 捷克克朗',
            'DKK'=>'DKK 丹麦克朗',
            'EUR'=>'EUR 欧元',
            'GBP'=>'GBP 英镑',
            'HKD'=>'HKD 港币',
            'HUF'=>'HUF 匈牙利福林',
            'IDR'=>'IDR 印度尼西亚盾',
            'INR'=>'INR 印度卢比',
            'JPY'=>'JPY 日元',
            'KRW'=>'KRW 韩元',
            'MXN'=>'MXN 墨西哥比索',
            'MYR'=>'MYR 马来西亚元',
            'NOK'=>'NOK 挪威克朗',
            'NZD'=>'NZD 新西兰元',
            'PHP'=>'PHP 菲律宾比索',
            'PLN'=>'PLN 波兰兹罗提',
            'RUB'=>'RUB 俄罗斯卢布',
            'SAR'=>'SAR 沙特里亚尔',
            'SEK'=>'SEK 瑞典克朗',
            'SGD'=>'SGD 新加坡元',
            'THB'=>'THB 泰铢',
            'TWD'=>'TWD 台币',
            'VND'=>'VND 越南盾',
            'ZAR'=>'ZAR 南非兰特'
        );
        $this->assign('currency',$a);
        $this->assign('selected_1','USD');
        $this->assign('selected_2','CNY');
        $this->display();
	}
    public function rateGet() {
		$num=$_POST['num'];
		$from=$_POST['from'];
		$to=$_POST['to'];
        if(empty($num)||empty($from)||empty($to)){
            $this->error("必填项不能为空");
        }
        if($from==$to){
            $this->error("请选择不同的货币单位");
        }
        $path ="http://www.google.com/ig/calculator?hl=zh-CN&q=$num$from=?$to";
        $data = file_get_contents($path);
        if(empty($data)){
            $this->error('网络传递较慢，请重试一次');
        }else{
            $data =iconv("gb2312", "UTF-8", $data);
            $data =str_replace('&#160;', ' ', $data);
            $data = str_replace('rhs:', '"rhs":', $data);
            $data = str_replace('lhs:', '"lhs":', $data);
            $data = str_replace('error:', '"error":', $data);
            $data = str_replace('icc:', '"icc":', $data);
            $my=json_decode($data,true);
            $this->success($my['lhs'].' 可兑换 '.$my['rhs']);
        }
        
	}
    public function getSystem($name) {
		$system = D("System");
		$temp = explode(',', $system -> where("category='source' and name='" . $name . "'") -> getField("content"));
		$a = array();
		foreach($temp as $key => $value) {
			$a[$value] = $value;
		} 
		return $a;
	} 
    public function ranking() {
        $this->assign('category_fortag',$this -> getSystem("ranking"));
		if (isset($_GET['searchkey'])) {
			$map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        if (isset($_GET['category'])) {
			$map['category'] = $_GET['category'];
			$this -> assign('category_current', $_GET['category']);
		}
        $dao = D('ranking');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 12;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		}
        //热门文章的排列按照发布时间和点击率的加权,显示15篇热门文章
        $paper_time = $dao -> field('title, id') -> limit(15) -> order('ctime desc') -> select();
        $paper_hit = $dao -> field('title, id') -> limit(15) -> order('hit desc') -> select();
        $a = array();
        $b = array();
        for($i=0;$i<15;$i++) {
            $a[$paper_time[$i]["id"]] += ($i+1)*0.48;//发布时间的权重值为48%
            $b[$paper_time[$i]["id"]] = $paper_time[$i]["title"];
            $a[$paper_hit[$i]["id"]] += ($i+1)*0.52;//点击率的权重值为52%
            $b[$paper_hit[$i]["id"]] = $paper_hit[$i]["title"];
        }
        $paper = array();
        if(asort($a,SORT_NUMERIC)){
            $a = array_chunk($a,15,true);
            $i=0;
            foreach($a[0] as $key=>$value) {
                $paper[$i] = array("id"=>$key, "title"=>$b[$key]);
                $i++;
            }
        } else{
            $paper = $dao  -> field('title, id') -> limit(15) -> order('hit desc') -> select();
        }
        $this -> assign('paper', $paper);        
        $this -> display(); 
	}
    public function consulting() {
        $this -> assign('category_fortag', $this -> getSystem("consulting"));
        if (isset($_GET['searchkey'])) {
            $map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['category'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('consulting');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> display();
    } 
    
    public function autoMenuMusic($menu, $cate, $id) {
        $path = array();
		foreach($menu as $key => $value) {
			$is_on = ($key == $cate)?' class="on" ':'';
            $action = ACTION_NAME;
            if(!empty($id)) {
                $path[] = '<a href="__URL__/'. $action .'/id/'. $id .'/category/' . $key . '" ' . $is_on . '>' . $value . '</a>';
            } else{
                $path[] = '<a href="__URL__/'. $action .'/category/' . $key . '" ' . $is_on . '>' . $value . '</a>';
            }
			
		} 
		return implode(' | ', $path);
	}
    
    public function music() {
        $cate = $_GET['category'];
        if ($cate == null) {
            $cate = "华语";
        } 
        $menu = array();
        $menu = $this -> getSystem("music");
        $this->assign('menu',$this ->autoMenuMusic($menu, $cate)); 
        $my = D("Musicalbum");
        $dao = D('Singer');
        $map['category'] = $cate;
        $my =$my -> where($map) -> field('name,id,singerid,singer,pic') -> limit(5) -> order('ctime desc, id desc') -> select();
        $singer = $dao -> field('name,id,pic') -> limit(5) -> order('hit desc') -> select();
        $this -> assign('category_current',$cate);
        $this -> assign('singer', $singer);
        $this -> assign('my', $my);
        $this -> display(); 
	}
   
//此函数当点击更多后调用，显示全部专辑    
    public function moreMusic() {
         $cate = $_GET['category'];
        if ($cate == null) {
            $cate = "华语";
        } 
        $menu = array();
        $menu = $this -> getSystem("music");
        $this->assign('menu',$this ->autoMenuMusic($menu, $cate)); 
        $map['category'] = $cate;
        $Musicalbum = D('Musicalbum');
		$count = $Musicalbum -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $Musicalbum -> where($map) -> field('name,id,singerid,singer,pic') -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this -> display(); 
    }
    
    public function AllSinger() {
        $dao = D("Singer");
        $count = $dao -> count();
        if($count > 0) {
            import("@.ORG.Page");
			$listRows = 24;
			$p = new Page($count, $listRows);
            $singer = $dao -> field('name,id,pic') -> limit($p -> firstRow . ',' . $p -> listRows) -> order('hit desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('singer',$singer);
        }
        $this -> display();
    }
    
    public function singer() {
        $cate = $_GET['category'];
        if ($cate == null) {
            $cate = "music";
        } 
        if (isset($_GET['id'])) {
            $map['singerid'] = $_GET['id'];
            $map2['id'] = $_GET['id'];
        }
        $dao4 = D("Singer");
        $singer = $dao4 -> where($map2) -> field('id,name,intro,pic') -> find();
        $dao4->where($map2)->setInc('hit'); 
        $this -> assign("singer",$singer);
        if($cate == "music") {
            $dao = D("MusicalbumView");
            $count = $dao -> where($map) -> count();
            if($count > 0) {
                import("@.ORG.Page");
                $listRows = 20;
                $p = new Page($count, $listRows);
                $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('time desc,musicid desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign('my', $my);
            }
             $this -> display(); 
        } else if($cate == "album") {
            $dao2 = D("Musicalbum");
            $count2 = $dao2 -> where($map) -> count();
            if($count2 > 0) {
                import("@.ORG.Page");
                $listRows = 10;
                $p = new Page($count, $listRows);
                $album = $dao2 -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> field('id,name,category,pic,intro') -> order('ctime desc, id desc') -> select();
                $page = $p -> show();
                $this -> assign("page", $page);
                $this -> assign("album", $album);
            }
              $this -> display("album");
        } else if($cate == "pic") {
            $dao3 = D("Singerpic");
            $count3 = $dao3 -> where($map) -> count();
            if($count3 > 0) {
               import("@.ORG.Page");
               $listRows = 15;
               $p = new Page($count, $listRows); 
               $my = $dao3 -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc, id desc') -> select();
               $page = $p -> show();
               $this -> assign("page", $page);
               $this -> assign("my",$my);
            }
            $this -> display("picture");
        }
       
    }
    
    public function ebook() {
        $this -> assign('category_fortag', $this -> getSystem("ebook"));
        if (isset($_GET['searchkey'])) {
            $map['filename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['category'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('ebook');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> display();
    } 
    
    public function software() {
        $this -> assign('category_fortag', $this -> getSystem("software"));
        if (isset($_GET['searchkey'])) {
            $map['filename'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['category'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('software');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> display();
    } 
    public function courseware() {
        $this->assign('category_fortag',$this -> getSystem("courseware"));
		if (isset($_GET['searchkey'])) {
			$map['filename'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
        if (isset($_GET['category'])) {
			$map['category'] = $_GET['category'];
			$this -> assign('category_current', $_GET['category']);
		}
        $dao = D('courseware');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
        $this -> display(); 
	}
    public function document() {
        $this -> assign('category_fortag', $this -> getSystem("document"));
        if (isset($_GET['searchkey'])) {
            $map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        if (isset($_GET['category'])) {
            $map['category'] = $_GET['category'];
            $this -> assign('category_current', $_GET['category']);
        } 
        $dao = D('document');
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> display();
    } 
    public function musicMore() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Musicalbum');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
            $dao->where($map)->setInc('hit'); 
            $map2['albumid']=$my['id'];
            $music=D('Music')->where($map2)->select();
            $singer = $my['singer'];
            $other_album = $dao -> field('id, name, pic') -> where("singer='".$singer."' and id<>".$id) -> order('ctime desc') -> select();
            $this -> assign('my', $my);
            $this -> assign('music', $music);
            $this -> assign('album',$other_album);
            $this -> display();
		} else{
            $this -> error('该专辑不存在');
        } 
	}
    public function rankingMore() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('ranking');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
            $dao->where($map)->setInc('hit'); 
            $this -> assign('my', $my);
            $this -> display();
		} else{
            $this -> error('该记录不存在');
        } 
	}
    public function documentMore() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('document');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
            $dao->where($map)->setInc('hit'); 
            $this -> assign('my', $my);
            $this -> display();
		} else{
            $this -> error('该记录不存在');
        } 
	}
    public function consultingMore() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('consulting');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
            $dao->where($map)->setInc('hit'); 
            $this -> assign('my', $my);
            $this -> display();
		} else{
            $this -> error('该记录不存在');
        } 
	}
    public function download(){
        $id = $_GET['id'];
        $type = $_GET['type'];
		if (!isset($id)||!isset($type)) {
			$this -> error('参数缺失');
		} 
        $allow_type=array('music','courseware','software','ebook');
        if(in_array($type,$allow_type)==false){
            $this -> error('非法下载链接');
        }
		$dao = D($type);
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
        if($my){
            $filename=$my['filename'];//用户端显示的文件名 
            $php_path = dirname(__FILE__) . '/../../..'; //转化为物理路径
            $fileurl=$php_path.$my['fileurl'];
            $encoded_filename = urlencode($filename);
            $encoded_filename = str_replace("+", "%20", $encoded_filename);
            $downfile=$fileurl;//物理路径 
            $size=filesize($downfile);
            $file = @ fopen($downfile,"r");
            if (!$file) {
            echo "file not found";
            } else {
            $HTTP_USER_AGENT=$_SERVER["HTTP_USER_AGENT"];
            $now = gmdate('D, d M Y H:i:s') . ' GMT';
            $mime_type='application/lrcfile'; 
            header('Content-Type: ' . $mime_type);
            header('Expires: ' . $now);
            Header("Accept-Ranges: bytes"); 
            header('Content-Transfer-Encoding: binary');
            Header("Accept-Length: ".$size);
            header('Content-Length: '.$size);
            if (strstr($HTTP_USER_AGENT, 'compatible; MSIE ') !== false && strstr($HTTP_USER_AGENT, 'Opera') === false) {
               header("Content-Disposition: inline; filename=$encoded_filename");
               header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
               header('Pragma: public');
            } else {
               header("Content-Disposition: attachment; filename=\"$filename\"");
               header("Content-Type: $mime_type; name=\"$filename\"");
            }
            while (!feof ($file)) {
               echo fread($file,1000);
            }
            fclose ($file);
            $dao->where($map)->setInc('hit'); 
            }
        }
    }
    public function process(){
        $dao=D('Abroad');
        $map['susername']=$this->getStuName();
        $my=$dao->where($map)->find();
        $this->assign('my',$my);
        $school=D('Abroadschool')->where("abroadid=".$my['id'])->select();
        $this -> assign('school', $school);
        $this->display();

    }
    public function menumail() {
        $menu['mail']='收件箱';
        $menu['mailsend']='已发信件';
        $menu['mailAdd']='新建信件';
        $this->assign('menu',$this ->autoMenu($menu));  
	}
    
    public function mail() {
        if (isset($_GET['searchkey'])) {
            $map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('mail');
        $map['a1'] = session('username');
        $map['isdela'] = 0;
        $count = $dao -> where($map) -> count();
        $dao -> getLastSql();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menumail();
        $this -> display();
    } 
    public function mailsend() {
        if (isset($_GET['searchkey'])) {
            $map['title'] = array('like', '%' . $_GET['searchkey'] . '%');
            $this -> assign('searchkey', $_GET['searchkey']);
        } 
        $dao = D('mail');
        $map['b1'] = session('username');
        $map['isdelb'] = 0;
        $count = $dao -> where($map) -> count();
        if ($count > 0) {
            import("@.ORG.Page");
            $listRows = 20;
            $p = new Page($count, $listRows);
            $my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
            $page = $p -> show();
            $this -> assign("page", $page);
            $this -> assign('my', $my);
        } 
        $this -> menumail();
        $this -> display();
    } 
    public function mailAdd() {   
        $id = $_GET['id'];
        if (isset($id)) {
            $dao=D('Mail');
            $map['a1']=session('username');
            $map['isdela']=0;
            $map['id']=$id;
            $my=$dao->where($map)->find();
            if($my){
                $this->assign('rc',$my['b1'].'['.$my['b2'].']');
                $this->assign('title','Re:'.$my['title']);
                $this->assign('content','<br></br><br></br><br></br><br></br><br>'.$my['ctime'].', '.$my['b2'].'写道：</br>'.$my['content']); 
            }
        } 
        $dao1=D('StudentDirView');
        $map1['student']=$this->getStuName();
        $my1=$dao1->where($map1)->select();
        if($my1){
            $a=array();
            foreach($my1 as $key=>$value){
                $temp1=$value['teacher'].'['.$value['truename'].']';
                $a[$temp1]=$temp1;
            }
            $this->assign('a',$a);
        }
        $this -> menumail();
        $this -> display();
    } 
    public function mailDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $map['a1'] = session('username');
        $dao = D('mail');
        $data = array('isdela'=>1);
        $result = $dao -> where($map)->setField($data); 
        if ($result > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function mailsendDel() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $map['id'] = array('in', $id);
        $map['b1'] = session('username');
        $dao = D('mail');
        $data = array('isdelb'=>1);
        $result = $dao -> where($map)->setField($data); 
        if ($result > 0) {
            $this -> success('已成功删除');
        } else {
            $this -> error('影响的记录行数为0');
        } 
    } 
    public function mailView() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('mail');
        $map['id'] = $id;
        $map['a1'] = session('username');
        $map['isdela'] = 0;
        $data['isread'] = 1;
        $dao -> where($map) -> save($data);
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            
            $this -> menumail();
            $this -> display();
        } else {
            $this -> error('该记录不存在或权限不足');
        } 
    } 
    public function mailsendView() {
        $id = $_GET['id'];
        if (!isset($id)) {
            $this -> error('参数缺失');
        } 
        $dao = D('mail');
        $map['id'] = $id;
        $map['b1'] = session('username');
        $map['isdelb'] = 0;
        $my = $dao -> where($map) -> find();
        if ($my) {
            $this -> assign('my', $my);
            
            $this -> menumail();
            $this -> display();
        } else {
            $this -> error('该记录不存在或权限不足');
        } 
    } 
    public function mailInsert() {
        $rc = $_POST['rc'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        if (empty($rc) || empty($content)) {
            $this -> error('必填项不能为空');
        } 
        if (empty($title)) {
            $title='来自'.session('username').'['.session('truename').']'.'的邮件';
        } 
        $rcs=explode(',',$rc);
        $User=D('User');
        foreach($rcs as $key => $value) {
            $temp = explode('[', $value);
            $a2=substr($temp[1],0,-1);
            $last_letter=substr($temp[1],-1);
            if(empty($a2)||$last_letter!==']'){
                $this->error('收件人格式错误，请检查'.$temp[0].'附近');
            }
            $data_a[$key]['a1'] = $temp[0];
            $data_a[$key]['a2'] = $a2;
            $data_a[$key]['b1'] = session('username');
            $data_a[$key]['b2'] = session('truename');
            $data_a[$key]['title'] = $title;
            $data_a[$key]['content'] = $content;
            $data_a[$key]['ctime'] = date("Y-m-d H:i:s");
        } 
        $Mail=D('Mail');
        $Mail -> addAll($data_a);
        $this -> success('已成功发送');
    } 
    public function test() {
        
        $newQuestion=$this->getPaperRnd();
        $q=$this->getPaperExist($newQuestion);
        $select=$this->getSelect($q[1]);
        $fill=$this->getFill($q[2]);
        $read=$this->getRead($q[3]);
        $write=$this->getWrite($q[4]);
        $this->assign('select',$select);
        $this->assign('fill',$fill);
        $this->assign('read',$read);
        $this->assign('write',$write);
        $this->display();
	} 
    public function getPaperRnd(){
        $question_type=array(1=>'select',2=>'fill',3=>'read',4=>'write');
        $q=array();
        $rule=$this->getRule();
        foreach($question_type as $key=>$value){
            $hard=$this->formatRule($rule,$value);
            $question=$this->getQuestionByhard($hard,$value,$key);
            foreach($question as $value2){
                $q[]=$value2;
            }
        }
        $result=implode(',',$q); //变为由','连接的字符串
        return $result;
    }
    public function getRule($ruleid=100005) {//返回难度为key、题目数量为value的数组
		$dao = D("Examrule");
        $map['category']='selftest';
        $map['id']=$ruleid;
		$temp = $dao -> where($map) -> find();
		return $temp;
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
    public function getPaperExist($paper){
        $q_id=explode(',',$paper);
        $q1=array();
        $q2=array();
        $q3=array();
        $q4=array();
        $q=array(1=>$q1,2=>$q2,3=>$q3,4=>$q4);
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
            $select[$key]['myitem']=$this->getItem($value['item'],'q1_'.$value['id'],"<br />");
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
    
} 

?>