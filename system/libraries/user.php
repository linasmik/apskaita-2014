<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die();

// Informacija apie vartotoja
class userInfo
{
	public $id		= 0;
	public $login;
	public $email;
	public $access_level;
	public $login_time;
	public $permissions	= 1023;
	public $timezone	= "Europe/Vilnius";
	public $lastIp; // hex
	public $IPAddress; // normal
	public $sess_id;
}

// Vartotojo valdymo klase
class user
{
	private $logged = false;
	public $core;
	public $info;
	public $action;

	public function __construct()
	{
		$this->info = new userInfo;
		$this->core =& core::init();

		if($this->core->sess->userdata('logged'))
		{
			$this->logged = true;

			// Pasiemame visa vartotojo informacija
			$this->getUserInfo();

			// Nustatome vartotojo pasirinkta laiko juosta
			date_default_timezone_set($this->info->timezone);

			// Paleidziam teisiu klase
			$this->core->load('permissions');
			$this->core->perm->load('user');
			$this->core->perm->load_sector('user','main');
			$this->core->perm->set_perm('user','main',(int)$this->info->permissions);

			// Ikeliame teisiu masyva
			require_once DIR_CONFIGS.'permissions'.EXT;
			$this->core->perm->assign($permissions);

		}
	}

	public function isLogged()
	{
		return (bool) $this->logged;
	}

	public function registerUserSession($data)
	{
		$this->logged = true;

		$session = array(
			'logged'	=> true,
			'login'		=> $data['login'],
			'login_time'	=> time()
		);

		$this->core->sess->set_userdata($session);
		
		// Atnaujiname prisijungimo duomenis, users lentelei
		$updateSql = 'UPDATE '.TABLE_USERS.' SET lastLogin = "'.time().'", lastIp = "'.user_ip2hex().'" WHERE login = "'.$data['login'].'" LIMIT 1';

		$this->core->db->simple_query($updateSql);

		// Pasiemame vartotojo informacija
		$this->getUserInfo();
		
		// Uzloginame, jo prisijungima loguose
		$this->log('login');
	}

	public function unregisterUserSession()
	{
		$this->core->sess->sess_destroy();
	}

	private function getUserInfo()
	{
		$this->info->login		= $this->core->sess->userdata('login');
		$this->info->login_time		= $this->core->sess->userdata('login_time');
		$this->info->IPAddress		= hex2ip($this->core->sess->userdata('ip_address'));

		// Paimti duomenis is user
		$sql = 'SELECT * FROM '.TABLE_USERS.' WHERE login = "'.$this->core->db->escape_str($this->info->login).'" LIMIT 1';
		$query = $this->core->db->query($sql);

		foreach($query->row_array() as $key => $val)
		{
			if($val != "") $this->info->$key = $val;
		}
	}

	public function login($accountid,$password)
	{
		$accountid = $this->core->db->escape_str($accountid);
		$password = $this->password_encode($password);

		$sql = 'SELECT login FROM '.TABLE_USERS.' WHERE login = "'.$accountid.'" LIMIT 1';
		$query = $this->core->db->query($sql);

		if($query->num_rows > 0)
		{
		        $login_data = $query->result_array();
			$this->registerUserSession($login_data[0]);
			return true;
		}

		return false;
	}

	public function changePassword($realpass,$newpass)
	{
		$sql = 'SELECT * FROM '.TABLE_USERS.' WHERE login = "'.$this->core->sess->userdata('login').'" AND password = "'.$this->password_encode($realpass).'" LIMIT 1';
	
		$query = $this->core->db->query($sql);

		if($query->num_rows > 0)
		{
			$newPassword = $this->password_encode($newpass);
			$sql = 'UPDATE '.TABLE_USERS.' SET password = "'.$newPassword.'" WHERE login = "'.$this->core->sess->userdata('login').'" LIMIT 1';
			$this->core->db->simple_query($sql);
			return true;
		}

		return false;
	}

	public function password_encode($password)
	{
		return base64_encode(pack("H*", sha1(utf8_encode("x48778x".$password."58afc25"))));
	}
}

?>