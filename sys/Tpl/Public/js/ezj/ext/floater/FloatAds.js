//千一网络 www.cftea.com
//FloatAds v1.1.1 ezj 版本

//v1.1.1 ezj 版本

//v1.1.1
//修改　类名称。
//修正　指定 DOCTYPE 时兼容性问题。
//删除　构造函数中的 objStr 参数。

//v1.1
//增加暂停、继续浮动功能


var FLOATADS_INSTRUCTION_NONE = 0;
var FLOATADS_INSTRUCTION_RUN = 1
var FLOATADS_INSTRUCTION_PAUSE = 2;

//target 浮动物件对象
//position 样式属性，决定 fixedX 和 fixedY 使用的是绝对位置还是相对位置，可选值 absolute 和 relative
//fixedX 浮动物件 X 方向开始位置
//fixedY 浮动物件 Y 方向开始位置
//floatDelay 是可选参数，浮动延迟时间，单位毫秒，默认值是 30
function FloatAds(target, position, fixedX, fixedY)
{
    this.target = target;
    this.target.style.position = position;
    this.fixedX = fixedX;
    if (this.fixedX >= 0)
    {
        this.target.style.left = this.fixedX + "px";
    }
    this.fixedY = fixedY;
    if (this.fixedY >= 0)
    {
        this.target.style.top = this.fixedY + "px";
    }
    this.floatDelay = (arguments.length>=6)?arguments[5]:30;
    
    this.instruction = FLOATADS_INSTRUCTION_NONE;
    this.timer = null;
    
    this.StartFloat = FloatAds_StartFloat; //启用浮动
    this.PauseFloat = FloatAds_PauseFloat; //暂停浮动
    this.ResumeFloat = FloatAds_ResumeFloat; //从暂停恢复浮动
    this.FloatX = FloatAds_FloatX; //在 X 方向浮动，被 this.StartFloat 调用
    this.FloatY = FloatAds_FloatY; //在 Y 方向浮动，被 this.StartFloat 调用
}


//启用浮动
function FloatAds_StartFloat()
{
    if (this.instruction == FLOATADS_INSTRUCTION_PAUSE)
    {
        //不能由暂停指令开始
        return;
    }
    
    if (this.fixedX >= 0)
    {
        this.FloatX();
    }
    if (this.fixedY >= 0)
    {
        this.FloatY();
    }
    
    this.instruction = FLOATADS_INSTRUCTION_RUN;
    var me = this;
    this.timer = setTimeout(function () { me.StartFloat(); }, me.floatDelay);
}


function FloatAds_PauseFloat()
{
    if (this.instruction != FLOATADS_INSTRUCTION_RUN)
    {
        //只能由运行指令开始
        return;
    }
    
    this.instruction = FLOATADS_INSTRUCTION_PAUSE;
    clearTimeout(this.timer);
}


function FloatAds_ResumeFloat()
{
    if (this.instruction != FLOATADS_INSTRUCTION_PAUSE)
    {
        //只能由暂停指令开始
        return;
    }
    
    this.instruction = FLOATADS_INSTRUCTION_RUN;
    this.StartFloat();
}


//在 X 方向浮动，被 this.StartFloat 调用
function FloatAds_FloatX()
{
    if (this.instruction != FLOATADS_INSTRUCTION_RUN)
    {
        //不是运行指令，退出
        return;
    }
    
    var stepX = 10;
    var offsetX = (document.documentElement.scrollLeft || document.body.scrollLeft) + this.fixedX - parseInt(this.target.style.left);
    var newX = parseInt(this.target.style.left);
    if (offsetX > 0)
    {
        newX += (offsetX>stepX)?parseInt(offsetX/stepX):1;
        this.target.style.left = newX + "px";
    }
    else if (offsetX < 0)
    {
        newX += (-offsetX>stepX)?parseInt(offsetX/stepX):-1;
        this.target.style.left = newX + "px";
    }
}


//在 Y 方向浮动，被 this.StartFloat 调用
function FloatAds_FloatY()
{
    if (this.instruction != FLOATADS_INSTRUCTION_RUN)
    {
        //不是运行指令，退出
        return;
    }
    
    var stepY = 10;
    var offsetY = (document.documentElement.scrollTop || document.body.scrollTop) + this.fixedY - parseInt(this.target.style.top);
    var newY = parseInt(this.target.style.top);
    if (offsetY > 0)
    {
        newY += (offsetY>stepY)?parseInt(offsetY/stepY):1;
        this.target.style.top = newY + "px";
    }
    else if (offsetY < 0)
    {
        newY += (-offsetY>stepY)?parseInt(offsetY/stepY):-1;
        this.target.style.top = newY + "px";
    }
}