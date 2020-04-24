#### 命题逻辑原语
##### Conjunction(合取)

###### and-introduction
> 由横线上部分推导出下部分,上部分是前提,下部分是结论

![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\qquad\psi}{\phi\land\psi}\land\mathbf{i})

###### and-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\land\psi}{\phi}\land\mathbf{e}_1)

![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\land\psi}{\phi}\land\mathbf{e}_2)

###### double negation
![equ](http://latex.codecogs.com/gif.latex?\frac{\neg\neg\psi}{\psi}\neg\neg\mathbf{e})

![equ](http://latex.codecogs.com/gif.latex?\frac{\psi}{\neg\neg\psi}\neg\neg\mathbf{i})

###### implies-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\quad\phi\to\psi}{\psi}\to\mathbf{e})

![*modus tollens*(MT)](http://latex.codecogs.com/gif.latex?\frac{\phi\to\psi\quad\neg\psi}{\neg\phi}\mathbf{ML})

![equ](http://latex.codecogs.com/gif.latex?\frac{_{\phi}^{_{\vdots}^{\psi}}}{\phi\to\psi}\to\mathbf{i})
> 上面这种又可以表示为下面这样

![equ](https://latex.codecogs.com/gif.latex?\vdash\quan\phi_1\to\(\phi_2\to\(...\to\(\phi_\mathbf{n}\to\psi\)...\))

##### Disjunction(析取)

###### or-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\phi}{\phi\lor\psi}\lor\mathbf{i}_1)

![equ](http://latex.codecogs.com/gif.latex?\frac{\psi}{\phi\lor\psi}\lor\mathbf{i}_2)

![equ](https://microsoft.codecogs.com/svg.latex?\frac{\phi\lor\psi\quad_{\chi}^{_{\vdots}^{\phi}}\quad_{\chi}^{_{\vdots}^{\psi}}}{\chi}\lor\mathbf{e})

##### Negation

###### bottom-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\bot}{\phi}\bot\mathbf{e})

###### not-elimination
![equ](http://latex.codecogs.com/gif.latex?\frac{\phi\qquad\neg\phi}{\bot}\neg\mathbf{e})

![equ](http://latex.codecogs.com/gif.latex?\frac{_{\bot}^{_{\vdots}^{\phi}}}{\neg\phi}\neg\mathbf{i})