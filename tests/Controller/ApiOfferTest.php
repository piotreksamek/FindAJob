<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Fixtures\Builder\OfferBuilder;
use App\Tests\Fixtures\Builder\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiOfferTest extends WebTestCase
{
    use ResetDatabase;

    private $client;

    private $offerBuilder;

    private $userBuilder;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->offerBuilder = static::getContainer()->get(OfferBuilder::class);
        $this->userBuilder = static::getContainer()->get(UserBuilder::class);
    }

    public function testShowOffers(): void
    {
        $this->offerBuilder->createOffer();

        $this->client->request('GET', '/api/offers');;

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame($response[0]['name'], 'Senior Developer');
    }

    public function testCanAddPostWithJwtToken(): void
    {
        $this->requestApiAddOffers('asdasd', 'qweqwe');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('"Success"', $this->client->getResponse()->getContent());
    }

    public function testCannotAddPostWithJwtTokenWithoutNameOffer(): void
    {
        $this->requestApiAddOffers('', '');

        $this->assertSame(500, $this->client->getResponse()->getStatusCode());
    }

    public function testCannotAddPostWithoutJwtToken(): void
    {
        $this->requestApiAddOffers('asdqwe', 'qwe', false);

        $this->assertSame(401, $this->client->getResponse()->getStatusCode());
        $this->assertSame('{"code":401,"message":"JWT Token not found"}', $this->client->getResponse()->getContent());
    }

    public function testCanEditOfferWithJwtToken(): void
    {
        $this->requestDeleteOrEditApiOffers('PUT', 'asdasdwqeqweqzxc213123');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('"Success"', $this->client->getResponse()->getContent());
    }

    public function testCannotEditOfferWithoutJwtToken(): void
    {
        $this->requestDeleteOrEditApiOffers('PUT', 'asdasdwqeqweqzxc213123', false);

        $this->assertSame(401, $this->client->getResponse()->getStatusCode());
        $this->assertSame('{"code":401,"message":"JWT Token not found"}', $this->client->getResponse()->getContent());
    }

    public function testDeleteOfferWithJwtToken(): void
    {
        $this->requestDeleteOrEditApiOffers('DELETE');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('"Delete Offer"', $this->client->getResponse()->getContent());
    }

    public function testDeleteOfferWithoutJwtToken(): void
    {
        $this->requestDeleteOrEditApiOffers('DELETE', server: false);

        $this->assertSame(401, $this->client->getResponse()->getStatusCode());
        $this->assertSame('{"code":401,"message":"JWT Token not found"}', $this->client->getResponse()->getContent());
    }

    private function requestDeleteOrEditApiOffers(string $method, string $description = null, bool $server = true): void
    {
        $params = [
            'description' => $description,
            'city' => 'city',
            'price' => '123',
        ];

        $this->offerBuilder->createOffer();

        $token = $this->getToken();

        $content = json_encode($params);

        $headers = [];

        if($server){
            $headers = [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ];
        }

        $this->client->request(
            $method,
            '/api/offers/1',
            server: $headers,
            content: $content
        );
    }

    private function requestApiAddOffers($name, $description, bool $server = true): void
    {
        $this->userBuilder->createCompanyOwner();

        $token = $this->getToken();

        $params = [
            'name' => $name,
            'description' => $description,
            'city' => 'city',
            'price' => 'price',
        ];

        $headers = [];

        if ($server) {
            $headers = [
                'HTTP_Authorization' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ];
        }

        $this->client->request(
            'POST',
            '/api/offers',
            server: $headers,
            content: json_encode($params),
        );
    }

    private function getToken(): string
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'company.owner@gmail.com',
                'password' => 'companyowner',
            ])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true);
        return $token['token'];
    }
}
