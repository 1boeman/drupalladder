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

  static function get_cities(){
    $locaties_db = self::open_locaties_db();
    $rv = $locaties_db->query('SELECT * from City order by Name');
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
