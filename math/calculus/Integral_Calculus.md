---
title: 积分
---
### 积分
> `积分是导数的逆运算`
> $$f'(x)=\frac{d}{dx}\int_0^xf(t)dt$$
> $\int{f(x)dy}$ 读作"求 $f(x)$ 关于 $y$ 的积分"

### 不定积分公式
> $$\int{f(x)dx}=F(x)+C$$
> (其中 $F(x)$ 为`原函数`)

### 定积分公式
> $$\int_a^bf(x)dx=F(x)\vert_a^b=F(b)-F(a)$$

### 用例
#### 求圆锥体积
> 将圆锥竖立，横向将圆锥切割为为高为 $\Delta{h}$ 的小圆柱体
$$
\int_0^h\pi(\frac{x}{h}r)^2dx
=
\pi\times\frac{1}{3}x^3\times\frac{r^2}{h^2}\vert_0^h
=
\frac{1}{3}\pi{hr^2}
$$
#### 求圆面积公式
> 已知圆周长 $2\pi{r}$
> 将圆分割为顶点是圆心、底为圆弧的小三角形
$$
\int_0^{2\pi{r}}\frac{r}{2}\times1\times{dx}
=
\frac{xr}{2}\vert_0^{2\pi{r}}
=
\pi{r^2}
$$