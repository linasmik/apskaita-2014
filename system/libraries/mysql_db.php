<?php

/*
 * Duomenu bazes klase
 * @file    db.php
 * @author  Linas Mikalauskas (aka) lnix
 * @updated 2014-05-15
 */

// Jei nera konstantos metam errora
defined('WEB_INIT') or die();

class mysql_db
{
	private $hostname		= 'localhost';
	private $password		= '';
	private $username		= '';
	private $database		= '';
	private $char_set		= 'utf8';
	private $conn_id;
	private $queries 		= array();
	private $query_times		= array();
	private $query_count		= 0;
	private $port;
	private $benchmark		= 0;
	private $dbcollat		= 'utf8_general_ci';
	private $save_queries		= true;
	private $result_id;
	private $cache_salt		= "^&Aa58*^hF17}!5z";
	private $trans_status		= TRUE;
	public	$cache_on;
	public	$cachedir;
	public	$CACHE;

	public function __construct($config)
	{
		foreach($config as $key => $val)
		{
			$this->$key = $val;
		}
		
		unset($config);

		// Patikrinam ar nera nurodytas serverio portas
		if($this->port != '')
		{
			$this->hostname .= ':'.$this->port;
		}

		// Sukuriam prisijungimo id
		$this->conn_id = mysql_connect($this->hostname,$this->username,$this->password,TRUE);

		// Patikrinam ar prisijungimas pavyko
		if(!$this->conn_id)
		{
			// Debugas is kur keipiasi
			$bc = debug_backtrace();
			// Uzregistruojam klaida loguose
			log_message('[ Object -> mysql_db ] '.mysql_error().' | Call from '.$bc[0]['file'].':'.$bc[0]['line'],'mysql_db');
		
			die("DB ERROR");
		}

		// Jungiames prie duomenu bazes
		if($this->database != '')
		{
			if(!mysql_select_db($this->database,$this->conn_id))
			{
				// Debugas is kur keipiasi
				$bc = debug_backtrace();
				// Uzregistruojam klaida loguose
				log_message('[ Object -> mysql_db ] '.mysql_error().' | Call from '.$bc[0]['file'].':'.$bc[0]['line'],'mysql_db');
				// Grazinam rezultata, kad nepavyko paselektinti duomenu baze
				return FALSE;
			}
			else
			{
				$this->password = '<i><b>[HIDE**]</b></i>';
				// Bandom nustatyti koduote
				if(!mysql_query("SET NAMES '".$this->char_set."' COLLATE '".$this->dbcollat."'",$this->conn_id))
				{
					// Uzklausa nesekminga
					return FALSE;
				}
				else
				{
					return TRUE;
				}
			}
		}

		// Darbas su duomenu baze paruostas
		return TRUE;
	}

	/**
	* Uzklausos vykdimo funkcija
	* @access	public
	* @param	str
	* @return	bool
	*/

	public function query($sql)
	{
		// Patikrinam ar nurode uzklausa
		if($sql == "")
		{
			return FALSE;
		}

		if ($this->cache_on == TRUE AND stristr($sql, 'SELECT'))
		{
			if ($this->_cache_init())
			{
				if (FALSE !== ($cache = $this->CACHE->read($sql)))
				{
					return $cache;
				}
			}
		}

		// saugom debuginimui, jei nustatyta
		if($this->save_queries == TRUE)
		{
			$this->queries[] = $sql;
		}

		// paleidziam uzklausos laiko matavima
		$time_start = list($sm, $ss) = explode(' ', microtime());

		if(FALSE === ($this->result_id = mysql_query($sql,$this->conn_id)))
		{
			// Jei vyksta transkacija, pazymime kad ivykdytu roolbacka
			$this->trans_status = false;

			// Debugas is kur keipiasi
			$bc = debug_backtrace();
			// Uzregistruojam klaida loguose
			log_message('[ Object -> mysql_db ] '.mysql_error().' | SQL: '.$sql.' | Call from '.$bc[0]['file'].':'.$bc[0]['line'],'mysql_db');
			// Grazinam neigiama rezultata
			return false;
		}

		// baigiam skaiciuoti
		$time_end = list($em, $es) = explode(' ', microtime());

		// skaiciuojam visa atlikto darbo laika
		$this->benchmark += ($em + $es) - ($sm + $ss);

		if($this->save_queries == TRUE)
		{
			$this->query_times[] = ($em + $es) - ($sm + $ss);
		}

		// Skaiciuojam uzklausas
		$this->query_count++;

		// Paleidziam result klase
		$RES				= new mysql_db_result;
		$RES->conn_id		= $this->conn_id;
		$RES->result_id		= $this->result_id;
		$RES->num_rows		= $RES->num_rows();

		// Jei ijunktas duomenu kesavimas
		if ($this->cache_on == TRUE AND $this->_cache_init())
		{
			$CR = new mysql_db_result();
			$CR->num_rows 		= $RES->num_rows();
			$CR->result_object	= $RES->result_object();
			$CR->result_array	= $RES->result_array();
			
			$CR->conn_id		= NULL;
			$CR->result_id		= NULL;
			// Irasom kesha
			$this->CACHE->write($sql, $CR);
		}
		// grazinam rezultata
		return $RES;
	}

