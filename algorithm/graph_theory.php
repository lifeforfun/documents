<?php
/**
 * 图论
 * @author: ZhangMingming
 * Date: 2020/5/16
 * Time: 下午2:48
 */

/**
 * 构造有向图多重图邻接矩阵
 * @param $vertexCnt int 顶点数
 * @param $edges array [ [v边->u边],...]
 * @return array
 */
function make_adjacent_matrix_directed_graph($vertexCnt,$edges)
{
    $matrix = [];
    for ($i=0;$i<$vertexCnt;++$i) {
        $line = [];
        for($j=0;$j<$vertexCnt;++$j) {
            $line[] = 0;
        }
        $matrix[] = $line;
    }

    foreach ($edges as $edge) {
        $matrix[$edge[0]][$edge[1]] += 1;
    }

    return $matrix;
}