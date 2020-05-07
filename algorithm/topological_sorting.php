<?php
/**
 * 拓扑排序: 由偏序集得到全序集
 * @author: ZhangMingming
 * Date: 2020/5/7
 * Time: 下午4:39
 */

/**
 * 寻找极小元
 * @param $posetArr
 * @return null|string
 */
function pop_minimal_element(&$posetArr)
{
    foreach ($posetArr as $large => &$smallArr) {
        // 没有更小元
        if (empty($smallArr)) {
            // 此处跳过当前循环是为了找到下一个极小元，等所有极小元找完后再将数组key中的大元元素依次弹出
            continue;
        }
        foreach ($smallArr as $k => $small) {
            if (!isset($posetArr[$small])) {
                unset($smallArr[$k]);
                return $small;
            }
        }
    }
    foreach ($posetArr as $large => $smallArr) {
        unset($posetArr[$large]);
        return $large;
    }
    return null;
}

/**
 * @param $posetArr
 * @return array
 */
function topological_sort($posetArr)
{
    $totalOrderSet = [];
    while ($me = pop_minimal_element($posetArr)) {
        if (!in_array($me, $totalOrderSet)) {
            $totalOrderSet[] = $me;
        }
    }
    return $totalOrderSet;
}

$ch = fopen('php://stdin', 'r');
if (!$ch) {
    exit('failed to open input stream');
}
echo 'Read poset elements, one ordered pair each line. Blank line to end input.',PHP_EOL;
echo 'Each ordered pair separated by comma, the large one at first, the small one at second.',PHP_EOL;
$posets = [];
while ($cnt = fgets($ch)) {
    if (!($cnt = trim($cnt, " \n"))) {
        break;
    }
    list($large, $small) = explode(',', $cnt);
    if (!isset($posets[$large])) {
        $posets[$large] = [];
    }
    $posets[$large][] = $small;
}
fclose($ch);

foreach (topological_sort($posets) as $small) {
    echo $small,PHP_EOL;
}