	/**
	* Vykdo paprastas uzklausas, be informacijos gavimo
	* @access	public
	* @param	string
	* @return	bool
	*/

	public function simple_query($sql)
	{
		// Ivykdom uzklausa
		$result = mysql_query($sql,$this->conn_id);

		// Patikrinam ar uzklausa pavyko
		if(!$result)
		{
			// Debugas is kur keipiasi
			$bc = debug_backtrace();
			// Uzregistruojam klaida loguose
			log_message('[ Object -> mysql_db ] '.mysql_error().' | SQL: '.$sql.' | Call from '.$bc[0]['file'].':'.$bc[0]['line'],'mysql_db');
			// Grazinam neigiama rezultata
			return false;
		}
		else
		{
			// Grazinam rezultata
			return $result;
		}
	}

	/**
	* Sutvarko uzklausai paduodamus parametrus
	* escape(string);
	* @access	public
	* @param	string
	* @return	mix
	*/

	public function escape($str)
	{	
		switch (gettype($str))
		{
			case 'string'	:	$str = "'".$this->escape_str($str)."'";
				break;
			case 'boolean'	:	$str = ($str === FALSE) ? 0 : 1;
				break;
			default			:	$str = ($str === NULL) ? 'NULL' : $str;
				break;
		}		

		return $str;
	}

	/**
	* Transakcijos pradzia
	* @access	public
	* @return	string
	*/

	public function trans_start()
	{
		if ($this->trans_status === FALSE)
		{
		    $this->trans_status = TRUE;
		}

		$this->trans_begin();
	}

	/**
	* Transakcijos pradzia
	* @access	public
	* @return	string
	*/

	public function trans_begin()
	{
		$this->simple_query('SET AUTOCOMMIT=0');
		$this->simple_query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
		return TRUE;
	}

	/**
	* Transakcijos pabaiga
	* @access	public
	* @return	string
	*/

	public function trans_commit()
	{
		$this->simple_query('COMMIT');
		$this->simple_query('SET AUTOCOMMIT=1');
		return TRUE;
	}
	
	/**
	* Transakcijos atgrazinimas
	* @access	public
	* @return	string
	*/

	public function trans_rollback()
	{
		$this->simple_query('ROLLBACK');
		$this->simple_query('SET AUTOCOMMIT=1');
		return TRUE;
	}

	/**
	* Transakcijos pabaiga
	* @access	public
	* @return	string
	*/

	public function trans_complete()
	{
		if ($this->trans_status === FALSE)
		{
			$this->trans_rollback();		
		}
		
		$this->trans_commit();
		return TRUE;	
	}
	
	/**
	* Transakcijos status
	* @access	public
	* @return	string
	*/

	public function trans_status()
	{
		return $this->trans_status;	
	}

	/**
	* Uzklausu apsauga
	* @access	public
	* @param	string, array
	* @return	string
	*/

