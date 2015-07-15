//千一网络 www.cftea.com
//TabControl（标签控件） v2.0 ezj 版本
//http://www.cftea.com/products/webComponents/TabControl/
//应用示例：
/*
var tabControl = new TabControl("normalTitle", "", "activatedTitle", "mouseover");
tabControl.bindTabPagesByContainerId("titles", "contents");
tabControl.activateTabPage(0);

var tabControl2 = new TabControl("normalTitle", "hoverTitle", "activatedTitle", "click");
tabControl2.bindTabPagesByContainerId("titles2", "contents2");
tabControl2.activateTabPage(0);
*/


//标签控件
//属性 normalTitleClassName，标签页标题的默认样式名称。
//属性 hoverTitleClassName，鼠标移到标签页的标题上时标签页标题的样式名称，当 eventName 不为 mouseover 时才生效。
//属性 activatedTitleClassName，已激活的标签页的标题样式名称。
//属性 eventName，触发标签页切换的事件名称，一般为 click 或 mouseover。
//属性 tabPages，获得标签页数组。
//属性 activatedTitleIndex，获得当前已经激活的标签页的索引值。
//方法 bindTabPagesByContainerId，根据标签页标题和标签页内容的上级容器 Id 绑定多个标签页。
//方法 bindTabPagesByContainer，根据标签页标题和标签页内容的上级容器绑定多个标签页。
//方法 bindTabPages，绑定多个标签页。
//方法 bindTabPage，绑定标签页。
//方法 activateTabPage，激活对应的标签页。
function TabControl(normalTitleClassName, hoverTitleClassName, activatedTitleClassName, eventName)
{
    this.normalTitleClassName = normalTitleClassName; //未活动标签的样式名称
    this.hoverTitleClassName = hoverTitleClassName;
    this.activatedTitleClassName = activatedTitleClassName; //激活标签的样式名称
    this.eventName = eventName;
    
    this.tabPages = new Array();
    this.activatedTitleIndex = 0;
    this.bindTabPagesByContainerId = TabControl_bindTabPagesByContainerId;
    this.bindTabPagesByContainer = TabControl_bindTabPagesByContainer;
    this.bindTabPages = TabControl_bindTabPages;
    this.bindTabPage = TabControl_bindTabPage;
    this.activateTabPage = TabControl_activateTabPage;
}


//标签页
function TabPage(title, content)
{
    this.title = title;
    this.content = content;
}


//根据标签页标题和标签页内容的上级容器 Id 绑定多个标签页
function TabControl_bindTabPagesByContainerId(titleContainerId, contentContainerId)
{
    this.bindTabPagesByContainer(document.getElementById(titleContainerId), document.getElementById(contentContainerId));
}


//根据标签页标题和标签页内容的上级容器绑定多个标签页
function TabControl_bindTabPagesByContainer(titleContainer, contentContainer)
{
    this.bindTabPages(titleContainer.childNodes, contentContainer.childNodes);
}


//绑定多个标签页
function TabControl_bindTabPages(titles, contents)
{
    for (var i = 0; i < titles.length && i < contents.length; i++)
    {
        if (contents[i].nodeType == 1)
        {
            //是一个 element
            this.bindTabPage(titles[i], contents[i]);
        }
    }
}


//绑定标签页
function TabControl_bindTabPage(title, content)
{
    var index = this.tabPages.length;
    this.tabPages[index] = new TabPage(title, content);
    
    this.tabPages[index].title.className = this.normalTitleClassName; //设置默认样式
    var me = this;
    if (window.attachEvent)
    {
        //IE
        this.tabPages[index].title.attachEvent("on" + this.eventName, function () { me.activateTabPage(index); });
        if (this.hoverTitleClassName != "" && this.eventName != "mouseover")
        {
            //增加鼠标移上去时的效果
            this.tabPages[index].title.attachEvent("onmouseover", function () { 
                if (index != me.activatedTitleIndex)
                {
                    me.tabPages[index].title.className = me.hoverTitleClassName;
                }
             });
            this.tabPages[index].title.attachEvent("onmouseout", function () { 
                if (index != me.activatedTitleIndex)
                {
                    me.tabPages[index].title.className = me.normalTitleClassName;
                }
             });
        }
    }
    else
    {
        //FireFox
        this.tabPages[index].title.addEventListener(this.eventName, function () { me.activateTabPage(index); }, false);
        if (this.hoverTitleClassName != "" && this.eventName != "mouseover")
        {
            //增加鼠标移上去时的效果
            this.tabPages[index].title.addEventListener("mouseover", function () { 
                if (index != me.activatedTitleIndex)
                {
                    me.tabPages[index].title.className = me.hoverTitleClassName;
                }
             }, false);
            this.tabPages[index].title.addEventListener("mouseout", function () { 
                if (index != me.activatedTitleIndex)
                {
                    me.tabPages[index].title.className = me.normalTitleClassName;
                }
             }, false);
        }
    }
    this.tabPages[index].content.style.display = "none"; //将 content 隐藏起来
}


//激活标签页
function TabControl_activateTabPage(newTabPageIndex)
{
    this.tabPages[this.activatedTitleIndex].title.className = this.normalTitleClassName;
    this.tabPages[this.activatedTitleIndex].content.style.display = "none";
    this.activatedTitleIndex = newTabPageIndex;
    this.tabPages[this.activatedTitleIndex].title.className = this.activatedTitleClassName;
    this.tabPages[this.activatedTitleIndex].content.style.display = "block";
}