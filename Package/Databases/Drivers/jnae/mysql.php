<?php
/*****	MYSQL 3.0
******	修改where运行机制，修改save排除主键更新,修改自动释放mysql链接，修改add方法成功后返回插入ID
******	
******	添加count、max、min、avg、sum、getField、setField、setInc、setDec、select_one方法
******
******	3.1
******	添加get_create_table、get_create_info方法
******	3.2
******	改善add方法，支持同时多条数据添加，
******				
******			
*****/
class mysql {
	private $db_host='188.188.1.240';		//数据库地址
	private $db_prot='3306';		//数据库端口
	private $db_user='root';		//数据库用户名
	private $db_pass='123456';		//数据库密码
	private $db_name='study';		//数据库名称
	private $tb_pre='';			//数据表前缀

	private $database='mysql';		//数据库版本
	private $charset='utf8';		//数据库校对字符集
	private $major='';			//主键字段(由系统自动判断，不需要设置)
	private $basic;			//表字段

	private $tb_field=array();	//表字段集(含主键,自动获取)
	private $error='';			//错误信息(由系统自动生成，不需要设置)
	// 数据库表达式
	protected $exp = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=');
	public $sql='';	//最后执行sql
	/*	构造函数：
	 *		连接数据库，选中要操作的库，设置字符集，生成表结构，获取主键
	 *
	 */
	public function __construct($db_table=null,$config=null){

		if(!empty($config['mysql_host'])) $this->db_host=$config['mysql_host'];
		if(!empty($config['mysql_prot'])) $this->db_prot=$config['mysql_prot'];
		if(!empty($config['mysql_user'])) $this->db_user=$config['mysql_user'];
		if(!empty($config['mysql_pass'])) $this->db_pass=$config['mysql_pass'];
		if(!empty($config['mysql_name'])) $this->db_name=$config['mysql_name'];

		if(!empty($config['mysql_pre'])) $this->tb_pre=$config['mysql_pre'];
		if(!empty($config['charset'])) $this->charset=$config['charset'];

		if($db_table){
			return $this->table($db_table);
		}else{
			$this->link();
			//@mysql_select_db($this->db_name);
			return $this;
		}
		
	}
	/*
	 *	更换数据库
	 */
	public function _change(){
	
	}
	/*	数据库连接
	 *
	 */
	public function table($db_table=null){
	
		if($db_table) $this->db_table=$this->tb_pre.$db_table;
	
		$this->link();
		$this->init();
		return $this;
	}
	/*	数据库连接
	 *		连接数据库，校对字符集
	 *
	 */
	public function link(){
		@$link=mysql_connect($this->db_host.':'.$this->db_prot,$this->db_user,$this->db_pass);	//连接数据库
		if($link){
			$this->link[]=$link;
			return $this;
		}else{
			$this->error='数据库连接失败！请检查数据库配置是否正确！';
		}
		
	}
	/*	对数据表进行初始化操作
	 *		选中要操作的库，设置字符集，生成表结构，获取主键
	 *
	 */
	public function init(){
	
			@mysql_select_db($this->db_name,$this->link[count($this->link)-1]);
			if(!$this->query('SET NAMES '.$this->charset)){
				$this->error='字符集校对失败！';
				return false;
			}
			if(empty($this->db_table)){
				$this->error='表不存在！';
				return false;
			}
			$result=$this->query('desc '.$this->db_table);
			$this->tb_field=$this->getArray($result);
			$basic=array();
			foreach($this->tb_field as $k=>$v){
				$basic[]=$v['Field'];
				if($v['Key']=='PRI'){
					$this->major=$v['Field'];
				}
			}
			$this->basic=$basic;
			return $this;
	}
	/*	设置要查找的条件，可选
	 *	参数类型：string array\
	 *	返回Mysql对象
	 */
	private $where='';
	public function where($where=''){
		if($where==null){
			$this->where='';
			return $this;
		}
		if(is_string($where)){
			$this->where=' WHERE '.$where;
			return $this;
		}
		if(is_array($where)){
			$str='';
		
			$where['_relation'] ? $relation=$where['_relation'] : $relation=' and ';
			unset($where['_relation']);
			
			if(@$where['_string']){
				
				
				@$str=$relation.$where['_string'];
				unset($where['_string']);
			}
			$whereText='';
			foreach($where as $key=>$value){
				if(in_array($key,$this->basic)){
					if(is_string($value) || is_int($value)){
						$whereText.='`'.addslashes($key).'` = \''.addslashes($value).'\' AND ';
					}
					if(is_array($value)){
						if(strtolower($value[0]) == 'in' || strtolower($value[0]) == 'not in'){
							if(is_string($value[1])) $whereText.=$key.' '.$value[0].' ('.$value[1].') ';
							if(is_array($value[1])) $whereText.=$key.' '.$value[0].' ('.implode(',',$value[1]).') ';
						}
						if(strtolower($value[0]) == 'like'){
							if(is_string($value[1])) $whereText.=$key.' '.$value[0].' \''.$value[1].'\'  ';
						}
						if(array_key_exists(strtolower($value[0]),$this->exp)) $whereText.=$key.' '.$this->exp[strtolower($value[0])].' '.$value[1];
						if(in_array(strtolower($value[0]),$this->exp)) $whereText.=$key.' '.strtolower($value[0]).' '.$value[1].'  ';
						if(strtolower($value[0]) == 'between' || strtolower($value[0]) == 'not between'){
							if(is_string($value[1])) $value[1]=explode(',',$value[1]);
							$whereText.=$key.' '.strtolower($value[0]).' \''.$value[1][0].'\' AND \''.$value[1][1].'\'';
						}
						$whereText.=' AND ';
					}
				}
			}
			$this->where='WHERE '.rtrim($whereText,'AND ').$str;
			return $this;
		}
		$this->where='';
		return $this;
	}
	/*	设置查询行数，
	 *	参数类型：int
	 *	返回Mysql对象
	 */
	private $limit='';
	public function limit($limit=''){
		empty($limit) ? $this->limit=$limit : $this->limit='limit '.$limit;
		return $this;
	}

