<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fonctions_model extends CI_Model
{
    protected $table = 'fonction';
    protected $table_join_contact = 'fonction_contact';

	function __construct()
    {
        parent::__construct();
    }
    
    public function get_liste_fonction($tri)
    {
        $this->db->select('fonction_id, nom, actif');
		$this->db->order_by($tri['champ'], $tri['type']); 
		$query = $this->db->get($this->table);

		$liste_fonction = array();
		foreach ($query->result_array() as $row)
		{
			$liste_fonction[$row['fonction_id']] = $row;
		}
		return $liste_fonction;
    }
	
	public function get_fonctions_actives()
    {
        $this->db->select('fonction_id, nom');
		$this->db->where('actif', 1);
		$this->db->order_by('nom', 'asc'); 
		$query = $this->db->get($this->table);

		$liste_fonction = array();
		foreach ($query->result_array() as $row)
		{
			$liste_fonction[$row['fonction_id']] = $row['nom'];
		}
		return $liste_fonction;
    }
	
	public function get_fonction($id)
    {
        $this->db->select('fonction_id, nom, actif');
		$this->db->where('fonction_id', $id);
		return $this->db->get($this->table, 1)->row_array();
    }
	
	public function get_fonctions_contact($contact_id, $mode = 'select')
    {
        $this->db->select($this->table.'.fonction_id, '.$this->table.'.nom');
		$this->db->join($this->table_join_contact, $this->table_join_contact.'.fonction_id = '.$this->table.'.fonction_id', 'left');
		$this->db->where('contact_id', $contact_id);
		$this->db->order_by('nom', 'asc'); 
		$query = $this->db->get($this->table);

		$liste_fonction = array();
		foreach ($query->result_array() as $row)
		{
			if($mode == 'select')
				$liste_fonction[] = $row['fonction_id'];
			else
				$liste_fonction[] = $row['nom'];
		}
		return $liste_fonction;
    }
	
	public function modifier_fonction($id, $actif, $nom)
    {
        $data = array(
		   'actif' => $actif,
		   'nom' => $nom,
		);
		$this->db->where('fonction_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function ajouter_fonction($actif, $nom)
    {
        $data = array(
		   'actif' => $actif,
		   'nom' => $nom,
		);
		$this->db->insert($this->table, $data); 
    }
	
	public function activer_fonction($id)
    {
        $data = array('actif' => 1);
		$this->db->where('fonction_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function desactiver_fonction($id)
    {
        $data = array('actif' => 0);
		$this->db->where('fonction_id', $id);
		$this->db->update($this->table, $data); 
    }
}