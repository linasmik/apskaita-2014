<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die();

class session
{
	private $core;
	private $sess_cookie_name		= 'web_sess_id';
	private $sess_expiration		= 7200;
	private $cookie_path			= '/';
	private $cookie_domain			= '';
	private $userdata			= array();
	private $sess_time_to_update		= 300;
	private $status = false;

	/**
	* Klases konstruktorius
	* @access  private
	* @return  void
	*/

	public function __construct()
	{
		require_once DIR_CONFIGS.'sessions'.EXT;

		foreach($config as $key => $val)
		{
			$this->$key = $val;
		}
		
		unset($config);

		// Pasiemam branduoli
		$this->core =& core::init();

		// Patikrinam ar yra sukurtas cookie
		if(!$this->sess_read())
		{
			$this->sess_create();
		}
		else
		{
			$this->sess_update();
		}
	}

	private function sess_read()
	{
		// Patikrinam ar sukurtas sesijos sausainelis
		if (!isset($_COOKIE[$this->sess_cookie_name]))
		{
			return FALSE;
		}

		// user agent hash
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		else
		{
			$user_agent = 'not-set';
		}

		// SQL sesijos duomenim paimti
		$sql = 'SELECT * FROM '.TABLE_SESSIONS.' '.
			'WHERE (session_id = '.$this->core->db->escape($_COOKIE[$this->sess_cookie_name]).' OR previous_id = '.$this->core->db->escape($_COOKIE[$this->sess_cookie_name]).') '.
			'AND ip_address = '.$this->core->db->escape(user_ip2hex()).' '.
			'AND user_agent = '.$this->core->db->escape($user_agent).' '.
			'AND last_activity > '.(time() - $this->sess_expiration).' LIMIT 1';

		// Vykdome uzklausa
		$query = $this->core->db->query($sql);

		// Jei nera rezultatu, naikinam sesija
		if ($query->num_rows() == 0)
		{
			$this->sess_destroy();
			return FALSE;
		}

		// Lenteles eilutes
		$row = $query->row();
		if(isset($row->user_data) AND $row->user_data != '')
		{
			$custom_data = $this->_unserialize($row->user_data);
			if (is_array($custom_data))
			{
				foreach ($custom_data as $key => $val)
				{
					$session[$key] = $val;
				}
			}
		}

		// Kiti svarbus sesijos duomenys
		$session['id'] = $row->id;
		$session['session_id'] = $row->session_id;
		$session['previous_id'] = $row->previous_id;
		$session['ip_address'] = $row->ip_address;
		$session['last_activity'] = $row->last_activity;
		$session['user_agent'] = $row->user_agent;
		$session['status'] = $row->status;

		// Sesija paleista
		$this->userdata = $session;
		unset($session);

		return TRUE;
	}

	private function sess_create()
	{
		$sessid = '';
		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		// Del saugumo dar pridedam ip adresa
		$sessid .= get_ip_address();

		// user agent hash
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		else
		{
			$user_agent = 'not-set';
		}
		
		$sess_id = md5(uniqid($sessid, TRUE));
		
		$this->userdata = array(
								'session_id' 	=> $sess_id,
								'previous_id'	=> $sess_id,
								'ip_address' 	=> user_ip2hex(),
								'user_agent'	=> $this->core->db->escape_str($user_agent),
								'last_activity'	=> time(),
								'joined'		=> time(),
								'status'		=> 1
								);

		// Irasom duomenis i sesijos lentele
		$this->core->db->insert(TABLE_SESSIONS, $this->userdata);

		// Irasom i cookie
		setcookie(
					$this->sess_cookie_name,
					$this->userdata['session_id'],
					$this->sess_expiration + time(),
					$this->cookie_path,
					$this->cookie_domain,
					0
				);
	}

