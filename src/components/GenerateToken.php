<?php

declare(strict_types=1);

namespace Api\Components;

use DateTimeImmutable;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\Micro;

/**
 * GenerateToken class
 * Generate the new token for differen user
 */
final class GenerateToken implements MiddlewareInterface
{
    public function authorizeApiToken($app): void
    {
        $now = new DateTimeImmutable();
        $key = 'example_key';
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        $payload = [
            'name' => 'abhishek',
            'iss' => 'https://phalcon.io',
            'exp ' => $now->modify('+1 day')->getTimestamp(),
            'aud' => 'https://target.phalcon.io',
            'iat' => $now->getTimestamp(),
            'nbf' => $now->modify('-1 minute')->getTimestamp(),
            'password' => $passphrase
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        $app->response->setStatusCode(400)
            ->setJsonContent($token)
            ->send();
    }

    /**
     * validate function
     *
     * Validate the token provided by the user
     *
     * @param string $token
     */
    public function validate(string $token, $app): void
    {
        try {
            JWT::decode($token, new Key('example_key', 'HS256'));
        } catch (Exception $err) {
            $app->response->setStatusCode(404)
                ->setJsonContent('Token has expired!')
                ->send();
        }
    }

    public function call(Micro $app): void
    {
        $check = explode('/', $app->request->get()['_url'])[2];
        if ($check === 'generateApiToken') {
            $this->authorizeApiToken($app);
        } else {
            $token = $app->request->get('token');
            $this->validate($token, $app);
        }
    }
}
