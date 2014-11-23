<?php


class CContent{

    protected $db;
    public $id;
    
    public function __construct($database) 
    { 
        $this->db = $database;  
        
    }
    
     // Create a link to the content, based on its type.
      // @param object $content to link to.
       // @return string with url to display content.
     
     public function getUrlToContent($content) {
      switch($content->type) {
        case 'page': return "page.php?url={$content->url}"; break;
        case 'post': return "blog.php?slug={$content->slug}"; break;
        default: return null; break;
      }
    }
    
    public function setId(){
        $this->id     = isset($_POST['id'])    ? strip_tags($_POST['id']) : (isset($_GET['id']) ? strip_tags($_GET['id']) : null);
    }
    
        // Get all content
    public function getContentList(){
        $sql = '
          SELECT *, (published <= NOW()) AS available
          FROM Content;
        ';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

        // Put results into a list
        $items = null;
        foreach($res AS $key => $val) {
        $items .= "<li>{$val->type} (" . (!$val->available ? 'inte ' : null) . "publicerad): " . htmlentities($val->title, null, 'UTF-8') . 
            " (<a href='edit.php?id={$val->id}'>editera</a> <a href='" . $this->getUrlToContent($val) . "'>visa</a> <a href='delete.php?id=" . $val->id . "'>ta bort</a>)</li>\n";
        }
        
        return $items; 
    }
    
    public function getLatestBlogposts() { 
        $sql = " 
            SELECT * FROM Content  
            WHERE type = 'post'  
            ORDER BY published DESC LIMIT 3; 
            ; 
        "; 
         
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql); 
        
        $html = null; 
         
        foreach($res AS $key => $val) { 
            $html .= "<h5><a href='blog.php?slug={$val->slug}'>{$val->title}</a></h5>"; 
            $html .= "<p>Publicerad: {$val->published}</p>"; 
            $html .= "<hr />"; 
        } 
         
