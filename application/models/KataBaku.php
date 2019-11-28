<?php defined('BASEPATH') OR exit('No direct script access allowed');

class KataBaku extends CI_Model
{
    private $_table = "kata_baku";

    public function getAll()
    {
        $this->db->select('kata, kata_asli'); 
        $this->db->from($this->_table);
        return $this->db->get()->result();
    }

    public function getByWord($word){
        $this->db->select('kata_asli'); 
        $this->db->from($this->_table);
        $this->db->where('kata', $word);
        return $this->db->get()->result();
    }
}
