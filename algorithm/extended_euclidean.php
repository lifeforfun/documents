<?php
/**
 * 扩展欧几里得
 * @link https://en.wikipedia.org/wiki/Extended_Euclidean_algorithm
 * @author: ZhangMingming
 * Date: 2020/3/6
 * Time: 上午8:58
 */

/**
 * @param $a
 * @param $b
 * @return array
 */
function extended_euclidean($a, $b)
{

    $s1 = 1;
    $s2 = 0;
    $t1 = 0;
    $t2 = 1;

    $r1 = $a;
    $r2 = $b;

    // Si = Si-2 - Si-1*Qi-1
    while ($r2!=0) {
        $quotient = (int)($r1/$r2);

        $s3 = $s1 - $quotient*$s2;
        $s1 = $s2;
        $s2 = $s3;

        $t3 = $t1 - $quotient*$t2;
        $t1 = $t2;
        $t2 = $t3;

        $r3 = $r1 - $quotient*$r2;
        $r1 = $r2;
        $r2 = $r3;
    }
    return [$s1, $t1, $r1];
}

// 要保证入参$a>$b;
var_dump(extended_euclidean(356, 252));