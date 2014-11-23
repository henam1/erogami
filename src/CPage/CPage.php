<?php 


class CPage extends CContent{

    public $url=null;
    public $textFilter=null;
    
    public function __construct($db) {  
          parent::__construct($db); 
        } 
    
    public function getPage(){
        
        if(isset($_GET['url'])) {
            $this->url = strip_tags($_GET['url']);
        }
            $this->textFilter = new CTextFilter(); 
        
             $sql = "SELECT * FROM Content WHERE url = '$this->url' AND type = 'page'"; 
                $res = $this->db->ExecuteSelectQueryAndFetchAll($sql); 
                $html = null; 
                if(isset($res[0]))    { 
                    foreach($res as $c)    { 
                        $title = htmlentities($c->title, null, 'UTF-8'); 
                        $data  = $this->textFilter->doFilter(htmlentities($c->data, null, 'UTF-8'), $c->filter); 

                        $html .= "<h1>{$title}</h1>";  
                        $html .= "<p>{$data}</p>"; 
                    } 
                } 
                return $html; 
    } 
    

}