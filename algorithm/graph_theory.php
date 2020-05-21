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
 * @param $edges array [ [v顶点索引,u顶点索引],...]
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
            // 环为边的度贡献2
            $matrix[$edge[0]][$edge[0]] += 2;
        }
    }

    return $matrix;
}

/**
 * 构造无向多重图关联矩阵
 * @param $vertexCnt int 顶点数组
 * @param $edges array [ [v顶点索引,u顶点索引],... ]
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
            // 环为边的度贡献2
            $matrix[$edge[0]][$idx] += 2;
        }
    }

    return $matrix;
}


/**
 * 基本法(套圈法)构造欧拉回路
 */
function euler_circuit($vertexCnt, $edges)
{
    $matrix = make_adjacent_matrix_directed_graph($vertexCnt, $edges);

    print_matrix($matrix);
}

function print_matrix($matrix)
{
    $rowCnt = count($matrix);
    $colCnt = count($matrix[0]);
    $lines  = [];
    for ($i=1;$i<=$rowCnt;++$i) {
        // 添加表头行
        if ($i===1) {
            $lines[] = '';
        }
        $lines[] = '';

        for($j=1;$j<=$colCnt;++$j) {
            if ($i===1) {
                if ($j===1) {
                    $lines[0] = make_width(' ');
                }
                $lines[0] .= make_width('b'.$j);
            }
            if ($j===1) {
                $lines[$i] .= make_width('a'.$i);
            }
            $lines[$i].= make_width($matrix[$i-1][$j-1]);
        }
    }
    echo implode(PHP_EOL, $lines);
}

function make_width($text, $width=4)
{
    if (strlen($text)<$width) {
        return str_pad($text, $width, ' ', STR_PAD_LEFT);
    }
    return $text;
}

euler_circuit(5, []);