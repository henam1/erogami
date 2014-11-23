<?php 
/** 
 * Class to represent content on webpage 
 */ 
class CBlog extends CContent { 

  public $slug = null;
  public $textFilter = null; 

        public function __construct($db) {  
           parent::__construct($db); 
        }  
    
    
        public function getPost() { 
            
            if(isset($_GET['slug'])) { 
                $this->slug = strip_tags($_GET['slug']); 
            } 
            
            $this->textFilter = new CTextFilter(); 
            
            if(isset($_GET['slug'])) { 
                $sql = "SELECT * FROM Content WHERE slug = '$this->slug'"; 
                $res = $this->db->ExecuteSelectQueryAndFetchAll($sql); 

                $html = null; 
                if(isset($res[0]))    { 
                    foreach($res as $c)    { 

                        $title    = htmlentities($c->title, null, 'UTF-8'); 
                        $data    =  substr($this->textFilter->doFilter(htmlentities($c->data, null, 'UTF-8'), $c->filter),0,50); 

                        $html .= "<h1>{$title}</h1>"; 
                        $html .= "<p class='smaller'>Publiceringsdatum: {$c->published}</p>"; 
                        $html .= "<p>{$data}</p>"; 
                    } 

                } 
            return $html; 
            } else { 
                $sql = "SELECT * FROM Content WHERE type = 'post'"; 
                $res = $this->db->ExecuteSelectQueryAndFetchAll($sql); 
                $html = null; 
                if(isset($res[0]))    { 
                    foreach($res as $c)    { 

                        $title    = htmlentities($c->title, null, 'UTF-8'); 
                        $data    = substr($this->textFilter->doFilter(htmlentities($c->data, null, 'UTF-8'), $c->filter), 0, 70) . "...";
                        $data    .= "<br><a href='blog.php?slug={$c->slug}'>Läs mer&raquo;</a>";

                        $html .= "<a class ='none' href='blog.php?slug={$c->slug}'><h2>{$title}</h2></a>"; 
                        $html .= "<p class='smaller'>Publiceringsdatum: {$c->published}</p>"; 
                        $html .= "<p>{$data}</p>"; 
                    } 
                return $html; 
                } 
            } 

        } 
} 
