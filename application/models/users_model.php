<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model
{
    protected $table = 'user';

	function __construct()
    {
        parent::__construct();
    }
    
	public function compte_existe($login = '', $password = '')
    {
        $this->db->where('login', $login);
        $this->db->where('password', $password);
        return $this->db->count_all_results($this->table);
    }
    
    public function compte_actif($login = '', $password = '')
    {
        $this->db->where('login', $login);
        $this->db->where('password', $password);
        $this->db->where('actif', 1);
        return $this->db->count_all_results($this->table);
    }
 
	public function get_user($login, $password)
	{
		$this->db->select('user_id, nom, prenom, statut');
		$this->db->where('login', $login);
        $this->db->where('password', $password);
        $this->db->where('actif', 1);
		return $this->db->get($this->table, 1)->row_array();
	}
	
	public function get_user_complet($id)
	{
        $this->db->where('user_id', $id);
		return $this->db->get($this->table, 1)->row_array();
	}
	
    public function ajouter_user($data)
    {
        $this->db->insert($this->table, $data); 
    }
	
    public function get_liste_user($tri)
    {
        $this->db->select('user_id, nom, prenom, actif');
		if($tri['champ'] == 'membre'){
			$this->db->order_by('prenom', $tri['type']);
			$this->db->order_by('nom', $tri['type']);
		}
		$query = $this->db->get($this->table);

		$liste_user = array();
		foreach ($query->result_array() as $row)
		{
			$liste_user[$row['user_id']] = $row;
		}
		return $liste_user;
    }
	
	public function login_unique($login, $id)
    {
		$this->db->where('login', $login);
		$this->db->where_not_in('user_id', array($id));
		return ($this->db->count_all_results($this->table) == 0);
    }
	
	public function email_unique($email, $id)
    {
		$this->db->where('email', $email);
		$this->db->where_not_in('user_id', array($id));
		return ($this->db->count_all_results($this->table) == 0);
    }
	
	public function modifier_user($data, $id)
    {
		$this->db->where('user_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function activer_user($id)
    {
        $data = array('actif' => 1);
		$this->db->where('user_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function desactiver_user($id)
    {
        $data = array('actif' => 0);
		$this->db->where('user_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function supprimer_user($id)
    {
		$this->db->where('user_id', $id);
		$this->db->delete($this->table); 
    }
}