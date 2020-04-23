<?php
/**
 * 截断二进制指数退避算法
 * @author: ZhangMingming
 * Date: 2020/4/23
 * Time: 上午9:47
 */

/**
 * @param $retryTime int 重试次数
 * @param $t int T时间,单位:微秒(即由单端到端传送一次的用时)
 * @return int 延迟微秒数
 */
function TruncatedBinaryExponentialBackoff($retryTime, $t)
{
    $k = min($retryTime, 10);
    $r = mt_rand(0, 2**$k-1);
    return $r*2*$t;
}