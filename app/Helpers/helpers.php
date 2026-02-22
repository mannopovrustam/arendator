<?php

function tableName($table){
    $columns = (object)[];
    foreach (\DB::select("SHOW COLUMNS FROM $table") as $column) {
        $d = $column->Field;
        $columns->$d = '';
    }
    return $columns;
}
