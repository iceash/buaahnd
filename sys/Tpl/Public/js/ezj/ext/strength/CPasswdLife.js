//千一网络 www.cftea.com
//CPasswdLife v1.0

//密码强壮度类
function CPasswdLife(passwd, userName)
{
    this.passwd = passwd;
    this.userName = userName;
    this.activeLen = 0; //密码的有效长度
    this.activeChrCnt = 0; //密码中不重复的字符数
    this.numCnt = 0; //密码中的数字数量
    this.lAlphaCnt = 0; //密码中的小写字母数量
    this.uAlphaCnt = 0; //密码中的大写字母数量
    this.othersChrCnt = 0; //密码中的其它字符数量
    
    this.GetActiveLen = CPasswdLife_GetActiveLen; //私有函数
    this.DecryptChr = CPasswdLife_DecryptChr; //私有函数
    this.ChckLife = CPasswdLife_ChckLife;
}

//获得密码的有效长度
//当用户名达到一定的长度后，密码中的用户名字符串长度不计入有效长度
//一定长度物理上相邻的字符串长度不完全计入有效长度，指 asdf 和 jkl
//一定长度逻辑上相邻的字符串长度不完全计入有效长度
//一定长度连续重复的字符串不完全计入有效长度
//存储为 this.activeLen 并返回
function CPasswdLife_GetActiveLen()
{
    var str = this.passwd;
    
    //用户名
    if (this.userName.length*2 >= str.length)
    {
        str = str.replace(this.userName, "");
    }
    
    //物理相邻
    str = str.replace("asdf", "af");
    str = str.replace("jkl", "jl");
    
    if (str.length < 2)
    {
        this.activeLen = 0;
        return this.activeLen;
    }
    
    //逻辑相邻、连续重复
    this.activeLen = 0;
    var neighbourCnt = 0;
    var sPos = 0;
    var i = 0;
    for (i=0; i<str.length; i++)
    {
        sPos++;
        
        if (sPos == 1)
        {
            this.activeLen++;
            neighbourCnt = 0;
            sPos = 1;
        }
        else if (sPos == 2)
        {
            if (Math.abs(str.charCodeAt(i)-str.charCodeAt(i-1)) <= 1)
            {
                neighbourCnt++;
                sPos++;
            }
            else
            {
                this.activeLen++;
                neighbourCnt = 0;
                sPos = 1;
            }
        }
        else if (sPos >= 3)
        {
            if ((str.charCodeAt(i)-str.charCodeAt(i-1)) == (str.charCodeAt(i-1)-str.charCodeAt(i-2)))
            {
                neighbourCnt++;
                sPos++;
            }
            else
            {
                this.activeLen++;
                if (neighbourCnt < 2)
                {
                    this.activeLen += neighbourCnt;
                }
                neighbourCnt = 0;
                sPos = 1;
            }
        }
    }
    if (neighbourCnt < 2)
    {
        this.activeLen += neighbourCnt;
    }
    
    return this.activeLen;
}

//解析密码中的字符串
function CPasswdLife_DecryptChr()
{
    var i = 0;
    var j = 0;
    var c = ""; //当前字符
    var cCode = 0; //当前字符的 Unicode 编码
    var found = false; //当前字符是否在密码中的某段位置存在，某段位置是指位置 0 到当前字符的前一个位置
    for (i=0; i<this.passwd.length; i++)
    {
        c = this.passwd.substr(i, 1);
        cCode = c.charCodeAt(0);
        if (cCode>=48 && cCode<=57)
        {
            //数字
            this.numCnt++;
        }
        else if (cCode>=97 && cCode<=122)
        {
            //小写字母
            this.lAlphaCnt++;
        }
        else if (cCode>=65 && cCode<=90)
        {
            //大写字母
            this.uAlphaCnt++;
        }
        else
        {
            //其它字符
            this.othersChrCnt++;
        }
        
        //计算密码中不重复的字符数
        found = false;
        for (j=0; j<i; j++)
        {
            if (this.passwd.substr(j,1) == c)
            {
                found = true;
                break;
            }
        }
        if (!found)
        {
            this.activeChrCnt++;
        }
    }
}

//执行密码分析，并计算密码得分
//有两个缺省参数
function CPasswdLife_ChckLife()
{
    if (arguments.length >= 2)
    {
        this.passwd = arguments[0];
        this.userName = arguments[1];
    }
    
    var score = 0;
    this.GetActiveLen();
    this.DecryptChr();
    
    //密码有效长度计分
    //这里设置最高分，后面再根据其它指标进行减分    
    if (this.activeLen >= 10)
    {
        score = 100;
    }
    else
    {
        score = this.activeLen * 10;
    }
    
    //密码中不重复的字符数计分
    if (this.activeChrCnt < 4)
    {
        score -= 40;
    }
    else if (this.activeChrCnt < 6)
    {
        score -= 10;
    }
    
    //密码纯数字情况
    if (this.numCnt == this.passwd.length)
    {
        //纯数字
        score -= 20;
    }
    
    //字符类型数量，字符类型这里分为：数字、小写字母、大写字母、其它字符
    var chrTypeCnt = 0;
    if (this.numCnt > 0);
    {
        chrTypeCnt++;
    }
    if (this.lAlphaCnt > 0)
    {
        chrTypeCnt++;
    }
    if (this.uAlphaCnt > 0)
    {
        chrTypeCnt++;
    }
    if (this.othersChrCnt > 0)
    {
        chrTypeCnt++;
    }
    
    //字符类型计分
    if (this.chrTypeCnt < 2)
    {
        score -= 10;
    }
    
    return score;
}