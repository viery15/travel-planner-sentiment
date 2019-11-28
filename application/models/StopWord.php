<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StopWord extends CI_Model
{
    private $_table = "stoplist";

    public function getAll()
    {
        $this->db->select('stoplist'); 
        $this->db->from($this->_table);
        return $this->db->get()->result();
    }

    public function getByWord($word){
        $this->db->select('stoplist'); 
        $this->db->from($this->_table);
        $this->db->where('stoplist', $word);
        return $this->db->get()->result();
    }
}
