//ǧһ���� www.cftea.com
//CPasswdLife v1.0

//����ǿ׳����
function CPasswdLife(passwd, userName)
{
    this.passwd = passwd;
    this.userName = userName;
    this.activeLen = 0; //�������Ч����
    this.activeChrCnt = 0; //�����в��ظ����ַ���
    this.numCnt = 0; //�����е���������
    this.lAlphaCnt = 0; //�����е�Сд��ĸ����
    this.uAlphaCnt = 0; //�����еĴ�д��ĸ����
    this.othersChrCnt = 0; //�����е������ַ�����
    
    this.GetActiveLen = CPasswdLife_GetActiveLen; //˽�к���
    this.DecryptChr = CPasswdLife_DecryptChr; //˽�к���
    this.ChckLife = CPasswdLife_ChckLife;
}

//����������Ч����
//���û����ﵽһ���ĳ��Ⱥ������е��û����ַ������Ȳ�������Ч����
//һ���������������ڵ��ַ������Ȳ���ȫ������Ч���ȣ�ָ asdf �� jkl
//һ�������߼������ڵ��ַ������Ȳ���ȫ������Ч����
//һ�����������ظ����ַ�������ȫ������Ч����
//�洢Ϊ this.activeLen ������
function CPasswdLife_GetActiveLen()
{
    var str = this.passwd;
    
    //�û���
    if (this.userName.length*2 >= str.length)
    {
        str = str.replace(this.userName, "");
    }
    
    //��������
    str = str.replace("asdf", "af");
    str = str.replace("jkl", "jl");
    
    if (str.length < 2)
    {
        this.activeLen = 0;
        return this.activeLen;
    }
    
    //�߼����ڡ������ظ�
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

//���������е��ַ���
function CPasswdLife_DecryptChr()
{
    var i = 0;
    var j = 0;
    var c = ""; //��ǰ�ַ�
    var cCode = 0; //��ǰ�ַ��� Unicode ����
    var found = false; //��ǰ�ַ��Ƿ��������е�ĳ��λ�ô��ڣ�ĳ��λ����ָλ�� 0 ����ǰ�ַ���ǰһ��λ��
    for (i=0; i<this.passwd.length; i++)
    {
        c = this.passwd.substr(i, 1);
        cCode = c.charCodeAt(0);
        if (cCode>=48 && cCode<=57)
        {
            //����
            this.numCnt++;
        }
        else if (cCode>=97 && cCode<=122)
        {
            //Сд��ĸ
            this.lAlphaCnt++;
        }
        else if (cCode>=65 && cCode<=90)
        {
            //��д��ĸ
            this.uAlphaCnt++;
        }
        else
        {
            //�����ַ�
            this.othersChrCnt++;
        }
        
        //���������в��ظ����ַ���
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

//ִ���������������������÷�
//������ȱʡ����
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
    
    //������Ч���ȼƷ�
    //����������߷֣������ٸ�������ָ����м���    
    if (this.activeLen >= 10)
    {
        score = 100;
    }
    else
    {
        score = this.activeLen * 10;
    }
    
    //�����в��ظ����ַ����Ʒ�
    if (this.activeChrCnt < 4)
    {
        score -= 40;
    }
    else if (this.activeChrCnt < 6)
    {
        score -= 10;
    }
    
    //���봿�������
    if (this.numCnt == this.passwd.length)
    {
        //������
        score -= 20;
    }
    
    //�ַ������������ַ����������Ϊ�����֡�Сд��ĸ����д��ĸ�������ַ�
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
    
    //�ַ����ͼƷ�
    if (this.chrTypeCnt < 2)
    {
        score -= 10;
    }
    
    return score;
}