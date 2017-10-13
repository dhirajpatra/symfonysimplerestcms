<?php

use PHPUnit\Framework\TestCase;

class ArticleControllerTest extends TestCase
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
     * This will test http://cms.dev/api/v1/article/new api
     */
    public function testNew()
    {

        $title = $this->randText(100);
        $author = $this->randText(10);
        $body = $this->randText(200);
        // this value should be checked from application db whether topic id present
        $topicId = 70;

        $response = $this->client->post('/api/v1/article/new', [
            'json' => [
                'title' => $title,
                'author' => $author,
                'body' => $body,
                'topicid' => $topicId
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    /**
     * this will test http://cms.dev/api/v1/article/{id} api
     * article id should be present in db
     */
    public function testGet()
    {
        // $id value should be check from application db whether article id present
        $id = 10;
        $response = $this->client->get('/api/v1/article/' . $id);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode(json_decode($response->getBody(), true), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertContains('Article found successfully', $data['message']);
        $this->assertArrayHasKey('topic_id', $data['data']);
        $this->assertArrayHasKey('topic_title', $data['data']);
        $this->assertArrayHasKey('article_id', $data['data']);
        $this->assertArrayHasKey('article_title', $data['data']);
        $this->assertArrayHasKey('author_name', $data['data']);

    }

    /**
     * This will test http://cms.dev/api/v1/getallarticle api
     */
    public function testGetAll()
    {
        $response = $this->client->get('/api/v1/getallarticle');

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode(json_decode($response->getBody(), true), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertContains('Article found successfully', $data['message']);
        $this->assertArrayHasKey('article_id', $data['data'][0]);
        $this->assertArrayHasKey('article_title', $data['data'][0]);

    }

    /**
     * this will test http://cms.dev/api/v1/article/{id} api
     */
    public function testDelete()
    {
        // $id value should be check from application db whether article id present
        $id = 10;
        $response = $this->client->delete('/api/v1/article/' . $id, [
            'http_errors' => false
        ]);

        $this->assertEquals(204, $response->getStatusCode());

    }


    /**
     * create random text
     * @return string
     */
    private function randText($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
