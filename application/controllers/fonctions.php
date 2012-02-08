<?php

class Fonctions extends CI_Controller
{	
	public function __construct()
	{
		parent::__construct();
        
		if(!$this->session->userdata('user_id')){
			redirect('/connexion');
		}
		
        // Chargement du fichier de langue général
		$this->lang->load('main');
        
        // Chargement du thème
		$this->load->library('layout');
        
        // Ajout d'une feuille de style
        $this->layout->ajouter_css('style_general');
		
		// Chargement du modèle
        $this->load->model('fonctions_model');
	}
	
	public function index()
	{
		$this->liste();
	}

	public function liste($message = '')
	{
		// Ajout d'un script js pour des colonnes redimensionnables
        $this->layout->ajouter_js('colResizable/colResizable-1.3.source.min');
        $this->layout->ajouter_js('colResizable/start');
		
		$sess_fonction = $this->session->userdata('fonction');
		if(empty($sess_fonction) || empty($sess_fonction['tri'])){
			$sess_fonction['tri'] = array(
				'champ' => 'nom',
				'type'	=> 'asc',
			);
		}
		$data['tab_fonctions'] = $this->fonctions_model->get_liste_fonction($sess_fonction['tri']);
		$data['message'] = $message;
		$this->layout->view('fonctions/liste', $data);
	}
	
	public function tri($champ)
	{
		// recherche du tri en session
		$sess_fonction = $this->session->userdata('fonction');
		
		// mise à jour
		$data_session['fonction']['tri'] = array(
			'champ' => $champ,
			'type'	=> $sess_fonction['tri']['champ'] != $champ ? 'asc' : ($sess_fonction['tri']['type'] == 'asc' ? 'desc' : 'asc'),
		);
		$this->session->set_userdata($data_session);
		
		// redirection vers la liste
		redirect('/fonctions/liste');
	}
	
	protected function init_form()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->layout->ajouter_js('design_check_radio/design_check_radio.min');
		$this->layout->ajouter_css('design_check_radio/style');
		
		$this->form_validation->set_rules('actif', $this->lang->line('fonctions_libelle_actif'), 'required');
		$this->form_validation->set_rules('nom', $this->lang->line('fonctions_libelle_nom'), 'required');
	}
	
	public function ajouter()
	{
		// Initialisation du formulaire
		$this->init_form();
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('table');
			$data['fonction'] = array(
				'actif' => 0,
				'nom'	=> '',
			);
			$this->layout->view('fonctions/form', $data);
		}
		else
		{
			$actif = $this->input->post('actif', TRUE);
			$nom = $this->input->post('nom', TRUE);
			$this->fonctions_model->ajouter_fonction($actif, $nom);
			redirect('/fonctions/liste/fonctions_message_ajouter');
		}
	}
	
	public function modifier($id)
	{
		// Initialisation du formulaire
		$this->init_form();

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('table');
			$data['fonction'] = $this->fonctions_model->get_fonction($id);
			$this->layout->view('fonctions/form', $data);
		}
		else
		{
			$actif = $this->input->post('actif', TRUE);
			$nom = $this->input->post('nom', TRUE);
			$this->fonctions_model->modifier_fonction($id, $actif, $nom);
			redirect('/fonctions/liste/fonctions_message_modifier');
		}
	}
	
	public function activer($id)
	{
		$this->fonctions_model->activer_fonction($id);
		redirect('/fonctions/liste');
	}
	
	public function desactiver($id)
	{
		$this->fonctions_model->desactiver_fonction($id);
		redirect('/fonctions/liste');
	}
}