	/*	设置查找的字段名，可选
	 *	参数类型：string
	 *	返回Mysql对象
	 */
	private $field='*';
	public function field($field='*'){
		empty($field) ? $this->field='*' : $this->field=$field;
		unset($field);
		return $this;
	}
	/*	设置排序，可选
	 *	参数类型：string
	 *	返回Mysql对象
	 */
	private $order='';
	public function order($order=''){
		$this->order=$order;
		return $this;
	}
	/*	查询方法，(连贯操作放到最后)
	 *	无参数
	 *	返回值：数组
	 */
	public function select($clear=null){
		$sql='select '.$this->field.' from '.$this->db_table.' '.$this->where.' '.$this->order.' '.$this->limit.' '.$this->group;

		$result=$this->query($sql);

		return $this->getArray($result);
	}
	/*	
	 *	分组
	 *	
	 */
	private $group='';
	public function group($group){
		$this->group=$group;
		return $this;
	}
	//获取单条记录，返回一维数组
	public function find(){
		$sql='select '.$this->field.' from '.$this->db_table.' '.$this->where.' limit 1';
		$result=$this->query($sql);
		$data=$this->getArray($result);
		return @$data[0];
	}
	/*	为用户提供的执行sql语句方法(谨慎使用)，
	 *	参数类型：字符串
	 *	根据操作类型(insert,update,select...)返回不同的值
	 *
	 */
	public function query($sql,$sign = false){
	
		if($sql){

			$this->sql[]=$sql;
			@$result=mysql_query($sql);
			$this->setChche();
			unset($sql);
			if(!$result){
				$this->error=mysql_errno().':'.mysql_error();
				return false;
			}else{

				if($sign){
					return $this->getArray($result);
				}else{
					return $result;
				}
			}
		}
	}
	/*	添加数据方法，
	 *	参数类型：数组(数组的键名就是对应的字段名,)，
	 *	返回值：Boole
	 *	
	 */
	public function add($data=''){
		$result = $this->add_data_format($data);
		if(empty($result['key'])){
			$this->error='传入的值无效！';
			return false;	
		}
		$sql='INSERT INTO '.$this->db_table.$result['key'].' VALUES'.$result['value'];
		if($result=$this->query($sql)){
			$row=$this->query('select ROW_COUNT()');
			@$row=mysql_fetch_assoc($row);
			return $row['ROW_COUNT()'];
		}else{
			return $result;
		}
	}
	public function last_id(){
		$row=$this->query('SELECT LAST_INSERT_ID()');
		@$row=mysql_fetch_assoc($row);
		return $row['LAST_INSERT_ID()'];
	}
	private function add_data_format($data){
		if(empty($data)){
			$this->error='添加的内容为空！';
			return false;
		}
		$result['value']=$result['key']=null;
		foreach($data as $k=>$v){
			if(is_array($v)){
				$once = $this->add_data_one($v);
				if(empty($result['key'])) $result['key'] = $once['key'];
				$result['value'] .= $once['value'].',';
			}else{
				$result = $this->add_data_one($data);
				break;
			}
		}
		$result['value'] = trim($result['value'],',');
		return $result;
	}
	private function add_data_one($data){
		if(empty($data)) return null;
		foreach($data as $k=>$v){
			if(!empty($k) && in_array($k,$this->basic)) {
				$key[]='`'.$k.'`';
					$value[]=$v;
			}
		}
		$result['key'] = '('.implode(',',$key).')';
		$result['value']='(\''.implode('\',\'',$value).'\')';
		return $result;
	}
	
