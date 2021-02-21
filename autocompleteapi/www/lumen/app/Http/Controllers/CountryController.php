<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Client;

class CountryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
	}

    public function search(){
        $searchterm = strtolower(trim($_REQUEST['q']));
        if( !isset($searchterm) || strlen($searchterm) == 0){
            return response()->json([
                'data' => '[]'
            ]
            );
        }

                
        /*
        * These models are working, but retrieving specific search items by using them  is not working
        * $countries = \App\Models\Country::where('name', "like", trim($_REQUEST['q']).'%')
        *       ->get();
        *
        * Let's use guzzlehttp to make call Elasticsearch
        **/
        $client = new Client(
            [    
                'base_uri' => 'http://elastic:9200/countries', 
                'timeout'  => 2.0,
                'headers' => [ 'Content-Type' => 'application/json', "Accept" => "application/json"],
                'verify' => false
            ]
        );
        $body = '{
            "query": {
                "wildcard": {
                    "name": {
                        "value": "'.$searchterm.'*",
                        "boost": 1.0,
                        "rewrite": "constant_score"
                    }
                }
            }
        }';

        $statuscode;
        try {
            $response = $client->get('/_search',[ 'body' => $body ]);
            $statuscode = $response->getStatusCode();
            $resp_str = $response->getBody()->getContents();
            $resp_arr = json_decode($resp_str, true);
            
            $result_arr;
            if( is_array($resp_arr['hits']['hits']) && (count($resp_arr['hits']['hits']) > 0) ){
                foreach ($resp_arr['hits']['hits']as $k => $v){
                    
                    $result_arr[$k] = $v['_source'];
                    $result_arr[$k]['displayname'] = $v['_source']['nicename'].',  '.$v['_source']['iso3'].', +'.$v['_source']['phonecode'];
                }

            }else{
                return response()->json([
                        'data' => '[]'
                    ]
                );
            }
        } catch (Exception $e) {
            if ($statuscode > 300) {
                // To do: Log error with full stacktrace to Sentry logging service with critical flag for triage and notification
                // return empty response
                return response()->json([
                        'data' => '[]'
                    ]
                );
            }else{
                // To do: Log error full stacktrace to Sentry logging service for triage and notification
                // return empty response
                return response()->json([
                        'data' => '[]'
                    ]
                );
            }
        } finally {
            
        }


        return response()->json([ "data" => $result_arr]);

	}

    private function  dd($data){
        echo '<pre>';
        die(var_dump($data));
        echo '</pre>';
    }
 
    public function createCountry(Request $request){
 
    	$car = Country::create($request->all());
 
    	return response()->json($car);
 
	}

	public function updateCountry(Request $request, $id){

    	$country = Country::find($id);
    	$country->country_name = $request->input('country_name');
    	$country->iso_code = $request->input('iso_code');
    	$country->save();
 
    	return response()->json($country);
	}  

	public function deleteCountry($id){
    	$country  = Country::find($id);
    	$country->delete();
 
    	return response()->json('Removed successfully.');
	}

	
}
