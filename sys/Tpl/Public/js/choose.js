$(function(){
	
	var page = 1;
    var i = 1; 
	$('#up_right').click(function(){
		var $pictureShow = $('div#down');
		var downwidth = $pictureShow.height();
		var len = $('#icontable').find('tr').length;
		var page_number = Math.ceil(len/i);
		if( !$('div#downContent').is(":animated") ){
		if( page == page_number){
			$('div#downContent').animate({top:'0px'},"slow");
		page = 1;
		}else{
			$('div#downContent').animate({top:'-='+downwidth},"slow");
			page++;
		}
			}
		$('div#up span').eq((page-1)).addClass("current").siblings().removeClass("current");	
		});
		//左移动画
    $('#up_left').click(function(){
		var $pictureShow = $('div#down');
		var downwidth = $pictureShow.height();
		var len = $('#icontable').find('tr').length;
		var page_number = Math.ceil(len/i);
		if( !$('div#downContent').is(":animated") ){
		 if(page == 1){
	$('div#downContent').animate({top: '-='+downwidth*(page_number-1)},"slow");
			page = page_number;
			}else{
			$('div#downContent').animate({top:'+='+downwidth},"slow");	
			page--;	
				}
		}
		$('div#up span').eq((page-1)).addClass("current").siblings().removeClass("current");	
		});   
	$("div#up span").click(function(){ 
 
	    var topage=$("div#up span").index(this)+1;
		var $pictureShow = $('div#down');
		var downwidth = $pictureShow.height();
		var len = $('#icontable').find('tr').length;
		var page_number = Math.ceil(len/i);
		if( !$('div#downContent').is(":animated") ){
		 if(topage<page){
			 var maxwidth=downwidth*(page-topage);
			$('div#downContent').animate({top:'+='+maxwidth},"slow");
			page=topage;	
			}else{
			 var maxwidth=downwidth*(topage-page);	
			$('div#downContent').animate({top:'-='+maxwidth},"slow");	
			page=topage;	
				}
		}
		$('div#up span').eq((page-1)).addClass("current").siblings().removeClass("current");	
		 
       });
	});