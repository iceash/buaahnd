<?php 
class MusicalbumMusicModel extends ViewModel {
	public $viewFields = array(
        'Musicalbum' => array('name','singerid'),
        'Music' => array('id'=>'musicid','filename','ctime'=>'time','hit'=>'mhit','_on'=>'musicalbum.id=music.albumid')
    );

}
?>