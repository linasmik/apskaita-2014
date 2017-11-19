<?php

// Turinys prieinamas tik su WEB_INIT konstanta

defined('WEB_INIT') or die('Access denied!');

/**
 * Teisiu valdymo klase 		| permissions.php
 * Paskutini karta modifikuotas | 23:49 2009 m. spalio 26 d.
 * @autorius	Linas Mikalauskas
 * @versija		0.5
 */
 
class Permissions
{
	// Teisiu masyvas
	private $permissions = array();
	
	// Teisem sukurtu grupiu masyvas
	private $groups = array();
	
	// Teisem priskirtu zodziu masyvas
	private $assign = array();

	/**
	* Teisiu bloko sukurimas
	* load([Nauja teisiu grupe]);
	* @access	public
	* @param	string
	* @return	bool
	*/

	public function load($name = "")
	{
		if(($name != "") AND (is_string($name)))
		{
			if(!isset($this->permissions[$name]))
			{
				$this->permissions[$name] = array();
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Teisiu sektoriaus sukurimas
	* load_sector([Teisiu grupe],[Sektoriaus pavadinimas]);
	* @access	public
	* @param	string
	* @return	bool
	*/

	public function load_sector($perm_group, $sector = "")
	{
		// patikriname ar egzistuoja tokia teisiu grupe
		if(!isset($this->permissions[$perm_group]))
		{
			return FALSE;
		}
		// jei teisiu grupei jau yra prisektas sektorius, grazinam teigiama rezultata
		if(isset($this->permissions[$perm_group][$sector]))
		{
			return TRUE;
		}
		// Sukuriame sektoriu
		if($sector != "")
		{
			$this->permissions[$perm_group][$sector] = array("permission" => 0x0,"locked_add" => 0x0,"locked_rem" => 0x0);
			return TRUE;
		}
		// Jei nepavyko sukurti graziname neigiamai
		return FALSE;
	}

	/**
	* Teisiu priskirymas
	* set_perm([Teisiu grupe],[Sektoriaus pavadinimas],[Pagrindines teises],[Uzblokuoti teisiu panaikinimai],[Uzblokuoti teisiu suteikimai]);
	* @access	public
	* @param	string
	* @return	bool
	*/

	public function set_perm($perm_group, $sector, $permission = 0x0, $locked_rem = 0x0, $locked_add = 0x0)
	{
		if(isset($this->permissions[$perm_group][$sector]))
		{
			// Teisiu apibrezimai nuo 0 iki 2^32 -1
			if(is_int($permission) AND ($permission >= 0x0) AND ($permission <= 0x7FFFFFFF))
			{
				// priskiriame teises
				$this->permissions[$perm_group][$sector]["permission"] = $permission;
			}
			
			if(is_int($locked_rem) AND ($locked_rem >= 0x0) AND ($locked_rem <= 0x7FFFFFFF))
			{
				// priskiriame uzblokuotas teises nuimti
				$this->permissions[$perm_group][$sector]["locked_rem"] = $locked_rem;
			}
			
			if(is_int($locked_add) AND ($locked_add >= 0x0) AND ($locked_add <= 0x7FFFFFFF))
			{
				// priskiriame uzblokuotas teises prideti
				$this->permissions[$perm_group][$sector]["locked_add"] = $locked_add;
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Uzdraudzia isjungti teises
	* lock_rem([Teisiu grupe],[Teisiu grupes sektorius],[Teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function lock_rem($perm_group, $sector, $permission)
	{
		if(isset($this->permissions[$perm_group][$sector]))
		{
			$permission = $this->process($permission);
			
			if($permission)
			{
				// Uzdedame draudima, kad negalima pasalinti teisiu nurodytam teisiu sektoriui
				$this->permissions[$perm_group][$sector]["locked_rem"] |= $permission;
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	* Uzdraudzia prideti teises
	* lock_add([Teisiu grupe],[Teisiu grupes sektorius],[Teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function lock_add($perm_group, $sector, $permission)
	{
		if(isset($this->permissions[$perm_group][$sector]))
		{
			$permission = $this->process($permission);
			
			if($permission)
			{
				// Uzdedame draudima, kad negalima prideti teisiu nurodytam sektoriui
				$this->permissions[$perm_group][$sector]["locked_add"] |= $permission;
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	* Panaikina draudima nuimti teises
	* unlock_rem([Teisiu grupe],[Teisu grupes sektorius],[teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function unlock_rem($perm_group, $sector, $permission)
	{
		if(isset($this->permissions[$perm_group][$sector]))
		{
			// Sutvarkome gauta teise
			$permission = $this->process($permission);
			
			if($permission)
			{
				// Atblokuoja tik tada jei teise yra uzblokuota
				if($this->permissions[$perm_group][$sector]["locked_rem"] & $permission)
				{
					$this->permissions[$perm_group][$sector]["locked_rem"] ^= $permission;
				}
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	* Panaikina draudima teises prideti
	* unlock_add([teisiu grupe],[Teisiu grupes sektorius],[teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function unlock_add($perm_group, $sector, $permission)
	{
		if(isset($this->permissions[$perm_group][$sector]))
		{
			// Sutvarkome gauta teise
			$permission = $this->process($permission);
			
			if($permission)
			{
				// Atblokuoja tik tada jei teise yra uzblokuota
				if($this->permissions[$perm_group][$sector]["locked_add"] & $permission)
				{
					$this->permissions[$perm_group][$sector]["locked_add"] ^= $permission;
				}
			}
		}
	}

	/**
	* Teisiu suteikimas
	* add([Teisiu grupe],[Teisiu sektorius grupe],[Teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function add($perm_group, $sector, $permission)
	{
		if(!isset($this->permissions[$perm_group][$sector]))
		{
			return FALSE;
		}
		// Susitvarkom gauta teise
		$permission = $this->process($permission);
		
		if($permission)
		{
			// Jei yra uzdrausta prideti teises tam sektoriui, neleidziam to daryti
			if($this->permissions[$perm_group][$sector]["locked_add"] & $permission)
			{
				return FALSE;
			}
			// Pridedam teise
			$this->permissions[$perm_group][$sector]["permission"] |= $permission;
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Teisiu panaikinimas
	* rem([Teisiu grupe],[Teisiu sektorius grupe],[Teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function rem($perm_group, $sector, $permission)
	{
		if(!isset($this->permissions[$perm_group][$sector]))
		{
			return FALSE;
		}
		
		$permission = $this->process($permission);
		
		if($permission)
		{
			// Jei yra uzdrausta nuimti teises tam sektoriui, neleidziam to daryti
			if($this->permissions[$perm_group][$sector]["locked_rem"] & $permission)
			{
				return FALSE;
			}
			// Teise nusiema tik tada jei ji yra prideta
			if($this->permissions[$perm_group][$sector]["permission"] & $permission)
			{
				$this->permissions[$perm_group][$sector]["permission"] ^= $permission;
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	* Patikrina ar yra suteikta teise
	* validate([Teisiu grupe],[Teisiu sektorius grupe],[Teise]);
	* @access	public
	* @param	string, integer
	* @return	bool
	*/

	public function validate($perm_group,$sector,$permission)
	{
		if(!isset($this->permissions[$perm_group][$sector]))
		{
			return FALSE;
		}
		// Susitvarko gauta teise
		$permission = $this->process($permission);
			
		if($permission)
		{
			// Jei teise ijungta graziname teigiama rezultata
			if($this->permissions[$perm_group][$sector]["permission"] & $permission)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}

	/**
	* Teisem priskiriamas pavadinimas
	* assign([Pavadinimas],[Teise]);
	* @access	public
	* @param	string, integer, array
	* @return	bool
	*/

	public function assign($name,$permission = "")
	{
		if(is_array($name) AND (count($name) > 0))
		{
			foreach($name as $names => $permissions)
			{
				if(isset($names) && isset($permissions))
				{
					if(!empty($names) && is_string($names) && is_int($permissions) && ($permissions >= 0x1) && ($permissions <= 0x1F))
					{
						$this->assign[$names] = $permissions;
					}
				}
			}
			return TRUE;
		}
		
		elseif(!empty($name) && is_string($name) && ($permission >= 0x1) && ($permission <= 0x1F))
		{
			$this->assign[$name] = $permission;
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Teisiu grupes sukurimas
	* group_create([Pavadinimas],[Teises]);
	* @access	public
	* @param	string, integer, array
	* @return	bool
	*/

	public function group_create($name, $permissions)
	{
		if(!empty($name) && is_string($name))
		{
			if((is_array($permissions)) && (count($permissions) > 0))
			{
				foreach($permissions as $permission)
				{
					$validate = $this->process($permission);
					// Jei teise tinkama, ja pridedame
					if($validate)
					{
						$this->groups[$name][] = $permission;
					}
				}
				return TRUE;
			}
			else
			{
				$validate = $this->process($permission);
				
				if($validate)
				{
					$this->groups[$name][] = $permission;
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	* Teisiu grupes priskirimas teisiu grupes sektoriui
	* group_add([Teisiu grupe],[Teisiu grupes sektorius],[Grupe]);
	* @access	public
	* @param	string, array
	* @return	bool
	*/

	public function group_add($perm_group,$sector,$group)
	{
		if(isset($this->permissions[$perm_group][$sector]) && isset($this->groups[$group]))
		{
			foreach($this->groups[$group] as $list => $permission)
			{
				$this->add($perm_group,$sector,$permission);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Teisiu grupes panaikinimas teisiu grupes sektoriui
	* group_rem([Teisiu grupe],[Teisiu grupes sektorius],[Grupe]);
	* @access	public
	* @param	string, array
	* @return	bool
	*/

	public function group_rem($perm_group,$sector,$group)
	{
		if(isset($this->permissions[$perm_group][$sector]) && isset($this->groups[$group]))
		{
			foreach($this->groups[$group] as $permission)
			{
				$this->rem($perm_group,$sector,$permission);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Sutvarko teise ir ja grazina
	* @access	private
	* @param	integer
	* @return	integer
	*/

	private function process($permission)
	{
		// Teisiu nedaugiau kaip 31.
		if(is_int($permission) AND ($permission >= 0x1) AND ($permission <= 0x1F))
		{
			$permission = (int) $permission;
		}
	
		// Jei teise ateina kaip zodis, patikriname ar siam zodziui yra suteikta teise
		elseif(is_string($permission) AND isset($this->assign[$permission]))
		{
			$permission = (int) $this->assign[$permission];
		}
	
		// Jeigu tai nera teise, graziname false.
		else
		{
			return FALSE;
		}
		
		/** 
		* Du pakeliam jusu teises laipsniu-1
		* 1 teise -> 2^1-1 = 2^0 = 1 -> 0001
		* 2 teise -> 2^2-1 = 2^1 = 2 -> 0010
		* 3 teise -> 2^3-1 = 2^2 = 4 -> 0100
		* 4 teise -> 2^4-1 = 2^3 = 8 -> 1000
		**/
	 
		$permission = pow(0x2,$permission-0x1);

		// graziname teises
		return $permission;
	}

	/**
	* Teisiu grupes informacija
	* get_info([Teisiu grupe],[Teisiu grupes sektorius]);
	* @access	public
	* @param	string
	* @return	array
	*/
	
	public function get_info($perm_group,$sector)
	{
		if(isset($this->permissions[$perm_group][$sector]))
		{
			return $this->permissions[$perm_group][$sector];
		}
		return FALSE;
	}
}

?>