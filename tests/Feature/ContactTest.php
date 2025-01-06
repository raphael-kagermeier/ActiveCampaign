<?php

use GuzzleHttp\Psr7\Response;
use Mockery;
use PerformRomance\ActiveCampaign\ActiveCampaign;
use PerformRomance\ActiveCampaign\DataTransferObjects\ContactDto;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;
use PerformRomance\ActiveCampaign\Exceptions\ValidationException;
use PerformRomance\ActiveCampaign\Support\Request;

beforeEach(function () {
    config([
        'activecampaign.url' => 'https://test.api.activecampaign.com',
        'activecampaign.key' => 'fake-key-123',
        'activecampaign.version' => '3',
    ]);
});

function createMockedActiveCampaign(Response $response): ActiveCampaign
{
    $mock = Mockery::mock(Request::class);

    $mock->shouldReceive('getEndpoint')
        ->with('contact/sync')
        ->andReturn('contact/sync');

    $mock->shouldReceive('make')
        ->once()
        ->andReturn(json_decode(json_encode([
            'contact' => json_decode($response->getBody()->getContents())->contact,
        ])));

    app()->instance(Request::class, $mock);

    return app(ActiveCampaign::class);
}

it('can sync a contact', function () {
    $response = [
        'contact' => [
            'id' => 123,
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890',
            'organization' => 'Test Corp',
            'country' => 'US',
            'addressStreet' => '123 Test St',
            'addressZip' => '12345',
            'addressCity' => 'Test City',
            'addressState' => 'TS',
            'hash' => 'abc123',
            'cdate' => '2023-01-01',
            'udate' => '2023-01-02',
            'deleted_at' => null,
            'is_duplicate' => false,
            'links' => [],
            'orgid' => 456,
            'fieldValues' => [],
        ],
    ];

    $activeCampaign = createMockedActiveCampaign(
        new Response(200, ['Content-Type' => 'application/json'], json_encode($response))
    );

    $contact = $activeCampaign->contact()
        ->setContactData([
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890',
        ])
        ->sync();

    expect($contact->getContactData())
        ->toBeInstanceOf(ContactDto::class)
        ->email->toBe('test@example.com')
        ->first_name->toBe('John')
        ->last_name->toBe('Doe')
        ->phone->toBe('1234567890')
        ->id->toBe(123);
});

it('validates contact data', function () {
    $mock = Mockery::mock(Request::class);
    $mock->shouldNotReceive('make');
    app()->instance(Request::class, $mock);

    app(ActiveCampaign::class)
        ->contact()
        ->setContactData([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890',
        ])
        ->sync();
})->throws(ValidationException::class, 'The email field is required');

it('throws exception on API error', function () {
    $mock = Mockery::mock(Request::class);
    $mock->shouldReceive('getEndpoint')
        ->with('contact/sync')
        ->andReturn('contact/sync');
    $mock->shouldReceive('make')
        ->once()
        ->andThrow(new ActiveCampaignException('Invalid API key'));

    app()->instance(Request::class, $mock);

    app(ActiveCampaign::class)
        ->contact()
        ->setContactData([
            'email' => 'test@example.com',
        ])
        ->sync();
})->throws(ActiveCampaignException::class);
