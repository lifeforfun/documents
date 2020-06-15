<?php
/**
 * 排序算法
 * @author: ZhangMingming
 * Date: 2020/6/9
 * Time: 上午9:03
 */

/**
 * 快速排序：
 * 1. 选取第一个元素，其余值按左大右小分列两边
 * 2. 递归排序两边的数组
 */
function quick_sorting($arr)
{
    $sort = function ($arr) use(&$sort) {
        $num = count($arr);
        if ($num<2) {
            return $arr;
        }
        // 普通快排
        // $rk = 0;
        // 随机快排
        $rk = rand(0, $num-1);
        $rv = $arr[$rk];
        $left = $right = [];
        foreach ($arr as $k => $v) {
            if ($rv>$v) {
                $left[] = $v;
            } else {
                $right[] = $v;
            }
        }

        return array_merge(
            $sort($left),
            $sort($right)
        );
    };
    return $sort($arr);
}

foreach ([
    [56,12,3,1,99,],
    [56,18,3,1,99,],
    [1,2,3,4,5,],
    [5,4,3,2,1,0],
         ] as $sort) {
    echo 'quick sort:[',implode(',', $sort),']',PHP_EOL;
    echo 'sort result:[',implode(',', quick_sorting($sort)),']',PHP_EOL;
}
