<?php
/**
 * Created by ZhangMingming
 * @author: ZhangMingming
 * Date: 17-11-20
 * Time: 下午6:19
 */
function deamonize()
{
    umask(0);
    $pid = pcntl_fork();
    if (-1 === $pid) {
        exit('fork failed!' . PHP_EOL);
    } elseif (0 < $pid) {
        exit(0);
    }
    if (-1 === posix_setsid()) {
        exit('child process setsid failed!' . PHP_EOL);
    }
    // fork again to avoid SVR4 system regain the control of terminal
    $pid = pcntl_fork();
    if (-1 === $pid) {
        exit('fork again failed!' . PHP_EOL);
    } elseif (0 !== $pid) {
        exit(0);
    }
    $i = 0;
    while(1) {
        echo 'timer tick: ' . $i++ . PHP_EOL;
        sleep(10);
    }
}

deamonize();