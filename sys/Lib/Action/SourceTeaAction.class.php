<?php
class SourceTeaAction extends CommonAction {
	public function index() {
		$User = D('User');
		$map['username'] = session('username');
		$photo = $User -> where($map) -> getField('photo');
		$this -> assign('photo', $photo);
		$roles = explode(',', session('role'));
		if (count($roles) > 1) {
			$all_role = R('Index/getRole');
			$my_role = array();
			foreach($roles as $key => $value) {
				$my_role[$value] = $all_role[$value];
			} 
			$this -> assign('my_role', $my_role);
			$this -> assign('select_role', $this -> getActionName());
		} 
		$this -> display();
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
	public function documentCategory() {
		$a = array();
		$a['ranking'] = '大学排名';
		$a['document'] = '文库';
		$a['consulting'] = '心理辅导';
		return $a;
	} 
	public function menuMusic() {
		$menu['musicAlbum'] = '所有专辑';
		$menu['musicAlbumAdd'] = '新建专辑';
		$menu['Singer'] = '所有歌手';
		$menu['SingerAdd'] = '新建歌手';
		$this -> assign('menu', $this -> autoMenu($menu));
	} 
	public function menuCourseware() {
		$menu['courseware'] = '所有课件';
		$menu['coursewareAdd'] = '新建课件';
		$menu['software'] = '所有软件';
		$menu['softwareAdd'] = '新建软件';
		$menu['ebook'] = '所有电子书';
		$menu['ebookAdd'] = '新建电子书';
		$this -> assign('menu', $this -> autoMenu($menu));
	} 
	public function menuRanking() {
		$menu['ranking'] = '大学排名';
		$menu['rankingAdd'] = '新建大学排名';
		$menu['document'] = '文库';
		$menu['documentAdd'] = '新建文库';
		$menu['consulting'] = '心理辅导';
		$menu['consultingAdd'] = '新建心理辅导';
		$this -> assign('menu', $this -> autoMenu($menu));
	} 
	public function musicAdd() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Musicalbum');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$map2['albumid'] = $my['id'];
			$music = D('Music') -> where($map2) -> select();
			$this -> assign('my', $my);
			$this -> assign('music', $music);
			$this -> menuMusic();
			$this -> display();
		} else {
			$this -> error('该专辑不存在');
		} 
	} 
	public function coursewareAdd() {
		$this -> assign('category_fortag', $this -> getSystem("courseware"));
		$this -> menuCourseware();
		$this -> display();
	} 
	public function rankingAdd() {
		$this -> assign('category_fortag', $this -> getSystem("ranking"));
		$this -> assign('mydate', date('Y-m-d H:i:s'));
		$this -> menuRanking();
		$this -> display();
	} 

	public function musicInsert() {
		$uploader_count = $_POST['uploader_count'];
		if ($uploader_count > 0) {
			$dao = D('Music');
			$data['albumid'] = $_POST['id'];
			for($i = 0;$i < $uploader_count;$i++) {
				$data['filename'] = $_POST['uploader_' . $i . '_name'];
				$data['fileurl'] = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_' . $i . '_tmpname'];
				$data['filesize'] = $_POST['uploader_' . $i . '_size'];
				$data['ctime'] = date("Y-m-d H:i:s");
				$dao -> add($data);
			} 
			$this -> success('已成功保存');
		} else {
			$this -> error('未上传任何文件');
		} 
	} 
	public function musicDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$map['id'] = $id;
		$dao = D('Music');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$oldurl = $dao -> where($map) -> getField('fileurl');
			$php_path = dirname(__FILE__) . '/../../..'; //转化为物理路径
			$oldurl = $php_path . $oldurl;
			$isDelete = true;
			if (file_exists($oldurl)) {
				$isDelete = @unlink($oldurl);
			} 
			if ($isDelete == false) {
				$this -> error('因该文件正在被下载，暂无法删除。请稍后再试。');
			} else {
				$result = $dao -> where($map) -> delete();
				$this -> success('已成功删除');
			} 
		} else {
			$this -> error('该文件不存在');
		} 
	} 
	public function coursewareDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 

		$map['id'] = array('in', $id);
		$dao = D('courseware');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$ids = explode(',', $id);
			$i = 0;
			foreach($ids as $value) {
				$map2['id'] = $value;
				$old = $dao -> where($map2) -> find();
				$oldurl = dirname(__FILE__) . '/../../..'; //转化为物理路径
				$oldurl .= $old['fileurl'];
				$isDelete = true;
				if (file_exists($oldurl)) {
					$isDelete = @unlink($oldurl);
				} 
				if ($isDelete) {
					$result = $dao -> where($map2) -> delete();
					$i++;
				} 
			} 
			if ($i == $count) {
				$this -> success('已成功删除');
			} else {
				$this -> error('已删除部分文件。某些文件正在被下载，暂无法删除。请稍后再试。');
			} 
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function musicAlbum() {
		$this -> assign('category_fortag', $this -> getSystem("music"));
		if (isset($_GET['searchkey'])) {
			$map['name'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
		if (isset($_GET['category'])) {
			$map['category'] = $_GET['category'];
			$this -> assign('category_current', $_GET['category']);
		} 
		$Musicalbum = D('Musicalbum');
		$count = $Musicalbum -> where($map) -> join('u_singer ON u_singer.id = u_musicalbum.singerid') -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $Musicalbum -> Field('u_musicalbum.id,u_musicalbum.name,u_musicalbum.pic,u_musicalbum.intro,u_musicalbum.ctime,u_musicalbum.hit,category,u_singer.name as singer') -> where($map) -> join('u_singer ON u_singer.id = u_musicalbum.singerid') -> limit($p -> firstRow . ',' . $p -> listRows) -> order('u_musicalbum.ctime desc,u_musicalbum.id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> menuMusic();
		$this -> display();
	} 
	public function Singer() {
		$this -> assign('category_fortag', $this -> getSystem("music"));
		if (isset($_GET['searchkey'])) {
			$map['name'] = array('like', '%' . $_GET['searchkey'] . '%');
			$this -> assign('searchkey', $_GET['searchkey']);
		} 
		$Musicalbum = D('Singer');
		$count = $Musicalbum -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $Musicalbum -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> menuMusic();
		$this -> display();
	} 
	public function courseware() {
		$this -> assign('category_fortag', $this -> getSystem("courseware"));
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
		$this -> menuCourseware();
		$this -> display();
	} 
	public function ranking() {
		$this -> assign('category_fortag', $this -> getSystem("ranking"));
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
			$listRows = 20;
			$p = new Page($count, $listRows);
			$my = $dao -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc,id desc') -> select();
			$page = $p -> show();
			$this -> assign("page", $page);
			$this -> assign('my', $my);
		} 
		$this -> menuRanking();
		$this -> display();
	} 
	public function SingerAdd() {
		$this -> assign('category_fortag', $this -> getSystem("music"));
		$this -> assign('test', date("Y-m-d H:i:s"));
		$this -> menuMusic();
		$this -> display();
	} 
	public function HasSinger() {
		$singername = $_GET['singer'];
        if(!empty($singername)){
            $result = $this -> checksinger($singername);
		if ($result) {
			$this -> error('数据库中已存在该歌手信息');
		} else {
			$this -> success('数据库中暂不存在该歌手信息');
		} 
        }
		
	} 
	public function checksinger($singername) {
		$map['name'] = $singername;
		$singer = D("Singer");
		$check = $singer -> where($map) -> find();
		if ($check) {
			return true;
		} else {
			return false;
		} 
	} 
	public function SingerInsert() {
		$name = $_POST['name'];
		$intro = $_POST['intro'];
		if (empty($name)) {
			$this -> error('必填项不能为空');
		} 
		$checkSinger = $this -> checksinger($name);
		if ($checkSinger) {
			$this -> error('数据库中已存在该歌手信息');
		} 
		$dao = D('Singer');
		if ($dao -> create()) {
			$dao -> ctime = date("Y-m-d H:i:s");

			$insertID = $dao -> add();
			if ($insertID) {
				$singerpic = D('Singerpic');
				$uploader_count = $_POST['uploader_count'];
				if ($uploader_count > 0) {
					for($i = 0;$i < $uploader_count;$i++) {
						$singer['fileurl'] = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_' . $i . '_tmpname'];
						$singer['singerid'] = $insertID;
						$singer['ctime'] = date("Y-m-d H:i:s");
						$singerpic -> add($singer);
					} 
				} 
				$this -> ajaxReturn($insertID, '已成功保存！', 1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
	public function SingerEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Singer');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("music"));
			$this -> menuMusic();
			$this -> display();
		} else {
			$this -> error('该专辑不存在');
		} 
	} 
	public function SingerUpdate() {
		$name = $_POST['name'];
		$intro = $_POST['intro'];

		if (empty($name)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Singer');
		if ($dao -> create()) {
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
	public function SingerDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$map['id'] = array('in', $id);
		$map1['singerid'] = array('in', $id);
		$pic = D('Singerpic');
		$count = $pic -> where($map1) -> select();
		if ($count > 0) {
			$this -> error('请先删除所选歌手的所有图片，再进行操作');
		} 
		$result = D('Singer') -> where($map) -> delete();
		if ($result > 0) {
			$this -> success('已成功删除');
		} 
	} 
	public function Photo() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$this -> assign('singerid', $id);
		$map['singerid'] = $id;
		$pic = D('Singerpic');
		$singer = D('Singer');
		$singername = $singer -> where('id =' . $id) -> find();
		$count = $pic -> where($map) -> count();
		if ($count > 0) {
			import("@.ORG.Page");
			$listRows = 9;
			$p = new Page($count, $listRows);
			$page = $p -> show();
			$result = $pic -> where($map) -> limit($p -> firstRow . ',' . $p -> listRows) -> order('ctime desc') -> select();
			$this -> assign("page", $page);
			$this -> assign('my', $result);
			$this -> assign('singername', $singername);
		} 
		$this -> menuMusic();
		$this -> display();
	} 
	public function photopicDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 

		$map['id'] = array('in', $id);
		$dao = D('Singerpic');
		$singer = D('Singer');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$ids = explode(',', $id);
			$i = 0;
			foreach($ids as $value) {
				$map2['id'] = $value;
				$old = $dao -> where($map2) -> find();
				$pic = $singer -> where("pic ='" . $old['fileurl'] . "'") -> find();
				if ($pic) {
					$singer -> where("pic ='" . $old['fileurl'] . "'") -> setField('pic', null);
				} 
				$oldurl = dirname(__FILE__) . '/../../..'; //转化为物理路径
				$oldurl .= $old['fileurl'];
				$isDelete = true;
				if (file_exists($oldurl)) {
					$isDelete = @unlink($oldurl);
				} 
				if ($isDelete) {
					$result = $dao -> where($map2) -> delete();
					$i++;
				} 
			} 
			if ($i == $count) {
				$this -> success('已成功删除');
			} else {
				$this -> error('已删除部分文件。某些文件正在被下载，暂无法删除。请稍后再试。');
			} 
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function Cover() {
		$id = $_GET['id'];
		$url = $_GET['url'];
		if (empty($id) || empty($url)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Singer');
		$data['pic'] = $url;

		$insertID = $dao -> where('id =' . $id) -> save($data);
		if ($insertID) {
			$this -> ajaxReturn($insertID, '已成功保存！', 1);
		} else {
			$this -> error('设置失败');
		} 
	} 
	public function photopicInsert() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); 
		// Settings
		$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		$php_path = dirname(__FILE__) . '/';
		$targetDir = $php_path . '../../upload_pl/' . DIRECTORY_SEPARATOR . date("Ym");
		if (!file_exists($targetDir))
			@mkdir($targetDir);
		$targetDir = $php_path . '../../upload_pl/' . DIRECTORY_SEPARATOR . date("Ym") . DIRECTORY_SEPARATOR . date("d");

		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds  
		// 5 minutes execution time
		@set_time_limit(5 * 60); 
		// Uncomment this one to fake upload time
		// usleep(5000);
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : ''; 
		// Clean the fileName for security reasons
		// $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
			$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		} 

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName; 
		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir); 
		// Remove old temp files
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file; 
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				} 
			} 

			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}'); 
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"]; 
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
						fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
					fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		} 
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
			$singerpic = D('Singerpic');
			$singer['fileurl'] = '/sys/upload_pl/' . date ('Ym/d/') . $fileName;
			$singer['singerid'] = $id;
			$singer['ctime'] = date("Y-m-d H:i:s");
			$singerpic -> add($singer);

			$singers = D('Singer');
			$map['id'] = $id;
			$urls = $singers -> where('id = ' . $id) -> getField('pic');
			if ($urls == null || $urls == false || empty($urls)) {
				$s['pic'] = '/sys/upload_pl/' . date ('Ym/d/') . $fileName;
				$singers -> where('id = ' . $id) -> save($s);
			} 
		} 
		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	} 
	public function musicAlbumAdd() {
		$singer = D("Singer");
		$singers = $singer -> Field('id,name')->order('CONVERT( name USING gbk ) COLLATE gbk_chinese_ci ASC') -> select();
		foreach($singers as $vo) {
			$list[$vo['id']] = $vo['name'];
		} 
		$this -> assign('list', $list);
		$this -> assign('category_fortag', $this -> getSystem("music"));
		$this -> assign('test', date("Y-m-d H:i:s"));
		$this -> menuMusic();
		$this -> display();
	} 
	public function musicAlbumInsert() {
		$name = $_POST['name'];
		$category = $_POST['category'];
		$singer = $_POST['singerid'];
		if (empty($name) || empty($category) || empty($singer)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Musicalbum');
		$Singer = D('Singer');
		if ($dao -> create()) {
			$dao -> ctime = date("Y-m-d H:i:s");
			$sing = $Singer -> where(" id = " . $singer) -> getField("name");
			$dao -> singer = $sing;
			$uploader_count = $_POST['uploader_count'];
			if ($uploader_count > 0) {
				$dao -> pic = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_0_tmpname'];
			} 
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> ajaxReturn($insertID, '已成功保存！', 1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
	public function coursewareInsert() {
		$category = $_POST['category'];
		if (empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$uploader_count = $_POST['uploader_count'];
		if ($uploader_count > 0) {
			$dao = D('courseware');
			$data['category'] = $category;
			for($i = 0;$i < $uploader_count;$i++) {
				$data['filename'] = $_POST['uploader_' . $i . '_name'];
				$data['fileurl'] = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_' . $i . '_tmpname'];
				$data['filesize'] = $_POST['uploader_' . $i . '_size'];
				$data['ctime'] = date("Y-m-d H:i:s");
				$data['author'] = session('username');
				$dao -> add($data);
			} 
			$this -> success('已成功保存');
		} else {
			$this -> error('未上传任何文件');
		} 
	} 
	public function musicAlbumEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('Musicalbum');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$singer = D("Singer");
			$singers = $singer -> Field('id,name') -> select();
			foreach($singers as $vo) {
				$list[$vo['id']] = $vo['name'];
			} 
			$this -> assign('list', $list);
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("music"));
			$this -> menuMusic();
			$this -> display();
		} else {
			$this -> error('该专辑不存在');
		} 
	} 
	public function coursewareEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('courseware');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("courseware"));
			$this -> menuCourseware();
			$this -> display();
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function musicAlbumDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$map['id'] = array('in', $id);
		$result = D('Musicalbum') -> where($map) -> delete();
		if ($result > 0) {
			$this -> success('已成功删除');
		} 
	} 

	public function musicAlbumUpdate() {
		$name = $_POST['name'];
		$category = $_POST['category'];
		$singer = $_POST['singerid'];
		if (empty($name) || empty($category) || empty($singer)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('Musicalbum');
		if ($dao -> create()) {
			$uploader_count = $_POST['uploader_count'];
			if ($uploader_count > 0) {
				$map['id'] = $_POST['id'];
				$oldpic = $dao -> where($map) -> getField('pic');
				$php_path = dirname(__FILE__) . '/../../..'; //转化为物理路径
				$oldpic = $php_path . $oldpic;
				unlink($oldpic);
				$dao -> pic = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_0_tmpname'];;
			} 
			$Singer = D('Singer');
			$sing = $Singer -> where(" id = " . $singer) -> getField("name");
			$dao -> singer = $sing;
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
	public function coursewareUpdate() {
		$filename = $_POST['filename'];
		$category = $_POST['category'];
		if (empty($filename) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('courseware');
		if ($dao -> create()) {
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
	public function parameter() {
		$Sys = D("System");
		$map['category'] = 'source';
		$count = $Sys -> where($map) -> count();
		if ($count > 0) {
			$my = $Sys -> where($map) -> order('id desc') -> select();
			$this -> assign('my', $my);
		} 
		$this -> display();
	} 
	public function parameterSave() {
		$id = $_POST['id'];
		$content = $_POST['content'];
		if (empty($id) || empty($content)) {
			$this -> error('必填项不能为空');
		} 
		$Sys = D("System");
		$data['id'] = $id;
		$data['content'] = $content;
		$result = $Sys -> save($data);
		if ($result > 0) {
			$this -> success('已成功保存');
		} 
	} 
	public function download() {
		$id = $_GET['id'];
		$type = $_GET['type'];
		if (!isset($id) || !isset($type)) {
			$this -> error('参数缺失');
		} 
		$allow_type = array('music', 'courseware', 'software', 'ebook');
		if (in_array($type, $allow_type) == false) {
			$this -> error('非法下载链接');
		} 
		$dao = D($type);
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();

		$filename = $my['filename']; //用户端显示的文件名 
		$php_path = dirname(__FILE__) . '/../../..'; //转化为物理路径
		$fileurl = $php_path . $my['fileurl'];
		$encoded_filename = urlencode($filename);
		$encoded_filename = str_replace("+", "%20", $encoded_filename);
		$downfile = $fileurl; //物理路径 
		$size = filesize($downfile);
		$file = @ fopen($downfile, "r");
		if (!$file) {
			echo "file not found";
		} else {
			$HTTP_USER_AGENT = $_SERVER["HTTP_USER_AGENT"];
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			$mime_type = 'application/lrcfile';
			header('Content-Type: ' . $mime_type);
			header('Expires: ' . $now);
			Header("Accept-Ranges: bytes");
			header('Content-Transfer-Encoding: binary');
			Header("Accept-Length: " . $size);
			header('Content-Length: ' . $size);
			if (strstr($HTTP_USER_AGENT, 'compatible; MSIE ') !== false && strstr($HTTP_USER_AGENT, 'Opera') === false) {
				header("Content-Disposition: inline; filename=$encoded_filename");
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Content-Type: $mime_type; name=\"$filename\"");
			} while (!feof ($file)) {
				echo fread($file, 1000);
			} 
			fclose ($file);
		} 
	} 
	public function softwareAdd() {
		$this -> assign('category_fortag', $this -> getSystem("software"));
		$this -> menuCourseware();
		$this -> display();
	} 
	public function softwareDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 

		$map['id'] = array('in', $id);
		$dao = D('software');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$ids = explode(',', $id);
			$i = 0;
			foreach($ids as $value) {
				$map2['id'] = $value;
				$old = $dao -> where($map2) -> find();
				$oldurl = dirname(__FILE__) . '/../../..'; //转化为物理路径
				$oldurl .= $old['fileurl'];
				$isDelete = true;
				if (file_exists($oldurl)) {
					$isDelete = @unlink($oldurl);
				} 
				if ($isDelete) {
					$result = $dao -> where($map2) -> delete();
					$i++;
				} 
			} 
			if ($i == $count) {
				$this -> success('已成功删除');
			} else {
				$this -> error('已删除部分文件。某些文件正在被下载，暂无法删除。请稍后再试。');
			} 
		} else {
			$this -> error('该记录不存在');
		} 
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
		$this -> menuCourseware();
		$this -> display();
	} 
	public function softwareInsert() {
		$category = $_POST['category'];
		if (empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$uploader_count = $_POST['uploader_count'];
		if ($uploader_count > 0) {
			$dao = D('software');
			$data['category'] = $category;
			for($i = 0;$i < $uploader_count;$i++) {
				$data['filename'] = $_POST['uploader_' . $i . '_name'];
				$data['fileurl'] = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_' . $i . '_tmpname'];
				$data['filesize'] = $_POST['uploader_' . $i . '_size'];
				$data['ctime'] = date("Y-m-d H:i:s");
				$dao -> add($data);
			} 
			$this -> success('已成功保存');
		} else {
			$this -> error('未上传任何文件');
		} 
	} 
	public function rankingInsert() {
		$category = $_POST['category'];
		$title = $_POST['title'];
		if (empty($title) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('ranking');
		if ($dao -> create()) {
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> ajaxReturn($insertID, '已成功保存！', 1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
	public function rankingDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$map['id'] = array('in', $id);
		$dao = D('ranking');
		$count = $dao -> where($map) -> delete();
		if ($count > 0) {
			$this -> success('已成功删除');
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function softwareEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('software');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("software"));
			$this -> menuCourseware();
			$this -> display();
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function rankingEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('ranking');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("ranking"));
			$this -> menuRanking();
			$this -> display();
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function rankingUpdate() {
		$title = $_POST['title'];
		$category = $_POST['category'];
		if (empty($title) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('ranking');
		if ($dao -> create()) {
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
	public function softwareUpdate() {
		$filename = $_POST['filename'];
		$category = $_POST['category'];
		if (empty($filename) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('software');
		if ($dao -> create()) {
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
	public function ebookAdd() {
		$this -> assign('category_fortag', $this -> getSystem("ebook"));
		$this -> menuCourseware();
		$this -> display();
	} 
	public function ebookDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 

		$map['id'] = array('in', $id);
		$dao = D('ebook');
		$count = $dao -> where($map) -> count();
		if ($count > 0) {
			$ids = explode(',', $id);
			$i = 0;
			foreach($ids as $value) {
				$map2['id'] = $value;
				$old = $dao -> where($map2) -> find();
				$oldurl = dirname(__FILE__) . '/../../..'; //转化为物理路径
				$oldurl .= $old['fileurl'];
				$isDelete = true;
				if (file_exists($oldurl)) {
					$isDelete = @unlink($oldurl);
				} 
				if ($isDelete) {
					$result = $dao -> where($map2) -> delete();
					$i++;
				} 
			} 
			if ($i == $count) {
				$this -> success('已成功删除');
			} else {
				$this -> error('已删除部分文件。某些文件正在被下载，暂无法删除。请稍后再试。');
			} 
		} else {
			$this -> error('该记录不存在');
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
		$this -> menuCourseware();
		$this -> display();
	} 
	public function ebookInsert() {
		$category = $_POST['category'];
		if (empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$uploader_count = $_POST['uploader_count'];
		$titlepic = $_POST['titlepic'];
		if ($uploader_count > 0) {
			$dao = D('ebook');
			$data['category'] = $category;
			for($i = 0;$i < $uploader_count;$i++) {
				$data['filename'] = $_POST['uploader_' . $i . '_name'];
				$data['fileurl'] = '/sys/upload_pl/' . date ('Ym/d/') . $_POST['uploader_' . $i . '_tmpname'];
				$data['filesize'] = $_POST['uploader_' . $i . '_size'];
				$data['ctime'] = date("Y-m-d H:i:s");
				$data['picurl'] = $titlepic;
				$dao -> add($data);
			} 
			$this -> success('已成功保存');
		} else {
			$this -> error('未上传任何文件');
		} 
	} 
	public function ebookEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('ebook');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("ebook"));
			$this -> menuCourseware();
			$this -> display();
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function ebookUpdate() {
		$filename = $_POST['filename'];
		$category = $_POST['category'];
		if (empty($filename) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('ebook');
		if ($dao -> create()) {
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
		$this -> menuRanking();
		$this -> display();
	} 
	public function documentAdd() {
		$this -> assign('category_fortag', $this -> getSystem("document"));
		$this -> assign('mydate', date('Y-m-d H:i:s'));
		$this -> menuRanking();
		$this -> display();
	} 
	public function documentDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$map['id'] = array('in', $id);
		$dao = D('document');
		$count = $dao -> where($map) -> delete();
		if ($count > 0) {
			$this -> success('已成功删除');
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function documentEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('document');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("document"));
			$this -> menuRanking();
			$this -> display();
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function documentUpdate() {
		$title = $_POST['title'];
		$category = $_POST['category'];
		if (empty($title) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('document');
		if ($dao -> create()) {
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
	public function documentInsert() {
		$category = $_POST['category'];
		$title = $_POST['title'];
		if (empty($title) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('document');
		if ($dao -> create()) {
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> ajaxReturn($insertID, '已成功保存！', 1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
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
		$this -> menuRanking();
		$this -> display();
	} 
	public function consultingAdd() {
		$this -> assign('category_fortag', $this -> getSystem("consulting"));
		$this -> assign('mydate', date('Y-m-d H:i:s'));
		$this -> menuRanking();
		$this -> display();
	} 
	public function consultingDel() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$map['id'] = array('in', $id);
		$dao = D('consulting');
		$count = $dao -> where($map) -> delete();
		if ($count > 0) {
			$this -> success('已成功删除');
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function consultingEdit() {
		$id = $_GET['id'];
		if (!isset($id)) {
			$this -> error('参数缺失');
		} 
		$dao = D('consulting');
		$map['id'] = $id;
		$my = $dao -> where($map) -> find();
		if ($my) {
			$this -> assign('my', $my);
			$this -> assign('category_fortag', $this -> getSystem("consulting"));
			$this -> menuRanking();
			$this -> display();
		} else {
			$this -> error('该记录不存在');
		} 
	} 
	public function consultingUpdate() {
		$title = $_POST['title'];
		$category = $_POST['category'];
		if (empty($title) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('consulting');
		if ($dao -> create()) {
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
	public function consultingInsert() {
		$category = $_POST['category'];
		$title = $_POST['title'];
		if (empty($title) || empty($category)) {
			$this -> error('必填项不能为空');
		} 
		$dao = D('consulting');
		if ($dao -> create()) {
			$insertID = $dao -> add();
			if ($insertID) {
				$this -> ajaxReturn($insertID, '已成功保存！', 1);
			} else {
				$this -> error('没有更新任何数据');
			} 
		} else {
			$this -> error('插入数据出错');
		} 
	} 
} 

?>