<?php


class CMovie{
    
    private $title      = null;
    private $genre      = null;
    private $hits       = null;
    private $page       = null;
    private $year1      = null;
    private $year2      = null;
    private $orderby    = null;
    private $order      = null;
    private $genres     = null;
    private $rows       = null;
    private $max        = null;
    private $row        = array();
    public $id;
    private $director;
    private $year;
    private $plot;
    private $image;
    private $price;
    private $published;
    private $db  = null;
    
    public function __construct($database) 
    { 
        $this->db = $database;  
        
    }
       
    /**
    *Get parameters
    *
    **/
    
    private function getParams(){
        
        $this->id        = isset($_GET['id'])      ? $_GET['id']        : null; 
        $this->plot      = isset($_GET['plot']) ? $_GET['plot'] : null; 
        $this->image     = isset($_GET['image']) ? $_GET['image'] : null; 
        $this->price     = isset($_GET['price']) ? $_GET['price'] : 20; 
        $this->published = isset($_GET['published']) ? $_GET['published'] : null; 
        $this->year      = isset($_GET['year']) ? $_GET['year'] : 1900; 
        $this->director  = isset($_GET['director']) ? $_GET['director'] : null; 
    
        $this->title    = isset($_GET['title']) ? $_GET['title'] : null;
        $this->genre    = isset($_GET['genre']) ? $_GET['genre'] : null;
        $this->hits     = isset($_GET['hits'])  ? $_GET['hits']  : 8;
        $this->page     = isset($_GET['page'])  ? $_GET['page']  : 1;
        $this->year1    = isset($_GET['year1']) && !empty($_GET['year1']) ? $_GET['year1'] : null;
        $this->year2    = isset($_GET['year2']) && !empty($_GET['year2']) ? $_GET['year2'] : null;
        $this->orderby  = isset($_GET['orderby']) ? strtolower($_GET['orderby']) : 'id';
        $this->order    = isset($_GET['order'])   ? strtolower($_GET['order'])   : 'asc';
        
        // Check that incoming parameters are valid
        is_numeric($this->hits) or die('Check: Hits must be numeric.');
        is_numeric($this->page) or die('Check: Page must be numeric.');
        is_numeric($this->year1) || !isset($this->year1)  or die('Check: Year must be numeric or not set.');
        is_numeric($this->year2) || !isset($this->year2)  or die('Check: Year must be numeric or not set.');
    
    }
    
    
    public function getLatestMovies(){
        $sql = ' 
                SELECT * FROM Movie  
                ORDER BY published DESC  
                LIMIT 4; 
            '; 
        
        $params = null; 
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params); 
        
        $html = "<div class = 'center'>"; 
           foreach($res AS $key => $val) { 
               $html .= "<a href='movie_info.php?id={$val->id}'><figure><img src='img.php?src={$val->image}&amp;width=160&amp;height=250&amp;crop-to-fit' alt='{$val->title}'>"; 
               $html .= "<figcaption>{$val->title}</figcaption></figure></a>"; 
           } 
           $html .= "</div>"; 
         
