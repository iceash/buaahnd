/*
 * ezj.ext.Focus2
 * v1.0.1
 */
ezj.ext.Focus2 = function(element, width, height)
{
///<summary>语法：ezj.ext.Focus2(element, width, height[, speed])</summary>
    element = $g(element);
    var me = this;
    
    // 成员变量和方法
    this.speed = Function.overload(3, "normal");
    this.speed = Array.map(["slow", "normal", "fast"], [4000, 3000, 2000], this.speed, (!Number.is(this.speed) ? 2000 : this.speed));
    this.timer = null;
    this.paused = false;
    this.show = function(index)
    {
        clearTimeout(this.timer);
        if (!this.paused)
        {
            this.container.show(index);
        }
        this.timer = setTimeout(function(){
            if (!me.paused)
            {
                index = (index < (me.container.nav.obj.childNodes.length - 1) ? index + 1 : 0);
            }
            me.show(index);
        }, this.speed);
    };
    this.container = { obj : $c("div") };
    this.container.obj.addClass("ezj_ext_focus2_container");
    this.container.obj.style.width = width + "px";
    this.container.obj.style.height = height + "px";
    this.container.show = function(index)
    {
        me.container.content.show(index);
        me.container.title.show(index);
        me.container.nav.show(index);
    };
    this.fx = null; // 切换特效
    
    // 主要部件
    this.container.content = { obj : $c("div") };
    this.container.content.obj.addClass("ezj_ext_focus2_content");
    this.container.content.show = function(index)
    {
        // 其上下文与 this.show 的不同，所以这里用 me。
        if (me.fx)
        {
            me.fx.start("screen", index, true, true);
        }
        /*
        // 无效果的切换
        for (var i = 0; i < me.container.content.obj.childNodes.length; i++)
        {
            $g(me.container.content.obj.childNodes[i]).display(i == index);
        }
        */
    };
    
    this.container.title = { obj : $c("div") };
    this.container.title.obj.addClass("ezj_ext_focus2_title");
    this.container.title.show = function(index){
        for (var i = 0; i < me.container.title.obj.childNodes.length; i++)
        {
            $g(me.container.title.obj.childNodes[i]).display(i == index);
        }
    };
    
    this.container.nav = { obj : $c("ul") };
    this.container.nav.obj.addClass("ezj_ext_focus2_nav");
    this.container.nav.show = function(index){
        for (var i = 0; i < me.container.nav.obj.childNodes.length; i++)
        {
            var e = $g(me.container.nav.obj.childNodes[i]);
            if (i != index)
            {
                e.removeClass("ezj_ext_focus2_nav_item_current");
            }
            else
            {
                e.addClass("ezj_ext_focus2_nav_item_current");
            }
        }
    };
    
    // 初始化数据
    $g("<a", element).each(function(e){
        var contentItem = $c("div", null, me.container.content.obj);
        var contetnItemA = $c("a", { href : e.href, title : e.title, target : e.target }, contentItem);
        var img = $g("<img", e)[0];
        var newImg = $c("img", { src : img.src, alt : img.alt }, contetnItemA);
        newImg.style.width = width + "px";
        newImg.style.height = height + "px";
        
        var navItem = $c("div", null, me.container.title.obj);
        navItem.val(e.title);
        
        var itemIndex = me.container.nav.obj.childNodes.length;
        var navItem = $c("li", null, me.container.nav.obj);
        $c("literal", { text : itemIndex + 1 }, navItem);
        navItem.mouseover(function(e){
            me.show(itemIndex);
            me.paused = true;
        }); // 它有动作
        navItem.mouseout(function(e){
            me.paused = false;
        });
    });
    
    // 装配
    this.container.obj.append([this.container.content.obj, this.container.title.obj, this.container.nav.obj]);
    element.before(this.container.obj);
    element.detach();
    ezj.ext.scroll(this.container.content.obj, { direction : "up", speed : "fast", step : 200 }, function(scroll){
        me.fx = scroll;
        me.fx.onMoving = function(distance)
        {
            return Math.max(Math.round(distance / 10), 1);
        }
        me.show(0);
    });
};