<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

/**
 * Funkcijos
 *
 * load($page)
 * publish()
 * replace($var, $content)
 * set_title($title)
 * set_location($location)
 * set_menu_active($menu)
 * menu_active()
 * append_title($title)
 * get_title()
 * load_meta($name,$content="")
 * load_js($url,$out = false,$extra_ext = '')
 * load_css($url,$out = false)
 * get_js()
 * get_meta()
 * set_extra_head($extra)
 * get_extra_head()
 */

class Template
{
	private $menu_active		= NULL;		// Aktyvaus meniu punkto pavadinimas
	private $title		  	= NULL;		// Pagrindine antraste
	private $appended_titles	= array();	// Prijunktos antrastes
	private $meta			= array();	// Meta zymos
	private $javascripts		= array();	// Javascriptai
	private $stylesheets		= array();	// Styliai
	private $extra_head		= NULL;		// Papildomi head duomenys
	public  $location		= '';
	private $page_content;
	private $justContent		= false;

	/**
	* Puslapio ikrovimas
	* @access	public
	* @param	string
	* @return	void
	*/

	public function load($page,$logged=false)
	{
		if(!$this->justContent)
		{
		        $this->page_content = file_get_contents(DIR_HTML.'layout.html');
		}
		else
		{
			$this->page_content = '<{CONTENT}>';
		}
		
		if($logged)
		{
			$this->replace('<{CONTENT}>',file_get_contents(DIR_HTML.'loggedLayout.html'));
		}

		$this->replace('<{CONTENT}>',file_get_contents(DIR_HTML.$page.'.html'));

	}

	/**
	* Puslapio atvaizdavimas i narsykle
	* @access	public
	* @return	void
	*/

	public function publish()
	{
		if(!$this->justContent)
		{
			// Meta tagai
			$metatag = '';

			foreach($this->get_meta() as $key => $value)
			{
				$metatag = $metatag.'<meta name="'.$key.'" content="'.$value.'" />'."\n		";
			}

			// Styliai
			$stylesheet = '';

			foreach($this->get_css() as $url)
			{
				$stylesheet = $stylesheet.'<link href="'.$url.'" rel="stylesheet" type="text/css" />'."\n		";
			}

			// Javascriptai
			$javascripts = '';

			foreach($this->get_js() as $url)
			{
				$javascripts = $javascripts.'<script type="text/javascript" src="'.$url.'"></script>'."\n		";
			}

			// Layouto kurimo masyvas
			$replace_array = array(
				'<{TITLE}>'		=> $this->get_title(),
				'<{METATAG}>'		=> $metatag,
				'<{STYLESHEET}>'	=> $stylesheet,
				'<{JAVASCRIPTS}>'	=> $javascripts,
				'<{EXTRA_HEAD}>'	=> $this->get_extra_head(),
			);
		}

		$replace_array['<{WEBROOT}>'] = $this->location;

		// Redaguojam layouta
		$this->replace(array_keys($replace_array),array_values($replace_array));
		
		if(!$this->justContent)
		{
			echo $this->page_content;
		}
		else
		{			
			// Patikrinam ar kreipiasi ajax
			if(!IS_AJAX)
			{
				die();
			}
			
			// Formuojame json headeri
			header("Content-type: application/json; charset=utf-8");

			// Formuojame turinio masyva
			$pageData		= new stdClass();
			
			$pageData->title	= $this->get_title();
			$pageData->js		= array_slice($this->get_js(),3);
			$pageData->css		= array_slice($this->get_css(),1);
			$pageData->content	= '<div id="pageInit" style="visibility:hidden;display:none;">a</div>'.$this->page_content;
			$pageData->menuActive	= 'menu_'.$this->menu_active();
		
			// Atvaizduojame json formatu
			echo json_encode($pageData);
		}
	}

	/**
	 * Funkcija skirta ijungti tik contento atvaizdavimui.
	 */
	
	public function justContent()
	{
		$this->justContent = true;
	}
	
	/**
	* Kintamuju replacinimas
	* @access	public
	* @return	void
	*/

	public function replace($var, $content)
	{
		$this->page_content = str_replace($var, $content, $this->page_content);
	}

