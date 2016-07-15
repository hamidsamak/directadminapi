<?php

/**
 * DirectAdmin API using cURL
 * 
 * @author Hamid Samak <hamid@limny.org>
 * @copyright 2016 Hamid Samak
 * @license MIT License
 */

class DirectAdminAPI {
	// domain name or ip address
	public $host;

	// connection port number
	public $port = 2222;

	// login username
	public $user;

	// login password
	public $pass;

	// current domain (for panels with multiple [addon] domains)
	public $domain;

	// SSL mode
	public $ssl = false;

	// path for saving cookie file (default = same directory [.directadmincookie])
	public $cookie_file_path;

	// result text after sending request to server (HTML output)
	public $response_text;

	private $url; // request URL will be made by "action" method
	private $status = false; // login status
	private $invalid_login_message = 'Invalid login. Please verify your Username and Password'; // message for detecting unsuccessful login attempt

	/**
	 * set default path for cookie file
	 * @return void
	 */
	public function __construct() {
		$this->cookie_file_path = __DIR__ . DIRECTORY_SEPARATOR . '.directadmincookie';
	}

	/**
	 * send request to server
	 * @param  string  $url  action address
	 * @param  array   $post post fields data
	 * @return boolean
	 */
	private function request($url, $post = array()) {
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_PORT, $this->port);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file_path);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file_path);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

		return curl_exec($ch);
	}

	/**
	 * create URL with given action name
	 * @param  string $name action name in DA panel
	 * @return string
	 */
	private function action($name = null) {
		$this->url = 'http';

		if ($this->ssl === true)
			$this->url .= 's';

		$this->url .= '://' . $this->host . '/' . $name;

		return $this->url;
	}

	/**
	 * login to account
	 * @return boolean
	 */
	public function login() {
		$data = $this->request($this->action('CMD_LOGIN'), array(
			'referer' => '/',
			'username' => $this->user,
			'password' => $this->pass
		));

		if ($data !== false && strpos($data, $this->invalid_login_message) === false) {
			$this->status = true;
			$this->response_text = $data;

			return true;
		}

		return false;
	}

	/**
	 * add new domain pointer
	 * @param  string  $source_domain domain name to park
	 * @param  boolean $alias         alias / pointer mode
	 * @return boolean
	 */
	public function domain_pointer_add($source_domain, $alias = true) {
		if ($this->status === false)
			return false;

		$data = $this->request($this->action('CMD_DOMAIN_POINTER'), array(
			'domain' => $this->domain,
			'action' => 'add',
			'from' => $source_domain,
			'alias' => ($alias === true ? 'yes' : 'no')
		));

		if ($data !== false) {
			$this->response_text = $data;

			return true;
		}

		return false;
	}

	/**
	 * delete domain pointer(s)
	 * @param  string $domain_names pointed domain name(s) [comma separated for multiple names]
	 * @return boolean
	 */
	public function domain_pointer_delete($domain_names) {
		if ($this->status === false)
			return false;

		$domain_names = explode(',', $domain_names);
		$domain_names = array_map('trim', $domain_names);

		$request_array = array(
			'domain' => $this->domain,
			'action' => 'delete',
			'delete' => 'Delete',
		);

		foreach ($domain_names as $key => $domain_name)
			$request_array['select' . $key] = $domain_name;

		$data = $this->request($this->action('CMD_DOMAIN_POINTER'), $request_array);

		if ($data !== false) {
			$this->response_text = $data;

			return true;
		}

		return false;
	}

	/**
	 * put contents to file
	 * @param  string $file_path full path of new or existing file
	 * @param  string $contents       text contents
	 * @return boolean
	 */
	public function file_put($file_path, $contents = null) {
		$data = $this->request($this->action('CMD_FILE_MANAGER'), array(
			'action' => 'edit',
			'path' => dirname($file_path),
			'filename' => basename($file_path),
			'text' => $contents,
		));

		if ($data !== false) {
			$this->response_text = $data;

			return true;
		}

		return false;
	}

	/**
	 * get file contents
	 * @param  string $file_path full path of existing file
	 * @return boolean
	 */
	public function file_get($file_path) {
		$data = $this->request($this->action('CMD_FILE_MANAGER' . $file_path));

		if ($data !== false)
			return $data;

		return false;
	}
}

?>