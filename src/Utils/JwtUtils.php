<?php

namespace App\Utils;

use App\Entity\User;
use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Checker\ExpirationTimeChecker;
use Jose\Component\Checker\IssuedAtChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @author Axel Brionne
 */
class JwtUtils
{
    private const PROJECT_DIRECTORY = '..' . DIRECTORY_SEPARATOR;
    private const COOKIE_NAME = 'refreshToken';

    /**
     * This function generate an access token
     * @param User $user
     * @return string
     */
    public static function generateAccessToken(User $user): string
    {
        return self::generateJwt($user, time() + $_ENV['JWT_ACCESS_TOKEN_EXPIRE']);
    }

    /**
     * This function generate an refresh cookie
     * @param User $user
     * @return string
     */
    public static function generateRefreshCookie(User $user): Cookie
    {
        $expireTime = time() + $_ENV['JWT_REFRESH_TOKEN_EXPIRE'];
        return new Cookie(
            self::COOKIE_NAME,
            self::generateJwt($user, $expireTime),
            $expireTime
        );
    }

    /**
     * This function verify the validity of Jwt token in parameter
     * @param string $token
     * @return bool
     */
    public static function verify(string $token): bool
    {
        $algorithmManager = new AlgorithmManager([
            new RS256(),
        ]);

        $jwsVerifier = new JWSVerifier(
            $algorithmManager
        );

        $jwk = JWKFactory::createFromKeyFile(
            self::PROJECT_DIRECTORY . $_ENV['JWT_PUBLIC_KEY'],
            $_ENV['JWT_PASSPHRASE']
        );
        $serializerManager = new JWSSerializerManager([new CompactSerializer()]);

        $claimCheckerManager = new ClaimCheckerManager(
            [
                new IssuedAtChecker(),
                new ExpirationTimeChecker(),
            ]
        );

        $jws = $serializerManager->unserialize($token);
        $claims = json_decode($jws->getPayload(), true);

        return $jwsVerifier->verifyWithKey($jws, $jwk, 0) && $claimCheckerManager->check($claims);
    }

    /**
     * This function decode a Jwt Token and serialize to User entity
     * @param string $token
     * @return User
     */
    public static function decode(string $token): User
    {
        $serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);
        $jws = $serializerManager->unserialize($token);
        $user = new User();
        $user->setEmail(json_decode($jws->getPayload())->email);
        return $user;
    }

    /**
     * This function generate a Jwt Token
     * @param User $user
     * @param int $expire
     * 
     * @return string
     */
    private static function generateJwt(User $user, int $expire): string
    {
        $algorithmManager = new AlgorithmManager([
            new RS256(),
        ]);
        $jwk = JWKFactory::createFromKeyFile(
            self::PROJECT_DIRECTORY . $_ENV['JWT_SECRET_KEY'],
            $_ENV['JWT_PASSPHRASE'],
            [
                'use' => 'sig',
            ]
        );
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $payload = json_encode([
            'iat' => time(),
            'exp' => $expire,
            'iss' => 'Urbex API',
            'aud' => 'Urbex',
            'email' => $user->getUserIdentifier(),
        ]);
        $jws = $jwsBuilder
            ->create()                               // We want to create a new JWS
            ->withPayload($payload)                  // We set the payload
            ->addSignature($jwk, ['alg' => 'RS256']) // We add a signature with a simple protected header
            ->build();
        $serializer = new CompactSerializer(); // The serializer

        return $serializer->serialize($jws, 0);
    }
}