	public function escape_str($str, $like = FALSE)
	{	
		if (is_array($str))
		{
			foreach($str as $key => $val)
	   		{
				$str[$key] = $this->escape_str($val, $like);
	   		}
   		
	   		return $str;
	   	}

		if (function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
		{
			$str = mysql_real_escape_string($str, $this->conn_id);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}
		
		// Del paieskos, escapinam nereikalingus simbolius
		if ($like === TRUE)
		{
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}
		
		return $str;
	}

	/**
	* Del paieskos galimai isiterpiamu nereikalingu simboliu escapinimas
	* @access	public
	* @param	string
	* @return	string
	*/

	public function escape_like_str($str)    
	{    
	    return $this->escape_str($str, TRUE);
	}

	/**
	* Iterpimo i duomenu baze funkcija
	* @access	public
	* @param	string, array
	* @return	bool
	*/

	public function insert($table,$array)
	{
		// Jei duomenis ne masyve, veiksmu neatliekame
		if(!is_array($array))
		{
			return false;
		}

		// Konstruojam sql`a
		$sql = 'INSERT INTO `'.$table.'` ('.implode(',',array_keys($array)).') VALUES ("'.implode('","',array_values($array)).'")';

		// vvkdome uzklausa
		if($this->simple_query($sql))
		{
			return true;
		}

		return false;
	}

	/**
	* Duomenu atnaujinimo f-ja
	* @access	public
	* @param	string, array
	* @return	bool
	*/

	public function update($table_name, $form_data, $where_clause='')
	{
		$whereSQL = '';

		if(!empty($where_clause))
		{
			if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
			{
				$whereSQL = " WHERE ".$where_clause;
				
			}
			else
			{
				$whereSQL = " ".trim($where_clause);
			}
		}
		
		$sql = "UPDATE ".$table_name." SET ";

		$sets = array();
		
		foreach($form_data as $column => $value)
		{
			$sets[] = "`".$column."` = '".$value."'";
		}
		
		$sql .= implode(', ', $sets);
		$sql .= $whereSQL;

		// vykdome uzklausa
		if($this->simple_query($sql))
		{
			return true;
		}

		return false;
	}

	/**
	* Ijungia kesavima
	* @access	public
	* @param	void
	* @return	bool
	*/

	public function cache_on()
	{
		$this->cache_on = TRUE;
		return TRUE;
	}

	/**
	* Isjungia kesavima
	* @access	public
	* @param	void
	* @return	bool
	*/
	
	public function cache_off()
	{
		$this->cache_on = FALSE;
		return FALSE;
	}

	/**
	* Sunaikina visus duomenu keshus
	* @access	public
	* @apram	void
	* @return	bool
	*/

	public function cache_delete_all()
	{
		if ( ! $this->_cache_init())
		{
			return FALSE;
		}

		return $this->CACHE->delete_all();
	}

	/**
	* Kesavimo klases sukurimas
	* @access	private
	* @param	void
	* @return	bool
	*/
	
	private function _cache_init()
	{
		if (is_object($this->CACHE))
		{
			return TRUE;
		}

		$this->CACHE = new mysql_db_cache($this); 
		return TRUE;
	}

	/**
	* Kehso druska
	* @access	public
	* @param	void
	* @return	string
	*/

	public function cache_salt()
	{
		return $this->cache_salt;
	}

	/**
	* Grazina hostname
	* @access	public
	* @param	void
	* @return	string
	*/
	
       public function get_hostname()
       {
	   return $this->hostname;
       }

	/**
	* Duomenu bazes atsijungimas
	* @access	public
	* @param	void
	* @return	void
	*/

	public function __destruct()
	{
		mysql_close($this->conn_id);
	}
}


/* Rezultato klase */
class mysql_db_result
{
	public $conn_id			= NULL;
	public $result_id		= NULL;
	public $result_array	= array();
	public $result_object	= array();
	public $current_row		= 0;
	public $num_rows		= 0;
	public $row_data		= NULL;

	/**
	* Grazina uzklausos rezultata nurodytu formatu
	* @access 	public
	* @param	string
	* @return	array, object
	*/

	public function result($type = 'object')
	{
		return ($type == 'object') ? $this->result_object() : $this->result_array();
	}

	/**
	* Duomenis objekto formatu
	* @access	public
	* @param	void
	* @return	object
	*/

	public function result_object()
	{
		if(count($this->result_object) > 0)
		{
			return $this->result_object;
		}

		if(($this->result_id === FALSE) OR ($this->num_rows == 0))
		{
			return array();
		}

		$this->_data_seek(0);

		while($row = $this->_fetch_object())
		{
			$this->result_object[] = $row;
		}

		return $this->result_object;
	}

	/**
	* Duomenis masyvo formatu
	* @access	private
	* @param	void
	* @return	array
	*/

