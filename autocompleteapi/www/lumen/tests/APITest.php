<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class APITest extends TestCase
{
    /**
     * Test the API at application level.
     *
     * @return void
     */
    public function testAPIUp()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

    }

    /**
     * Test the Search - failure case.
     * No country exists starting with indome
     * @return void
     */
    public function testSearchFail()
    {
        $this->json('GET', '/search', ['q' => 'indome'])
             ->seeJsonEquals([
                'data' => '[]',
             ]);
    }


    /**
     * Test the Search - success case.
     *
     * @return void
     */
    public function testSearchSuccess()
    {
        $this->json('GET', '/search', ['q' => 'indo'])
            ->seeJsonEquals([
            'data' => '[{"id":100,"iso":"ID","name":"INDONESIA","nicename":"Indonesia","iso3":"IDN","numcode":"360","phonecode":"62","displayname":"Indonesia,  IDN, +62"}]',
         ]);
    }


}
