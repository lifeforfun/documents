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
- `Sec-WebSocket-Protocol`: (应用级别的)子协议选择器
- `Sec-WebSocket-Extensions`: 客户端支持的扩展列表
- `Origin`: 请求域
- `Sec-WebSocket-Key`: base64编码,由客户端发送，服务端用来生成证明其有效身份的验证串,生成规则:
    > BASE64(SHA1(websocket-key+GUID)) 
    > 其中SHA1生成的是160位(20字节)字串
- `Connection`
- `Upgrade`
- `Sec-WebSocket-Accept`: 用于指明服务器是否期望接受连接, 值即为使用`Sec-WebSocket-Key`+GUID生成的BASE64字串,
任何其他值都被视为不接受连接


> [`GUID`](https://www.rfc-editor.org/rfc/rfc4122)(Globally Unique Identifier):
> "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"