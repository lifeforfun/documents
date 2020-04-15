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

Payload length: 7位,7+16位,7+64位,载荷长度(字节),多字节长度值以大端序表示
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

#### 分片
> 分片方案：
> 1. 一个未分片信息,设置FIN并且opcode非0
> 2. 一个分片信息,首帧未设置FIN且opcode非0,紧随其后的是0到多个
> 未设置FIN且opcode设为0的帧并且以设置FIN且opcode是0的帧结束.

> - "扩展数据"可能只出现在第一个分片中并应用到后续的分片;
> 或者出现在每个分片中并应用于特定的那个分片。
> - 分片中可能出现控制帧,控制帧必须不能切片
> - 分片信息必须按顺序传送给接收者
> - 一条消息的分片必须不能与其他消息的分片交错出现,除非指定了可以解释此交错现象的扩展
> - 一端必须有在分片消息中接收控制帧的能力
> - 发送端可能创建任意大小的非控制消息
> - 客户端、服务端必须支持接收分片以及非分片的消息
> - 由于控制帧不可切片,中间媒介不可尝试给控制帧切片
> - 中间媒介在消息中使用了保留值并且不清楚这些值的用途时不能更改消息的切片
> - 中间媒介不能在不清楚连接协商的扩展的用法时更改消息的切片.
> 同样地,中间媒介不理解建立WebSocket连接的WebSocket握手时不能更改此连接的任何消息的切片
> - 结论：一条消息的所有分片的类型一致,并且通过第一个分片的opcode确定.
> 由于控制帧不能被切片,所有一条消息内所有切片的类型必须是文本、二进制和保留opcode之一
>
> 小记：由于如果不允许插入控制帧,那么在一条很长的消息时一个ping帧将有很长的延迟,
> 所与需要支持在分片消息中间接收控制帧
>
> 实现小记: 如果没有出现任何扩展,接收端不需要缓存整个帧再处理它,
> 然而这个假设可能在将来的WebSocket扩展里总是成立

#### 控制帧
> 控制帧的opcode最高有效位是1.
> 已定义的opcode有0x8(Close),0x9(Ping),0xA(Pong). 0xB-0xF为保留值.
> 所有的控制帧最多能有125字节的载荷数据并且不能被切片

##### Close帧
> 此帧可包含内容体(在"应用数据"部分)用于指明挥手原因. 
> 如果包含内容体,其前2个字节是一个标识状态码的无符号整型(网络字节序,即大端),
> 其后是UTF-8编码的原因数据. 内容体不需要便于阅读,所以不能向用户展示.
> - 应用在发送挥手帧后**不能**发送其他消息帧
>
> 应用在接收到一个挥手帧后如果在之前没发送过挥手帧,需要发送一个挥手帧作为响应. (通常在响应挥手帧时发送其接收到的状态码)
> 这种处理应尽可能快. 然后并不保证一端在发送挥手帧后继续处理数据.
> 在双方都发送和接收到挥手帧后应断开WebSocket及其下层的TCP连接. 

##### Ping帧
> Ping帧可包含"应用数据". 除非接收到挥手帧,一端在接收到Ping帧后应响应一个Pong帧. 
> 在建立连接后、断开连接之前的任何时间都可以发送Ping帧.

##### Pong帧
> Pong帧的"应用数据"应与其应答的Ping帧内的一致.
> 一端在接收到一个Ping帧后但还未响应之前接收到的Ping帧,可以只响应最近的Ping帧.
> Pong帧可以被主动发送出去,这用于提供单向的心跳包. 此种主动发送的Pong帧不能被应答.

##### 数据帧
> 数据帧(即,非控制帧)通过最高位是0的opcode指定. 当前定义的数据帧opcode有0x1(文本),0x2(二进制). 
> 0x3-0x7的opcode作为非控制帧保留值.
> 数据帧携带应用层和/或扩展层数据. 其opcode决定了数据如何解析:
> - 文本:
>   数据载荷是UTF-8编码的文本. 
> - 二进制:
>   数据载荷是任意二进制数据,其如何被解析仅交由应用层负责.

#### 示例
> 未遮蔽的文本消息的单独帧:
> 0x81 0x05 0x48 0x65 0x6c 0x6c 0x6f (包含"Hello")
> 
> 已遮蔽的文本消息的单独帧:
> 0x81 0x85 0x37 0xfa 0x21 0x3d 0x7f 0x9f 0x4d 0x51 0x58 (包含 "Hello")
>
> 分片未遮蔽的文本消息:
> 0x01 0x03 0x48 0x65 0x6c (包含 "Hel")
> 0x80 0x02 0x6c 0x6f (包含 "lo")
>
> 未遮蔽的Ping请求及已遮蔽的Pong应答:
> 0x89 0x05 0x48 0x65 0x6c 0x6c 0x6f (包含内容体"Hello")
> 0x8a 0x85 0x37 0xfa 0x21 0x3d 0x7f 0x9f 0x4d 0x51 0x58 (包含内容体"Hello", 与ping内容体一致)
>
> 256字节二进制消息的单独未遮蔽帧:
> 0x82 0x7E 0x0100 [256字节二进制数据]
>
> 64KiB 二进制消息的单独未遮蔽帧:
> 0x82 0x7F 0x0000000000010000 [65536字节二进制数据]

#### 扩展性
> 0x3-0x7、0xB-0xF的opcode是"扩展数据"域. 帧头部的RSV1-RSV3比特位被这些扩展使用.
> 扩展用法说明(非详尽说明且不是强制约束):
> - 扩展数据可以放到应用数据之前的载荷数据中
> - 可以为每个帧分配保留比特位
> - 保留opcode可以被定义
> - 如果需要更多opcode值,保留比特位可以被分配给opcode域
> - 保留位或者"扩展"opcode可以定义分配"载荷数据"以外的位来实现更大的opcode定义或者为每帧指定更大的大小.

#### 发送、接收数据

##### 发送数据
> 1. 必须确保WebSocket连接在OPEN状态,任何时间连接状态变动后必须中断以下步骤.
> 2. 必须将数据封装为WebSocket帧. 如果发送的数据过大或者暂时不能获得完整的数据,可以将数据封装为一组帧来发送.
> 3. 包含数据的第一个帧的opcode必须正确设置.
> 4. 包含数据的最后一帧的FIN位必须设置为1.
> 5. 客户端发送的帧必须遮蔽处理.
> 6. 需要额外考虑任何商定的扩展.
> 7. 所有帧由其下层网络连接传输.

##### 接收数据
> 接收的数据必须是协议定义的帧. 接收到帧后先通过opcode确定帧类型.
> 确定帧是否是分片帧，如果是分片帧需要组装完整的消息数据. 
> 扩展可能更改数据的读取行为,甚至是消息边界.
> 服务端必须去遮蔽掉客户端发送的帧数据.

##### 断开连接
> 一端断开连接时其下的TCP连接以及TLS会话也要断开, 如果可行,丢弃最后接收的字节数据.
> 其下层的TCP连接通常由服务端断开以便其保持TIME_WAIT状态而不是由客户端保持
> (这可以避免在2倍最大生存时间(2MSL)内重建连接).
> 异常情况(如在特定时间内未收到服务端的TCP挥手)下客户端可初始化连接挥手. 