<?php
/**
 * 任意进制转换
 * @author: ZhangMingming
 * Date: 2020/5/16
 * Time: 下午2:05
 */

/**
 * @param $num int
 * @param $toBase int 2-32
 */
function baseConvert($num, $toBase)
{
    static $map = '0123456789abcdefghijklmnopqrstuvwxyz';
    $str = '';
    do {
        $str = $map{$num%$toBase}.$str;
        $num = floor($num/$toBase);
    } while($num>0);
    return $str;
}

echo baseConvert(0, 26),PHP_EOL;
echo baseConvert(25, 26),PHP_EOL;
echo baseConvert(26, 26),PHP_EOL;
echo baseConvert(27, 26),PHP_EOL;
echo baseConvert(999, 26),PHP_EOL;