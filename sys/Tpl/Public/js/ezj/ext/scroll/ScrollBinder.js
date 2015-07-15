//千一网络 www.cftea.com
//ScrollBinder v1.0.1 ezj 版本 MIT
//http://www.cftea.com/products/webComponents/ScrollBinder/
//此文件为 UTF-8 格式
//应用示例：
/*
var continuousUp = new ScrollBinder("continuousUp", "up", 1, 50, 0);
continuousUp.setAutoPause();
continuousUp.start();
*/


//语法：ScrollBinder(target, direction, step, delay, screenDelay[, initialize])
//参数 target，对象。要进行滚动的控件对象。
//参数 direction，字符串。滚动方向，可选值：up、down、left、right。
//参数 step，数字。步进长度，该值越大表示滚动速度越快。
//参数 delay，数字。延时，该值越大表示滚动速度越慢。
//参数 screenDelay，数字。屏停延时，即在到达一屏时停留的时间，屏停滚动时有效。
//参数 initialize，布尔。是否初始化内容和位置。默认为 true。
function ScrollBinder(target, direction, step, delay, screenDelay)
{
    this.target = target;
    this.direction = direction;
    this.step = step;
    this.delay = delay;
    this.screenDelay = screenDelay;
    var initialize = true;
    if (arguments.length >= 6)
    {
        initialize = arguments[5];
    }
    
    this.timer = null;
    this.movingState = "stopped"; //moving、stopped、paused、prePaused、preMoving
    
    
    this.scrollType = "continuous";
    this.targetScreenIndex = null;
    this.stopAtTargetScreen = false;
    
    
    //暂停
    //从 stopped 状态转为 prePaused 状态；
    //从 moving 状态转为 paused 状态。
    this.pause = function ()
    {
        if (this.movingState == "stopped")
        {
            clearTimeout(this.timer);
            this.movingState = "prePaused";
        }
        else if (this.movingState == "moving")
        {
            clearTimeout(this.timer);
            this.movingState = "paused";
        }
    }
    
    
    //恢复
    //从 prePaused 状态转为 stopped 状态；
    //从 preMoving 或 paused 状态转为 moving 状态。
    this.resume = function ()
    {
        if (this.movingState == "prePaused")
        {
            clearTimeout(this.timer);
            this.movingState = "stopped";
        }
        else if (this.movingState == "preMoving" || this.movingState == "paused")
        {
            clearTimeout(this.timer);
            this.movingState = "moving";
            this.move();
        }
    }
    
    
    //停止
    //从任何状态转为 stopped 状态。
    this.stop = function ()
    {
        clearTimeout(this.timer);
        this.movingState = "stopped";
    }
    
    
    //初始化内容
    this.initializeContent = function ()
    {
        this.target.style.overflow = "hidden";
        if (this.direction == "up" || this.direction == "down")
        {
            this.target.innerHTML += this.target.innerHTML;
        }
        else if (this.direction == "left" || this.direction == "right")
        {
            this.target.innerHTML = "<table border='0' cellspacing='0' cellpadding='0'><tr><td><div style='white-space:nowrap;'>" + this.target.innerHTML + "</div></td><td><div style='white-space:nowrap;'>" + this.target.innerHTML + "</div></td></tr></table>";
        }
    }
    
    
    //初始化位置
    this.initializePosition = function ()
    {
        if (this.direction == "up")
        {
            this.target.scrollTop = 0;
        }
        else if (this.direction == "down")
        {
            this.target.scrollTop = this.target.scrollHeight - this.target.clientHeight;
        }
        else if (this.direction == "left")
        {
            this.target.scrollLeft = 0;
        }
        else if (this.direction == "right")
        {
            this.target.scrollLeft = this.target.scrollWidth - this.target.clientWidth;
        }
    }
    
    
    //根据 this.direction 确定的方向进行滚动。
    this.move = function ()
    {
        if (this.movingState != "moving")
        {
            return;
        }
        clearTimeout(this.timer);
        
        var scrollType = this.scrollType;
        var targetScreenIndex = this.targetScreenIndex;
        var stopAtTargetScreen = this.stopAtTargetScreen;
        
        var xScreenCount = Math.floor(((this.target.scrollWidth / 2) + this.target.clientWidth - 1) / this.target.clientWidth);
        var yScreenCount = Math.floor(((this.target.scrollHeight / 2) + this.target.clientHeight - 1) / this.target.clientHeight);
        var curScreenIndex = 0;
        var preScreenIndex = 0;
        var arrived = false; //是否到达目的地，scrollType 为 continuous 时，该值永远为 false。
        if (this.direction == "up")
        {
            curScreenIndex = Math.floor(this.target.scrollTop / this.target.clientHeight);
            preScreenIndex = ((curScreenIndex + 1) >= yScreenCount) ? 0 : curScreenIndex + 1;
            arrived = this.moveUp(scrollType, (targetScreenIndex != null) ? parseInt(targetScreenIndex) : preScreenIndex);
        }
        else if (this.direction == "down")
        {
            curScreenIndex = Math.floor((this.target.scrollHeight - this.target.scrollTop - this.target.clientHeight) / this.target.clientHeight);
            preScreenIndex = ((curScreenIndex + 1) >= yScreenCount) ? 0 : curScreenIndex + 1;
            arrived = this.moveDown(scrollType, (targetScreenIndex != null) ? parseInt(targetScreenIndex) : preScreenIndex);
        }
        else if (this.direction == "left")
        {
            curScreenIndex = Math.floor(this.target.scrollLeft / this.target.clientWidth);
            preScreenIndex = ((curScreenIndex + 1) >= xScreenCount) ? 0 : curScreenIndex + 1;
            arrived = this.moveLeft(scrollType, (targetScreenIndex != null) ? parseInt(targetScreenIndex) : preScreenIndex);
        }
        else if (this.direction == "right")
        {
            curScreenIndex = Math.floor((this.target.scrollWidth - this.target.scrollLeft - this.target.clientWidth) / this.target.clientWidth);
            preScreenIndex = ((curScreenIndex + 1) >= xScreenCount) ? 0 : curScreenIndex + 1;
            arrived = this.moveRight(scrollType, (targetScreenIndex != null) ? parseInt(targetScreenIndex) : preScreenIndex);
        }
        else
        {
            return;
        }
        
        if (arrived && stopAtTargetScreen)
        {
            //到达目的地，并要求停止
            this.stop();
            return;
        }
        
        var me = this;
        var delay = (this.screenDelay > 0 && arrived) ? this.screenDelay : this.delay;
        this.timer = setTimeout(function () { me.move(); }, delay);
    }
    
    
    //向上滚动
    this.moveUp = function (scrollType, targetScreenIndex)
    {
        var step = this.step; //实际移动的步长，可能会被 onMoving 事件处理程序改变，且在具有屏停的滚动模式下最大不会超过现在位置距目标的距离。
        var arrived = false; //是否到达目的地。continuous 模式下，其值永远为 false。
        
        //若当前位置越界，则拉回等效位置。
        if (this.target.scrollTop >= (this.target.scrollHeight / 2))
        {
            this.target.scrollTop = this.target.scrollTop - (this.target.scrollHeight / 2);
        }
                
        if (scrollType == "continuous")
        {
            step = (typeof(this.onMoving) == "function") ? this.onMoving(Number.MAX_VALUE) : step;
            arrived = false;
        }
        else if (scrollType == "screen")
        {
            var curPos = this.target.scrollTop;
            var targetPos = this.target.clientHeight * targetScreenIndex;
            if (curPos == targetPos)
            {
                arrived = true;
                return arrived;
            }
            var distance = (curPos > targetPos) ? (this.target.scrollHeight / 2 - (curPos - targetPos)) : (targetPos - curPos);
            step = (typeof(this.onMoving) == "function") ? this.onMoving(distance) : step;
            step = Math.min(distance, step);
            arrived = (step == distance) ? true : false;
        }
        else
        {
            return arrived;
        }
        
        //为了实现无缝滚动，在超过特定范围后，拉回。
        var preScrollTop = this.target.scrollTop + step;
        if (preScrollTop >= (this.target.scrollHeight / 2))
        {
            preScrollTop = preScrollTop - (this.target.scrollHeight / 2);
        }
        
        //滚动
        this.target.scrollTop = preScrollTop;
        
        return arrived;
    }
    
    
    //向下滚动
    this.moveDown = function (scrollType, targetScreenIndex)
    {
        var step = this.step; //实际移动的步长，可能会被 onMoving 事件处理程序改变，且在具有屏停的滚动模式下最大不会超过现在位置距目标的距离。
        var arrived = false; //是否到达目的地。continuous 模式下，其值永远为 false。
        
        //若当前位置越界，则拉回等效位置。
        if (this.target.scrollTop <= ((this.target.scrollHeight / 2) - this.target.clientHeight))
        {
            this.target.scrollTop = this.target.scrollTop + (this.target.scrollHeight / 2);
        }
        
        if (scrollType == "continuous")
        {
            step = (typeof(this.onMoving) == "function") ? this.onMoving(Number.MAX_VALUE) : step;
            arrived = false;
        }
        else if (scrollType == "screen")
        {
            //按 "scrollBottom" 计算。
            var curPos = this.target.scrollHeight - this.target.scrollTop - this.target.clientHeight;
            var targetPos = this.target.clientHeight * targetScreenIndex;
            if (curPos == targetPos)
            {
                arrived = true;
                return arrived;
            }
            var distance = (curPos > targetPos) ? (this.target.scrollHeight / 2 - (curPos - targetPos)) : (targetPos - curPos);
            step = (typeof(this.onMoving) == "function") ? this.onMoving(distance) : step;
            step = Math.min(distance, step);
            arrived = (step == distance) ? true : false;
        }
        else
        {
            return arrived;
        }
        
        //为了实现无缝滚动，在超过特定范围后，拉回。
        var preScrollTop = this.target.scrollTop - step;
        if (preScrollTop <= ((this.target.scrollHeight / 2) - this.target.clientHeight))
        {
            preScrollTop = preScrollTop + (this.target.scrollHeight / 2);
        }
        
        //滚动
        this.target.scrollTop = preScrollTop;
        
        return arrived;
    }
    
    
    //向左滚动
    this.moveLeft = function (scrollType, targetScreenIndex)
    {
        var step = this.step; //实际移动的步长，可能会被 onMoving 事件处理程序改变，且在具有屏停的滚动模式下最大不会超过现在位置距目标的距离。
        var arrived = false; //是否到达目的地。continuous 模式下，其值永远为 false。
        
        //若当前位置越界，则拉回等效位置。
        if (this.target.scrollLeft >= (this.target.scrollWidth / 2))
        {
            this.target.scrollLeft = this.target.scrollLeft - (this.target.scrollWidth / 2);
        }
        
        if (scrollType == "continuous")
        {
            step = (typeof(this.onMoving) == "function") ? this.onMoving(Number.MAX_VALUE) : step;
            arrived = false;
        }
        else if (scrollType == "screen")
        {
            var curPos = this.target.scrollLeft;
            var targetPos = this.target.clientWidth * targetScreenIndex;
            if (curPos == targetPos)
            {
                arrived = true;
                return arrived;
            }
            var distance = (curPos > targetPos) ? (this.target.scrollWidth / 2 - (curPos - targetPos)) : (targetPos - curPos);
            step = (typeof(this.onMoving) == "function") ? this.onMoving(distance) : step;
            step = Math.min(distance, step);
            arrived = (step == distance) ? true : false;
        }
        else
        {
            return arrived;
        }
        
        //为了实现无缝滚动，在超过特定范围后，拉回。
        var preScrollLeft = this.target.scrollLeft + step;
        if (preScrollLeft >= (this.target.scrollWidth / 2))
        {
            preScrollLeft = preScrollLeft - (this.target.scrollWidth / 2);
        }
        
        //滚动
        this.target.scrollLeft = preScrollLeft;
        
        return arrived;
    }
    
    
    //向右滚动
    this.moveRight = function (scrollType, targetScreenIndex)
    {
        var step = this.step; //实际移动的步长，可能会被 onMoving 事件处理程序改变，且在具有屏停的滚动模式下最大不会超过现在位置距目标的距离。
        var arrived = false; //是否到达目的地。continuous 模式下，其值永远为 false。
        
        //若当前位置越界，则拉回等效位置。
        if (this.target.scrollLeft <= ((this.target.scrollWidth / 2) - this.target.clientWidth))
        {
            this.target.scrollLeft = this.target.scrollLeft + (this.target.scrollWidth / 2);
        }
        
        if (scrollType == "continuous")
        {
            step = (typeof(this.onMoving) == "function") ? this.onMoving(Number.MAX_VALUE) : step;
            arrived = false;
        }
        else if (scrollType == "screen")
        {
            //按 "scrollRight" 计算。
            var curPos = this.target.scrollWidth - this.target.scrollLeft - this.target.clientWidth;
            var targetPos = this.target.clientWidth * targetScreenIndex;
            if (curPos == targetPos)
            {
                arrived = true;
                return arrived;
            }
            var distance = (curPos > targetPos) ? (this.target.scrollWidth / 2 - (curPos - targetPos)) : (targetPos - curPos);
            step = (typeof(this.onMoving) == "function") ? this.onMoving(distance) : step;
            step = Math.min(distance, step);
            arrived = (step == distance) ? true : false;
        }
        else
        {
            return arrived;
        }
        
        //为了实现无缝滚动，在超过特定范围后，拉回。
        var preScrollLeft = this.target.scrollLeft - step;
        if (preScrollLeft <= ((this.target.scrollWidth / 2) - this.target.clientWidth))
        {
            preScrollLeft = preScrollLeft + (this.target.scrollWidth / 2);
        }
        
        //滚动
        this.target.scrollLeft = preScrollLeft;
        
        return arrived;
    }
    
    
    if (initialize)
    {
        this.initializeContent();
        this.initializePosition();
    }
}


