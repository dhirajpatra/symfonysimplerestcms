<?php

use PHPUnit\Framework\TestCase;

/**
 * Class TopicControllerTest
 */
class TopicControllerTest extends TestCase
{
    protected $client;

    /**
     * settings for test
     * need to change base_uri as per the vhost setup in system for application
     */
    protected function setUp()
    {
        // create http client
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://cms.dev',
            [
                'request.options' => [
                    'exceptions' => false
                ]
            ]
        ]);
    }

    /**
     * This will test http://cms.dev/api/v1/topic/new api
     */
    public function testNew()
    {

        $title = $this->randText();

        $response = $this->client->post('/api/v1/topic/new', [
            'json' => [
                'title' => $title
            ]
         ]);

        $this->assertEquals(201, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    /**
     * this will test http://cms.dev/api/v1/topic/{id} api
     * topic id should be present in db
     */
    public function testGet()
    {
        // $id value should be checked from application db whether topic id present
        $id = 70;
        $response = $this->client->get('/api/v1/topic/' . $id);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode(json_decode($response->getBody(), true), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertContains('Topic found successfully', $data['message']);
        $this->assertArrayHasKey('topic_id', $data['data']);
        $this->assertArrayHasKey('topic_title', $data['data']);

    }

    /**
     * This will test http://cms.dev/api/v1/getalltopic api
     */
    public function testGetAll()
    {
        $response = $this->client->get('/api/v1/getalltopic');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertContains('Topic found successfully', $data['message']);
        $this->assertArrayHasKey('topic_id', $data['data'][0]);
        $this->assertArrayHasKey('topic_title', $data['data'][0]);

    }

    /**
     * this will test http://cms.dev/api/v1/topic/{id} api
     */
    public function testDelete()
    {
        // $id value should be check from application db whether topic id present
        $id = 89;
        $response = $this->client->delete('/api/v1/topic/' . $id, [
            'http_errors' => false
        ]);

        $this->assertEquals(204, $response->getStatusCode());

    }


    /**
     * create random text
     * @return string
     */
    private function randText()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 100; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
