<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

class validation
{
    /**
    * IP (V4) adreso patikrinimas
    * @access	public
    * @param	string
    * @return	bool
    */

    function ip_address($ip_address)
    {
        // Suzinom, is kiek daliu sudarytas ip
        $pattern = explode('.',$ip_address);

        // Patikrinam ar yra lygiai 4
        if(count($pattern) != 4)
        {
            return false;
        }

        // Patikrinam, ar teisingas ip adresas
        foreach($pattern as $key)
        {
            if($key == '' OR preg_match("/[^0-9]/", $key) OR $key > 255 OR strlen($key) > 3)
            {
				// Neteisingas
                return false;
            }
        }

        // Teisingas
        return true;
    }

    /**
    * Elektroninio pasto patikrinimas
    * @access	public
    * @param	string
    * @return	bool
    */

    public function email($email)
    {
        if(preg_match("/^([a-zA-Z0-9]+[a-zA-Z0-9_.-]*@[a-zA-Z0-9]+[a-zA-Z0-9_.-])*\.[a-z]{2,4}$/", $email))
        {
            // Elektroninio pasto sintakse teisinga
            return true;
        }

        // Elektroninio pasto sintakse neteisinga
        return false;
    }
}
?>