<?php
/*
 *
 *
 */
interface driver {

	public function _connection();	//连接数据库
	public function _sql();	//获取最后一条sql
	public function error();	//获取错误信息
	public function select();	//查询数据
	public function where();	//设置条件
	public function find();		//查询单条数据
	public function add($data);		//添加数据
	public function _get_table();		//获取表名
	public function _set_table($table);		//设置当前操作表
	public function limit($limit);		//设置limit
	public function delete();		//删除
	public function order($order);		//排序



}