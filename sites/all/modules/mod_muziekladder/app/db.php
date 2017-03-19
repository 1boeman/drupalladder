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
  
  static function get_city_venues($cityno,$raw = true){
    $db = self::open_locaties_db();
    $statement = $db->prepare('SELECT * from Venue where Cityno = :id order by Title;');
    $statement->bindValue(':id', (int) $cityno);
    $result = $statement->execute();
    if ($raw){
      return $result;
    } else {
      return self::result_to_array($result);    
    }
  }

  static function get_venue_gigs($venue_id,$raw = false){
    $db = self::open_locaties_db(); 
    $statement = $db->prepare('
      SELECT *
      from Event E 
      WHERE E.Venue = :id 
      order by Date');

    $statement->bindValue(':id', $venue_id);
    $result = $statement->execute();
    if ($raw){
      return $result;
    } else {
      return self::result_to_array($result);    
    }
  }

  static function get_regio($regio_id){
    $db = self::open_locaties_db();
    $statement = $db->prepare('SELECT * FROM Groups WHERE Id = :id');
    $statement->bindValue( ':id', $regio_id);
    $result = $statement->execute();
    return $result->fetchArray();
  }
  
  static function get_regios(){
    $db = self::open_locaties_db();
    $statement = $db->prepare(' SELECT * from Groups');
    $result = $statement->execute();
    
    return self::result_to_array( $result );
  }

  static function get_regio_gigs($regio_id,$startdate,$page=0,$number_per_page=100){
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
        E.Type as Event_Type,
        V.Id as Venue_Id,
        V.Link as Venue_Link,
        V.Title as Venue_Title, 
        C.Name as City_Name,
        C.Id as City_Id,
        C.Countryno as City_Countryno ';

    $statement = $db->prepare('
     SELECT '.$result_fields.'
     from Event E 
      LEFT JOIN Venue V on V.id = E.Venue 
      LEFT JOIN City C on V.Cityno = C.Id  
        where E.Date >= :date
        and Venue in
        (select id from Venue where Cityno in
          (select cityno from CityGroups where GroupId = :id ))
      order by E.Date,C.Name,V.Title
      limit '.$offset.','.$number_per_page.';'); 
      $statement->bindValue(':id', $regio_id);


     $statement->bindValue(':date',  $startdate);
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
        E.Type as Event_Type,
        E.Venue as Event_Venue,
        V.Id as Venue_Id,
        V.Link as Venue_Link,
        V.Title as Venue_Title, 
        C.Name as City_Name,
        C.Id as City_Id,
        C.Countryno as City_Countryno
    '; 
    $city_clause = ''; 
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
  
  static function get_cities_by_ids($ids, $raw=false){
    $identifiers = array();
    foreach($ids as $id){
      $identifiers[]=(int)$id;
    }
    $locaties_db = self::open_locaties_db();
    $result = $locaties_db->query('SELECT * from City 
      where Id in ('.join(",",$identifiers).')' );
    if ($raw) return $result;
   
    $rv = array();   
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
      $rv[]=$res;
    } 
    return $rv;      
  }
    
  static function get_cities_with_names_containing_dash(){
    $db = self::open_locaties_db();
    $statement = $db->prepare("SELECT * from City WHERE Name like '%-%';");
    $result = $statement->execute();
    $rv = self::result_to_array($result);    

    return $rv;      
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
  
  private static function result_to_array($result){
    $rv = array();   
    while($res = $result->fetchArray(SQLITE3_ASSOC)){ 
      $rv[]=$res;
    } 
    return $rv; 
  }

  function get_gig_by_id($id){
    $statement = $this->dbhandle->prepare('
      SELECT * from Event  WHERE Id = :id;' );
    $statement->bindValue(':id',$id);
    $result = $statement->execute(); 
    $rv = $this->result_to_array($result);    
    return $rv;       
  }

  function get_gig($date,$url){
    $statement = $this->dbhandle->prepare('SELECT * from Event g WHERE g.Link = :url AND Date = :date ' );
    $statement->bindValue(':url',$url);
    $statement->bindValue(':date',$date);
    $result = $statement->execute(); 
    $rv = $this->result_to_array($result);    
    return $rv;       
  }
  
  function get_venue($venue_id){
    $statement = $this->dbhandle->prepare(
    'SELECT V.*, C.Name as City_name, 
      Co.Name as Country_name, Co.No as Countryno 
      FROM Venue V 
      LEFT JOIN City C on V.Cityno = C.Id 
      LEFT JOIN Country Co on Co.No = C.Countryno WHERE V.Id = :id;');
    $statement->bindValue(':id', $venue_id);
    $result = $statement->execute();
    return $result->fetchArray(SQLITE3_ASSOC);     
  }
  
  function get_city($cityno){
    $statement = $this->dbhandle->prepare('
      SELECT c.*,co.Name as Country_name FROM City c 
        LEFT JOIN Country co on co.No = c.Countryno 
        WHERE Id = :id;');
    $statement->bindValue(':id', (int)$cityno);
    $result = $statement->execute();
    return $result->fetchArray(SQLITE3_ASSOC); 
  }

}