	/**
	* Puslapio antrastes nustatymas
	* @access	public
	* @param	string
	* @return	void
	*/

	public function set_title($title)
	{
		$this->title = $title;
	}

	/**
	* Puslapio webroot
	* @access	public
	* @param	string
	* @return	void
	*/

	public function set_location($location)
	{
		$this->location = $location;
	}

	/**
	* Aktyvaus meniu punkto nustatymas
	* @access	public
	* @param	string
	* @return	void
	*/

	public function set_menu_active($menu)
	{
		$this->menu_active = $menu;
	}

	/**
	* Aktyvaus meniu grazinimas
	* @access	public
	* @param	void
	* @return	string
	*/

	public function menu_active()
	{
		return $this->menu_active;
	}

	/**
	* Puslapio antrastes papildomi poskiriai
	* @access	public
	* @param	string(array)
	* @return	void
	*/

	public function append_title($title)
	{
		if(is_array($title))
		{
			$this->appended_titles = array_merge($this->appended_titles,$title);
		}
		else
		{
			$this->appended_titles[] = $title;
		}
	}

	/**
	* Puslapio antrastes  grazinimas
	* @access	public
	* @param	void
	* @return	string
	*/

	public function get_title()
	{
		$title = array($this->title);
		$title = array_merge($title,$this->appended_titles);
		return implode(" - ",$title);
	}

	/**
	* Meta zymu ikrovimas
	* @access	public
	* @param	string(array), string
	* @return	void
	*/

	public function load_meta($name,$content="")
	{
		if(is_array($name))
		{
			$this->meta = array_merge($this->meta,$name);
		}
		else
		{
			$this->meta[$name] = $content;
		}
	}

	/**
	* Javascriptu ikelimas
	* @access	public
	* @param	string(array), bool
	* @return	void
	*/

	public function load_js($url,$out = false,$extra_ext = '')
	{
		if($this->justContent)
		{
		    $location = $this->location;
		    $this->location = '/';
		}

		if(is_array($url))
		{
			// patikrinam ar tai vidinis js
			if($out == false)
			{
				foreach($url as $js)
				{
					$this->javascripts[] = $this->location.'javascripts/'.$js.'.js'.$extra_ext;
				}
			}
			else
			{
				$this->javascripts = array_merge($this->javascripts,$url);
			}
		}
		else
		{
			if($out == false)
			{
				$this->javascripts[] = $this->location.'javascripts/'.$url.'.js'.$extra_ext;
			}
			else
			{
				$this->javascripts[] = $url;
			}
		}

		if($this->justContent)
		{
		    $this->location = $location;
		}
	}

	/**
	* Styliu ikelimas
	* @access	public
	* @param	string(array)
	* @return	void
	*/

	public function load_css($url,$out = false,$extra_ext = '')
	{
		if(is_array($url))
		{
			// patikrinam ar tai vidinis css
			if($out == false)
			{
				foreach($url as $out)
				{
					$this->stylesheets[] = $this->location.'css/'.$out.'.css'.$extra_ext;
				}
			}
			else
			{
				$this->stylesheets = array_merge($this->stylesheets,$url);
			}
		}
		else
		{
			if($out == false)
			{
				$this->stylesheets[] = $this->location.'css/'.$url.'.css'.$extra_ext;
			}
			else
			{
				$this->stylesheets[] = $url;
			}
		}
	}

	/**
	* Javascriptu grazinimas
	* @access	public
	* @param	void
	* @return	array
	*/

	public function get_js()
	{
		return $this->javascripts;
	}

	/**
	* Styliu grazinimas
	* @access	public
	* @param	void
	* @return	array
	*/

	public function get_css()
	{
		return $this->stylesheets;
	}

	/**
	* Meta zymu grazinimas
	* @access	public
	* @param	void
	* @return	array
	*/

	public function get_meta()
	{
		return $this->meta;
	}

	/**
	* Papildomos head zymos
	* @access	public
	* @param	string
	* @return	void
	*/

	public function set_extra_head($extra)
	{
		// pridedam extra
		$this->extra_head = $extra;
	}

	/**
	* Grazina papildomus head duomenis
	* @access	public
	* @param	void
	* @return	string
	*/

	public function get_extra_head()
	{
		return $this->extra_head;
	}
}

?>