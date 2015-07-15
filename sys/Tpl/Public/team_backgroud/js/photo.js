// JavaScript Document
jQuery(function(){
    jQuery('.gallary_item').hover(function(){
        jQuery('.gallary_wrap .gallary_item').removeClass('sel_on');
        jQuery(this).addClass('sel_on');
        var obj = this;
        jQuery(document).unbind('mousedown').bind("mousedown",function(e){
            var popup = obj;
            var e = e || window.event;
            var target = e.target || e.srcElement;
            while (target != document && target != popup 
                && target != jQuery('#meiu_float_bubble').get(0) 
                && target != jQuery('#meiu_float_box').get(0) 
                && target != jQuery('.modaldiv').get(0)
                && target != jQuery('.clipboard').get(0)) {
                target = target.parentNode;
            }
            if (target == document) {
                jQuery('.gallary_wrap .gallary_item').removeClass('sel_on');
            }
        });
    });
    
    jQuery('.inline_edit').hover(function(){
        jQuery(this).addClass('editbg');
    },function(){
        jQuery(this).removeClass('editbg');
    })
});

Madmin={};
Madmin.check_all = function(je,check){
    if(check){
        jQuery(je).attr('checked','checked');
    }else{
        jQuery(je).removeAttr('checked');
    }
}
Madmin.checked_action = function(je,action_url){
    var check_vals = jQuery(je+':checked');
    jQuery.post(action_url,check_vals.serializeArray(),function(data) {
        Mui.box.setData(data,true);
    },'html');
}
Madmin.rename = function(obj,url){
    var info = jQuery(obj).parent();
    var id = jQuery(obj).attr('nid');
    
    var info_txt = info.text();
    info.html('<input id="input_id_'+id+'" type="text" value="'+info_txt.replace(/\"/g, '&#34;')+'" class="inputstyle" />');
    var input = jQuery('#input_id_'+id);
    input.focus();
    input.select();
    input.blur(
        function(){
            if(this.value != info_txt && this.value!=''){
                jQuery.post(url,
                   {name:this.value},
                   function(data){
                        if(data.ret){
                            jQuery(obj).html(data.html);
                            info.empty().append(obj);
                        }else{
                            info.empty().append(obj);
                        }
                    },
                'json');
            }else{
                jQuery(obj).html(info_txt);
                info.empty().append(obj);
            }
        }
    );
    input.unbind('keypress').bind('keypress',
        function(e){
            if(e.keyCode == 13){
                input.blur();
            }
        }
    );
}

Madmin.inline_edit = function(je,url){
    var info = jQuery(je);
    var parent = jQuery(je).parent();
    jQuery.get(url,{ajax:'true','_t':Math.random()}, function(data) {
        info.hide();
        if(parent.find('form').length == 0){
            parent.append(data);
        }
        jQuery(parent).find('input[name=cancel]').click(function(){
            jQuery(parent).find('form').remove();
            info.show();
        });
        jQuery(parent).find('form').submit(function(){
            var postform = jQuery(this);
            jQuery.post(postform.attr('action'),postform.serializeArray(),function(data) {
                if(data.ret){
                    info.html(data.html+' <span class="i_editinfo sprite"></span>');
                    jQuery(parent).find('form').remove();
                    info.show();
                }else{
                    notice_div = postform.find('.form_notice_div');
                    if( notice_div.length == 0 ){
                        postform.prepend('<div class="form_notice_div">'+data.html+'</div>');
                    }else{
                        notice_div.html(data.html);
                    }
                    postform.find('.form_notice_div').css({display:'block'});
                }
            },'json');
        });
    },'html');
}

Madmin.addEditNav = function(o){
    o.before('<tr class="hover">\
        <td></td>\
        <td><input type="text" class="inputstyle iptw0" name="sortnew[]" value="" /></td>\
        <td><input type="text" class="inputstyle iptw1" name="namenew[]" value="" /></td>\
        <td><input type="text" class="inputstyle iptw2" name="urlnew[]" value="" /></td>\
        <td colspan="2"></td></tr>');
}

function admin_reply_comment(je,url){
    var btn = jQuery(je);
    var parent = jQuery(je).parent().parent();
    if(parent.next('tr.form').length == 0){
        jQuery.get(url,{ajax:'true','_t':Math.random()}, function(data) {
            parent.after('<tr class="form"><td colspan="5">'+data+'</td></tr>');
            parent.next('tr.form').find('input[name=cancel]').click(function(){
                parent.next('tr.form').hide();
            });
            parent.next('tr.form').find('form').submit(function(){
                var postform = jQuery(this);
                jQuery.post(postform.attr('action'),postform.serializeArray(),function(data) {
                    if(data.ret){
                        window.location.reload();
                    }else{
                        notice_div = postform.find('.form_notice_div');
                        if( notice_div.length == 0 ){
                            postform.prepend('<div class="form_notice_div">'+data.html+'</div>');
                        }else{
                            notice_div.html(data.html);
                        }
                        postform.find('.form_notice_div').css({display:'block'});
                    }
                },'json');
            });
        },'html');
    }else{
        parent.next('tr.form').show();
    }
}
