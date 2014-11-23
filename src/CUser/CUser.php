<?php


class CUser{

    private $db  = null;
    private $acronym = null;
    
    public function __construct($database) 
    { 
        $this->db = $database;  
        
    }

    function login($user, $password){
        
          $sql = "SELECT acronym, name FROM User WHERE acronym = ? AND password = md5(concat(?, salt))";
          $params = array($user, $password); 
          $paramsPrint = htmlentities(print_r($params, 1)); 
          $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params); 
        
          if(isset($res[0])) {
            $_SESSION['user'] = $res[0];
          }
    }


    function logout(){
    unset($_SESSION['user']);
    }


    function IsAuthenticated(){
       $this->acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

        if($this->acronym) {
          return true;
        }
        else {
          return false;
        }
    }
    
    
    function getAcronym(){
    
        $acronym = $this->acronym;
        
        return $acronym;
    
    }


    function getName(){

        return $_SESSION['user']->name; 

    }
}