$(document).ready(function(){
    $(document).on("click",".check",function(){
        //全选
        var b = this.checked;
        $(this).parents(".item").find(".td_forselect input:checkbox").attr("checked",b);
    });
    $(document).on("click",".btn_add",function(){
        //以新增形式出现新行
        $(this).parents(".item").find(".addfee").remove();
        $(this).parents(".item").find(".div_partner").remove();
        $(this).parents(".item").find(".mytable tbody").append($("#div_addfee").html());
        $(this).parents(".item").find(".btn_update").hide();
        $(this).hide();
    });
    
    $(document).on("click",".btn_cancel",function(){
        //撤销新行
        $(this).parents(".item").find(".btn_add").show();
        $(this).parents(".addfee").remove();
    });
    $(document).on("click",".add_separate",function(){
        //添加一个分隔值
        $(this).before('<input type="text" value="0" class="separate" />');
    });
    $(document).on("change",".pat_type select",function(){
        //改变返款类别
        $(this).parent(".pat_type").siblings(".pat_value").children("input").val("");
        var va = $(this).val();
        if (va == 1) {
            $(this).parent(".pat_type").siblings(".pat_value").children("span").text("返款值：");
            $(this).parent(".pat_type").siblings(".pat_other").remove();
        }else if(va == 0){
            $(this).parent(".pat_type").siblings(".pat_value").children("span").text("比例%：");
            $(this).parent(".pat_type").siblings(".pat_other").remove();
        }else if(va == 2){
            $(this).parent(".pat_type").siblings(".pat_value").children("span").text("比例%：");
            $(this).parent(".pat_type").after($("#label_allfee").html());
        };
    });
    $(document).on("click",".pat_cancel",function(){
        //撤销出现的合作方
        var tr = $(this).parents("tr");
        var count = 0;
        tr.find(".onepartner").each(function(){
            if ($(this).attr("status") == "new") {
                count++;
            };
        });
        if (count > 0) {
            if (confirm("还有"+count+"个新增项未保存，收起后将会失去，是否确定要收起？")) {
                tr.remove();
            };
        }else{
            tr.remove();
        };
    });
    $(document).on("click",".pat_add",function(){
        //在出现的合作方中新出现一行
        $(this).parent(".onefee").append($("#pat_in").html());
    });


    /*$(document).on("click","",function(){
        
    });*/
});

/*以下为自定义函数*/
function addcheck(_this){
    //新增和更新时的检测
    var item = $(_this).parents(".item").attr("value");
    var addfee = $(_this).parents(".addfee");
    var id = addfee.find("input[name='id']").attr("value");
    var name = addfee.find("input[name='name']")[0].value;
    var type = addfee.find("select.feetype")[0].value;
    var standard = addfee.find("input[name='standard']")[0].value;
    if (name.length <= 0) {
        alert("请输入收费项名称");
        return false;
    };
    if (type.length <= 0) {
        alert("请输入收费项类别");
        return false;
    };
    var numreg = new RegExp("^[0-9]*$");
    if (!numreg.test(standard) || standard.length <= 0 ) {
        alert("请输入此项收费值");
        return false;
    };
    var separate = [];
    addfee.find(".separate").each(function(){
        var num = parseInt($(this).val());
        if (num <= 0 || isNaN(num)) {
            return;
        };
        separate.push(num);
    });
    var feeinfo = [];
    feeinfo[0] = {};
    feeinfo[0]["id"] = id;
    feeinfo[0]["item"] = item;
    feeinfo[0]["name"] = name;
    feeinfo[0]["type"] = type;
    feeinfo[0]["standard"] = standard;
    if (separate.length > 0) {
        separate.push(0);
        separate.push(standard);
        separate.sort(function(a,b){return a>b?1:-1});
        var le = separate.length;
        if (separate[le - 1] != standard) {
            alert("分隔值不能大于收费标准");
            return false;
        };
        for (var i = 1,le = separate.length; i < le; i++) {
            feeinfo[i] = {};
            feeinfo[i]["item"] = item;
            feeinfo[i]["name"] = name + "-" + i;
            feeinfo[i]["type"] = type;
            feeinfo[i]["standard"] = separate[i] - separate[i - 1];
        };
    };
    return feeinfo;
}

function getchanged(that){
    var changeed = false;
    var item = $(that).parents(".item").attr("value");
    var addfee = $(that).parents(".addfee");
    if (addfee.find("input[name='standard']").attr("value") != addfee.find("input[name='standard']").attr("old")){
        changeed = true;
        return true;
    };
    var count = 0,len = 0;
    addfee.find(".separate").each(function(){
        len = $(this).attr("len");
        if (!len) {
            changeed = true;
            return true;
        }else{
            if ($(this).attr("value") != $(this).attr("old")) {
                changeed = true;
                return true;
            };
            count++;
        };
    });
    if (count != len) {
        changeed = true;
    };
    return changeed;
}
