<?php
namespace Library\Session\Driver;
use SessionHandler;

class Redis extends SessionHandler
{
    protected $handler = null;
    protected $config  = [
        'host'         => '127.0.0.1', // redis主机
        'port'         => 6379, // redis端口
        'password'     => '123456', // 密码
        'expire'       => 3600, // 有效期(秒)
        'timeout'      => 0, // 超时时间(秒)
        'persistent'   => true, // 是否长连接
        'session_name' => '', // sessionkey前缀
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);

    }

    /**
     * 打开Session
     * @access public
     * @param string    $savePath
     * @param mixed     $sessName
     */
    public function open($savePath = null, $sessName=null)
    {

        // 检测php环境
        if (!extension_loaded('redis')) {
            show_error('not support:redis');
        }
        $this->handler = new \Redis;

        // 建立连接
			$func = $this->config['persistent'] ? 'pconnect' : 'connect';
			$result = $this->handler->$func($this->config['host'], $this->config['port'], $this->config['timeout']);

			if(!$result){
				ob_clean();
				show_error('Redis connection fails<br>');
			}
			if ('' != $this->config['password']) {
				$this->handler->auth($this->config['password']);
			}

        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler->close();
        $this->handler = null;
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID)
    {
        return $this->handler->get($this->config['session_name'] . $sessID);
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessID, $sessData)
    {
        if ($this->config['expire'] > 0) {
            return $this->handler->setex($this->config['session_name'] . $sessID, $this->config['expire'], $sessData);
        } else {
            return $this->handler->set($this->config['session_name'] . $sessID, $sessData);
        }
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID)
    {
        return $this->handler->delete($this->config['session_name'] . $sessID) ? true : false;
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime)
    {
        return true;
    }



}