        return $html; 
    }
    
    // Create content table
    public function createTable(){
        $sql = "
        DROP TABLE IF EXISTS Content;
        CREATE TABLE Content
        (
          id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
          slug CHAR(80) UNIQUE,
          url CHAR(80) UNIQUE,
         
          type CHAR(80),
          title VARCHAR(80),
          data TEXT,
          filter CHAR(80),
         
          published DATETIME,
          created DATETIME,
          updated DATETIME,
          deleted DATETIME
         
        ) ENGINE INNODB CHARACTER SET utf8;
        ";
        
        $this->db->ExecuteQuery($sql);
        
    }
    
    // Delete and restore content
    public function resetTable(){
      $sql = " 
            DELETE FROM Content; 
            INSERT INTO Content (slug, url, type, title, data, filter, published, created) VALUES 
                ('hem', 'hem', 'page', 'Hem', 'Detta är min hemsida. Den är skriven i [url=http://en.wikipedia.org/wiki/BBCode]bbcode[/url] vilket innebär att man kan formattera texten till [b]bold[/b] och [i]kursiv stil[/i] samt hantera länkar.\n\nDessutom finns ett filter \"nl2br\" som lägger in <br>-element istället för \\n, det är smidigt, man kan skriva texten precis som man tänker sig att den skall visas, med radbrytningar.', 'bbcode,nl2br', NOW(), NOW()),
                ('om', 'om', 'page', 'Om', 'Detta är en sida om mig och min webbplats. Den är skriven i [Markdown](http://en.wikipedia.org/wiki/Markdown). Markdown innebär att du får bra kontroll över innehållet i din sida, du kan formattera och sätta rubriker, men du behöver inte bry dig om HTML.\n\nRubrik nivå 2\n-------------\n\nDu skriver enkla styrtecken för att formattera texten som **fetstil** och *kursiv*. Det finns ett speciellt sätt att länka, skapa tabeller och så vidare.\n\n###Rubrik nivå 3\n\nNär man skriver i markdown så blir det läsbart även som textfil och det är lite av tanken med markdown.', 'markdown', NOW(), NOW()),
                ('blogpost-1', NULL, 'post', 'Attack on titan gör rekord!', 'Attack on titan blir årets anime! läs mer om animen på: nhttp://myanimelist.net/anime/16498/Shingeki_no_Kyojin.', 'link,nl2br', NOW(), NOW()),
                ('blogpost-2', NULL, 'post', 'Highschool DxD, ny säsong bekräftad', 'Den alltmer populära animen Highschool DxD ska släppa en ny säsong till nästa år. Förbered er genom att hyra de gamla säsongerna nu!', 'nl2br', NOW(), NOW()),
                ('blogpost-3', NULL, 'post', 'Psycho Pass säsong 3 annnonserad', 'Psycho Pass säsong 2 gör succe och fansen kräver mer. En tredje säsong förebereds nu och blir redo till nästa år.', 'nl2br', NOW(), NOW()),
                ('blogpost-4', NULL, 'post', 'Clannad blir till drama', 'Den populära serien Clannad blir till en live action show med flera kända ansikten som t ex. Yamapi, Erika Toda och Aragaki Yui.', 'nl2br', NOW(), NOW()),
                ('blogpost-5', NULL, 'post', 'Death Note släpper ny film', 'Efter flera års väntan är den äntligen här! fortsättningen till Death Note väntas släppas redan till våren och fansen är förtjusta.', 'nl2br', NOW(), NOW()),
                ('blogpost-6', NULL, 'post', 'Kalle Anka blir Japan', 'Japanerna gör nu ett försök till att animera om en välkänd disney karaktär. Det är ingen mindre än Kalle Anka! Första filmen väntas släppas redan nästa månad.', 'nl2br', NOW(), NOW()),
                ('blogpost-7', NULL, 'post', 'Naruto läggs ner', 'Den världskända animen Naruto har nu haft sitt sista avsnitt och författaren förbereder sig för pension.', 'nl2br', NOW(), NOW()),
                ('blogpost-8', NULL, 'post', 'One Piece fortsätter i 10 år till', 'Skaparen av One Piece har nu meddelat att de tänker fortsätta i minst 10 år till! Se fram emot massa spännande material i framtiden.', 'nl2br', NOW(), NOW());
                 
                 "; 
    $res = $this->db->executeQuery($sql); 
         
    if($res) { 
        return 'Databasen är återställd'; 
    } 
        else { 
            return 'Databasen kunde inte återställas'; 
    } 
    }
    
    public function updateItem() {
        $this->setId();
        $id = $this->id;
        $title  = isset($_POST['title']) ? $_POST['title'] : null;
        $slug   = isset($_POST['slug'])  ? $_POST['slug']  : null;
        $url    = isset($_POST['url'])   ? strip_tags($_POST['url']) : null;
        $data   = isset($_POST['data'])  ? $_POST['data'] : array();
        $type   = isset($_POST['type'])  ? strip_tags($_POST['type']) : array();
        $filter = isset($_POST['filter']) ? $_POST['filter'] : array();
        $published = isset($_POST['published'])  ? strip_tags($_POST['published']) : array();
        $save   = isset($_POST['save'])  ? true : false;
        
        // Check numeric
        is_numeric($id) or die('Check: Id must be numeric.');
        
        if($save) {
            
            if (empty($filter)){
                die("Filter is empty, please try again!");
            }
            if (empty($slug)){
                die("Slug is empty, please try again!");
            }
            if (empty($type)){
                die("Type is empty, please try again!");
            }
            
          $sql = '
            UPDATE Content SET
              title   = ?,
              slug    = ?,
              url     = ?,
              data    = ?,
              type    = ?,
              filter  = ?,
              published = ?,
              updated = NOW()
            WHERE 
              id = ?
          ';
          $url = empty($url) ? null : $url;
          $params = array($title, $slug, $url, $data, $type, $filter, $published, $id);
          $res = $this->db->ExecuteQuery($sql, $params);
          if($res) {
            $output = 'Informationen sparades.';
          }
          else {
            $output = 'Informationen sparades EJ.<br><pre>' . print_r($db->ErrorInfo(), 1) . '</pre>';
          }
            return $output;
            
        }
    }
    
    public function getItem(){
    
        $this->setId();
        
            // Select from database
        $sql = 'SELECT * FROM Content WHERE id = ?';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($this->id));

        if(isset($res[0])) {
          $c = $res[0];
        }
        else {
          die('Misslyckades: det finns inget innehåll med sådant id.');
        }
    
        return $c;
    }
    
    
    public function sanatize($c){
    
    // Sanitize content before using it.
        $title  = htmlentities($c->title, null, 'UTF-8');
        $slug   = htmlentities($c->slug, null, 'UTF-8');
        $url    = htmlentities($c->url, null, 'UTF-8');
        $data   = htmlentities($c->data, null, 'UTF-8');
        $type   = htmlentities($c->type, null, 'UTF-8');
        $filter = htmlentities($c->filter, null, 'UTF-8');
        $published = htmlentities($c->published, null, 'UTF-8');
        $params = array($title, $slug, $url, $data, $type, $filter, $published); 
        
        return $params;
    
    }
    
    // Create a slug of a string, to be used as url. 
    private function slugify($str) { 
      $str = mb_strtolower(trim($str)); 
      $str = str_replace(array('å','ä','ö'), array('a','a','o'), $str); 
      $str = preg_replace('/[^a-z0-9-]/', '-', $str); 
      $str = trim(preg_replace('/-+/', '-', $str), '-'); 
      return $str; 
  } 
    
    public function createItem($title){
    
      $slug = strip_tags($title); 
      $slug = $this->slugify($title); 
      $sql = ' 
      INSERT INTO  
          Content (slug, title, type, filter, created)  
      VALUES(?, ?,"post", "markdown", NOW())'; 
           
      $params = array($slug, $title); 
      $res = $this->db->ExecuteQuery($sql, $params); 
      if($res) { 
          return '"' . htmlentities($title) . '"' . ' är skapad'; 
      } else { 
          return "Sidan gick inte att skapa"; 
      } 
    
    }
    
    public function deleteItem($id, $title){
    $sql = ' 
      DELETE FROM Content WHERE id = ? LIMIT 1'; 
          $res = $this->db->ExecuteQuery($sql, array($id)); 
          
            if($res) { 
              return '"' . $title . '"' . ' är raderad'; 
          } 
                else { 
                    return "Sidan kunde ej tas bort"; 
          } 
    
    }
    
    //Check login
    public function checkLogin() { 
        $acronym = isset($_SESSION['user']) ? $_SESSION['user'] : null; 
        isset($acronym) or die('Check: You must login to edit.'); 
   } 


}