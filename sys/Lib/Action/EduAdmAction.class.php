<?php
class EduAdmAction extends CommonAction {
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
    public function sys(){
        $majors = M("major")->select();
        $this->assign("majors",$majors);
        $this->menuMajor();
        $this->display();
    }
    public function majorAdd(){
        $this -> assign('category_fortag', $this->getYear());
        $system = M("system")->where('name="items"')->getField("content");
        $items = explode(",", $system);
        foreach ($items as $va) {
            $item[$va] = $va;
        }
        $this->assign("items",$item);
        $this->menuMajor();
        $this->display();
    }
    public function majorInsert(){
        $data["major"] = $_POST["major"];
        $data["item"] = $_POST["item"];
        $data["year"] = $_POST["year"];
        if ($data["major"] == '') {
            $this->ajaxReturn($_POST,"专业名未填写",0);
        }
        if ($data["item"] == '') {
            $this->ajaxReturn($_POST,"项目名称未填写",0);
        }
        if ($data["year"] == '') {
            $this->ajaxReturn($_POST,"年份未填写",0);
        }
        if (M("major")->add($data)) {
            $this->ajaxReturn($_POST,"成功",1);
        }else{
            $this->ajaxReturn($_POST,"新增失败",0);
        }
    }
    public function delmajor(){
        $id = $_POST["id"];
        if (!$id) {
            $this->ajaxReturn($id,"未选择专业",0);
        }
        if (M("major")->where(array("id" => $id))->delete()) {
            $this->ajaxReturn($id,"删除成功",1);
        }else{
            $this->ajaxReturn($id,"删除失败",0);
        }
    }
    public function majorEdit(){
        $id = $_GET["id"];
        if (!$id) {
            $this->redirect('EduAdm/sys');
        }
        $this -> assign('category_fortag', $this->getYear());
        $my = M("major")->where(array("id" => $id))->find();
        $this->assign("my",$my);
        $system = M("system")->where('name="items"')->getField("content");
        $items = explode(",", $system);
        foreach ($items as $va) {
            $item[$va] = $va;
        }
        $this->assign("items",$item);
        $this->display();
    }
    public function majorUpdate(){
        $map["id"] = $_POST["id"];
        if (!$map["id"]) {
            $this->ajaxReturn($_POST,"未选择专业",0);
        }
        $data["major"] = $_POST["major"];
        $data["item"] = $_POST["item"];
        $data["year"] = $_POST["year"];
        if ($data["major"] == '') {
            $this->ajaxReturn($_POST,"专业名未填写",0);
        }
        if ($data["item"] == '') {
            $this->ajaxReturn($_POST,"项目名称未填写",0);
        }
        if ($data["year"] == '') {
            $this->ajaxReturn($_POST,"年份未填写",0);
        }
        if (M("major")->where($map)->save($data)) {
            $this->ajaxReturn($_POST,"成功",1);
        }else{
            $this->ajaxReturn($_POST,"无更新",0);
        }
    }
    public function menuMajor() {
        $menu['sys']='专业列表';
        $menu['majorAdd']='新建专业';
        $this->assign('menu',$this ->autoMenu($menu));  
    }
    public function getYear() {
        $a=array();
        $current_year=Date('Y');
        for($i=$current_year;$i>=2005;$i--){
            $a[$i]=$i;
        }
        return $a; 
    }
}