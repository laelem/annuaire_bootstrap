<?php

class Connexion extends CI_Controller
{	
	public function __construct()
	{
		parent::__construct();
        
		if($this->session->userdata('user_id')){
			redirect('/accueil');
		}
		
        // Chargement du fichier de langue général
		$this->lang->load('main');
        
        // Chargement du thème
		$this->load->library('layout');
        
        // Ajout d'une feuille de style
        $this->layout->ajouter_css('style_general');
        
		// Chargement du helper et de la librairie pour les formulaires
        $this->load->helper('form');
        $this->load->library('form_validation');
		
		// Chargement du modèle
        $this->load->model('users_model');
	}
	
	public function index()
	{
		$this->connexion();
	}
	
	public function connexion()
	{
		$this->form_validation->set_rules('login', $this->lang->line('form_libelle_champ_email'), "required|alpha_dash|xss_clean|callback_verif_compte");
		$this->form_validation->set_rules('password', $this->lang->line('form_libelle_champ_password'), 'required|alpha_dash|xss_clean');
	
		if($this->form_validation->run() == FALSE)
		{
			$this->load->library('table');
			$this->layout->view('connexion');
		}
		else{
			// Enregistrement de l'utilisateur en session
			$login = $this->input->post('login', TRUE);
			$password = $this->input->post('password', TRUE);
			$user = $this->users_model->get_user($login, $password);
			$data = array(
			   'user_id'  	=> $user['user_id'],
			   'nom'     	=> $user['nom'],
			   'prenom' 	=> $user['prenom'],
			   'statut'		=> $user['statut'],
			);
			$this->session->set_userdata($data);
			redirect('/accueil');
		}
	}
	
	public function verif_compte()
	{
		$login = $this->input->post('login', TRUE);
		$password = $this->input->post('password', TRUE);
		if(!$this->users_model->compte_existe($login, $password)){
			$this->form_validation->set_message('verif_compte', $this->lang->line('global_erreur_compte_invalide'));
			return FALSE;
		}
		elseif(!$this->users_model->compte_actif($login, $password)){
			$this->form_validation->set_message('verif_compte', $this->lang->line('global_erreur_compte_inactif'));
			return FALSE;
		}
		return TRUE;
	}
}