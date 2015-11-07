<?php

function CheckEmptyString($C_char) {
    if (!is_string($C_char)) return false; //判断是否是字符串类型
    if (empty($C_char)) return false; //判断是否已定义字符串
    if ($C_char=='') return false; //判断字符串是否为空
    return true;
}

?>  