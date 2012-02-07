<?php

class Users extends CI_Controller
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
        $this->load->model('users_model');
	}
	
	public function index()
	{
		$this->liste();
	}

	public function liste($message = '')
	{
		$sess_user = $this->session->userdata('user');
		if(empty($sess_user) || empty($sess_user['tri'])){
			$sess_user['tri'] = array(
				'champ' => 'membre',
				'type'	=> 'asc',
			);
		}
		$data['tab_users'] = $this->users_model->get_liste_user($sess_user['tri']);
		$data['message'] = $message;
		$this->layout->view('users/liste', $data);
	}
	
	public function tri($champ)
	{
		// recherche du tri en session
		$sess_user = $this->session->userdata('user');
		
		// mise à jour
		$data_session['user']['tri'] = array(
			'champ' => $champ,
			'type'	=> $sess_user['tri']['champ'] != $champ ? 'asc' : ($sess_user['tri']['type'] == 'asc' ? 'desc' : 'asc'),
		);
		$this->session->set_userdata($data_session);
		
		// redirection vers la liste
		redirect('/users/liste');
	}
	
	public function ajouter()
	{	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('login', 			$this->lang->line('users_libelle_login'), 			'required|alpha_dash|is_unique[user.login]');
		$this->form_validation->set_rules('password', 		$this->lang->line('users_libelle_password'), 		'required|alpha_dash');
		$this->form_validation->set_rules('nom', 			$this->lang->line('users_libelle_nom'), 			'required');
		$this->form_validation->set_rules('date_naissance',	$this->lang->line('users_libelle_date_naissance'), 	'callback_date_check');
		$this->form_validation->set_rules('adresse', 		$this->lang->line('users_libelle_adresse'), 		'required');
		$this->form_validation->set_rules('cp', 			$this->lang->line('users_libelle_cp'), 				'required|numeric|exact_length[5]');
		$this->form_validation->set_rules('ville', 			$this->lang->line('users_libelle_ville'), 			'required');
		$this->form_validation->set_rules('pays', 			$this->lang->line('users_libelle_pays'), 			'required');
		$this->form_validation->set_rules('tel', 			$this->lang->line('users_libelle_tel'), 			'required');
		$this->form_validation->set_rules('email', 			$this->lang->line('users_libelle_email'), 			'required|valid_email|is_unique[user.email]');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('table');
			$data['user'] = array(
				'actif' 			=> 1,
				'civ'				=> 1,
				'login'				=> '',
				'password'			=> '',
				'statut'			=> 1,
				'nom'				=> '',
				'prenom'			=> '',
				'date_naissance'	=> '',
				'adresse'			=> '',
				'adresse2'			=> '',
				'cp'				=> '',
				'ville'				=> '',
				'pays'				=> '',
				'tel'				=> '',
				'tel2'				=> '',
				'email'				=> '',
			);
			$this->layout->view('users/form', $data);
		}
		else
		{
			$date_naissance = $this->input->post('date_naissance', TRUE);
			if(!empty($date_naissance))
				$date_naissance = implode('-', array_reverse(explode('-', $date_naissance)));
			else
				$date_naissance = NULL;
			
			$data = array(
				'actif' 			=> $this->input->post('actif', TRUE),
				'civ' 				=> $this->input->post('civ', TRUE),
				'login' 			=> $this->input->post('login', TRUE),
				'password' 			=> $this->input->post('password', TRUE),
				'statut' 			=> $this->input->post('statut', TRUE),
				'nom' 				=> strtoupper($this->input->post('nom', TRUE)),
				'prenom' 			=> ucwords($this->input->post('prenom', TRUE)),
				'date_naissance' 	=> $date_naissance,
				'adresse' 			=> $this->input->post('adresse', TRUE),
				'adresse2' 			=> $this->input->post('adresse2', TRUE),
				'cp' 				=> $this->input->post('cp', TRUE),
				'ville' 			=> ucwords($this->input->post('ville', TRUE)),
				'pays' 				=> strtoupper($this->input->post('pays', TRUE)),
				'tel' 				=> $this->input->post('tel', TRUE),
				'tel2' 				=> $this->input->post('tel2', TRUE),
				'email' 			=> $this->input->post('email', TRUE),
			);
			$this->users_model->ajouter_user($data);
			redirect('/users/liste/users_message_ajouter');
		}
	}
	
	public function modifier($id)
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('login', 			$this->lang->line('users_libelle_login'), 			'required|alpha_dash|callback_login_unique');
		$this->form_validation->set_rules('password', 		$this->lang->line('users_libelle_password'), 		'required|alpha_dash');
		$this->form_validation->set_rules('nom', 			$this->lang->line('users_libelle_nom'), 			'required');
		$this->form_validation->set_rules('date_naissance',	$this->lang->line('users_libelle_date_naissance'), 	'callback_date_check');
		$this->form_validation->set_rules('adresse', 		$this->lang->line('users_libelle_adresse'), 		'required');
		$this->form_validation->set_rules('cp', 			$this->lang->line('users_libelle_cp'), 				'required|numeric|exact_length[5]');
		$this->form_validation->set_rules('ville', 			$this->lang->line('users_libelle_ville'), 			'required');
		$this->form_validation->set_rules('pays', 			$this->lang->line('users_libelle_pays'), 			'required');
		$this->form_validation->set_rules('tel', 			$this->lang->line('users_libelle_tel'), 			'required');
		$this->form_validation->set_rules('email', 			$this->lang->line('users_libelle_email'), 			'required|valid_email|callback_email_unique');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->library('table');
			$data['user'] = $this->users_model->get_user_complet($id);
			$date_naissance = $data['user']['date_naissance'];
			if(!empty($date_naissance))
				$data['user']['date_naissance'] = implode('-', array_reverse(explode('-', $date_naissance)));
			else
				$data['user']['date_naissance'] = '';
			$this->layout->view('users/form', $data);
		}
		else
		{
			$date_naissance = $this->input->post('date_naissance', TRUE);
			if(!empty($date_naissance))
				$date_naissance = implode('-', array_reverse(explode('-', $date_naissance)));
			else
				$date_naissance = NULL;
			
			$data = array(
				'actif' 			=> $this->input->post('actif', TRUE),
				'civ' 				=> $this->input->post('civ', TRUE),
				'login' 			=> $this->input->post('login', TRUE),
				'password' 			=> $this->input->post('password', TRUE),
				'statut' 			=> $this->input->post('statut', TRUE),
				'nom' 				=> strtoupper($this->input->post('nom', TRUE)),
				'prenom' 			=> ucwords($this->input->post('prenom', TRUE)),
				'date_naissance' 	=> $date_naissance,
				'adresse' 			=> $this->input->post('adresse', TRUE),
				'adresse2' 			=> $this->input->post('adresse2', TRUE),
				'cp' 				=> $this->input->post('cp', TRUE),
				'ville' 			=> ucwords($this->input->post('ville', TRUE)),
				'pays' 				=> strtoupper($this->input->post('pays', TRUE)),
				'tel' 				=> $this->input->post('tel', TRUE),
				'tel2' 				=> $this->input->post('tel2', TRUE),
				'email' 			=> $this->input->post('email', TRUE),
			);
			$this->users_model->modifier_user($data, $id);
			redirect('/users/liste/users_message_modifier');
		}
	}
	
	public function activer($id)
	{
		$this->users_model->activer_user($id);
		redirect('/users/liste');
	}
	
	public function desactiver($id)
	{
		if($this->session->userdata('user_id') != $id)
			$this->users_model->desactiver_user($id);
		redirect('/users/liste');
	}
	
	public function supprimer($id)
	{
		if($this->session->userdata('user_id') != $id){
			$this->users_model->supprimer_user($id);
			redirect('/users/liste/users_message_supprimer');
		}
		redirect('/users/liste');
	}
	
	public function date_check($str)
	{
		if(empty($str))
			return TRUE;
		$tab = explode('-', $str);
		if(count($tab) != 3  
		|| !ctype_digit($tab[0]) || !ctype_digit($tab[1]) || !ctype_digit($tab[2]) 
		|| !checkdate(intval($tab[1]), intval($tab[0]), intval($tab[2]))
		){
			$this->form_validation->set_message('date_check', $this->lang->line('global_erreur_format_date'));
			return FALSE;
		}
		return TRUE;
	}
	
	public function login_unique($str)
	{
		$id = $this->input->post('user_id', TRUE);
		if(!$this->users_model->login_unique($str, $id)){
			$this->form_validation->set_message('login_unique', $this->lang->line('is_unique'));
			return FALSE;
		}
		return TRUE;
	}
	
	public function email_unique($str)
	{
		$id = $this->input->post('user_id', TRUE);
		if(!$this->users_model->email_unique($str, $id)){
			$this->form_validation->set_message('email_unique', $this->lang->line('is_unique'));
			return FALSE;
		}
		return TRUE;
	}
}








