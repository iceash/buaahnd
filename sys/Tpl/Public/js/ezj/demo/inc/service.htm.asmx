<%@ WebService Language="C#" Class="WebService" %>

using System;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;

[WebService(Namespace = "http://www.getezj.com/demo/")]
[WebServiceBinding(ConformsTo = WsiProfiles.BasicProfile1_1)]
public class WebService  : System.Web.Services.WebService
{

    [WebMethod]
    public string SayString(string name)
    {
        return "您好，" + name + "。";
    }

    [WebMethod]
    public string SayStringXml()
    {
        return @"<root>
                  <item>1</item>
                  <item>2</item>
                 </root>";
    }

    [WebMethod]
    public int SayInt()
    {
        return 135;
    }

    [WebMethod]
    public int[] SayIntArray()
    {
        return new int[]{1, 3, 5};
    }
}