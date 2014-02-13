<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Annuaire_model extends CI_Model
{
    protected $table = 'contact';
	protected $table_join_fonction = 'fonction_contact';

	function __construct()
    {
        parent::__construct();
    }
	
	public function get_contact($id)
	{
        $this->db->where('contact_id', $id);
		return $this->db->get($this->table, 1)->row_array();
	}
	
	public function get_photo($id)
	{
		$this->db->select('photo');
        $this->db->where('contact_id', $id);
		$res = $this->db->get($this->table, 1)->row_array();
		return $res['photo'];
	}
	
	public function suppr_photo($id)
	{
		$data['photo'] = NULL;
        $this->db->where('contact_id', $id);
		$this->db->update($this->table, $data); 
	}
	
    public function ajouter_contact($data)
    {
        $this->db->insert($this->table, $data); 
		return $this->db->insert_id();
    }
	
	public function supprimer_fonctions_contact($id)
    {
        $this->db->where('contact_id', $id);
		$this->db->delete($this->table_join_fonction); 
    }
	
	public function ajouter_fonctions_contact($contact_id, $fonctions)
    {
		$data['contact_id'] = $contact_id;
		foreach($fonctions as $fct){
			$data['fonction_id'] = $fct;
			$this->db->insert($this->table_join_fonction, $data); 
		}
    }
	
	public function get_liste_contact($nb, $offset, $tri, $filtre, $inactif)
    {
        $this->db->select('contact_id, actif, societe, nom, prenom, tel');
		$this->db->order_by($tri['champ'], $tri['type']); 
		
		if(!$inactif)
			$this->db->where('actif', '1');
		if(!empty($filtre['recherche_val']))
			$this->db->like($filtre['recherche_type'], $filtre['recherche_val'], $filtre['like']);
		if(!empty($filtre['lettre_val']))
			$this->db->like('nom', $filtre['lettre_val'], $filtre['like']);
		$query = $this->db->get($this->table, $nb, $offset);

		$liste_contact = array();
		foreach ($query->result_array() as $row)
		{
			$liste_contact[$row['contact_id']] = $row;
		}
		
		return $liste_contact;
    }
	
	public function get_nb_contact($filtre)
    {
		if(!empty($filtre['recherche_val']))
			$this->db->like($filtre['recherche_type'], $filtre['recherche_val'], $filtre['like']);
		elseif(!empty($filtre['lettre_val']))
			$this->db->like('nom', $filtre['lettre_val'], $filtre['like']);
		$this->db->from($this->table);
		return $this->db->count_all_results();
    }
	
	public function modifier_contact($data, $id)
    {
		$this->db->where('contact_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function activer_contact($id)
    {
        $data = array('actif' => 1);
		$this->db->where('contact_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function desactiver_contact($id)
    {
        $data = array('actif' => 0);
		$this->db->where('contact_id', $id);
		$this->db->update($this->table, $data); 
    }
	
	public function supprimer_contact($id)
    {
		$this->db->where('contact_id', $id);
		$this->db->delete($this->table); 
    }
}