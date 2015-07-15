<?php 
class MusicalbumSingerViewModel extends ViewModel {
	public $viewFields = array(
        'Musicalbum' => array('singerid','category','singer'),
        'Singer' => array('id','intro'=>'singerintro','pic'=>'singerpic','ctime'=>'sctime','hit'=>'shit','_on'=>'musicalbum.singerid=singer.id')
    );

}
?>