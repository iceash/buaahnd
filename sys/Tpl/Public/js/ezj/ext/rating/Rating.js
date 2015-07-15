/*
 * ezj.ext.Rating
 * v1.0
 */
 
ezj.ext.Rating = function(container, config)
{
    // 容纳星星的元素
    container = $g(container);
    
    // 事件
    var changingEvent = new ezj.Event();
    var changedEvent = new ezj.Event();
    // 成员函数
    this.changing = function(handler){
        changingEvent.addHandler(handler);
    };
    this.changed = function(handler){
        changedEvent.addHandler(handler);
    };
    
    // 配置
    var min = Object.prop(config, "min", 1);
    var max = Object.prop(config, "max", 5);
    var val = Object.prop(config, "val", 0);
    var starUrl = Object.prop(config, "starUrl", ezj.about.path + "ext/rating/star.gif");
    var activatedStarUrl = Object.prop(config, "activatedStarUrl", ezj.about.path + "ext/rating/activatedStar.gif");
    var starWidth = Object.prop(config, "starWidth", -1); // -1 不控制
    var starHeight = Object.prop(config, "starHeight", -1); // -1 不控制
    
    // 星星
    var stars = new Array(max);
    
    // 创建星星
    stars.each(function(e, index){
        stars[index] = $c("img", {src:((index + 1 <= val) ? activatedStarUrl : starUrl)}, container);
        stars[index].style.cursor = "pointer";
        if (starWidth > 0)
        {
            stars[index].style.width = starWidth + "px";
        }
        if (starHeight > 0)
        {
            stars[index].style.height = starHeight + "px";
        }
    });
    
    // 星星事件触发配置
    stars.each(function(e, index){
        e.mouseover(function(){
            for (var i = 0; i <= Math.max(index, min - 1); i++)
            {
                stars[i].src = activatedStarUrl;
            }
            for (var i = Math.max(index, min - 1) + 1; i < max; i++)
            {
                stars[i].src = starUrl;
            }
            changingEvent.trigger(this, Math.max(index + 1, min));
        });
        
        e.click(function(){
            changedEvent.trigger(this, Math.max(index + 1, min));
        });
    });
};