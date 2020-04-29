##### 关系
- 自反的
> 若对每个元素a∈A有(a,a)∈R,那么定义在集合A上的关系R称为**自反的**.

![equ](http://latex.codecogs.com/gif.latex?\forall\mathbf{a}\(\(a,a\)\in\mathbf{R}\))

- 传递的
> 若对任意a,b,c∈A, (a,b)∈R并且(b,c)∈R则(a,c)属于R,那么定义在集合A上的关系R称为传递的

![equ](http://latex.codecogs.com/gif.latex?\forall\mathbf{a}\forall\mathbf{b}\forall\mathbf{c}\(\(\(a,b\)\in\mathbf{R}\land\(b,c\)\in\mathbf{R}\)\to\(a,c\)\in\mathbf{R})

- 对称的
> 对任意a,b∈A,若只要(a,b)∈R就有(b,a)∈R,则称定义在集合A上的关系R为对称的

![equ](http://latex.codecogs.com/gif.latex?\forall\mathbf{a}\forall\mathbf{b}\(\(a,b\)\in\mathbf{R}\to\(b,a\)\in\mathbf{R}\))

- 反对称的
> 对任意a,b∈A,若(a,b)属于R且(b,a)属于R,一定有a=b,则称定义在集合A上的关系R为反对称的

![equ](http://latex.codecogs.com/gif.latex?\forall\mathbf{a}\forall\mathbf{b}\(\(a,b\)\in\mathbf{R}\land\(b,a\)\in\mathbf{R}\)\to\(a=b\))

##### 关系的合成
> 设R是从集合A到集合B的关系，S是从集合B到集合C的关系。R与S的合成是由有序对(a,c)的集合构成的关系，其中a∈A,c∈C,并且
> 存在一个b∈B的元素，使得(a,b)∈R且(b,c)∈S。

![equ](http://latex.codecogs.com/gif.latex?\mathbf{S}\circ\mathbf{R})

> 关系R的n次幂(n=1,2,...)递归定义

![equ](http://latex.codecogs.com/gif.latex?\mathbf{R}^1=\mathbf{R})
和
![equ](http://latex.codecogs.com/gif.latex?\mathbf{R}^\mathbf{n+1}=\mathbf{R}^n\circ\mathbf{R})

> 集合A上的关系R是传递的，当且仅当对n=1,2,3,...有
![equ](http://latex.codecogs.com/gif.latex?\mathbf{R}^n\subseteq\mathbf{R})

##### n元关系
> 设A和B是集合，一个从A到B的二元关系R是AXB的子集

![equ](https://latex.codecogs.com/gif.latex?\{\mathbf{aRb}|\(a,b\)\in\mathbf{R},a\in\mathbf{A},b\in\mathbf{B}\})

> 设A1,A2,...,An是集合。定义在这些集合上的n元关系是A1XA2X...XAn的子集。这些集合A1,A2,...,An称为关系的域，n称为关系的阶。

##### n元关系运算
- 选择运算符
> 设R是一个n元关系，C是R中元素可能满足的一个条件。那么选择运算符
> ![equ](https://latex.codecogs.com/gif.latex?\mathbf{S}_C)
> 将n元关系R映射到R中满足条件C的所有n元组构成的n元关系。

- 投影
> 投影 ![equ](https://latex.codecogs.com/gif.latex?P_{i_1,i_2,...,i_m})
> 其中i1<i2<...<im,将n元组(a1,a2,...,an)映射到m元组
> ![equ](https://latex.codecogs.com/gif.latex?\(a_{i_1},a_{i_2},...,a_{i_m}\))
> , 其中 ![equ](https://latex.codecogs.com/gif.latex?m\leqslant{n})
> 即,投影 ![equ](https://latex.codecogs.com/gif.latex?P_{i_1,i_2,...,i_m})
> 删除了n元组的n-m个分量，保留了第i1,i2,...im个分量

- 连接运算
> 设R是m元关系，S是n元关系。连接运算
> ![equ](https://latex.codecogs.com/gif.latex?J_p\(R,S\))
> 是m+n-p元关系，其中p<=m,p<=n,它包含了所有的(m+n-p)元组
> ![equ](https://latex.codecogs.com/gif.latex?\(a_1,a_2,...,a_{m-p},c_1,c_2,...,c_p,b_1,b_2,...,b_{n-p}\))
> ,其中m元组
> ![equ](https://latex.codecogs.com/gif.latex?\(a_1,a_2,...,a_{m-p},c_1,c_2,...,c_p\))
> 属于R且n元组
> ![equ](https://latex.codecogs.com/gif.latex?\(c_1,c_2,...,c_p,b_1,b_2,...,b_{n-p}\))
> 属于S。
> 即，连接运算符![equ](https://latex.codecogs.com/gif.latex?J_p) 将m元组的后p个分量与n元组的前p个分量相同的第一个关系中
> 的所有m元组和第二个关系的所有n元组组合起来产生一个新的关系。

##### 关系的表示
> 用0-1矩阵表示一个有穷集之间的关系。假设R是从
> ![equ](https://latex.codecogs.com/gif.latex?A=\{a_1,a_2,...,a_m\})
> 到
> ![equ](https://latex.codecogs.com/gif.latex?B=\{b_1,b_2,...,b_n\})
> 的关系。关系R可以用矩阵
> ![equ](https://latex.codecogs.com/gif.latex?M_R=[m_{ij}])
> 来表示，其中
> ![equ](https://latex.codecogs.com/gif.latex?m_{ij}=\begin{cases}1\quad\(a_i,b_j\)\in{R}\\0\quad\(a_i,b_j\)\notin{R}\end{cases})