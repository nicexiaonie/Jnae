<?php
use \Databases\Driver;
class jnae_driver implements Driver{
	public function __construct($db_config){

		$this->_db_config = $db_config;

		$db_file = __DIR__.'/'.$this->_db_config['dbdriver'].'.php';

		if(!is_file($db_file))
			show_error("错误：{$db_file} 不存在!");

		trace_add('load_file',$db_file);	//记录加载文件
		require_once($db_file);


	}

	public function _connection(){
		$db_config = $this->_db_config;
		$config['mysql_host'] = $db_config['hostname'];
		$config['mysql_name'] = $db_config['database'];
		$config['mysql_user'] = $db_config['username'];
		$config['mysql_pass'] = $db_config['password'];
		$config['charset'] = $db_config['char_set'];
		$config['mysql_prot'] = $db_config['port'];
		$config['mysql_pre'] = $db_config['prefix'];

		$class_name = $db_config['dbdriver'];
		try{
			$medoo = new $class_name(null,$config);

			$this->db = $medoo;
			return $this;
		}catch(Exception $e){
			show_error('Message: ' .$e->getMessage());
		}
	}
	public function _sql(){
		return $this->db->_sql();
	}
	public function error(){
		$result = $this->db->error();
		return $result;
	}
	public function select(){

		$result = $this->db->select();

		return $result;
	}
	public function where($where = null){
		$result = $this->db->where($where);
		return 'this';

	}
	public function find(){
		$result = $this->db->find();
		return $result;
	}
	public function add($data){
		$result = $this->db->add($data);
		return $result;
	}
	public function _get_table(){
		return $this->_db_config['prefix'].$this->_table;
	}
	public function _set_table($table){
		if(!empty($table)) {
			$this->_table = $table;
			$this->db->table($table);
		}
		return 'this';

	}
	public function limit($limit =null){

		$this->db->limit($limit);
		return 'this';

	}
	public function delete(){
		$result = $this->db->delete();
		return $result;
	}
	public function order($order = null){
		$result = $this->db->order($order);
		return 'this';

	}


}
