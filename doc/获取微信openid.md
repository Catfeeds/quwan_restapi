 
**简要描述：** 

- 获取微信openid

**请求URL：** 
- ` https://restapi.qu666.cn/quwan/get_openid `
  
**请求方式：**
- post

**参数：** 
```
{
    appid: 小程序appid | 字符串 | 必填
    secret:  小程序secret | 字符串 | 必填
    js_code:  小程序登录时获取的 code | 字符串 | 必填
    grant_type:  填写为 authorization_code | 字符串 | 必填
} 

```




 **成功返回**
```
//正常返回的JSON数据包
{
      "openid": "OPENID",
      "session_key": "SESSIONKEY",
      "unionid": "UNIONID"
}

```

 **失败返回** 

```
//错误时返回JSON数据包(示例为Code无效)
{
    "errcode": 40029,
    "errmsg": "invalid code"
}

```

 **备注** 
```
返回的token是用户的登录信息,以后每次调用接口需要加在头信息里面,注意Bearer后面必须有一个空格
如:
authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJlYjRlZDY4MC1jOTAwLTExZTctOWJiMy1kMzU5ZWQwMjkyOTciLCJpYXQiOjE1MTA2MzkxNDQsIm5iZiI6MTUxMDYzOTE0NCwiZXhwIjoxNTExMjQzOTQ0LCJzdWIiOiIxMjM0NSJ9.RFbkpiPWfiYiAxawfSM485wL4cUV0701nrPB2AL0I4c

```
