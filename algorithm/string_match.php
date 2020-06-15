<?php
/**
 * 子串匹配
 * @author: ZhangMingming
 * Date: 2020/5/28
 * Time: 下午1:07
 */

/**
 * occurrence function
 * @param $haystack
 * @param $char
 * @return int
 */
function occ($haystack, $char)
{
    $len = strlen($haystack);
    $mat = -1;
    for($i=0;$i<$len;++$i) {
        if ($haystack{$i}===$char) {
            $mat = $i;
        }
    }

    return $mat;
}

/**
 * @param $str
 * @return array
 */
function bmInitocc($str)
{
    $occ = [];
    for($i=0;$i<256;++$i) {
        $occ[chr($i)] = -1;
    }

    $len = strlen($str);
    for($i=0;$i<$len;++$i) {
        $occ[$str{$i}] = $i;
    }

    return $occ;
}

function bmPreprocess1()
{

}

/**
 * BM 算法(坏字符+好后缀叠加判断右移)
 * @url https://www.inf.hs-flensburg.de/lang/algorithmen/pattern/bmen.htm#section4
 */
function boyer_moore_matching()
{

}

/**
 * @param $needle
 * @param $needleLen
 * @return array
 */
function sunInitShift($needle, $needleLen)
{
    $shift = [];
    for($i=0;$i<256;++$i) {
        $shift[chr($i)] = $needleLen+1;
    }

    for($i=0;$i<$needleLen;++$i) {
        $shift[$needle{$i}] = $needleLen-$i;
    }

    return $shift;
}

/**
 * @url https://www.inf.hs-flensburg.de/lang/algorithmen/pattern/sundayen.htm
 * @param $haystack
 * @param $needle
 */
function sunday_matching($haystack, $needle)
{
    $hayLen = strlen($haystack);
    $neLen = strlen($needle);
    $shift = sunInitShift($needle, $neLen);

    if ($hayLen<$neLen) {
        return -1;
    }

    $i   = 0;
    $matOff = 0;
    while (($i+$matOff)<$hayLen) {
        // 已匹配
        if ($matOff==$neLen) {
            return $i;
        }

        if ($haystack{$matOff+$i}===$needle{$matOff}) {
            ++$matOff;
        } else {
            // 当前字符不匹配
            $next = $i+$matOff;
            if ($next>=$hayLen) {
                return -1;
            }
            $matOff = 0;
            $shifted = $shift[$haystack{$next}];
            $i += $shifted;
        }
    }
    return -1;
}

foreach ([
    ['fffffffff', 'fff'],
    ['fffffsfff', 'sf'],
    ['fffffsfff', 'fs'],
    ['fffffssff', 'ssf'],
    ['fffffssff', 'sssf'],
         ] as $mat)
{
    echo 'haystack:',$mat[0],', needle:',$mat[1],', match result:', sunday_matching($mat[0], $mat[1]),PHP_EOL;
}