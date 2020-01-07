<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sentiment extends CI_Controller {

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->load->model("KataBaku");
        $this->load->model("SentimentalWord");
        $this->load->model("StopWord");
        $this->load->model("WordDictionary");
    }

	public function index()
	{
        
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = json_decode($stream_clean);

        $kalimat = $request->reviews;

        // $kalimat = [
        //     "Jarak Tempat dg jalan aspal deket.\nKita parkir di atas. Arah tempat rekreasi nya, menjorok ke bawah.\nSampai di depan gerbang disambut para penjual cilok dan sempol. Dan juga penjaga pintu masuk. Harga tiket  per orang 5.000 rupiah, tdk mngikat, dalam arti fleksibel. Tergantung kebijakan dari penjaga pintu.\nKadang ada yg mmbawa anak kecil, anak kecil tdk dtarik tiket.\nBegitu kita masuk kawasan, ada bbrp pohon besar di sekitar, ada dua kolam renang, mushola kecil, bbrp warung, 2 toilet, dua tempat ganti, odong2, dan juga persewaan ban.\n\nDeskripsi kolam renang\n-kolam anak, kedalaman sekitar 40 cm, berlumut, licin. Air tdk mengalir, tergenang dan diam, sedikit hangat, karena tdk ada sirkulasi air dan banyak anak (yg maaf, pipis di kolam).\n\n- satu kolam besar, kedalaman sekitar 170-200 cm. Air nya cukup dingin, karena sumber air alami. Banyak lumut di pinggiran tembok dan juga bebatuan di bawah. Jika diperhatikan, banyak pengunjung yg tdk berenang. Mereka hanya tubing, diatas ban. Namun hanya dipinggiran saja. Airnya sih jernih, namun terlihat kotor, banyak sampah alami, seperti daun dan dahan. Jadi gak seru klo berenang sendirian.\n\nInfo tambahan\n-Sewa ban 5.000\n-Masuk toilet 2.000\n-Naik odong2 5.000\n\nKesimpulan secara keseluruhan\n-kebersihan   ❌\n-kenyamanan❌\n-keindahan    ❌\n-keamanan    ❌\n-fasilitas         ❌\n-harga             ✅\n\nSemoga infonya membantu.",
        //     "Lokasinya nyaman, teduh, airnya dingin. Sebelnya harus bayar dobel ya bayar parkir ya bayar tiket masuk tapi gpp deh. Tolong banget di perketat keamanan disana terkait para wisatawan yg seenak jidat keramas dan mandi pake sabun trs langsung …",
        //     "Terakhir kesini tahun 2015, waktu ber kunjung kesini lagi tahun 2019, sudah sangat berubah, dulu warna airnya masih lebih bersih dari sekarang.... ke bersihin lumayan terjaga, namun mohon bagi pihak yang menjaga tempat, jangan biarkan org2 mandi di air memakai shampo atau sabun, karna bisa me rusak ekosistem yang ada di dalam airnya.",
        //     "Sumber Jenon, salah satu obyek wisata kolam  pemandian.  bagian dasar kolsm ini tertutupi batako. Kedalaman sumber mata air ini lumayan dalam, kirang lebih sampai 4 meter. Jadi, bagi pendatang yang tak bisa berenang, bisa menyewa ban yang …",
        //     "Harga yang tiket masuk murah. Airnya bersih tapi bisa keruh karena bawahnya adalah pasir. Air tidak mengandung kaporit karena berasal dari mata air. Disarankan membawa alat snorkel sehingga bisa melihat isi kolam. Terdapat fasilitas …",
        //     "Tempat Wisata yg Murah Meriah dengan Pemandangan yg Alami & Sayang sekali belum ada dari pihak pemerintah yg memperhatikan untuk mengelolah",
        //     "Tempatnya terlalu dalam, jadi yang kurang pandai berenang tidak berani berenang. Bisa diantisipasi dengan pengurangan kadar air atau pengurukan",
        //     "salah satu tempat andalan buat bersantai di kabupaten Malang. dan dulu punya teman juga yang ngurus tempat wisata ini",
        //     "Dekat dengan kota malang\nHarga 5000, parkuran sudah di perluas kepintu sebelum gerbang\nAir jernih (sumber beneran)\nPemandangan ok ok, banyak gadis desa\nRekomendasi klo mau berenang murah",
        //     "Sebuah wisata alam yang cukup bagus dan cukup murah. Sayangnya lahan parkir masih perlu disiapkan dengan lebih luas, rata dan nyaman.\n\nBegitu pula kebersihan kolam air perlu ditingkatkan kebersihannya. Karena banyak daun pohon — terutama …",
        //     "Dingin..dalam 4 meter lebih tapi ada yg 1 meter kok",
        //     "Merupakan salah satu sumber yang terkenal di malang. Sumber jenon ini memiliki keunikan berupa ikan ikan besar yang berada di kolamnya. Konon ikan ikan ini dikeramatkan dan meruoakan penjaga sumber jenon. Kedalaman sumber ini cukup dalam …",
        //     "Kolam Dari sumber Murni .. dinggin dan masih rindang akan pepohonan",
        //     "Tempatnya sejuk dikelilingi pepohonan yg rindang, airnya jernih, menurut saya tempat ini ramah anak",
        //     "Sumber jenon suasananya bikin hati adem ayem, di kolamnya bisa dikatakan cukup dalam, jadi yang tidak bisa berenang harap hati hati ya agar tidak tenggelam, terdapat banyak ikan di dalam kolam tersebut akan tetapi airnya sangat jernih, tiket masuk murah, jalan menuju sumber cukup bagus.",
        //     "Kemarin saat saya diving tempatnya sudah mulai kotor, dan lumayan mistis karena saya tertarik ke dasar",
        //     "Merupakan tempat wisata air yang sudah populer di kec. Tajinan. Air sumbernya cukup jernih dan udaranya masih segar. Perlu penanganan yang lebih baik sehingga bisa menarik masyarakat.",
        //     "Tempat berenang yg murah dan airnya jernih dari air sumber",
        //     "Rugi kalok gak main kesini 😁😁😁",
        // ];

        // $kalimat = $this->tokenizing($kalimat);
        $kalimat = $this->caseFolding($kalimat);
        $kalimat = $this->removeSimbol($kalimat);
        $kalimat = $this->removeEmoji($kalimat);
        $kalimat = $this->removeNonWord($kalimat);
        $kalimat = $this->removeRedundanChar($kalimat);
        $kalimat = $this->removeSpace($kalimat);
        $kalimat = $this->removeNumber($kalimat);
        $kalimat = $this->removeStopWord($kalimat);
        $kalimat = $this->cekKataBaku($kalimat);

        for ($i=0; $i < count($kalimat); $i++) { 
            $result[$i] = $this->sentimentProcess($kalimat[$i]);
        }

        for ($j=0; $j < count($result); $j++) { 
            $final_result[$j]['word'] = "";
            for ($k=0; $k < count($result[$j]['word']); $k++) { 
                $final_result[$j]['word'] = $final_result[$j]['word'] . " " . $result[$j]['word'][$k]['word'];
                $final_result[$j]['scorelist'] = $result[$j]['scorelist'];
                $final_result[$j]['score'] = $result[$j]['score'];
            }
        }

        $total_score = 0;
        for ($i=0; $i < count($final_result); $i++) { 
            if ($final_result[$i]['score'] > 0) {
                $total_score++;
            }
        }

        echo json_encode($total_score);
        // print_r($kalimat);
        
    }

    public function tokenizing($kalimat){
        $kata = explode(" ",$kalimat);

        return $kalimat;
    }

    public function sentimentProcess($kalimat){
        $sentence = explode(" ", $kalimat);
        $i = 0;
        $wordList = [];
        $result = [];
        $score = [];
        foreach($sentence as $word){
            $wordList[$i] = $this->getSentimentalWords($word);
            $i++;
        }

        for($i = 0; $i < count($wordList); $i++)
        {
            //cek verba
            if($this->checkVerb($wordList[$i]))
            {
                //cek ada keterangan sebelum verba
                if($i != 0 && $i != count($wordList)-1) // jika tidak di awal kalimat
                {
                    if($this->checkAdverb($wordList[$i-1]))
                    {
                        error_reporting(0);
                        if($this->checkAdjective($wordList[$i+1]))
                        {
                            $verb_adj = $this->countLogic($this->getValue($wordList[$i]), $this->getValue($wordList[$i+1]), 'after');
                            $score[$i] = $this->countLogic($this->getValue($wordList[$i-1]), $verb_adj, 'before');
                            $i++;
                        }
                        else
                        {
                            $score[$i] = $this->countLogic($this->getValue($wordList[$i-1]), $this->getValue($wordList[$i]), 'before');
                        }
                    }
                    elseif($this->checkAdjective($wordList[$i+1]))
                    {
                        $score[$i] = $this->countLogic($this->getValue($wordList[$i]), $this->getValue($wordList[$i+1]), 'after');
                        $i++;
                    }
                    else
                    {
                        $score[$i] = $this->getValue($wordList[$i]);
                    }
                }

                //cek ada adjektiva sesudah verba
                elseif($i != count($wordList)-1) //jika tidak diakhir kalimat
                {
                    if($this->checkAdjective($wordList[$i+1]))
                    {
                        echo 'masuk';
                        $score[$i] = $this->countLogic($this->getValue($wordList[$i]), $this->getValue($wordList[$i+1]), 'after');
                        $i++;
                    }
                    else
                    {
                        $score[$i] = $this->getValue($wordList[$i]);
                    }
                }
                else
                {
                    $score[$i] = $this->getValue($wordList[$i]);
                }
            }

            //cek adjektiva
            elseif($this->checkAdjective($wordList[$i]))
            {
                //cek ada keterangan sebelum adjektiva
                if($i != 0) // jika tidak di awal kalimat
                {
                    if($this->checkAdverb($wordList[$i-1]))
                    {
                        error_reporting(0);
                        if($this->checkVerb($wordList[$i+1]))
                        {
                            $pre_adj = $this->countLogic($this->getValue($wordList[$i-1]) , $this->getValue($wordList[$i]), 'after');
                            $score[$i] = $this->countLogic(intval($pre_adj), $this->getValue($wordList[$i+1]), 'before');
                            $i++;
                        }
                        else
                        {
                            $score[$i] = $this->countLogic($this->getValue($wordList[$i-1]), $this->getValue($wordList[$i]), 'before');
                        }
                    }
                    else
                    {
                        $score[$i] = $this->getValue($wordList[$i]);
                    }   
                }
                //cek ada verba sesudah adjektiva
                elseif($i != count($wordList)-1) //jika tidak di akhir kalimat
                {
                    if($this->checkVerb($wordList[$i+1]))
                    {
                        $score[$i] = $this->countLogic($this->getValue($wordList[$i]), $this->getValue($wordList[$i+1]), 'after');
                        $i++;
                    }
                    else
                    {
                        $score[$i] = $this->getValue($wordList[$i]);
                    }
                }
                else
                {
                    $score[$i] = $this->getValue($wordList[$i]);
                }
            }

            elseif($this->getValue($wordList[$i]) != 0 && !$this->checkAdverb($wordList[$i]))
            {
                $score[$i] = $this->getValue($wordList[$i]);
            }
        }
        $result['word'] = $wordList;
        $result['scorelist'] = $this->reconstructArray($score);
        $result['score'] = array_sum($score);

        return $result;
    }

    public function reconstructArray($array){
        $i = 0;
        $result = [];
        foreach($array as $row)
        {
            $result[$i] = $row;
            $i++;
        }
        return $result;
    }

    public function checkVerb($word) {
        // print_r($word['type']);
        if($word['type'] == 'verba')
            return true;
        return false;
    }

    public function checkAdverb($word){
        if(strtolower($word['type'])=='keterangan')
            return true;
        return false;
    }

    public function checkAdjective($word){
        if(strtolower($word['type'])=='adjektiva')
            return true;
        return false;
    }

    public static function getValue($word){
        return intval($word['value']);
    }

    function countLogic($x, $y, $type){
        //positif ketemu positif
        if($x == 1 && $y == 1)
        {
            if($type == 'before')
                $result = 1;
            elseif($type == 'after')
                $result = 1;
        }

        //positif ketemu negatif
        elseif($x == 1 && $y == -1)
        {
            if($type == 'before')
                $result = -1;
            elseif($type == 'after')
                $result = -1;
        }

        //negatif ketemu positif
        elseif($x == -1 && $y == 1)
        {
            if($type == 'before')
                $result = -1;
            elseif($type == 'after')
                $result = -1;
        }

        //negatif ketemu negatif
        elseif($x == -1 && $y == -1)
        {
            if($type == 'before')
                $result = 1;
            elseif($type == 'after')
                $result = -1;
        }

        elseif($x == 0)
            $result = $y;
        elseif($y == 0)
            $result = $x;
        return $result;
    }

    public function getSentimentalWords($word){
        $result = [];
        $data = $this->SentimentalWord->getByWord($word);
        if($data)
            return $data;
        $result['word'] = $word;
        $result['type'] = $this->getWordType($word);
        $result['value'] = 0;
        return $result; 
    }

    public function getWordType($word){
        $data = $this->WordDictionary->getByWord($word);
        if($data)
            return $data['tipe_katadasar'];
        return "Unknown";
    }
    
    public function caseFolding($kalimat){
        for ($i=0; $i < count($kalimat); $i++) { 
            $kalimat[$i] = strtolower($kalimat[$i]);
        }
        return $kalimat;
    }

    public function removeRedundanChar($kalimat){
        for ($i=0; $i < count($kalimat); $i++) { 
            $string[$i] = explode(" ",$kalimat[$i]);
            for ($j=0; $j < count($string[$i]); $j++) { 
                $split = str_split($string[$i][$j]);
                for ($k=0; $k < count($split); $k++) { 
                    if ($k != 0 && $split[$k] == $split[$k-1]) {
                        unset($split[$k]);
                        $split = array_values($split);
                        $k--;
                    }  
                }
                $string[$i][$j] = implode("",$split);
            }
            $string[$i] = implode(" ",$string[$i]);
        }
        
        return $string;
    }

    public function removeNonWord($kalimat){
        for ($i=0; $i < count($kalimat); $i++) { 
            $kalimat[$i] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $kalimat[$i]);
        }

        return $kalimat;
    }

    public function removeSimbol($kalimat){
        $simbol = [",", ".", "?", "!", "(", ")","…","\n",'/'];
        for ($i=0; $i < count($kalimat); $i++) { 
            for ($j=0; $j < count($simbol); $j++) { 
                $kalimat[$i] = str_replace($simbol[$j],' ',$kalimat[$i]);
            }
        }
        return $kalimat;
    }

    public function removeSpace($kalimat){
        for ($i=0; $i < count($kalimat); $i++) { 
            $string[$i] = explode(" ",$kalimat[$i]);
            $string[$i] = array_filter($string[$i]);
            $string[$i] = implode(" ",$string[$i]);
        }
        return $string;
    }

    public function removeNumber($kalimat){

        for ($i=0; $i < count($kalimat); $i++) { 
            $kalimat[$i] = preg_replace('/[0-9]+/', '', $kalimat[$i]);
        }

        return $kalimat;
    }

    public function removeStopWord($kalimat){
        for ($i=0; $i < count($kalimat); $i++) { 
            $string[$i] = explode(" ", $kalimat[$i]);
            $hasil = [];

            for ($j=0; $j < count($string[$i]); $j++) { 
                if ($this->SentimentalWord->getByWord($string[$i][$j])) {
                    $hasil[$j] = $string[$i][$j];
                }

                elseif ($this->WordDictionary->getByWord($string[$i][$j])) {
                    $hasil[$j] = $string[$i][$j];
                }

                else if($this->StopWord->getByWord($string[$i][$j])){
                    $hasil[$j] = $this->nazief($string[$i][$j]);
                }
            }
            $string[$i] = $hasil;
            $string[$i] = implode(" ", $hasil);
        }

        return $string;
    }

    public function cekKataBaku($kalimat){
        for ($i=0; $i < count($kalimat); $i++) { 
            $string[$i] = explode(" ", $kalimat[$i]);
            for ($j=0; $j < count($string[$i]); $j++) { 
                $kata = $this->KataBaku->getByWord($string[$i][$j]);
                if ($kata) {
                    $string[$i][$j] = $kata[0]->kata_asli;
                }
            }
            $string[$i] = implode(" ", $string[$i]);
        }

        return $string;
    }

    public function nazief($word){
        if($this->WordDictionary->getByWord($word)){
            return $word;
        }else{
            $word = $this->deleteInflectionSuffixes($word);
            $word = $this->deleteDerivationSuffixes($word);
            $word = $this->deleteDerivationPrefixes($word);
            return $word;
        }
    }

    public function deleteInflectionSuffixes($word){
        if (preg_match('/([km]u|nya|[kl]ah|pun)$/i', $word)) 
        {
            $__word = preg_replace('/(nya|[kl]ah|pun)$/i', '', $word);
            if (preg_match('/([klt]ah|pun)$/i', $word))
            {
                if (preg_match('/([km]u|nya)$/i', $word))
                {
                    $__word__ = preg_replace('/([km]u|nya)$/i', '', $word);
                    return $__word__;
                }
            }
            return $__word;
        }
        return $word;      
    }

    public function deleteDerivationSuffixes($kata) 
    {
        $kataAsal = $kata;
        if (preg_match('/(i|an)$/i', $kata)) 
        {
            
            $__kata = preg_replace('/(i|an)$/i', '', $kata);
            if ($this->WordDictionary->getByWord($__kata))
            {
                return $__kata;
            }
            
            
            if (preg_match('/(kan)$/i', $kata)) 
            {
                $__kata__ = preg_replace('/(kan)$/i', '', $kata);
                if ($this->WordDictionary->getByWord($__kata__)) 
                {
                    return $__kata__;
                }
            }
            if ($this->checkPrefixDisallowedSuffixes($kata)) 
            {
                return $kataAsal;
            }
        }
        return $kataAsal;
    }

    function deleteDerivationPrefixes($kata) 
    {
        $kataAsal = $kata;
        // Jika di-,ke-,se-
        if (preg_match('/^(di|[ks]e)/i', $kata)) 
        {
            $__kata = preg_replace('/^(di|[ks]e)/i', '', $kata);
            
            if ($this->WordDictionary->getByWord($__kata)) 
            {
                return $__kata;
            }
            
            $__kata__ = $this->deleteDerivationSuffixes($__kata);
            if ($this->WordDictionary->getByWord($__kata__)) 
            {
                return $__kata__;
            }
            
            if (preg_match('/^(diper)/i', $kata)) 
            {
                $__kata = preg_replace('/^(diper)/i', '', $kata);
                if ($this->WordDictionary->getByWord($__kata)) 
                {
                    return $__kata;
                }
                
                $__kata__ = $this->deleteDerivationSuffixes($__kata);
                if ($this->WordDictionary->getByWord($__kata__)) 
                {
                    return $__kata__;
                }
                
                $__kata = preg_replace('/^(diper)/i', 'r', $kata);
                if ($this->WordDictionary->getByWord($__kata)) 
                {
                    return $__kata; // Jika ada balik
                }
                
                $__kata__ = $this->deleteDerivationSuffixes($__kata);
                if ($this->WordDictionary->getByWord($__kata__)) 
                {
                    return $__kata__;
                }
            }
        }
        
        if (preg_match('/^([tmbp]e)/i', $kata)) 
        { 
            
            if (preg_match('/^(te)/i', $kata)) 
            { 
                if (preg_match('/^(terr)/i', $kata)) 
                {
                    return $kata;
                }
                
                if (preg_match('/^(ter)[aiueo]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(ter[^aiueor]er[aiueo])/i', $kata)) 
                {
                    $__kata = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(ter[^aiueor]er[^aiueo])/i', $kata)) 
                {
                    $__kata = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata))
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(ter[^aiueor][^(er)])/i', $kata)) 
                {
                    $__kata = preg_replace('/^(ter)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(te[^aiueor]er[aiueo])/i', $kata)) 
                {
                    return $kata;
                }
                
                if (preg_match('/^(te[^aiueor]er[^aiueo])/i', $kata)) 
                {
                    $__kata = preg_replace('/^(te)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
            }
            
            if (preg_match('/^(me)/i', $kata)) 
            {
                if (preg_match('/^(meng)[aiueokghq]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(meng)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                    
                    $__kata = preg_replace('/^(meng)/i', 'k', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }

                if (preg_match('/^(meny)/i', $kata)) 
                {
                    $__kata = preg_replace('/^(meny)/i', 's', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(mem)[bfpv]/i', $kata)) 
                { // 3.
                    $__kata = preg_replace('/^(mem)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                    
                    $__kata = preg_replace('/^(mem)/i', 'p', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }

                    $__kata = preg_replace('/^(mempek)/i', 'k', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(men)[cdjsz]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(men)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(me)/i', $kata)) 
                {
                    $__kata = preg_replace('/^(me)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                    
                    $__kata = preg_replace('/^(men)/i', 't', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }

                    $__kata = preg_replace('/^(mem)/i', 'p', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
            }

            if (preg_match('/^(be)/i', $kata)) 
            {
                if (preg_match('/^(ber)[aiueo]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(ber)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata = preg_replace('/^(ber)/i', 'r', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }

                if (preg_match('/(ber)[^aiueo]/i', $kata)) 
                { // 2.
                    $__kata = preg_replace('/(ber)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata;
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) {
                        return $__kata__;
                    }
                }
                if (preg_match('/^(be)[k]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(be)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
            }
            
            if (preg_match('/^(pe)/i', $kata)) 
            {
                if (preg_match('/^(peng)[aiueokghq]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(peng)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }

                if (preg_match('/^(peny)/i', $kata)) 
                {
                    $__kata = preg_replace('/^(peny)/i', 's', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(pem)[bfpv]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(pem)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }

                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(pen)[cdjsz]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(pen)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                    
                    $__kata = preg_replace('/^(pem)/i', 'p', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                }

                if (preg_match('/^(pen)[aiueo]/i', $kata)) 
                {
                    $__kata = preg_replace('/^(pen)/i', 't', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(per)/i', $kata)) 
                {
                    $__kata = preg_replace('/^(per)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                    
                    $__kata = preg_replace('/^(per)/i', 'r', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
                
                if (preg_match('/^(pe)/i', $kata)) 
                {
                    $__kata = preg_replace('/^(pe)/i', '', $kata);
                    if ($this->WordDictionary->getByWord($__kata)) 
                    {
                        return $__kata; // Jika ada balik
                    }
                    
                    $__kata__ = $this->deleteDerivationSuffixes($__kata);
                    if ($this->WordDictionary->getByWord($__kata__)) 
                    {
                        return $__kata__;
                    }
                }
            }

            if (preg_match('/^(memper)/i', $kata)) 
            {
                $__kata = preg_replace('/^(memper)/i', '', $kata);
                if ($this->WordDictionary->getByWord($__kata)) 
                {
                    return $__kata; // Jika ada balik
                }
                
                $__kata__ = $this->deleteDerivationSuffixes($__kata);
                if ($this->WordDictionary->getByWord($__kata__)) 
                {
                    return $__kata__;
                }
                
                $__kata = preg_replace('/^(memper)/i', 'r', $kata);
                if ($this->WordDictionary->getByWord($__kata)) 
                {
                    return $__kata; // Jika ada balik
                }
                
                $__kata__ = $this->deleteDerivationSuffixes($__kata);
                if ($this->WordDictionary->getByWord($__kata__)) 
                {
                    return $__kata__;
                }
            }
        }
        
        /* --- Cek Ada Tidaknya Prefik/Awalan ------ */
        if (preg_match('/^(di|[kstbmp]e)/i', $kata) == FALSE) 
        {
            return $kataAsal;
        }
        
    }

    function checkPrefixDisallowedSuffixes($kata) 
    {
        // be- dan -i
        if (preg_match('/^(be)[[:alpha:]]+(i)$/i', $kata)) 
        {
            return true;
        }
        
        // di- dan -an
        if (preg_match('/^(di)[[:alpha:]]+(an)$/i', $kata)) 
        {
            return true;
        }
        
        // ke- dan -i,-kan
        if (preg_match('/^(ke)[[:alpha:]]+(i|kan)$/i', $kata)) 
        {
            return true;
        }
        
        // me- dan -an
        if (preg_match('/^(me)[[:alpha:]]+(an)$/i', $kata)) 
        {
            return true;
        }
        
        // se- dan -i,-kan
        if (preg_match('/^(se)[[:alpha:]]+(i|kan)$/i', $kata)) 
        {
            return true;
        }
        
        return FALSE;
    }

    
    function removeEmoji($text){
        $i = 0;
        foreach($text as $text) {
            $kalimat[$i] = preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
            $i++;
        }
        return $kalimat;
    }
}