<?php 
class MusicalbumViewModel extends ViewModel {
	public $viewFields = array(
        'musicalbum' => array('name','singerid'),
        'music' => array('id'=>'musicid','filename','ctime'=>'time','hit'=>'mhit','_on'=>'musicalbum.id=music.albumid')
    );

}
?>