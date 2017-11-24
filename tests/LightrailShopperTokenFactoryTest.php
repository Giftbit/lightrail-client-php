<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailShopperTokenFactoryTest extends TestCase
{

    public function testSignsShopperId()
    {
        Lightrail::$apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJnIjp7Imd1aSI6Imdvb2V5IiwiZ21pIjoiZ2VybWllIn19.XxOjDsluAw5_hdf5scrLk0UBn8VlhT-3zf5ZeIkEld8";
        Lightrail::$sharedSecret = "secret";

        $shopperToken = LightrailShopperTokenFactory::generate(array("shopperId" => "zhopherId"), 600);
        $shopperPayload = \Firebase\JWT\JWT::decode($shopperToken, Lightrail::$sharedSecret, array('HS256'));

        $this->assertEquals("zhopherId", $shopperPayload->g->shi, "g.shi");
        $this->assertEquals("gooey", $shopperPayload->g->gui, "g.gui");
        $this->assertEquals("germie", $shopperPayload->g->gmi, "g.gmi");
        $this->assertEquals("MERCHANT", $shopperPayload->iss, "iss");
        $this->assertGreaterThan(0, $shopperPayload->iat, "iat is a number > 0");
        $this->assertEquals($shopperPayload->iat + 600, $shopperPayload->exp, "exp = iat + 600");
    }

    public function testSignsContactUserSuppliedId()
    {
        Lightrail::$apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJnIjp7Imd1aSI6Imdvb2V5IiwiZ21pIjoiZ2VybWllIn19.XxOjDsluAw5_hdf5scrLk0UBn8VlhT-3zf5ZeIkEld8";
        Lightrail::$sharedSecret = "secret";

        $shopperToken = LightrailShopperTokenFactory::generate(array("userSuppliedId" => "luserSuppliedId"), 600);
        $shopperPayload = \Firebase\JWT\JWT::decode($shopperToken, Lightrail::$sharedSecret, array('HS256'));

        $this->assertEquals("luserSuppliedId", $shopperPayload->g->cui, "g.cui");
        $this->assertEquals("gooey", $shopperPayload->g->gui, "g.gui");
        $this->assertEquals("germie", $shopperPayload->g->gmi, "g.gmi");
        $this->assertEquals("MERCHANT", $shopperPayload->iss, "iss");
        $this->assertGreaterThan(0, $shopperPayload->iat, "iat is a number > 0");
        $this->assertEquals($shopperPayload->iat + 600, $shopperPayload->exp, "exp = iat + 600");
    }

    public function testSignsContactId()
    {
        Lightrail::$apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJnIjp7Imd1aSI6Imdvb2V5IiwiZ21pIjoiZ2VybWllIn19.XxOjDsluAw5_hdf5scrLk0UBn8VlhT-3zf5ZeIkEld8";
        Lightrail::$sharedSecret = "secret";

        $shopperToken = LightrailShopperTokenFactory::generate(array("contactId" => "chauntaktEyeDee"), 600);
        $shopperPayload = \Firebase\JWT\JWT::decode($shopperToken, Lightrail::$sharedSecret, array('HS256'));

        $this->assertEquals("chauntaktEyeDee", $shopperPayload->g->coi, "g.coi");
        $this->assertEquals("gooey", $shopperPayload->g->gui, "g.gui");
        $this->assertEquals("germie", $shopperPayload->g->gmi, "g.gmi");
        $this->assertEquals("MERCHANT", $shopperPayload->iss, "iss");
        $this->assertGreaterThan(0, $shopperPayload->iat, "iat is a number > 0");
        $this->assertEquals($shopperPayload->iat + 600, $shopperPayload->exp, "exp = iat + 600");
    }

}