//滚动中，即将进行移动时的事件处理程序。
//传入参数 distance 表示当前位置距目的地的距离，如果 scrollType 为 continuous，则 distance 为 Number.MAX_VALUE。
//必须返回一个新的步进值，如果不需要对步进值进行改变，则返回 this.step。
//该函数可以用来改变步进值，比如当 distance 越大时，步进值越大，滚动越快。
ScrollBinder.prototype.onMoving = null;


//设置鼠标移上后自动停止，鼠标移出后自动继续的功能。
ScrollBinder.prototype.setAutoPause = function ()
{
    var me = this;
    if (window.attachEvent)
    {
        this.target.attachEvent("onmousemove", function () { me.pause(); });
        this.target.attachEvent("onmouseout", function () { me.resume(); });
    }
    else
    {
        this.target.addEventListener("mousemove", function () { me.pause(); }, false);
        this.target.addEventListener("mouseout", function () { me.resume(); }, false);
    }
}


//开始滚动
//语法：start([scrollType[, targetScreenIndex[, stopAtTargetScreen[, startImmediately]]]])
//参数 scrollType，字符串。滚动类型，可选值：continuous（连续滚动）、screen（具有屏停效果的滚动）。默认值为 continuous。
//参数 targetScreenIndex，数字。表示滚动到哪个屏，scrollType 为 screen 时有效。如果为 null，则表示滚动到下一个屏。默认值为 null。
//参数 stopAtTargetScreen，数字。到达指定屏时是否停止，scrollType 为 screen 时有效。默认值为 false，表示不停止，而是使用屏停延时。
//参数 startImmediately，布尔。是否立即开始：如果为 false 且屏停延时 > 0 且滚动类型为 screen，则在屏停延时后开始；如果为 false 且（屏停时间 <= 0 或 滚动类型不是 screen），则在延时后开始；如果为 true，则立即开始。默认值为 false。
ScrollBinder.prototype.start = function ()
{
    this.scrollType = (arguments.length >= 1) ? arguments[0] : "continuous";
    this.targetScreenIndex = (arguments.length >= 2) ?arguments[1] : null;
    this.stopAtTargetScreen = (arguments.length >= 3) ?arguments[2] : false;
    var startImmediately = (arguments.length >= 4) ?arguments[3] : false;
    if (this.movingState == "stopped")
    {
        this.movingState = "moving";
        var me = this;
        if (!startImmediately)
        {
            this.timer = setTimeout(function () { me.move(); }, (this.screenDelay > 0 && this.scrollType == "screen") ? this.screenDelay : this.delay);
        }
        else
        {
            me.move();
        }
    }
    else if (this.movingState == "prePaused")
    {
        this.movingState = "preMoving";
    }
}