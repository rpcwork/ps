<?php

namespace App\Models;
 
use Basemkhirat\Elasticsearch\Model;

class Country extends Model
{ 
   protected $type = "country";
   
   protected $index = "countries";
}
?>