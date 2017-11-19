<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

if(!function_exists('debug'))
{
	/* Debuginimo funkcija */
	function debug($array)
	{
		// Pasiemam informacija, is kur yra iskvieciama funkcija
		$bc = debug_backtrace();
		// Formuojam debuginimo ouputa
		$debug = "\n<pre>\nDebug | File: <b>".$bc[0]["file"]."</b> on line: <b>".$bc[0]["line"]."</b> |\n".print_r($array,true)."</pre>\n";
		// Grazinam informacija
		//echo $debug;
		return $debug;
	}
}

if(!function_exists('get_ip_address'))
{
	/* ip adreso gavimo funkcija */
	function get_ip_address()
	{
		// Suzinom ip adresa
		if(isset($_SERVER['REMOTE_ADDR']) AND isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(isset($_SERVER['REMOTE_ADDR']))
		{
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif(isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		$core =& core::init();
		$core->load('validation');

		// Patikrinam ar ip adresas teisingas
		if($core->valid->ip_address($ip_address))
		{
			return $ip_address;
		}
		else
		{
			return '0.0.0.0';
		}
	}
}

if(!function_exists('user_ip2hex'))
{
	/* Vartotojo ip pavertimas i sesioliktaini formata */
	function user_ip2hex()
	{
		return dechex(ip2long(get_ip_address()));
	}
}

if(!function_exists('user_hex2ip'))
{
	/* Vartotojo ip gavimas is sesioliktainio formato */
	function user_hex2ip()
	{
		return long2ip(hexdec(user_ip2hex()));
	}
}

if(!function_exists('ip2hex'))
{
	/* ip pavertimas i sesioliktaini formata */
	function ip2hex($ip)
	{
		return dechex(ip2long($ip));
	}
}

if(!function_exists('hex2ip'))
{
	/* ip gavimas is sesioliktainio formato */
	function hex2ip($ip)
	{
		return long2ip(hexdec($ip));
	}
}

if(!function_exists('log_message'))
{
	/* Zinuciu loginimas */
	function log_message($msg,$type='')
	{
		// Siandienos logai
		$filepath = DIR_LOGS.'log_'.$type.'-'.date('Y-m-d').EXT;

		// Zinutes pradzia
		$message = '';

		// patikrinam ar failas egzistuoja
		if(!file_exists($filepath))
		{
			$message .= "<"."?php defined('DIR_LOGS') or die('Access denied!'); ?".">\n\n";
		}
		else
		{
			//$message = read_file($filepath);
		}

		// Konstruojam zinute
		$message .= '['.get_ip_address().'] '.date('Y-m-d H:i:s',time()). ' --> '.$msg."\n";

		// Iterpiam loga
		if(!write_file($filepath, $message,FOPEN_READ_WRITE_CREATE))
		{
			return false;
		}

		@chmod($filepath, '0666');
		return TRUE;
	}
}

// Konstantos pagalbinem funkcijom

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

define('FOPEN_READ','rb');
define('FOPEN_READ_WRITE','r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE','wb');
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE','w+b');
define('FOPEN_WRITE_CREATE','ab');
define('FOPEN_READ_WRITE_CREATE','a+b');
define('FOPEN_WRITE_CREATE_STRICT','xb');
define('FOPEN_READ_WRITE_CREATE_STRICT','x+b');

// Failo turinio nuskaitymo funkcija
if(!function_exists('read_file'))
{
	function read_file($file)
	{
		if(!file_exists($file))
		{
			return FALSE;
		}

		if(function_exists('file_get_contents'))
		{
			return file_get_contents($file);
		}

		if(!$fp = @fopen($file, FOPEN_READ))
		{
			return FALSE;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0)
		{
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}
}

// Funkcija rasymui i faila
if(!function_exists('write_file'))
{
	function write_file($path, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE)
	{
		if (!$fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return TRUE;
	}
}

if(!function_exists('is_really_writable'))
{
	/* Patikrina ar i faila galima irasyti duomenis */
	function is_really_writable($file)
	{	
		return true; // siuo momentu isjungiam  
		if(DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		if(is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(rand(1,100));

			if(($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, '0777');
			@unlink($file);
			return TRUE;
		}
		elseif(($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// Failu istrinymo funkcija
if(!function_exists('delete_files'))
{
	function delete_files($path, $del_dir = FALSE, $level = 0)
	{
		$path = preg_replace("|^(.+?)/*$|", "\\1", $path);

		if(!$current_dir = @opendir($path))
			return;

		while(FALSE !== ($filename = @readdir($current_dir)))
		{
			if($filename != "." and $filename != "..")
			{
				if(is_dir($path.'/'.$filename))
				{
					if(substr($filename, 0, 1) != '.')
					{
						delete_files($path.'/'.$filename, $del_dir, $level + 1);
					}
				}
				else
				{
					unlink($path.'/'.$filename);
				}
			}
		}

		@closedir($current_dir);

		if($del_dir == TRUE AND $level > 0)
		{
			@rmdir($path);
		}
	}
}

if(!function_exists('jump_to_main_page'))
{
    /* Perkelia i pradini puslapi */
    function jump_to_main_page($extra_url_append='')
    {
        $core =& core::init();
        // perkeliam i pradini puslapi
        header("Location: ".$core->dbcfg('web_location').$extra_url_append);
        // Jei netycia neperkletu i pradini puslapi, stabdom veikima
        exit;
    }
}

?>