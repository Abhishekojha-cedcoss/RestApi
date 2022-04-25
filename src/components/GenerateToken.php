<?php

namespace Api\Components;

use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\Micro;
use DateTimeImmutable;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

/**
 * GenerateToken class
 * Generate the new token for differen user
 */
class GenerateToken implements MiddlewareInterface
{
    public function authorizeApiToken($app)
    {
        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $key = "example_key";

        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        $payload = array(
            "name" => "abhishek",
            "iss" => "'https://phalcon.io'",
            "exp " => $expires,
            "aud" => "https://target.phalcon.io",
            "iat" => $issued,
            "nbf" => $notBefore,
            "password" => $passphrase
        );

        $token = JWT::encode($payload, $key, 'HS256');
        $app->response->setStatusCode(400)
            ->setJsonContent($token)
            ->send();
    }

    /**
     * validate function
     *
     * Validate the token provided by the user
     * @param [type] $token
     * @param [type] $app
     * @return void
     */
    public function validate($token, $app)
    {
        $key = "example_key";
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
    }
    public function call(Micro $app)
    {
        $check = explode('/', $app->request->get()['_url'])[1];
        if ($check == "/api/generateApiToken") {
            $this->authorizeApiToken($app);
        } else {
            $token =  $app->request->get("token");
            $this->validate($token, $app);
        }
    }
}
