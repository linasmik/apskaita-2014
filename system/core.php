<?php// Jei nera konstantos metam erroradefined('WEB_INIT') or die();// Duomenu bazerequire_once DIR_LIBRARIES.'mysql_db'.EXT;// Sesijosrequire_once DIR_LIBRARIES.'session'.EXT;// Vartotojo inforequire_once DIR_LIBRARIES.'user'.EXT;class core{	// Sistemos kintamasis	private static $system;	// Ikrautos bibliotekos	private $loaded_libs = array();	// Configai esantis duomenu bazeje TABLE_CONFIGS	private $dbcfg;	public function __construct()	{		self::$system =& $this;		// Duomenu bazes konfiguracija		require_once DIR_CONFIGS.'database'.EXT;				$this->db	= new mysql_db($config['webdb']);				// Konfiguracija is TABLE_CONFIGS		$dbcfg_query = $this->db->query('SELECT config_name, config_value FROM '.TABLE_CONFIGS);		// Jei yra konfigu		if ($dbcfg_query->num_rows() > 0)		{			// Susideliojam konfigu masyva			foreach($dbcfg_query->result_array() as $key)			{				$dbcfg[$key['config_name']] = $key['config_value'];			}			// Ikraunam konfigus			$this->dbcfg = $dbcfg;		}		// Sesijos objekas		$this->sess		= new session;		// Vartotojo objektas		$this->user		= new user;	}	public function load($lib)	{		if(isset($this->loaded_libs[$lib]))		{			// biblioteka jau yra ikelta			return true;		}		$libraries = array(			'template'	=> 'temp',			'validation'	=> 'valid',			'permissions'	=> 'perm',			'members'	=> 'members'		);		// Patikriname ar biblioteka nebuvo ikelta		if(isset($libraries[$lib]) AND !isset($this->loaded_libs[$lib]))		{			require_once DIR_LIBRARIES.$lib.EXT;			$this->loaded_libs[$lib]	= true;			$this->$libraries[$lib]		= new $lib;			// Biblioteka ikelta			return true;		}		// Biblioteka neikelta		return false;	}	public function dbcfg($cfg)	{		if(isset($this->dbcfg[$cfg]))		{			return $this->dbcfg[$cfg];		}		else		{			return false;		}	}	    public static function &init()    {        return self::$system;    }}?>