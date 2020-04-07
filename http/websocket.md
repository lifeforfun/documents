#### websocket 握手
```text
// 客户端
GET /chat HTTP/1.1
Host: server.example.com
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==
Origin: http://example.com
Sec-WebSocket-Protocol: chat, superchat
Sec-WebSocket-Version: 13
```

```text
// 服务端
HTTP/1.1 101 Switching Protocols
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Accept: s3pPLMBiTxaQ9kYGzzhZRbK+xOo=
Sec-WebSocket-Protocol: chat
```
> 数据是由一到多个帧组成，帧类型(6种已生效类型，10种保留类型)：
- 文本类型([`UTF-8`](https://www.rfc-editor.org/rfc/rfc3629))
- 二进制(由应用解析)
- 控制帧(不用于传输数据,而是传输协议信号)

#### 常用协议头
- [Request|Response]`Sec-WebSocket-Protocol`: 逗号分隔的(应用级别的)子协议选择器;指定服务端采纳的子协议
- [Request]`Sec-WebSocket-Extensions`: 客户端支持的(协议级)扩展列表
- [Request]`Origin`: 请求域
- [Request]`Sec-WebSocket-Key`: 16字节随机值base64编码,由客户端发送，服务端用来生成证明其有效身份的验证串,生成规则:
    > BASE64(SHA1(websocket-key+GUID)) 
    > 其中SHA1生成的是160位(20字节)字串
- [Request]`Connection`: 值是`Upgrade`
- [Request]`Upgrade`: 值是`websocket`
- [Response]`Sec-WebSocket-Accept`: 用于指明服务器是否期望接受连接, 值即为使用`Sec-WebSocket-Key`+GUID生成的BASE64字串,
任何其他值都被视为不接受连接
- [Request]`Sec-WebSocket-Version`: 值必须是13

> [`GUID`](https://www.rfc-editor.org/rfc/rfc4122)(Globally Unique Identifier):
> "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"

> `Sec-WebSocket-Accept`值验证失败、头信息丢失、HTTP状态码非101都不会建立连接

> 挥手流程： Peer A -> 发送挥手控制帧 -> Peer B -> 发送挥手响应帧 -> Peer A 断开连接

##### 客户端握手请求需要发送的头信息
1. HTTP/1.1或更高的GET请求
2. `Host`
3. `Upgrade`:值"websocket"
4. `Connection`:值"Upgrade"(大小写不敏感)
5. `Sec-WebSocket-Key`
6. `Sec-WebSocket-Version`:值13
7. `Origin`:可选，不携带不应当认为是浏览器客户端
8. `Sec-WebSocket-Protocol`:可选
9. `Sec-WebSocket-Extensions`:可选
10. [RFC2616](https://www.rfc-editor.org/rfc/rfc2616)定义的其他头字段

##### 服务端响应握手
1. 连接发生于HTTPS端口，则要使用加密通道
2. 可执行额外的客户端授权，如响应401状态码及`WWW-Authenticate`头字段
3. 可在第2步之前或之后响应3xx状态码重定向客户端
4. 建立如下信息:
    > - 服务器如果不接受`Origin`(值为小写)头字段定义的源,则返回相应的状态码(如403 Forbidden)并终止握手
    > - 服务器不接受`Sec-WebSocket-Version`,则终止握手并返回相应的状态码(如426 Upgrade Required)
    > 以及一个`Sec-WebSocket-Version`以表明服务器接受的版本号
    > - 资源名(resource name),即GET方法的"Request-URI"部分，如果对应的服务不可用，则返回相应的状态码(如404)并终止握手
    > - 子协议,如果客户端不携带此头字段或服务端不接受指定的任何子协议，则其值只能是null.
    > 缺省此头字段与null值相等并且服务端不允许再发送`Sec-WebSocket-Protocol`头字段.
    > - 扩展字段,服务端可响应一个或多个支持的扩展，但值必须由客户端发送的`Sec-WebSocket-Extensions`中选取.
5. 服务端接受握手后必须响应合法的HTTP响应
    1. 包含101状态码的状态行,如"HTTP/1.1 101 Switching Protocols"
    2. `Upgrade`:值"websocket"
    3. `Connection`:值"Upgrade"
    4. `Sec-WebSocket-Accept`