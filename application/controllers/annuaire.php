<?php

class Annuaire extends CI_Controller
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
		
		// Chargement du mod�le
        $this->load->model('annuaire_model');
		
		// On v�rifie les droits d'acc�s
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
		// chargement des librairies
		$this->load->helper(array('form', 'url'));
		$this->load->library('table');
		
		$data['message'] = $message;
		
		$sess_annuaire = $this->init_liste($page);
		
		// Cr�ation de la pagination
		$this->load->library('pagination');
		$config_page = array(
			'base_url' 			=> base_url().'annuaire/liste',
			'first_url' 		=> base_url().'annuaire/liste/1',
			'total_rows' 		=> $this->annuaire_model->get_nb_contact($sess_annuaire['filtre']),
			'per_page' 			=> 4,
			'num_links' 		=> 2,
			'use_page_numbers' 	=> TRUE,
			'full_tag_open' 	=> '<div class="pagination"><ul>',
			'full_tag_close' 	=> '</ul></div>',
			'first_link' 		=> '&lt;&lt;',
			'first_tag_open' 	=> '<li>',
			'first_tag_close' 	=> '</li>',
			'last_link' 		=> '&gt;&gt;',
			'last_tag_open' 	=> '<li>',
			'last_tag_close' 	=> '</li>',
			'next_tag_open' 	=> '<li>',
			'next_tag_close' 	=> '</li>',
			'prev_tag_open' 	=> '<li>',
			'prev_tag_close' 	=> '</li>',
			'cur_tag_open' 		=> '<li class="active"><a href="#">',
			'cur_tag_close' 	=> '</a></li>',
			'num_tag_open' 		=> '<li>',
			'num_tag_close' 	=> '</li>',
		);
		$this->pagination->initialize($config_page); 
		$data['pagination'] = $this->pagination->create_links();

		// Si l'utilisateur est client, on ne prend pas les contacts inactif
		$inactif = TRUE;
		$statuts = $this->config->item('statut');
		if($statuts[$this->session->userdata('statut')] != 'admin')
			$inactif = FALSE;
		
		// D�finition de la requ�te en fonction du num�ro de page et du tri
		// Calcul de l'offset en fonction du num�ro de page
		$offset = $sess_annuaire['page'] == 1 ? 0 : ($sess_annuaire['page'] - 1) * $config_page['per_page'];
		$data['liste_contact'] = $this->annuaire_model->get_liste_contact($config_page['per_page'], $offset, $sess_annuaire['tri'], $sess_annuaire['filtre'], $inactif);
		
		// Affichage de la vue
		$this->layout->view('annuaire/liste', $data);
	}
	
	protected function init_liste($page = '')
	{
		// r�cup�ration du contexte en session
		$data_session['annuaire'] = $this->session->userdata('annuaire');

		// Si aucun tri n'est en session
		if(empty($data_session['annuaire']) || empty($data_session['annuaire']['tri']['champ'])){
		
			// On prend les valeurs par d�faut
			$data_session['annuaire']['tri'] = array(
				'champ' => 'date_maj',
				'type'	=> 'desc',
			);
		}
		
		if(empty($data_session['annuaire']) || empty($data_session['annuaire']['filtre'])){
			$data_session['annuaire']['filtre'] = array(
				'recherche_val' 	=> '',
				'recherche_type' 	=> '',
				'recherche_del' 	=> '',
				'lettre_val' 		=> '',
				'lettre_del' 		=> '',
				'like'				=> '',
			);
		}
		
		// si une page est demand�e
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
		// recherche des donn�es en session
		$data_session['annuaire'] = $this->session->userdata('annuaire');
		
		// mise � jour
		$data_session['annuaire']['tri'] = array(
			'champ' => $champ,
			'type'	=> $data_session['annuaire']['tri']['champ'] != $champ ? 'asc' : ($data_session['annuaire']['tri']['type'] == 'asc' ? 'desc' : 'asc'),
		);
		$this->session->set_userdata($data_session);
		
		// redirection vers la liste
		redirect('/annuaire/liste/'.$data_session['annuaire']['page']);
	}
	
	public function filtre()
	{
		// on r�cup�re les donn�es annuaire en session pour les modifier
		$data_session['annuaire'] = $this->session->userdata('annuaire');
		
		// Mise en session des filtres post�s
		$data_session['annuaire']['filtre'] = array(
			'recherche_val' 	=> $this->input->post('filtre_recherche_val', TRUE),
			'recherche_type' 	=> $this->input->post('filtre_recherche_type', TRUE),
			'recherche_del' 	=> $this->input->post('filtre_recherche_del', TRUE),
			'lettre_val' 		=> $this->input->post('filtre_lettre_val', TRUE),
			'lettre_del' 		=> $this->input->post('filtre_lettre_del', TRUE),
		);
		$data_session['annuaire']['filtre']['like'] = !empty($data_session['annuaire']['filtre']['recherche_val']) ? 'both' : (!empty($data_session['annuaire']['filtre']['lettre_val']) ? 'after' : 'none');
		
		$this->session->set_userdata($data_session);

		// redirection vers la liste
		redirect('/annuaire/liste');
	}
	
	protected function init_form()
	{
		// chargement des librairies
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		// r�gles de validation
		$this->form_validation->set_rules('nom', 			$this->lang->line('annuaire_libelle_nom'), 			'required');
		$this->form_validation->set_rules('prenom', 		$this->lang->line('annuaire_libelle_prenom'), 		'required');
		$this->form_validation->set_rules('societe', 		$this->lang->line('annuaire_libelle_societe'), 		'required');
		$this->form_validation->set_rules('fonctions', 		$this->lang->line('annuaire_libelle_fonctions'), 	'required');
		$this->form_validation->set_rules('cp', 			$this->lang->line('users_libelle_cp'), 				'numeric|exact_length[5]');
		$this->form_validation->set_rules('email', 			$this->lang->line('users_libelle_email'), 			'valid_email');

		// r�gles pour l'upload
		$config_upload_image = array(
			'upload_path' 		=> './upload/user_photo/',
			'allowed_types' 	=> 'gif|jpg|png',
			'max_size'			=> '1000', // Ko
			'max_width'  		=> '1024', // pixels
			'max_height'  		=> '768',
		);
		$this->load->library('upload');
		$this->upload->initialize($config_upload_image);
	}
	
	protected function recup_form()
	{
		$data['data_contact'] = array(
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
			'com' 				=> $this->input->post('com', TRUE),
			'date_maj' 			=> date_format(date_create(), 'Y-m-d H:i:s'),
		);
			
		$data['data_fonctions'] = $this->input->post('fonctions', TRUE);
		return $data;
	}
	
	public function ajouter()
	{	
		// Initialisation du formulaire
		$this->init_form();
		
		// si on arrive sur le formulaire
		// ou si il y a des erreurs g�n�rales 
		if ($this->form_validation->run() == FALSE || (!$this->upload->do_upload('photo') && $_FILES['photo']['error']!=4 ))
		{
			// chargement des librairies
			$this->load->library('table');
			$this->load->model('fonctions_model');
			
			// Initialisation des valeurs par d�faut
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
			
			$data['tab_pays'] = liste_pays();
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
			// Si aucun fichier n'est pr�sent
			if($_FILES['photo']['error']==4)
				$data_photo['file_name'] = NULL;
			else{
			
				$data_photo = $this->upload->data();
				
				// Cr�ation de la miniature
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
			
			$data = $this->recup_form();
			$data['data_contact']['photo'] = $data_photo['file_name'];
			
			// traitement
			$contact_id = $this->annuaire_model->ajouter_contact($data['data_contact']);
			$this->annuaire_model->ajouter_fonctions_contact($contact_id, $data['data_fonctions']);
			
			// Redirection vers la liste � la page mise en session
			$sess_annuaire = $this->session->userdata('annuaire');
			redirect('/annuaire/liste/'.$sess_annuaire['page'].'/annuaire_message_ajouter');
		}
	}
	
	public function modifier($id)
	{
		// Initialisation du formulaire
		$this->init_form();

		// si on arrive sur le formulaire
		// ou si il y a des erreurs g�n�rales 
		// ou si on a tent� d'uploader un fichier mais qu'une erreur est survenue (le champ photo n'est pas requis)
		if ($this->form_validation->run() == FALSE || ($_FILES['photo']['error']!=4  && !$this->upload->do_upload('photo')))
		{
			// chargement des librairies
			$this->load->library('table');
			$this->load->model('fonctions_model');
			
			// r�cup�ration des valeurs
			$data['contact'] = $this->annuaire_model->get_contact($id);
			$data['contact']['fonctions'] = $this->fonctions_model->get_fonctions_contact($id);
			$data['fonctions'] = $this->fonctions_model->get_fonctions_actives();
			$data['tab_pays'] = liste_pays();
			
			// gestion de l'affichage de l'upload
			$data['erreur_upload_photo'] = $this->upload->display_errors();
			if(!empty($data['contact']['photo'])){
				$this->layout->ajouter_css('colorbox/colorbox');
				$this->layout->ajouter_js('colorbox/jquery.colorbox-min');
				$data['lien_photo'] = '<a href="'.base_url().'upload/user_photo/'.$data['contact']['photo'].'" title="Photo" id="lien_photo" class="colorbox pull-left"><img src="'.base_url().'upload/user_photo/thumbs/'.$data['contact']['photo'].'" alt="Photo" /></a>';
				$data['btn_suppr_photo'] = ' <button class="btn" type="button" onclick="window.location.href=\''.site_url('/annuaire/supprimer_photo/'.$id).'\'" >'.$this->lang->line('general_supprimer').'</button><div class="clear"></div>';
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
			// Si aucun fichier n'est pr�sent
			if($_FILES['photo']['error']==4){
				$data_photo['file_name'] = $this->annuaire_model->get_photo($id);
			}
			else{
				$data_photo = $this->upload->data();
				
				// Supression de l'�ventuelle image existante + miniature
				$nom_fichier = $this->annuaire_model->get_photo($id);
				if(!empty($nom_fichier)){
					unlink('./upload/user_photo/'.$nom_fichier);
					unlink('./upload/user_photo/thumbs/'.$nom_fichier);
				}
				
				// Cr�ation de la miniature
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

			$data = $this->recup_form();
			$data['data_contact']['photo'] = $data_photo['file_name'];
			
			// traitement
			$this->annuaire_model->supprimer_fonctions_contact($id);
			$this->annuaire_model->modifier_contact($data['data_contact'], $id);
			$this->annuaire_model->ajouter_fonctions_contact($id, $data['data_fonctions']);
			
			// Redirection vers la liste � la page mise en session
			$sess_annuaire = $this->session->userdata('annuaire');
			redirect('/annuaire/liste/'.$sess_annuaire['page'].'/annuaire_message_modifier');
		}
	}
	
	public function visualiser($id)
	{
		// chargement des librairies
		$this->load->library('table');
		$this->load->model('fonctions_model');
		
		// r�cup�ration des valeurs
		$data['contact'] = $this->annuaire_model->get_contact($id);
		if(!empty($data['contact']['photo'])){
			$this->layout->ajouter_css('colorbox/colorbox');
			$this->layout->ajouter_js('colorbox/jquery.colorbox-min');
			$data['lien_photo'] = '<a href="'.base_url().'upload/user_photo/'.$data['contact']['photo'].'" title="Photo" id="lien_photo" class="colorbox pull-left"><img src="'.base_url().'upload/user_photo/thumbs/'.$data['contact']['photo'].'" alt="Photo" /></a>';
		}else
			$data['lien_photo'] = 'Aucune photo';
		$data['contact']['fonctions'] = $this->fonctions_model->get_fonctions_contact($id, 'affichage');
		$data['fonctions'] = $this->fonctions_model->get_fonctions_actives();
		$data['tab_pays'] = liste_pays();
		
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
		$nom_fichier = $this->annuaire_model->get_photo($id);
		unlink('./upload/user_photo/'.$nom_fichier);
		unlink('./upload/user_photo/thumbs/'.$nom_fichier);
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