        return $html; 
    }
    
    public function getSingleMovie(){
        
        $this->getParams(); 
        $sql = "Select * FROM Movie WHERE id = $this->id;"; 
        
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
        
        $html = "<div>"; 
        foreach($res AS $key => $val) {  
$html .= <<<EOD
<h1>{$val->title}</h1>
<figure class = 'left top'><img src='img.php?src={$val->image}&amp;width=200&amp;height=300&amp;crop-to-fit' alt='{$val->title}'></figure>
<p>{$val->plot}</p> 
<p><b>År:</b> {$val->year} <b>Regissör:</b> {$val->director} <b>Pris:</b> {$val->price} kr</p> 
EOD;
}  
     $html .= "</div>";    
     return $html;  
    }
    
    
    public function getCategories(){
    
        $sql = '
          SELECT DISTINCT G.name
          FROM Genre AS G
            INNER JOIN Movie2Genre AS M2G
              ON G.id = M2G.idGenre
        ';

        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql);
        $counter=0;
        foreach($res as $val) {
          if($val->name == $this->genre) {
            $this->genres .= "$val->name ";
          }
          else {
            $this->genres .= "<a href='movie_index.php" . $this->getQueryString(array('genre' => $val->name)) . "'>{$val->name}</a> ";
          }
        }

            return $this->genres; 
    } 
    
    public function getMovieList(){
        $sql = ' 
            SELECT * 
            FROM Movie; 
        '; 
        
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

        // Put results into a list
        $items = "<table>";
        foreach($res AS $key => $val) { 
            $items .= "<tr><td><img src='img.php?src={$val->image}&amp;width=70&amp;sharpen' alt='{$val->title}' /></td><td style='padding:5px;'><h3>" . htmlentities($val->title, null, 'UTF-8') . "</h3></td><td style='padding:5px;'> <a href='movie_edit.php?id={$val->id}'>Editera</a> | <a href='movie_info.php?id={$val->id}'>Visa</a> | <a href='movie_delete.php?id={$val->id}&amp;title={$val->title}'>Ta bort</a></td></tr>"; 
        } 
         
        $items .= "</table>";
        
        return $items; 
    }
    
    public function deleteMovie(){
        
        $this->getParams();
        
        $sql = ' 
            DELETE FROM Movie2Genre WHERE idMovie = ?;
            DELETE FROM Movie WHERE id = ? LIMIT 1
              '; 
        
        $res = null;
        $res = $this->db->ExecuteQuery($sql, array($this->id, $this->id)); 

        if($res) { 
              return '"' . $this->title . '"' . ' är raderad'; 
          } 
                else { 
                    return "Filmen kunde ej tas bort"; 
          }
    
    }
    
    public function updateMovie() {
        
        $this->id = $id = isset($_POST['id'])    ? strip_tags($_POST['id']) : (isset($_GET['id']) ? strip_tags($_GET['id']) : null);
        $title  = isset($_POST['title']) ? $_POST['title'] : (isset($_GET['title']) ? strip_tags($_GET['title']) : null); 
        $image  = isset($_POST['image']) ? $_POST['image'] : null; 
        $director = isset($_POST['director']) ? $_POST['director'] : null; 
        $genre = isset($_POST['genre']) ? $_POST['genre'] : null; 
        $plot = isset($_POST['plot']) ? $_POST['plot'] : null; 
        $year   = isset($_POST['year']) ? $_POST['year'] : null;  
        $price  = isset($_POST['price']) ? $_POST['price'] : null; 
        $save   = isset($_POST['save'])  ? true : false;
        
        // Check numeric
        is_numeric($id) or die('Check: Id must be numeric.');
        
        if($save) {
            
            if (empty($title)){
                die("Title is empty, please try again!");
            }
            if (empty($image)){
                die("Image is empty, please try again!");
            }
            if (empty($director)){
                die("Director is empty, please try again!");
            }
            if (empty($genre)){
                die("Genre is empty, please try again!");
            }
            if ($genre>12){
                die("Genre must be between 1-12!");
            }
            if (empty($plot)){
                die("Plot is empty, please try again!");
            }
            if (empty($year)){
                die("Year is empty, please try again!");
            }
            if (empty($price)){
                die("Price is empty, please try again!");
            }
            
          $sql = ' 
                    UPDATE Movie SET 
                    title   = ?, 
                    image    = ?, 
                    director     = ?, 
                    plot    = ?,
                    year  = ?,  
                    price = ? 
                    WHERE  
                    id = ?;
                    UPDATE Movie2Genre SET 
                    idGenre = ?
                    WHERE 
                    idMovie = ? 
                '; 
            
          $params = array($title, $image, $director, $plot, $year, $price, $id, $genre, $id);
          $res = $this->db->ExecuteQuery($sql, $params);
          if($res) {
            $output = 'Informationen sparades.';
          }
          else {
            $output = 'Informationen sparades EJ.<br><pre>' . print_r($this->db->ErrorInfo(), 1) . '</pre>';
          }
            return $output;
            
        }
    }
    
    public function getItem(){
        
        $this->id = $id = isset($_POST['id'])    ? strip_tags($_POST['id']) : (isset($_GET['id']) ? strip_tags($_GET['id']) : null);
        
            // Select from database
        $sql = 'SELECT * FROM Movie WHERE id = ?';
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($this->id));
        
        if(isset($res[0])) {
          $c = $res[0];
        }
        
        else {
          die('Misslyckades: det finns inget innehåll med sådant id.');
        }
    
        return $c;
    }
    
    public function getGenre(){
        
        $sql2 = 'SELECT * FROM Movie2Genre WHERE idMovie = ?';
        $res2 = $this->db->ExecuteSelectQueryAndFetchAll($sql2, array($this->id));
        
        if(isset($res2[0])) {
          $c = $res2[0];
          $genre   = htmlentities($c->idGenre, null, 'UTF-8');
    
        }
        
        else {
          die('Misslyckades: det finns inget innehåll med sådant id.');
        }
        
        
        return $genre;
       
    }
    
    
    public function sanatize($c){
    
    // Sanitize content before using it. 
        $title  = htmlentities($c->title, null, 'UTF-8'); 
        $image   = htmlentities($c->image, null, 'UTF-8'); 
        $director    = htmlentities($c->director, null, 'UTF-8');  
        $plot   = htmlentities($c->plot, null, 'UTF-8'); 
        $year = htmlentities($c->year, null, 'UTF-8');    
        $price = htmlentities($c->price, null, 'UTF-8');
        $params = array($title, $image, $director, $plot, $year, $price); 
        
        return $params;
    
    }
    
    public function createMovie(){
        
        $title  = isset($_POST['title']) ? $_POST['title'] : (isset($_GET['title']) ? strip_tags($_GET['title']) : null); 
        $image  = isset($_POST['image']) ? $_POST['image'] : null; 
        $director = isset($_POST['director']) ? $_POST['director'] : null; 
        $genre = isset($_POST['genre']) ? $_POST['genre'] : null; 
        $plot = isset($_POST['plot']) ? $_POST['plot'] : null; 
        $year   = isset($_POST['year']) ? $_POST['year'] : null;  
        $price  = isset($_POST['price']) ? $_POST['price'] : null; 
        
        if (empty($title)){
                die("Title is empty, please try again!");
            }
            if (empty($image)){
                die("Image is empty, please try again!");
            }
            if (empty($director)){
                die("Director is empty, please try again!");
            }
            if (empty($genre)){
                die("Genre is empty, please try again!");
            }
            if ($genre>12){
                die("Genre must be between 1-12!");
            }
            if (empty($plot)){
                die("Plot is empty, please try again!");
            }
            if (empty($year)){
                die("Year is empty, please try again!");
            }
            if (empty($price)){
                die("Price is empty, please try again!");
            }
    
        $sql = ' 
      INSERT INTO  
          Movie (title, image, director, plot, year, price)  
      VALUES(?, ?, ?, ?, ?, ?)'; 
           
      $params = array($title, $image, $director, $plot, $year, $price); 
      $res = $this->db->ExecuteQuery($sql, $params);
        
      if($res) { 
        $sql3 = 'SELECT * FROM Movie WHERE title = ? LIMIT 1';
        $res3 = $this->db->ExecuteSelectQueryAndFetchAll($sql3, array($title));
          
          $id = null;
          foreach ($res3 as $val){
          $id = $val->id;
          }
          
            $sql2 =  '
          INSERT INTO  
          Movie2Genre (idMovie, idGenre)  
          VALUES (?, ?)';
          
          $res2 = $this->db->ExecuteQuery($sql2, array($id ,$genre));  
          
          if($res2){
          header('Location: admin.php');
          }
          else{
          return "något gick fel";
          }
      } else { 
          return "Sidan gick inte att skapa"; 
      } 
    
    return;
    
    }

    
    public function searchMovie(){

        $sql = '
          SELECT DISTINCT G.name
          FROM Genre AS G
            INNER JOIN Movie2Genre AS M2G
              ON G.id = M2G.idGenre
        ';

        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

        foreach($res as $val) {
          if($val->name == $this->genre) {
            $this->genres .= "$val->name ";
          }
          else {
            $this->genres .= "<a href='" . $this->getQueryString(array('genre' => $val->name)) . "'>{$val->name}</a> ";
          }
        }
        
        // Prepare the query based on incoming arguments
        $sqlOrig = '
          SELECT 
            M.*,
            GROUP_CONCAT(G.name) AS genre
          FROM Movie AS M
            LEFT OUTER JOIN Movie2Genre AS M2G
              ON M.id = M2G.idMovie
            INNER JOIN Genre AS G
              ON M2G.idGenre = G.id
        ';
        $where    = null;
        $groupby  = ' GROUP BY M.id';
        $limit    = null;
        $sort     = " ORDER BY $this->orderby $this->order";
        $params   = array();

        // Select by title
        if($this->title) {
          $where .= ' AND title LIKE ?';
          $params[] = $this->title;
        } 

        // Select by year
        if($this->year1) {
          $where .= ' AND year >= ?';
          $params[] = $this->year1;
        } 
        if($this->year2) {
          $where .= ' AND year <= ?';
          $params[] = $this->year2;
        } 

        // Select by genre
        if($this->genre) {
          $where .= ' AND G.name = ?';
          $params[] = $this->genre;
        } 

        // Pagination
        if($this->hits && $this->page) {
          $limit = " LIMIT $this->hits OFFSET " . (($this->page - 1) * $this->hits);
        }
        
        // Get max pages for current query, for navigation
        $sql = "
          SELECT
            COUNT(id) AS rows
          FROM 
          (
            $sqlOrig $where $groupby
          ) AS Movie
        ";
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);
        $this->rows = $res[0]->rows;
        $this->max = ceil($this->rows / $this->hits);

        // Complete the sql statement
        $where = $where ? " WHERE 1 {$where}" : null;
        $sql = $sqlOrig . $where . $groupby . $sort . $limit;
        $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);


        // Put results into a HTML-table
        $tr = "<tr><th class = 'large'>Id " . $this->orderby('id') . "</th><th class = 'large'>Bild</th><th class = 'large'>Titel " . $this->orderby('title') . "</th><th class = 'large'>År " . $this->orderby('year') . "</th><th class = 'large'>Genre</th><th class = 'large'>Beskrivning</th></tr>";
        foreach($res AS $key => $val) {
          $tr .= "<tr><td class = 'wider'>{$val->id}</td><td><a href='movie_info.php?id={$val->id}'><img width='70' height='95' src='img/{$val->image}' alt='{$val->title}' /></a></td><td>{$val->title}</td><td>{$val->year}</td><td>{$val->genre}</td><td><a class='none' href='movie_info.php?id={$val->id}'>".substr($val->plot,0,100)."...</a></td></tr>";
        }
        
        return $tr;
        
    }
    
    
    /**
     * Use the current querystring as base, modify it according to $options and return the modified query string.
     *
     * @param array $options to set/change.
     * @param string $prepend this to the resulting query string
     * @return string with an updated query string.
     */
    public function getQueryString($options=array(), $prepend='?') {
      // parse query string into array
      $query = array();
      parse_str($_SERVER['QUERY_STRING'], $query);

      // Modify the existing query string with new options
      $query = array_merge($query, $options);

      // Return the modified querystring
      return $prepend . htmlentities(http_build_query($query));
    }



    /**
     * Create links for hits per page.
     *
     * @param array $hits a list of hits-options to display.
     * @param array $current value.
     * @return string as a link to this page.
     */
    public function getHitsPerPage($hits, $current=null) {
      $nav = "Träffar per sida: ";
      foreach($hits AS $val) {
        if($current == $val) {
          $nav .= "$val ";
        }
        else {
          $nav .= "<a href='" . $this->getQueryString(array('hits' => $val)) . "'>$val</a> ";
        }
      }  
      return $nav;
    }



    /**
     * Create navigation among pages.
     *
     * @param integer $hits per page.
     * @param integer $page current page.
     * @param integer $max number of pages. 
     * @param integer $min is the first page number, usually 0 or 1. 
     * @return string as a link to this page.
     */
    function getPageNavigation($min=1) {
      $nav  = ($this->page != $min) ? "<a href='" . $this->getQueryString(array('page' => $min)) . "'>&lt;&lt;</a> " : '&lt;&lt; ';
      $nav .= ($this->page > $min) ? "<a href='" . $this->getQueryString(array('page' => ($this->page > $min ? $this->page - 1 : $min) )) . "'>&lt;</a> " : '&lt; ';

      for($i=$min; $i<=$this->max; $i++) {
        if($this->page == $i) {
          $nav .= "$i ";
        }
        else {
          $nav .= "<a href='" . $this->getQueryString(array('page' => $i)) . "'>$i</a> ";
        }
      }

      $nav .= ($this->page < $this->max) ? "<a href='" . $this->getQueryString(array('page' => ($this->page < $this->max ? $this->page + 1 : $this->max) )) . "'>&gt;</a> " : '&gt; ';
      $nav .= ($this->page != $this->max) ? "<a href='" . $this->getQueryString(array('page' => $this->max)) . "'>&gt;&gt;</a> " : '&gt;&gt; ';
      return $nav;
    }



    /**
     * Function to create links for sorting
     *
     * @param string $column the name of the database column to sort by
     * @return string with links to order by column.
     */
    function orderby($column) {
      $nav  = "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'asc')) . "'>&darr;</a>";
      $nav .= "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'desc')) . "'>&uarr;</a>";
      return "<span class='orderby'>" . $nav . "</span>";
    }
    
    
    
    public function getHtml() {  
        $this->getParams();   
        $tr = $this->searchMovie();  
        $hitsPerPage = $this->getHitsPerPage(array(2, 4, 8), $this->hits);  
        $navigatePage = $this->getPageNavigation();  
         
        $html ="<form> 
                <fieldset> 
                <legend>Sök</legend> 
                <p><label>Titel (delsträng, använd % som *): <input type='search' name='title' value='{$this->title}'/></label></p> 
                <p><label>Välj genre:</label>{$this->genres}</p> 
                <p><label>Skapad mellan åren:  
                    <input type='text' name='year1' value='{$this->year1}'/></label> 
                    -  
                    <label><input type='text' name='year2' value='{$this->year2}'/></label> 
     
                </p> 
                <p><input type='submit' name='submit' value='Sök'/></p> 
                <p><a href='?'>Visa alla</a></p> 
                </fieldset> 
            </form> 

            <div class = 'dbtable'> 
                <div class = 'rows'>{$this->rows} träffar. {$hitsPerPage}</div> 
                    <table> 
                        {$tr} 
                    </table> 
                <div class = 'pages'>{$navigatePage}</div> 
             </div>"; 
        return  $html; 

    }  
    
    
    
    
}