	/*	save方法更新数据库，并且也支持连贯操作的使用。
	 *	参数类型：数组(数组的键名就是对应的字段名,)，
	 */
	public function save($data){
		if(empty($this->where)){	
			if(!empty($data[$this->major])){
				$this->where='WHERE '.$this->major.' = '.addslashes($data[$this->major]);
			}else{
				$this->error='为了保证数据库的安全，避免出错更新整个数据表，如果没有任何更新条件，数据对象本身也不包含主键字段的话，save方法不会更新任何数据库的记录。并且此函数不能更新主键数据';
				return false;
			}
		}
		$basic=$this->basic;
		unset($basic[array_search($this->major,$basic)]);
		$str='';
		foreach($data as $k=>$v){
			if(in_array($k,$basic)){
				$str.='`'.$k.'`=\''.$v.'\',';
			}
			
		}
		$str=rtrim($str,',');
		$sql='UPDATE '.$this->db_table.' SET '.$str.' '.$this->where.' '.$this->limit;
		return $this->query($sql);
	}
	/*
	 *	统计数量，参数是要统计的字段名（可选）
	 */
	public function count($field='*'){
		$sql='select count('.addslashes($field).') from '.$this->db_table.' '.$this->where.' '.$this->limit;
		 $result=$this->query($sql);
		 @$row=mysql_fetch_assoc($result);
		 return $row['count('.$field.')'];
	}
	//获取最大值，参数是要统计的字段名
	public function max($field){
		$sql='select max('.addslashes($field).') from '.$this->db_table.' '.$this->where;
		$result=$this->query($sql);
		@$row=mysql_fetch_assoc($result);
		return $row['max('.$field.')'];
	}
	//获取最小值，参数是要统计的字段名
	public function min($field){
		$sql='select min('.addslashes($field).') from '.$this->db_table.' '.$this->where;
		$result=$this->query($sql);
		@$row=mysql_fetch_assoc($result);
		return $row['min('.$field.')'];
	}
	//获取平均值，参数是要统计的字段名
	public function avg($field){
		$sql='select avg('.addslashes($field).') from '.$this->db_table.' '.$this->where;
		$result=$this->query($sql);
		@$row=mysql_fetch_assoc($result);
		return $row['avg('.$field.')'];
	}
	//获取和，参数是要统计的字段名
	public function sum($field){
		$sql='select sum('.addslashes($field).') from '.$this->db_table.' '.$this->where;
		$result=$this->query($sql);
		@$row=mysql_fetch_assoc($result);
		return $row['sum('.$field.')'];
	}
	//获取某个字段的值
	public function getField($field){
		$sql='select '.addslashes($field).' from '.$this->db_table.' '.$this->where;
		$result=$this->query($sql);
		@$row=mysql_fetch_assoc($result);
		return $row[$field];
	}
	//设置某个字段的值
	public function setField($field,$value){
		$sql='UPDATE '.$this->db_table.' SET `'.addslashes($field).'`=\''.addslashes($value).'\' '.$this->where.' '.$this->limit;
		return $this->query($sql);
	}
	//字段值增长,默认增长1
	public function setInc($field,$value=1){
		$sql='UPDATE '.$this->db_table.' set `'.addslashes($field).'`=`'.addslashes($field).'`+'.addslashes($value).' '.$this->where.' '.$this->limit;
		return $this->query($sql);
	}
	//字段值减少,默认减少1
	public function setDec($field,$value=1){
		$sql='UPDATE '.$this->db_table.' set `'.addslashes($field).'`=`'.addslashes($field).'`-'.addslashes($value).' '.$this->where.' '.$this->limit;
		return $this->query($sql);
	}
	//删除操作
	public function delete(){
		$sql='DELETE FROM '.$this->db_table.' '.$this->where.' '.$this->order.' '.$this->limit;
		return $this->query($sql);
	}
	//清空表
	public function clear($data=null){
		$sql='TRUNCATE '.$data;
		return $this->query($sql);
	}
	/*	获取错误信息
	 */
	public function getError(){
		return $this->error;
	}
	public function error(){
		return $this->error;
	}
	/*	获取sql
	 */
	public function _sql($value=false){
		if($value) return $this->sql;
		return $this->sql[count($this->sql)-1];
	}
	/*
		获取表结构信息
	 */
	public function get_create_table($table=null,$strufour=false){
		$this->table($table);
		$dumpsql='';
		$dumpsql.="DROP TABLE IF EXISTS `".$this->db_table."`;\n\n";
		//设置引号
		$usql=$this->query("SET SQL_QUOTE_SHOW_CREATE=1;");
		//数据表结构
		$r=$this->getArray($this->query("SHOW CREATE TABLE `$this->db_table`;"));
		$r=$r[0];
		$create=str_replace("\"","\\\"",$r['Create Table']);
		//转为4.0格式
		if($strufour) $create=$this->Ebak_ToMysqlFour($create);
		$dumpsql.="".$create.";\n\n";
		return $dumpsql;		
	}
	/*
	 *	获取表数据sql
	 */
	public function get_create_info($table=null){
		$this->table($table);
		$data=$this->select();
		$sql="LOCK TABLES `$this->db_table` WRITE;\n";
		foreach($data as $key=>$value){
			$sql.='INSERT INTO `'.$this->db_table.'` VALUES (';
			foreach($value as $k=>$v){
				$sql.='\''.addslashes($v).'\',';
			}
			$sql=rtrim($sql,',');
			$sql.=");\n";
		}		
		$sql.="UNLOCK TABLES;\n\n\n";
		return $sql;
	}
	//转为Mysql4.0格式
	public function Ebak_ToMysqlFour($query){
		$exp="ENGINE=";
		if(!strstr($query,$exp))
		{
			return $query;
		}
		$exp1=" ";
		$r=explode($exp,$query);
		//取得表类型
		$r1=explode($exp1,$r[1]);
		$returnquery=$r[0]."TYPE=".$r1[0];
		return $returnquery;
	}
	

/********************************************************
 ********************************************************
 ********************************************************/

	/*	清除每次操作的缓存方法，
	 */
	private function setChche(){
		$this->where='';
		$this->order='';
		$this->limit='';
		$this->field='*';
		
	}
	public function getArray($result){
		$data=array();
		while(@$row=mysql_fetch_assoc($result)){
			$data[]=$row;
		}
		return $data;
	}
	//销毁
	public function __destruct(){
		//if(@mysql_connect($this->db_host.':'.$this->db_prot,$this->db_user,$this->db_pass)){
			//return mysql_close();
		//}
		if(empty($this->link)) return true;
		foreach($this->link as $k=>$v){
			mysql_close($v);
		}
		return true;
	}
}
