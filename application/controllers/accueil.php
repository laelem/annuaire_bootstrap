<?php

class Accueil extends CI_Controller
{	
	public function __construct()
	{
		parent::__construct();
        
		if(!$this->session->userdata('user_id')){
			redirect('/connexion');
		}
		
        // Chargement du fichier de langue g�n�ral
		$this->lang->load('main');
        
        // Chargement du th�me
		$this->load->library('layout');
	}
	
	public function index()
	{
		$this->accueil();
	}

	public function accueil()
	{
		$this->layout->view('accueil');
	}
}