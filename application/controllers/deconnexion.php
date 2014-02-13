<?php

class Deconnexion extends CI_Controller
{	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->deconnexion();
	}

	public function deconnexion()
	{
		$this->session->sess_destroy();
		redirect('/connexion');
	}
}