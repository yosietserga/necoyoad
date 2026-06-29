<?php

function memoizeRows($col_id) {
    static $columns_ready = [];
    if (in_array($col_id, $columns_ready))  return true;
    array_push($columns_ready, $col_id);
    return false;
}