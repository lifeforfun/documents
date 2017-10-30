## 1.恢复本地误删的文件
> 1. git reset HEAD (要恢复的文件路径)
> 2. git checkout (要恢复的文件路径)


## 2.设置分支
> 1. git push 远程名 新分支名 : 新建远程分支并将使用本地当前分支作为根仓库
> 2. git branch --set-upstream-to 远程名/远程分支 分支名 : 新建分支并与远程分支关联
> 3. git checkout 本地分支 : 设置当前分支
> 4. git pull 远程名 : 更新代码到本地 