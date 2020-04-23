#### 命题逻辑原语
##### Conjunction

###### and-introduction
> 由横线上部分推导出下部分,上部分是前提,下部分是结论

![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\qquad\psi}{\phi\land\psi}\land\mathbf{i})

###### and-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\land\psi}{\phi}\land\mathbf{e1})

![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\land\psi}{\phi}\land\mathbf{e2})

###### double negation
![equ](http://latex.codecogs.com/gif.latex?\frac{\neg\neg\psi}{\psi}\neg\neg\mathbf{e})

![equ](http://latex.codecogs.com/gif.latex?\frac{\psi}{\neg\neg\psi}\neg\neg\mathbf{i})

###### implies-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\quad\phi\to\psi}{\psi}\to\mathbf{e})

![*modus tollens*(MT)](http://latex.codecogs.com/gif.latex?\frac{\phi\to\psi\quad\neg\psi}{\neg\phi}\mathbf{ML})

![equ](http://latex.codecogs.com/gif.latex?\frac{_{\phi}^{_{...}^{\psi}}}{\phi\to\psi}\to\mathbf{i})