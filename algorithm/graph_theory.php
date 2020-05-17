<?php
/**
 * 图论
 * @author: ZhangMingming
 * Date: 2020/5/16
 * Time: 下午2:48
 */

/**
 * 构造有向多重图邻接矩阵
 * @param $vertexCnt int 顶点数
 * @param $edges array [ [v边索引,u边索引],...]
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
        if (count($edge)>1) {
            list($v, $u) = $edge;
            $matrix[$v][$u] += 1;
        } else {
            // 环
            $matrix[$edge[0]][$edge[0]] += 1;
        }
    }

    return $matrix;
}

/**
 * 构造无向多重图关联矩阵
 * @param $vertexCnt int 顶点数组
 * @param $edges array [ [v边索引,u边索引],... ]
 */
function make_incidence_matrix_undirected_graph($vertexCnt, $edges)
{
    $matrix = [];
    $edgeCnt = count($edges);

    for($i=0;$i<$vertexCnt;$i++) {
        $line = [];
        for($j=0;$j<$edgeCnt;$j++) {
            $line[] = 0;
        }
        $matrix[] = $line;
    }

    foreach ($edges as $idx => $edge) {
        if (count($edge)>1) {
            list($v, $u) = $edge;
            $matrix[$v][$idx] += 1;
            $matrix[$u][$idx] += 1;
        } else {
            // 环
            $matrix[$edge[0]] += 1;
        }
    }

    return $matrix;
}