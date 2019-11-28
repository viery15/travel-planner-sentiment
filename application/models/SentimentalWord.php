<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SentimentalWord extends CI_Model
{
    private $_table = "sentimental_word";

    public function getAll()
    {
        $this->db->select('word, type, value'); 
        $this->db->from($this->_table);
        return $this->db->get()->result();
    }

    public function getByWord($word){
        $this->db->select('word, type, value'); 
        $this->db->from($this->_table);
        $this->db->where('word', $word);
        return $this->db->get()->row_array();
    }
}
