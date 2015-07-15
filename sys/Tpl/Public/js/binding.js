$(document).ready(function(){
    //年份初始化
    var d = new Date();
    var theyaer = d.getFullYear();
    for (var i = theyaer - 10; i < theyaer + 11; i++) {
        $("#year").append('<option value="'+i+'">'+i+'年</option>');
    };
    $("#year").find("option[value='"+theyaer+"']").attr("selected",true);



    $(document).on("click",".check",function(){
        //勾选checkbox树状图子checkbox全部勾选
        var b = this.checked;
        $(this).parent("span").next("ul").find(".check").attr("checked",b);
    });
    $(document).on("click",".listspan span,.liname span,.stuliname span",function(){
        //树状图内点击出现、消失内部选项
        $(this).parent("span").next("ul").toggle(100);
    });
    $(document).on("change","#rebuilt",function(){
        //点击重修时所有勾选被取消
        b = this.checked;
        if (b) {
            $(".left .listdiv input:checkbox").attr("checked",false);
        }else{
            $(".left .listdiv input:checkbox").each(function(){
                var bound = $(this).attr("bound");
                if (bound == "false") {
                    $(this).attr("checked",false);
                }else if (bound == "true"){
                    $(this).attr("checked",true);
                };
            });
        };
    });
    $(document).on("click",".rebuiltcheckall",function(){
        //全选重修人员
        $(".right .listdiv .rebuiltcheck").attr("checked",this.checked);
    });
    /*$(document).on("click","",function(){
        
    });*/

});