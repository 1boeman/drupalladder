<php
class Muziekdata extends Controller {
  function __construct(){ }

  function __call($name, $arguments) {
       
    if (preg_match('/^20[0-9]{2}$/',$name) && $arguments[0] &&
        preg_match('/^[0-9]{2}-[0-9]{2}\.json$/',$arguments[0] )){
        return array('json_string'=> file_get_contents(MUZIEK_DATA.'/'.$name.'/'.$arguments[0])); 
        
    }
  }

  function index() {
    
  }
}
