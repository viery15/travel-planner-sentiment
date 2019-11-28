<?php defined('BASEPATH') OR exit('No direct script access allowed');

class WordDictionary extends CI_Model
{
    private $_table = "word_dictionary";

    public function getAll()
    {
        $this->db->select('katadasar, tipe_katadasar'); 
        $this->db->from($this->_table);
        return $this->db->get()->result();
    }

    public function getByWord($word){
        $this->db->select('*'); 
        $this->db->from($this->_table);
        $this->db->where('katadasar', $word);
        return $this->db->get()->row_array();
    }
}
