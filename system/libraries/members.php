<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die();

// Nariu valdymo klase
class members
{
	public $core;

	public function __construct()
	{
		$this->core =& core::init();
	}
	
	public function getMembers($from=0,$to=20,$search="")
	{
		if($search != "")
		{
			$search = $this->core->db->escape_str($search);
			$search = 'AND name LIKE "%'.$search.'%" OR lastname LIKE "%'.$search.'%" OR telephone LIKE "%'.$search.'%" OR birthday LIKE "%'.$search.'%" ';
		}

		$sql = 'SELECT m.*, DATE_FORMAT(FROM_UNIXTIME(m.createTime), "%Y-%m-%d") as "Date", t.year, t.month, t.taxes FROM '.TABLE_MEMBERS.' as m LEFT JOIN taxes as t ON m.id = t.memberId AND t.month = '.date('n').' AND t.year = '.date('Y').' WHERE m.status = 1 '.$search.' GROUP BY m.id, t.memberId ORDER BY id DESC LIMIT '.$from.', '.$to;

		$query = $this->core->db->query($sql);

		if($query->num_rows > 0)
		{
			return $query->result_array();
		}
		
		return false;
	}
	
	public function getMember($memberId)
	{
		$sql = 'SELECT * FROM '.TABLE_MEMBERS.' WHERE id = '.(int)$memberId.' LIMIT 1';

		$query = $this->core->db->query($sql);

		if($query->num_rows > 0)
		{
			return $query->result_array();
		}
		
		return false;
	}
	
	public function taxPay($id,$year,$month,$tax)
	{
		if($tax == 0){return false;}
		$month = $month+1;

		$sql = 'SELECT * FROM '.TABLE_TAXES.' WHERE memberId = '.(int)$id.' AND year = '.(int)$year.' AND month = '.$month.' LIMIT 1';
		
		$query = $this->core->db->query($sql);
		
		// Jei jau sumoketa, atnaujiname pagal nauja nurodyma
		if($query->num_rows > 0)
		{
			$sql = 'UPDATE '.TABLE_TAXES.' SET taxes = '.(int)$tax.', updateTime = '.time().' WHERE memberId = '.(int)$id.' AND year = '.(int)$year.' AND month = '.$month.' LIMIT 1';
		
			$this->core->db->simple_query($sql);
		}
		// Irasome i duomenu baze mokejima
		else
		{
			$insert_array = array(
				'memberId'	=> (int)$id,
				'year'		=> (int)$year,
				'month'		=> $month,
				'taxes'		=> (int)$tax,
				'createTime'	=> time(),
				'updateTime'	=> 0,
				'status'	=> 0
			);
			
			$this->core->db->insert(TABLE_TAXES,$insert_array);
		}

		return true;
	}
	
	public function getTaxes($memberId)
	{
		$sql = 'SELECT * FROM '.TABLE_TAXES.' WHERE memberId = '.(int)$memberId.' ORDER by year DESC, month DESC';
		
		$query = $this->core->db->query($sql);
		
		if($query->num_rows > 0)
		{
			return $query->result_array();
		}
		
		return false;
	}

	public function showTaxes($data)
	{
		$from = strtotime((int)$data['fromYear'].'-'.(int)$data['fromMonth'].'-'.(int)$data['fromDay'].' 00:00:00');
		$to = strtotime((int)$data['toYear'].'-'.(int)$data['toMonth'].'-'.(int)$data['toDay'].' 23:59:59');

		$sql = 'SELECT t.*, m.name, m.lastname, DATE_FORMAT(FROM_UNIXTIME(t.createTime), "%Y-%m-%d") as "Date" FROM '.TABLE_TAXES.' as t JOIN '.TABLE_MEMBERS.' as m ON t.memberId = m.id WHERE t.createTime >= '.$from.' AND t.createTime <= '.$to.' ORDER BY t.id DESC';
		
		$query = $this->core->db->query($sql);

		if($query->num_rows > 0)
		{
			return $query->result_array();
		}
		
		return false;
	}

	public function getTaxById($taxid)
	{
		$sql = 'SELECT t.*, m.name, m.lastname, DATE_FORMAT(FROM_UNIXTIME(t.createTime), "%Y-%m-%d") as "Date" FROM '.TABLE_TAXES.' as t JOIN '.TABLE_MEMBERS.' as m ON t.memberId = m.id WHERE t.id = '.(int)$taxid.' LIMIT 1';

		$query = $this->core->db->query($sql);

		if($query->num_rows > 0)
		{
			return $query->result_array();
		}
		
		return false;
	}
	public function edit($formData)
	{
		parse_str($formData,$data); 

		// Patikriname ar yra visi duomenys
		if(isset($data['memberId'],$data['name'],$data['telephone'],$data['lastname'],$data['memberBirthdayYear'],$data['memberBirthdayMonth'],$data['memberBirthdayDay'],$data['address'],$data['otherInfo']))
		{
			$id = (int)$data['memberId'];
			$name = $this->core->db->escape_str($data['name']);
			$lastname = $this->core->db->escape_str($data['lastname']);
			$telephone = $this->core->db->escape_str($data['telephone']);
			$month = (int)$data['memberBirthdayMonth'];
			$day = (int)$data['memberBirthdayDay'];
			if($month < 10){$month = '0'.$month;}
			if($day < 10){$day = '0'.$day;}
			$birthday = (int)$data['memberBirthdayYear']."-".$month."-".$day;
			$address = $this->core->db->escape_str($data['address']);
			$otherInfo = $this->core->db->escape_str($data['otherInfo']);

			$sql = 'UPDATE '.TABLE_MEMBERS.' SET name = "'.$name.'", lastname = "'.$lastname.'", telephone = "'.$telephone.'", birthday = "'.$birthday.'", address = "'.$address.'", otherInfo = "'.$otherInfo.'" WHERE id = '.$id.' LIMIT 1';
			
			$this->core->db->simple_query($sql);
			$return_array = array(
			    'memberId'	    => $id,
			    'name'	    => $name,
			    'lastname'	    => $lastname,
			    'birthday'	    => $birthday,
			    'telephone'	    => $telephone
			);

			return $return_array;
		}

		return false;
	}

	public function remove($memberId)
	{
		$sql = 'UPDATE '.TABLE_MEMBERS.' SET status = 0 WHERE id = '.(int)$memberId.' LIMIT 1';
		
		if($this->core->db->simple_query($sql))
		{
			return true;
		}
		
		return false;
	}

	public function create($data)
	{
		$name = $this->core->db->escape_str($data['memberName']);
		$lastname = $this->core->db->escape_str($data['memberLastname']);
		$phone = $this->core->db->escape_str($data['memberPhone']);
		$month = (int)$data['memberBirthdayMonth'];
		$day = (int)$data['memberBirthdayDay'];
		if($month < 10){$month = '0'.$month;}
		if($day < 10){$day = '0'.$day;}
		$birthday = (int)$data['memberBirthdayYear']."-".$month."-".$day;
		
	
		
		$insert_array = array(
		    'name'	    => $name,
		    'lastname'	    => $lastname,
		    'telephone'	    => $phone,
		    'birthday'	    => $birthday,
		    'status'	    => 1,
		    'createTime'    => time()
		);

		if($this->core->db->insert(TABLE_MEMBERS,$insert_array))
		{
			return true;
		}

		return false;
	}
}

?>