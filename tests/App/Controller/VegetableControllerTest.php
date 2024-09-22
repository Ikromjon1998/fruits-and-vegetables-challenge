<?php

namespace App\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VegetableControllerTest extends WebTestCase
{
    private $client;
    private string $baseUrl = '/api/vegetables/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->baseUrl);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testStore()
    {
        $data = [
            'name' => 'Carrot',
            'weight' => 45,
        ];

        $this->client->request('POST', $this->baseUrl, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testStoreValidationError()
    {
        $data = [
            'name' => '',
            'weight' => -2,
        ];

        $this->client->request('POST', $this->baseUrl, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testDestroy()
    {
        // first create a fruit
        $data = [
            'name' => 'Apple',
            'weight' => 45,
        ];

        $this->client->request('POST', $this->baseUrl, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Step 3: Retrieve the response data to get the ID
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Assuming that the response contains the ID of the created fruit
        // Adjust this part based on your actual response structure
        $createdFruitId = $responseData['id']; // Adjust based on your actual response structure

        $this->client->request('DELETE', $this->baseUrl.$createdFruitId);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testDestroyInvalidId()
    {
        $this->client->request('DELETE', $this->baseUrl.'-1');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
