<?php

namespace Erdemkeren\TemporaryAccess\Test;

use Carbon\Carbon;
use Erdemkeren\TemporaryAccess\GenericAccessToken;
use Erdemkeren\TemporaryAccess\Contracts\AccessToken;

class GenericAccessTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessToken
     */
    private $accessToken;
    /**
     * @var AccessToken
     */
    private $retrievedAccessToken;

    /**
     * @var array
     */
    private $accessTokenAttributes;

    public function setUp()
    {
        $this->accessTokenAttributes = $accessTokenAttributes = [
            'id'                 => 1,
            'authenticatable_id' => 1,
            'plain'              => 'foo',
            'token'              => 'bar',
            'created_at'         => '2016-12-28 19:35:27',
            'expires_at'         => '2016-12-28 19:50:27',
        ];

        $this->accessToken = new GenericAccessToken($accessTokenAttributes);

        $retrievedAccessTokenAttributes = $accessTokenAttributes;
        unset($retrievedAccessTokenAttributes['plain']);

        $this->retrievedAccessToken = new GenericAccessToken($retrievedAccessTokenAttributes);

        parent::setUp();
    }

    /** @test */
    public function it_shall_be_an_instance_of_access_token_contract()
    {
        $this->assertInstanceOf(AccessToken::class, $this->accessToken);
    }

    /** @test */
    public function it_shall_provide_the_unique_authenticatable_identifier()
    {
        $this->assertEquals($this->accessToken->authenticatableId(), $this->accessTokenAttributes['authenticatable_id']);
    }

    /** @test */
    public function it_shall_provide_the_plain_code()
    {
        $this->assertEquals($this->accessToken->plain(), $this->accessTokenAttributes['plain']);
    }

    /** @test */
    public function it_shall_provide_the_token_with_alias_code()
    {
        $this->assertEquals($this->accessToken->code(), $this->accessTokenAttributes['plain']);
    }

    /** @test */
    public function it_shall_throw_exception_when_no_plain_is_available()
    {
        $this->expectException(\LogicException::class);

        $this->retrievedAccessToken->plain();
    }

    /** @test */
    public function it_shall_provide_the_token()
    {
        $this->assertEquals($this->accessToken->token(), $this->accessTokenAttributes['token']);
    }

    /** @test */
    public function it_shall_provide_the_token_with_alias_encrypted()
    {
        $this->assertEquals($this->accessToken->encrypted(), $this->accessTokenAttributes['token']);
    }

    /** @test */
    public function it_shall_provide_the_date_access_token_created()
    {
        $this->assertInstanceOf(Carbon::class, $this->accessToken->createdAt());
        $this->assertTrue((new Carbon($this->accessTokenAttributes['created_at']))->eq($this->accessToken->createdAt()));
    }

    /** @test */
    public function it_shall_return_a_new_access_token_instance_when_prolong_called()
    {
        $accessToken = $this->accessToken->prolong(300);

        $this->assertNotSame($this->accessToken, $accessToken);
        $this->assertSame('2016-12-28 19:55:27', (string) $accessToken->expiresAt());
    }
}