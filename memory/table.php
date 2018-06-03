<?php
/**
 * Created by PhpStorm.
 * User: youth
 * Date: 2018-06-02
 * Time: 17:02
 */
//创建内存表
$table = new swoole_table(1024);

//内存表增加一列
$table->column('id', $table::TYPE_INT, 4);
$table->column('name', $table::TYPE_STRING, 64);
$table->column('age', $table::TYPE_INT, 3);
$table->create();

$table->set('singwa', ['id'  => 1, 'name' => 'singwa', 'age' => 30]);

//另外一种情况
$table['singwa2'] = [
    'id' => 2,
    'name' => 'singwa',
    'age' => 30,
];
$table->incr('singwa', 'age', 2);
print_r($table->get('singwa'));

$table->decr('singwa2', 'age', 3);
print_r($table->get('singwa2'));

