### CPU寄存器
#### 普通寄存器General Purpose Registers(GPRs)
> 16个,64bit

| 64-bit register| lowest 32bits | lowest 16bits | lowest 8bits |
|:---:|:---:|:---:|:---:|
| rax | eax | ax | al |
| rbx | ebx | bx | bl |
| rcx | ecx | cx | cl |
| rdx | edx | dx | dl |
| rsi | esi | si | sil |
| rdi | edi | di | dil |
| rbp | ebp | bp | bpl |
| rsp | esp | sp | spl |
| r8 | r8d | r8w | r8b |
| r9 | r9d | r9w | r9b |
| r10 | r10d | r10w | r10b |
| r11 | r11d | r11w | r11b |
| r12 | r12d | r12w | r12b |
| r13 | r13d | r13w | r13b |
| r14 | r14d | r14w | r14b |
| r15 | r15d | r15w | r15b |

#### 栈指针寄存器Stack Pointer Register(RSP)
> `rsp`指向栈顶,此寄存器不能用于数据或其他用途.

#### 基指针寄存器Base Pointer Register(RBP)
> `rbp`作为函数调用基址,此寄存器不能用于数据或其他用途.

#### 指令指针寄存器Instruction Pointer Register(RIP)
> `rip`指向下一条要执行的指令.

#### 标识符寄存器Flag Register(rFlags)
> 64bit. 用于状态及CPU控制信息, 由CPU在每次指令执行后更新, 不可由程序直接访问.

#### XMM寄存器
> 专门处理32bit、64bit浮点操作和单指令多数据(Single Instruction Multiple Data(`SIMD`))指令.
> 128bit, xmm0-xmm15. 最近一些X86-64处理器支持256bit XMM寄存器