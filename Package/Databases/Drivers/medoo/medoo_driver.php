<?php

use \Databases\Driver;
class medoo_driver implements Driver{


	private $_table = '';
	private $_field = '*';
	private $_where = '';
	private $_limit = '';
	private $_order = '';


	public function __construct($db_config){

		$this->_db_config = $db_config;

		$db_file = __DIR__.'/medoo.php';
		trace_add('load_file',$db_file);	//记录加载文件
		require_once($db_file);
	}


	public function _connection(){
		$db_config = $this->_db_config;
		$config['database_type'] = $db_config['dbdriver'];
		$config['server'] = $db_config['hostname'];
		$config['database_name'] = $db_config['database'];
		$config['username'] = $db_config['username'];
		$config['password'] = $db_config['password'];
		$config['charset'] = $db_config['char_set'];
		$config['port'] = $db_config['port'];
		$config['prefix'] = $db_config['prefix'];
		//try{
			$medoo = new medoo($config);
			$this->db = $medoo;
			return $this;
		//}catch(Exception $e){
		//	show_error('Message: ' .$e->getMessage());
		//}
	}

	public function _set_table($table){
		if(!empty($table)) $this->_table = $table;
		return 'this';
	}

	public function _get_table(){
		return $this->_db_config['prefix'].$this->_table;
	}

	public function where($where = null){
		if(!empty($where)){
			$this->_where = $where;
		}
		return 'this';
	}
	public function order($order = null){
		if(!empty($order)) $this->_order = $order;
		return 'this';
	}
	public function limit($limit = null){
		if(!empty($limit)){
			$this->_limit = $limit;
		}
		return 'this';
	}
	public function field($field = null){
		if(!empty($field)){
			$this->_field = $field;
		}
		return 'this';
	}

	public function select($table = null,$fields = null,$where = null){
		empty($table) ? $table = $this->_table : $table;
		empty($fields) ? $fields = $this->_field : $fields;


		$where = $this->_self_get_where();

		$result = $this->db->select($table,$fields,$where);
		return $result;
	}

	public function find($fields = null,$where = null){
		$table = $this->_table;
		empty($fields) ? $fields = $this->_field : $fields;
		empty($where) ? $where = $this->_where : $where;
		if($this->_order) $where['ORDER'] = $this->_order;
		$result = $this->db->get($table,$fields,$where);
		return $result;
	}

	public function add($data = null){
		empty($table) ? $table = $this->_table : $table;
		empty($data) ? $data = $this->_data : $data;

		$result = $this->db->insert($table,$data);
		return $result;
	}
	public function delete(){
		$table = $this->_table;
		if(empty($this->_where)) return false;	//防止删除全部

		$where = $this->_self_get_where();

		show($where);
		$result = $this->db->delete($table,$where);
		return $result;
	}

	public function _self_get_where(){
		$where = $this->_where;
		if(!empty($this->_limit)) $where['LIMIT'] = $this->_limit;
		if(!empty($this->_order)) $where['ORDER'] = $this->_order;

		return $where;
	}

	public function _sql(){
		return str_replace('"', '', $this->db->last_query());
	}

	public function error(){
		$error = $this->db->error();

		if(!empty($error[2]))
			$error_text = $error[2];

		return $error_text;
	}



}