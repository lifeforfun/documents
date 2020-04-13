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
    4. `Sec-WebSocket-Accept`: 可选
    5. `Sec-WebSocket-Protocol`:可选
    6. `Sec-WebSocket-Extensions`:可选
> 如果服务端不支持客户端发送的`Sec-WebSocket-Version`版本，服务端可以响应一到多个
> `Sec-WebSocket-Version`头字段以表达服务端期望获得的版本号，此时客户端可使用新版本号重新发起握手
> 示例：
```text
# Request
GET /chat HTTP/1.1
Host: server.example.com
Upgrade: websocket
Connection: Upgrade
...
Sec-WebSocket-Version: 25

#Response
HTTP/1.1 400 Bad Request
...
Sec-WebSocket-Version: 13
Sec-WebSocket-Version: 8, 7

# Request repeat handshake
GET /chat HTTP/1.1
Host: server.example.com
Upgrade: websocket
Connection: Upgrade
...
Sec-WebSocket-Version: 13
```

#### 数据帧
> 不管是否通过TLS通道客户端都要遮掩(mask)数据帧,否则服务端一旦收到未遮掩的帧要立刻断开连接。
> 此时服务端可响应1002(protocol error)。
> 而服务端必须不遮掩数据帧，否则客户端可响应1002(protocol error)。

> 基本帧协议定义了:帧类型操作码,载荷长度,"扩展数据"和"应用数据"的位置信息,而这两种数据组成了"载荷数据"。
> 保留了固定的位和操作码用作将来协议扩展.
> 在客户端或服务端发送挥手帧之前的任何时间其都可以发送数据帧。

> 帧格式:
```text
   0                   1                   2                   3
  0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
 +-+-+-+-+-------+-+-------------+-------------------------------+
 |F|R|R|R| opcode|M| Payload len |    Extended payload length    |
 |I|S|S|S|  (4)  |A|     (7)     |             (16/64)           |
 |N|V|V|V|       |S|             |   (if payload len==126/127)   |
 | |1|2|3|       |K|             |                               |
 +-+-+-+-+-------+-+-------------+ - - - - - - - - - - - - - - - +
 |     Extended payload length continued, if payload len == 127  |
 + - - - - - - - - - - - - - - - +-------------------------------+
 |                               |Masking-key, if MASK set to 1  |
 +-------------------------------+-------------------------------+
 | Masking-key (continued)       |          Payload Data         |
 +-------------------------------- - - - - - - - - - - - - - - - +
 :                     Payload Data continued ...                :
 + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
 |                     Payload Data continued ...                |
 +---------------------------------------------------------------+

FIN:  1位,指明是否是一条消息中的最后一个分片

RSV1, RSV2, RSV3:  各1位,除非商定了非0的值来指定一个扩展,否则是0.
如果接收到未定义的非0值,则必须以连接失败结束.

Opcode:  4位,定义了"载荷数据"
    %x0 表示一个延续帧(continuation frame)
    %x1 表示一个文本帧(text frame)
    %x2 表示一个二进制帧(binary frame)
    %x3-7 保留值，将来作为非控制帧
    %x8 表示断开连接
    %x9 表示ping
    %xA 表示pong
    %xB-F 保留值，将来作为控制帧

Mask: 1位,标识数据是否遮掩处理。1: masking-key里会包含用于去遮掩(unmasking)载荷数据的掩码

Payload length: 7位,7+16位,7+64位,载荷长度,多字节长度值以大端序表示
    第一个字节值: 
        0-125: 则其代表载荷数据长度
        126: 后续2字节代表一个16位无符号整数来表示载荷数据长度
        127: 后续8字节代表一个64位无符号整数(最高有效位most significant bit必须是0)来表示载荷长度

Masking-key: 0-4字节, 如果Mask设置为1则有一个32位值
    
Payload data: (x+y)字节
    Extension data: x字节,当握手时指定了扩展时不为0. 扩展数据长度必须由扩展字段指定或被计算出来。
    Application data: y字节

```

#### 遮蔽/去遮蔽
> - j = i MOD 4
> - transformed-octet-i = original-octet-i XOR masking-key-octet-j
> - i、j均为数据的字节索引位置