	private function sess_update()
	{				
		// Atnaujinam tik tada kai praeina nurodytas terminas po kiek laiko atnaujinti sesijos duomenis
		if(($this->userdata['last_activity'] + $this->sess_time_to_update) >= time())
		{
			return;
		}

		if(IS_AJAX)
		{
			setcookie($this->sess_cookie_name,$this->userdata['session_id'],$this->sess_expiration + time(),$this->cookie_path,$this->cookie_domain,0);
			$this->core->db->simple_query("UPDATE ".TABLE_SESSIONS." SET last_activity = ".time()." WHERE session_id = ".$this->core->db->escape($this->userdata['session_id'])." LIMIT 1");
			return;
		}
		// pasiemam sena sesijos id
		$old_sessid = $this->userdata['session_id'];

		$new_sessid = '';
		while(strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}

		// pridedam ip adresa, kad butu saugesnis sesijos hashas
		$new_sessid .= get_ip_address();

		// sukuriam nauja sesijos identifikatoriu
		$new_sessid = md5(uniqid($new_sessid, TRUE));

		// Atnaujinam sesijos masyva
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = time();

		// Atnaujinam sesija duomenu bazeje
		$this->core->db->simple_query('UPDATE '.TABLE_SESSIONS.' '.
			'SET last_activity = '.time().', '.
			'session_id = '.$this->core->db->escape($new_sessid).', '.
			'previous_id = '.$this->core->db->escape($old_sessid).' '.
			'WHERE session_id = '.$this->core->db->escape($old_sessid).' LIMIT 1');

		setcookie(
			$this->sess_cookie_name,
			$new_sessid,
			$this->sess_expiration + time(),
			$this->cookie_path,
			$this->cookie_domain,
			0
		);
	}

	private function sess_write()
	{
		// pasiemam duomenis redagavimui
		$custom_userdata = $this->userdata;

		// panaikinam duomenis kurie nebus talpinami i user_data laukeli
		foreach(array('session_id','ip_address','user_agent','last_activity','status','previous_id','id','joined') as $val)
		{
			unset($custom_userdata[$val]);
		}

		// patikrinam ar yra sesijos kintamuju
		if (count($custom_userdata) === 0)
		{
			$custom_userdata = '';
		}
		else
		{
			// Sesijos duomenis paruosiam patalpinimui
			$custom_userdata = $this->_serialize($custom_userdata);
		}

		// Atnaujinam sesijos duomenis
		if($this->core->db->simple_query('UPDATE '.TABLE_SESSIONS.' SET user_data = '.$this->core->db->escape($custom_userdata).', last_activity = '.time().' '.(($this->status != false)?', status = '.$this->status.' ':'').' WHERE session_id = '.$this->core->db->escape($this->userdata['session_id']).' OR previous_id = '.$this->core->db->escape($this->userdata['session_id']).' LIMIT 1'))
		{
			return true;
		}
		
		return false;
	}

	public function sess_destroy()
	{
		// Istrinam is duomenu bazes
		if(isset($_COOKIE[$this->sess_cookie_name]))
		{
			$this->core->db->simple_query('DELETE FROM '.TABLE_SESSIONS.' WHERE session_id = '.$this->core->db->escape($_COOKIE[$this->sess_cookie_name]).' OR previous_id = '.$this->core->db->escape($_COOKIE[$this->sess_cookie_name]).' LIMIT 1');
		}

		// Sunaikinam cookie
		setcookie(
			$this->sess_cookie_name,
			'',
			(time() - 31500000),
			$this->cookie_path,
			$this->cookie_domain,
			0
		);
	}

	public function userdata($item)
	{
		return(!isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	public function all_userdata()
	{
		return(!isset($this->userdata)) ? FALSE : $this->userdata;
	}

	public function set_userdata($newdata = array(), $newval = '')
	{
		if(is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if(count($newdata) > 0)
		{
			foreach($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		if($this->sess_write())
		{
			return true;
		}
		return false;
	}

	public function unset_userdata($newdata = array())
	{
		if(is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if(count($newdata) > 0)
		{
			foreach($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}

		$this->sess_write();
	}

	private function _serialize($data)
	{
		if(is_array($data))
		{
			foreach($data as $key => $val)
			{
				$data[$key] = str_replace('\\', '{{slash}}', $val);
			}
		}
		else
		{
			$data = str_replace('\\', '{{slash}}', $data);
		}

		return serialize($data);
	}

	private function _unserialize($data)
	{
		$data = @unserialize(stripslashes($data));

		if (is_array($data))
		{
			foreach($data as $key => $val)
			{
				$data[$key] = str_replace('{{slash}}', '\\', $val);
			}

			return $data;
		}

		return str_replace('{{slash}}', '\\', $data);
	}

	public function set_status($status = false)
	{
		$this->status = (int)$status;
	}
	
	public function get_expires()
	{
		return $this->sess_expiration;
	}
}

?>