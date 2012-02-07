<?php

class Annuaire extends CI_Controller
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
        $this->load->model('annuaire_model');
		
		// On vérifie les droits d'accès
		$acces_client = array('index', 'liste', 'tri', 'visualiser');
		$statuts = $this->config->item('statut');
		if(!in_array($this->uri->segment(2), $acces_client) && $statuts[$this->session->userdata('statut')] != 'admin')
			redirect('/annuaire/liste');
	}
	
	public function index()
	{
		$this->liste(1);
	}

	public function liste($page = 1, $message = '')
	{		
		// chargement des librairires
		$this->load->helper(array('form', 'url'));
		$this->load->library('table');
		
		$data['message'] = $message;
		
		$sess_annuaire = $this->init_liste($page);
		
		// Création de la pagination
		$this->load->library('pagination');
		$config_page = array(
			'base_url' 			=> base_url().'annuaire/liste',
			'first_url' 		=> base_url().'annuaire/liste/1',
			'total_rows' 		=> $this->annuaire_model->get_nb_contact($sess_annuaire['filtre']),
			'per_page' 			=> 2,
			'num_links' 		=> 2,
			'use_page_numbers' 	=> TRUE,
			'full_tag_open' 	=> '<p class="pagination">',
			'full_tag_close' 	=> '</p>',
			'first_link' 		=> '&lt;&lt;',
			'last_link' 		=> '&gt;&gt;',
			'cur_tag_open' 		=> '<span>',
			'cur_tag_close' 	=> '</span>',
		);
		$this->pagination->initialize($config_page); 
		$data['pagination'] = $this->pagination->create_links();

		// Si l'utilisateur est client, on ne prend pas les contacts inactif
		$inactif = TRUE;
		$statuts = $this->config->item('statut');
		if($statuts[$this->session->userdata('statut')] != 'admin')
			$inactif = FALSE;
		
		// Définition de la requête en fonction du numéro de page et du tri
		// Calcul de l'offset en fonction du numéro de page
		$offset = $sess_annuaire['page'] == 1 ? 0 : ($sess_annuaire['page'] - 1) * $config_page['per_page'];
		$data['liste_contact'] = $this->annuaire_model->get_liste_contact($config_page['per_page'], $offset, $sess_annuaire['tri'], $sess_annuaire['filtre'], $inactif);
		
		// Affichage de la vue
		$this->layout->view('annuaire/liste', $data);
	}
	
	protected function init_liste($page = '')
	{
		// récupération du contexte en session
		$data_session['annuaire'] = $this->session->userdata('annuaire');

		// Si aucun tri n'est en session
		if(empty($data_session['annuaire']) || empty($data_session['annuaire']['tri']['champ'])){
		
			// On prend les valeurs par défaut
			$data_session['annuaire']['tri'] = array(
				'champ' => 'date_maj',
				'type'	=> 'desc',
			);
		}
		
		if(empty($data_session['annuaire']) || empty($data_session['annuaire']['filtre']))
			$data_session['annuaire']['filtre'] = array(
				'champ' 	=> 'nom',
				'recherche' => '',
				'like'		=> 'none',
			);
			
		// si une page est demandée
		if(!empty($page)){
			if(intval($page) == 0)
				$data_session['annuaire']['page'] = 1;
			else
				$data_session['annuaire']['page'] = $page;
		}
		// Si aucune page n'est en session
		elseif(empty($data_session['annuaire']) || empty($data_session['annuaire']['page']))
			$data_session['annuaire']['page'] = 1;
		
		// enregistrement du contexte en session
		$this->session->set_userdata($data_session);
		
		return $data_session['annuaire'];
	}
	
	public function tri($champ)
	{
		// recherche des données en session
		$data_session['annuaire'] = $this->session->userdata('annuaire');
		
		// mise à jour
		$data_session['annuaire']['tri'] = array(
			'champ' => $champ,
			'type'	=> $sess_annuaire['tri']['champ'] != $champ ? 'asc' : ($sess_annuaire['tri']['type'] == 'asc' ? 'desc' : 'asc'),
		);
		$this->session->set_userdata($data_session);
		
		// redirection vers la liste
		redirect('/annuaire/liste/'.$data_session['annuaire']['page']);
	}
	
	public function filtre($lettre = '')
	{
		// on récupère les données annuaire en session pour les modifier
		$data_session['annuaire'] = $this->session->userdata('annuaire');
		
		if(!empty($lettre)){
		
			// mise à jour
			$data_session['annuaire']['filtre'] = array(
				'champ' => 'nom',
				'recherche'	=> $lettre,
				'like' => 'after',
			);
		}
		else{
		
			// Récupération des filtres postés
			$input_nom_prenom = $this->input->post('input_nom_prenom', TRUE);
			$radio_nom_prenom = $this->input->post('radio_nom_prenom', TRUE);
			
			// mise à jour
			$data_session['annuaire']['filtre'] = array(
				'champ' => $radio_nom_prenom,
				'recherche'	=> $input_nom_prenom,
				'like' => 'both',
			);
		}
		
		$this->session->set_userdata($data_session);
		
		// redirection vers la liste
		redirect('/annuaire/liste');
	}
	
	public function reset_filtre()
	{	
		// on récupère les données annuaire en session pour les modifier
		$data_session['annuaire'] = $this->session->userdata('annuaire');
		
		// mise à jour
		$data_session['annuaire']['filtre'] = array(
			'champ' => 'nom',
			'recherche'	=> '',
			'like' => 'none',
		);
		$this->session->set_userdata($data_session);
		
		// redirection vers la liste
		redirect('/annuaire/liste/'.$data_session['annuaire']['page']);
	}
	
	public function ajouter()
	{	
		// chargement des librairies
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		// règles de validation
		$this->form_validation->set_rules('nom', 			$this->lang->line('annuaire_libelle_nom'), 			'required');
		$this->form_validation->set_rules('prenom', 		$this->lang->line('annuaire_libelle_prenom'), 		'required');
		$this->form_validation->set_rules('societe', 		$this->lang->line('annuaire_libelle_societe'), 		'required');
		$this->form_validation->set_rules('fonctions', 		$this->lang->line('annuaire_libelle_fonctions'), 	'required');
		$this->form_validation->set_rules('cp', 			$this->lang->line('users_libelle_cp'), 				'numeric|exact_length[5]');
		$this->form_validation->set_rules('email', 			$this->lang->line('users_libelle_email'), 			'valid_email');

		// règles pour l'upload
		$config_upload_image = array(
			'upload_path' 		=> './upload/user_photo/',
			'allowed_types' 	=> 'gif|jpg|png',
			'max_size'			=> '1000', // Ko
			'max_width'  		=> '1024', // pixels
			'max_height'  		=> '768',
		);
		$this->load->library('upload');
		$this->upload->initialize($config_upload_image);
		
		// si on arrive sur le formulaire
		// ou si il y a des erreurs générales 
		if ($this->form_validation->run() == FALSE || (!$this->upload->do_upload('photo') && $_FILES['photo']['error']!=4 ))
		{
			// chargement des librairies
			$this->load->library('table');
			$this->load->model('fonctions_model');
			
			// Initialisation des valeurs par défaut
			$data['contact'] = array(
				'actif' 			=> 1,
				'civ'				=> 1,
				'nom'				=> '',
				'prenom'			=> '',
				'tel'				=> '',
				'mobile'			=> '',
				'fax'				=> '',
				'decideur'			=> 0,
				'societe'			=> '',
				'fonctions'			=> array(),
				'adresse'			=> '',
				'adresse2'			=> '',
				'cp'				=> '',
				'ville'				=> '',
				'pays'				=> '',
				'web'				=> '',
				'email'				=> '',
				'photo'				=> '',
				'com'				=> '',
			);
			
			$data['fonctions'] = $this->fonctions_model->get_fonctions_actives();
			$data['erreur_upload_photo'] = $this->upload->display_errors();
			$data['lien_photo'] = '';
			$data['btn_suppr_photo'] = '';
			
			// affichage de la vue
			$this->layout->view('annuaire/form', $data);
		}
		// Si pas d'erreur
		else
		{	
			// Si aucun fichier n'est présent
			if($_FILES['photo']['error']==4)
				$data_photo['file_name'] = NULL;
			else{
			
				$data_photo = $this->upload->data();
				
				// Création de la miniature
				$config_image = array(
					'source_image'		=> $data_photo['full_path'],
					'new_image' 		=> $data_photo['file_path'].'thumbs/',
					'create_thumb' 		=> TRUE,
					'maintain_ratio' 	=> TRUE,
					'thumb_marker' 		=> '',
					'width'	 			=> 50,
					'height'			=> 50,
				);
				$this->load->library('image_lib', $config_image); 
				$this->image_lib->resize();
			}
			
			// Reprise des valeurs
			$data_contact = array(
				'actif' 			=> $this->input->post('actif', TRUE),
				'civ' 				=> $this->input->post('civ', TRUE),
				'nom' 				=> strtoupper($this->input->post('nom', TRUE)),
				'prenom' 			=> ucwords($this->input->post('prenom', TRUE)),
				'tel' 				=> $this->input->post('tel', TRUE),
				'mobile' 			=> $this->input->post('mobile', TRUE),
				'fax' 				=> $this->input->post('fax', TRUE),
				'decideur' 			=> $this->input->post('decideur', TRUE),
				'societe' 			=> strtoupper($this->input->post('societe', TRUE)),
				'adresse' 			=> $this->input->post('adresse', TRUE),
				'adresse2' 			=> $this->input->post('adresse2', TRUE),
				'cp' 				=> $this->input->post('cp', TRUE),
				'ville' 			=> ucwords($this->input->post('ville', TRUE)),
				'pays' 				=> strtoupper($this->input->post('pays', TRUE)),
				'web' 				=> $this->input->post('web', TRUE),
				'email' 			=> $this->input->post('email', TRUE),
				'photo' 			=> $data_photo['file_name'],
				'com' 				=> $this->input->post('com', TRUE),
				'date_maj' 			=> date_format(date_create(), 'Y-m-d H:i:s'),
			);
			
			$data_fonctions = $this->input->post('fonctions', TRUE);
			
			// traitement
			$contact_id = $this->annuaire_model->ajouter_contact($data_contact);
			$this->annuaire_model->ajouter_fonctions_contact($contact_id, $data_fonctions);
			
			// Redirection vers la liste à la page mise en session
			$sess_annuaire = $this->session->userdata('annuaire');
			redirect('/annuaire/liste/'.$sess_annuaire['page'].'/annuaire_message_ajouter');
		}
	}
	
	public function modifier($id)
	{
		// chargement des librairies
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		// règles de validation
		$this->form_validation->set_rules('nom', 			$this->lang->line('annuaire_libelle_nom'), 			'required');
		$this->form_validation->set_rules('prenom', 		$this->lang->line('annuaire_libelle_prenom'), 		'required');
		$this->form_validation->set_rules('societe', 		$this->lang->line('annuaire_libelle_societe'), 		'required');
		$this->form_validation->set_rules('fonctions', 		$this->lang->line('annuaire_libelle_fonctions'), 	'required');
		$this->form_validation->set_rules('cp', 			$this->lang->line('users_libelle_cp'), 				'numeric|exact_length[5]');
		$this->form_validation->set_rules('email', 			$this->lang->line('users_libelle_email'), 			'valid_email');

		// règles pour l'upload
		$config_upload_image = array(
			'upload_path' 		=> './upload/user_photo/',
			'allowed_types' 	=> 'gif|jpg|png',
			'max_size'			=> '1000', // Ko
			'max_width'  		=> '1024', // pixels
			'max_height'  		=> '768',
		);
		$this->load->library('upload');
		$this->upload->initialize($config_upload_image);

		// si on arrive sur le formulaire
		// ou si il y a des erreurs générales 
		// ou si on a tenté d'uploader un fichier mais qu'une erreur est survenue (le champ photo n'est pas requis)
		if ($this->form_validation->run() == FALSE || ($_FILES['photo']['error']!=4  && !$this->upload->do_upload('photo')))
		{
			// chargement des librairies
			$this->load->library('table');
			$this->load->model('fonctions_model');
			
			// récupération des valeurs
			$data['contact'] = $this->annuaire_model->get_contact($id);
			$data['contact']['fonctions'] = $this->fonctions_model->get_fonctions_contact($id);
			$data['fonctions'] = $this->fonctions_model->get_fonctions_actives();
			
			// gestion de l'affichage de l'upload
			$data['erreur_upload_photo'] = $this->upload->display_errors();
			if(!empty($data['contact']['photo'])){
				$data['lien_photo'] = '<a href="'.base_url().'upload/user_photo/'.$data['contact']['photo'].'" target="_blank" ><img src="'.base_url().'upload/user_photo/thumbs/'.$data['contact']['photo'].'" /></a><br />';
				$data['btn_suppr_photo'] = '<button type="button" onclick="window.location.href=\''.site_url('/annuaire/supprimer_photo/'.$id).'\'" >'.$this->lang->line('general_supprimer').'</button><br />';
			}
			else{
				$data['lien_photo'] = '';
				$data['btn_suppr_photo'] = '';
			}
			
			// affichage de la vue
			$this->layout->view('annuaire/form', $data);
		}
		// Si pas d'erreur
		else
		{
			// Si aucun fichier n'est présent
			if($_FILES['photo']['error']==4){
				$data_photo['file_name'] = $this->annuaire_model->get_photo($id);
			}
			else{
				$data_photo = $this->upload->data();
				
				// Supression de l'éventuelle image existante + miniature
				$nom_fichier = $this->annuaire_model->get_photo($id);
				if(!empty($nom_fichier)){
					unlink('./upload/user_photo/'.$nom_fichier);
					unlink('./upload/user_photo/thumbs/'.$nom_fichier);
				}
				
				// Création de la miniature
				$config_image = array(
					'source_image'		=> $data_photo['full_path'],
					'new_image' 		=> $data_photo['file_path'].'thumbs/',
					'create_thumb' 		=> TRUE,
					'maintain_ratio' 	=> TRUE,
					'thumb_marker' 		=> '',
					'width'	 			=> 50,
					'height'			=> 50,
				);
				$this->load->library('image_lib', $config_image); 
				$this->image_lib->resize();
			}

			// récupération des valeurs
			$data_contact = array(
				'actif' 			=> $this->input->post('actif', TRUE),
				'civ' 				=> $this->input->post('civ', TRUE),
				'nom' 				=> strtoupper($this->input->post('nom', TRUE)),
				'prenom' 			=> ucwords($this->input->post('prenom', TRUE)),
				'tel' 				=> $this->input->post('tel', TRUE),
				'mobile' 			=> $this->input->post('mobile', TRUE),
				'fax' 				=> $this->input->post('fax', TRUE),
				'decideur' 			=> $this->input->post('decideur', TRUE),
				'societe' 			=> strtoupper($this->input->post('societe', TRUE)),
				'adresse' 			=> $this->input->post('adresse', TRUE),
				'adresse2' 			=> $this->input->post('adresse2', TRUE),
				'cp' 				=> $this->input->post('cp', TRUE),
				'ville' 			=> ucwords($this->input->post('ville', TRUE)),
				'pays' 				=> strtoupper($this->input->post('pays', TRUE)),
				'web' 				=> $this->input->post('web', TRUE),
				'email' 			=> $this->input->post('email', TRUE),
				'photo' 			=> $data_photo['file_name'],
				'com' 				=> $this->input->post('com', TRUE),
				'date_maj' 			=> date_format(date_create(), 'Y-m-d H:i:s'),
			);
			$data_fonctions = $this->input->post('fonctions', TRUE);
			
			// traitement
			$this->annuaire_model->supprimer_fonctions_contact($id);
			$this->annuaire_model->modifier_contact($data_contact, $id);
			$this->annuaire_model->ajouter_fonctions_contact($id, $data_fonctions);
			
			// Redirection vers la liste à la page mise en session
			$sess_annuaire = $this->session->userdata('annuaire');
			redirect('/annuaire/liste/'.$sess_annuaire['page'].'/annuaire_message_modifier');
		}
	}
	
	public function visualiser($id)
	{
		// chargement des librairies
		$this->load->library('table');
		$this->load->model('fonctions_model');
		
		// récupération des valeurs
		$data['contact'] = $this->annuaire_model->get_contact($id);
		if(!empty($data['contact']['photo']))
			$data['lien_photo'] = '<a href="'.base_url().'upload/user_photo/'.$data['contact']['photo'].'" target="_blank" ><img src="'.base_url().'upload/user_photo/thumbs/'.$data['contact']['photo'].'" /></a><br />';
		else
			$data['lien_photo'] = 'Aucune photo';
		$data['contact']['fonctions'] = $this->fonctions_model->get_fonctions_contact($id, 'affichage');
		$data['fonctions'] = $this->fonctions_model->get_fonctions_actives();
		
		// affichage de la vue
		$this->layout->view('annuaire/contact', $data);
	}
	
	public function activer($id)
	{
		$this->annuaire_model->activer_contact($id);
		$sess_annuaire = $this->session->userdata('annuaire');
		redirect('/annuaire/liste/'.$sess_annuaire['page']);
	}
	
	public function desactiver($id)
	{	
		$this->annuaire_model->desactiver_contact($id);
		$sess_annuaire = $this->session->userdata('annuaire');
		redirect('/annuaire/liste/'.$sess_annuaire['page']);
	}
	
	public function supprimer($id)
	{		
		$this->annuaire_model->supprimer_contact($id);
		$sess_annuaire = $this->session->userdata('annuaire');
		redirect('/annuaire/liste/'.$sess_annuaire['page'].'/annuaire_message_supprimer');
	}
	
	public function supprimer_photo($id)
	{		
		$nom_fichier = $this->annuaire_model->get_photo($id);
		unlink('./upload/user_photo/'.$nom_fichier);
		unlink('./upload/user_photo/thumbs/'.$nom_fichier);
		$this->annuaire_model->suppr_photo($id);
		redirect('/annuaire/modifier/'.$id);
	}
}








