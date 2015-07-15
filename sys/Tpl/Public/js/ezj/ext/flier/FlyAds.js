//千一网络 www.cftea.com
//FlyAds v1.1.1

//v1.1.1
//修改　类名称。
//修正　指定 DOCTYPE 时兼容性问题。
//删除　构造函数中的 objStr 参数。

//targetStr 飞行物件的 HTML 元素标签的 id，字符串类型
//startX 飞行物件 X 方向开始位置
//startY 飞行物件 Y 方向开始位置
//flyDelay 是可选参数，每次飞行的时间间隔，单位毫秒，默认值是 30
function FlyAds(targetStr, startX, startY)
{
    this.target = document.getElementById(targetStr);
    this.target.style.position = "absolute"; //将 position 样式属性设置为 absolute，保证可以飞行
    this.target.style.left = startX + "px";
    this.target.style.top = startY + "px";
    
    this.hd = "right"; //当前在 X 方向上向左还是向右飞行，right 向右飞行，其它值（如 left）向左飞行
    this.vd = "down"; //当前在 Y 方向上向上还是向下飞行，down 向下飞行，其它值（如 up）向上飞行 
    this.stepX = 2; //每次飞行在 X 方向上执行的偏移，单位像素
    this.stepY = 1; //每次飞行在 Y 方向上执行的偏移，单位像素
    this.flyDelay = ((arguments.length>=5)?arguments[4]:30); //每次飞行的时间间隔，单位毫秒
    
    this.timer = null;
    
    this.SetFlyProperty = FlyAds_SetFlyProperty; //设置飞行相关属性
    this.Fly = FlyAds_Fly; //开始飞行
    
    this.status = "flying"; //状态：flying－飞行中或允许飞行；paused－已经或即将暂停飞行；stopped－已经或即将停止飞行
    this.Pause = FlyAds_Pause; //飞行 和 暂停 状态之间互相切换，也可以指定一个参数，用于强制指定 status
    this.Clear = FlyAds_Clear; //清除飞行
    this.ReFly = FlyAds_ReFly; //继续飞行，仅用于停止状态
}

//设置飞行相关属性
function FlyAds_SetFlyProperty(hd, vd, stepX, stepY, flyDelay)
{
    this.hd = hd;
    this.vd = vd;
    this.stepX = stepX;
    this.stepY = stepY;
    this.flyDelay = flyDelay;
}   

//开始飞行
function FlyAds_Fly()
{
    var me = this;
    
    if (this.status == "paused")
    {
        //暂停，不执行后面的飞行代码
        this.timer = setTimeout( function () { me.Fly(); }, me.flyDelay);
        return;
    }
    else if (this.status == "stopped")
    {
        //已经要求停止，退出
        return;
    }

    //newX 和 newY 分别为预计的新位置
    var newX = parseInt(this.target.style.left);
    newX = (this.hd=="right")?(newX+this.stepX):(newX-this.stepX);
    var newY = parseInt(this.target.style.top);
    newY = (this.vd=="down")?(newY+this.stepY):(newY-this.stepY);
    
    //修正 newX 值，避免飞行物件出界
    if (newX >= (document.documentElement.scrollLeft || document.body.scrollLeft) + (document.documentElement.clientWidth || document.body.clientWidth) - this.target.offsetWidth)
    {
        newX = (document.documentElement.scrollLeft || document.body.scrollLeft) + (document.documentElement.clientWidth || document.body.clientWidth) - this.target.offsetWidth;
        this.hd = "left";
    }
    else if (newX < (document.documentElement.scrollLeft || document.body.scrollLeft))
    {
        newX = (document.documentElement.scrollLeft || document.body.scrollLeft);
        this.hd = "right";
    }
    
    //修正 newY 值，避免飞行物件出界
    if (newY >= (document.documentElement.scrollTop || document.body.scrollTop) + (document.documentElement.clientHeight || document.body.clientHeight) - this.target.offsetHeight)
    {
        newY = (document.documentElement.scrollTop || document.body.scrollTop) + (document.documentElement.clientHeight || document.body.clientHeight) - this.target.offsetHeight;
        this.vd = "up";
    }
    else if (newY < (document.documentElement.scrollTop || document.body.scrollTop))
    {
        newY = (document.documentElement.scrollTop || document.body.scrollTop);
        this.vd = "down";
    }
    
    //设置飞行物件新位置
    this.target.style.left = newX + "px";
    this.target.style.top = newY + "px";
    
    //准备下一次飞行
    this.timer = setTimeout(function () { me.Fly(); }, me.flyDelay);
}

//飞行状态切换到暂停状态
//暂停状态切换到飞行状态
function FlyAds_Pause()
{
    //如果有参数，那么第一个参数就是用于强制设置 status 值
    //否则“反转”status 值
    if (arguments.length>0)
    {
        if (arguments[0]=="flying" || arguments[0]=="paused")
        {
            this.status = arguments[0];
            return;
        }
    }
    
    if (this.status == "flying")
    {
        this.status = "paused";
    }
    else if (this.status == "paused")
    {
        this.status = "flying";
    }
}

//清除飞行
function FlyAds_Clear()
{
    clearTimeout(this.timer); //必须清除，否则 status 被恢复时，可能（仅仅是可能）会造成两个定时器在工作。
    this.status = "stopped";
    this.target.style.display = "none"
}

//继续飞行，仅用于停止状态
function FlyAds_ReFly()
{
    if (this.status == "stopped")
    {
        this.status = "flying";
        this.target.style.display = "block";
        this.Fly();
    }
}