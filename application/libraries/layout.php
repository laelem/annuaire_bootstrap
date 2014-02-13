<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Layout
{
	private $CI;
	private $var = array();
	private $theme = 'default';
	
/*
|===============================================================================
| Constructeur
|===============================================================================
*/
	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->var['output'] = '';
        
		$this->var['menu_item'] = $this->CI->router->fetch_class();
		$this->var['rubrique'] = $this->CI->lang->line('rubrique_'.$this->CI->router->fetch_class());
		$this->var['action'] = $this->CI->router->fetch_method();
		if(in_array($this->var['action'], array('ajouter', 'modifier')))
			$this->var['str_action'] = $this->CI->lang->line('action_'.$this->var['menu_item'].'_'.$this->var['action']);
		//	Le titre est composé du nom du contrôleur
		$this->var['titre'] =  $this->CI->lang->line('global_app_titre') . ' - ' . $this->var['rubrique'];
		$this->var['desc'] =  $this->CI->lang->line('global_app_desc');
		
		//	Nous initialisons la variable $charset avec la même valeur que
		//	la clé de configuration initialisée dans le fichier config.php
		$this->var['charset'] = $this->CI->config->item('charset');
		
		$this->var['css'] = array();
		$this->var['js'] = array();
		
		if($this->CI->session->userdata('user_id')){
			$this->var['profil'] = array(
				'prenom'			=> $this->CI->session->userdata('prenom'),
				'nom'				=> $this->CI->session->userdata('nom'),
				'statut'			=> $this->CI->session->userdata('statut'),
			);
			$this->var['statuts_dispos'] = $this->CI->config->item('statut');
		}
	}
	
/*
|===============================================================================
| Méthodes pour charger les vues
|	. view
|	. views
|===============================================================================
*/
	
	public function view($name, $data = array())
	{
		$this->var['output'].= $this->CI->load->view($name, $data, true);
		$this->CI->load->view('../themes/'.$this->theme, $this->var);
	}
	
	public function views($name, $data = array())
	{
		$this->var['output'].= $this->CI->load->view($name, $data, true);
		return $this;
	}
	
/*
|===============================================================================
| Méthodes pour modifier les variables envoyées au layout
|	. set_titre
|	. set_charset
|===============================================================================
*/
	public function set_titre($titre)
	{
		if(is_string($titre) AND !empty($titre))
		{
			$this->var['titre'] = $titre;
			return true;
		}
		return false;
	}

	public function set_charset($charset)
	{
		if(is_string($charset) AND !empty($charset))
		{
			$this->var['charset'] = $charset;
			return true;
		}
		return false;
	}
	
/*
|===============================================================================
| Méthodes pour ajouter des feuilles de CSS et de JavaScript
|	. ajouter_css
|	. ajouter_js
|===============================================================================
*/
	public function ajouter_css($nom)
	{
		if(is_string($nom) AND !empty($nom))
		{
			if(file_exists('./assets/css/' . $nom . '.css')){
				$this->var['css'][] = base_url().'assets/css/'.$nom.'.css';
				return true;
			}
			elseif(file_exists('./assets/plugins/' . $nom . '.css')){
				$this->var['css'][] = base_url().'assets/plugins/'.$nom.'.css';
				return true;
			}
			elseif(file_exists('./assets/bootstrap/css/' . $nom . '.css')){
				$this->var['css'][] = base_url().'assets/bootstrap/css/'.$nom.'.css';
				return true;
			}
		}
		return false;
	}

	public function ajouter_js($nom)
	{
		if(is_string($nom) AND !empty($nom))
		{
			if(file_exists('./assets/javascript/' . $nom . '.js')){
				$this->var['js'][] = base_url().'assets/javascript/'.$nom.'.js';
				return true;
			}
			elseif(file_exists('./assets/plugins/' . $nom . '.js')){
				$this->var['js'][] = base_url().'assets/plugins/'.$nom.'.js';
				return true;
			}
			elseif(file_exists('./assets/bootstrap/js/' . $nom . '.js')){
				$this->var['js'][] = base_url().'assets/bootstrap/js/'.$nom.'.js';
				return true;
			}
		}
		return false;
	}
	
/*
|===============================================================================
| Méthode pour modifier le thème
|	. set_theme
|===============================================================================
*/
	
	public function set_theme($theme)
	{
		if(is_string($theme) AND !empty($theme) AND file_exists('./application/themes/' . $theme . '.php'))
		{
			$this->theme = $theme;
			return true;
		}
		return false;
	}
}