	public function result_array()
	{
		if(count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		if($this->result_id === FALSE OR $this->num_rows == 0)
		{
			return array();
		}

		$this->_data_seek(0);

		while($row = $this->_fetch_assoc())
		{
			$this->result_array[] = $row;
		}

		return $this->result_array;
	}

	/**
	* Grazina eilutes duomenis su nurodytu formatu
	* @access	public
	* @param	integer, string
	* @return	object, array
	*/

	public function row($n = 0, $type = 'object')
	{
		if (!is_numeric($n))
		{
			if (!is_array($this->row_data))
			{
				$this->row_data = $this->row_array(0);
			}

			if (array_key_exists($n, $this->row_data))
			{
				return $this->row_data[$n];
			}		
			$n = 0;
		}
		
		return ($type == 'object') ? $this->row_object($n) : $this->row_array($n);
	}

	/**
	* Grazina eilutes duomenis objekte
	* @access	public
	* @param	integer
	* @return	object
	*/

	public function row_object($n = 0)
	{
		$result = $this->result_object();
		
		if (count($result) == 0)
		{
			return $result;
		}

		if ($n != $this->current_row AND isset($result[$n]))
		{
			$this->current_row = $n;
		}

		return $result[$this->current_row];
	}

	/**
	* Grazina eilutes duomenis masyve
	* @access	public
	* @param	integer
	* @return	array
	*/

	public function row_array($n = 0)
	{
		$result = $this->result_array();

		if (count($result) == 0)
		{
			return $result;
		}
			
		if ($n != $this->current_row AND isset($result[$n]))
		{
			$this->current_row = $n;
		}
		
		return $result[$this->current_row];
	}

	/**
	* Grazina pirma eilute
	* @access	public
	* @param	string
	* @return	string
	*/

	public function first_row($type = 'object')
	{
		$result = $this->result($type);

		if (count($result) == 0)
		{
			return $result;
		}
		return $result[0];
	}

	/**
	* Grazina paskutine eilute
	* @access	public
	* @param	string
	* @return	string
	*/

	public function last_row($type = 'object')
	{
		$result = $this->result($type);

		if (count($result) == 0)
		{
			return $result;
		}
		return $result[count($result) -1];
	}

	/**
	* Grazina sekancia eilute
	* @access	private
	* @param	string
	* @return	string
	*/

	public function next_row($type = 'object')
	{
		$result = $this->result($type);

		if (count($result) == 0)
		{
			return $result;
		}

		if (isset($result[$this->current_row + 1]))
		{
			++$this->current_row;
		}

		return $result[$this->current_row];
	}

	/**
	* Grazina ankstesne eilute
	* @access	public
	* @param	string
	* @return	string
	*/

	public function previous_row($type = 'object')
	{
		$result = $this->result($type);

		if (count($result) == 0)
		{
			return $result;
		}

		if (isset($result[$this->current_row - 1]))
		{
			--$this->current_row;
		}
		return $result[$this->current_row];
	}

	/**
	* Grazina eiluciu skaiciu
	* @access	public
	* @param	void
	* @return	integer
	*/

	public function num_rows()
	{
		$result = mysql_num_rows($this->result_id);
		return $result;
	}

	/**
	* Nustatom pointeri
	* @access	private
	* @param	integer
	* @return	bool
	*/

	private function _data_seek($n = 0)
	{
		return mysql_data_seek($this->result_id, $n);
	}

	/**
	* Grazina masyva
	* @access	private
	* @param	void
	* @return	array
	*/

	private function _fetch_assoc()
	{
		return mysql_fetch_assoc($this->result_id);
	}

	/**
	* Grazina objekta
	* @access	private
	* @param	void
	* @return	object
	*/

	private function _fetch_object()
	{
		return mysql_fetch_object($this->result_id);
	}
}

/* Duomenu kesavimas */
class mysql_db_cache
{
	private $CLL;
	private $db;


	/**
	* Kesavimo klases konstruktorius
	* @access	public
	* @param	object
	* @result	void
	*/

	public function __construct(&$db)
	{
		//$this->CLL =& core::init();
		$this->db =& $db;
	}

	/**
	* Patikrina ar egizstuoja tokia kesavimo direktorija
	* @access	public
	* @param	string
	* @result	bool
	*/

	public function check_path($path = '')
	{
		if ($path == '')
		{
			if ($this->db->cachedir == '')
			{
				return $this->db->cache_off();
			}
		
			$path = $this->db->cachedir;
		}
	
		$path = preg_replace("/(.+?)\/*$/", "\\1/",  $path);

		if ( ! is_dir($path) OR ! is_really_writable($path))
		{
			return $this->db->cache_off();
		}
		
		$this->db->cachedir = $path;
		return TRUE;
	}

	/**
	* Keso nuskaitymas
	* @access	public
	* @param	string
	* @result	mix
	*/

	public function read($sql)
	{
		if ( ! $this->check_path())
		{
			return $this->db->cache_off();
		}

		$filepath = $this->db->cachedir.'/'.md5($sql.$this->db->cache_salt());		
		
		if (FALSE === ($cachedata = read_file($filepath)))
		{	
			return FALSE;
		}

		return unserialize($cachedata);			
	}	

	/**
	* Keso rasymas i faila
	* @access	public
	* @param	string, object
	* @result	bool
	*/

	public function write($sql, $object)
	{
		if ( ! $this->check_path())
		{
			return $this->db->cache_off();
		}

		$dir_path = $this->db->cachedir.'/';
		
		$filename = md5($sql.$this->db->cache_salt());

		if ( ! @is_dir($dir_path))
		{
			if ( ! @mkdir($dir_path, '0777'))
			{
				return FALSE;
			}
			
			@chmod($dir_path, '0777');			
		}
		
		if (write_file($dir_path.$filename, serialize($object)) === FALSE)
		{
			return FALSE;
		}
		
		@chmod($dir_path.$filename, FILE_WRITE_MODE);
		return TRUE;
	}

	/**
	* Visu kesu sunaikinimas
	* @access	public
	* @param	void
	* @return	void
	*/

	public function delete_all()
	{
		delete_files($this->db->cachedir, TRUE);
	}

}

?>