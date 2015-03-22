<?php 
class Muziek_db {
  private $dbhandle; 

  function __construct(){
    $this->dbhandle = self::open_locaties_db(); 
  }

  static function open_locaties_db(){
    $dbhandle = new SQLite3(MUZIEK_SQL_DIR.'/locaties.db');
    return $dbhandle;   
  }
  
  static function get_city_venues($cityno){
     $db = self::open_locaties_db();
     $statement = $db->prepare('SELECT * from Venue where Cityno = :id order by Title;');
     $statement->bindValue(':id', (int) $cityno);
     $result = $statement->execute();
  
     return $result;
  }

  static function get_city_gigs($cityno,$startdate,$page=0,$number_per_page=100){
    $offset = $number_per_page * abs((int)$page); 
    $db = self::open_locaties_db();
    $result_fields = '
        E.Id as Event_Id,
        E.Date as Event_Date,
        E.Time as Event_Time,
        E.Img as Event_Img,
        E.Link as Event_Link,
        E.Title as Event_Title,
        E.Desc as Event_Desc,
        E.Venue as Event_Venue,
        V.Id as Venue_Id,
        V.Link as Venue_Link,
        V.Title as Venue_Title, 
        C.Name as City_Name,
        C.Countryno as City_Countryno
    '; 
    if ($cityno){
      $city_clause=' and Venue in
        (select id from Venue where Cityno = :id) ';
    }
    $statement = $db->prepare('
     SELECT '.$result_fields.'
     from Event E 
      LEFT JOIN Venue V on V.id = E.Venue 
      LEFT JOIN City C on V.Cityno = C.Id  
        where E.Date >= :date '.$city_clause.      
      ' order by E.Date,C.Name,V.Title
      limit '.$offset.','.$number_per_page.';'); 
     if ($cityno){
      $statement->bindValue(':id', (int) $cityno);
     }

     $statement->bindValue(':date',  $startdate);
     $result = $statement->execute();
  
     return $result; 
  }
    
  static function get_cities($raw = false){
    $locaties_db = self::open_locaties_db();
    $result = $locaties_db->query('SELECT * from City order by Name');
    if ($raw) return $result;
   
    $rv = array();   
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
      $rv[]=$res;
    } 
    return $rv;      
  }

  function get_venue($venue_id){
    $statement = $this->dbhandle->prepare('SELECT * FROM Venue WHERE Id = :id;');
    $statement->bindValue(':id', $venue_id);
    $result = $statement->execute();
    return $result->fetchArray();     
  }
  
  function get_city($cityno){
    $statement = $this->dbhandle->prepare('SELECT * FROM City WHERE Id = :id;');
    $statement->bindValue(':id', (int)$cityno);
    $result = $statement->execute();
    return $result->fetchArray(); 
  }
}
