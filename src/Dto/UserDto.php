<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Exception;

class UserDto extends AbstractDto {

    public string $email = '';

    public string $username = '';

    public string $password = '';

    public bool $isPublic = false;

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(RequestStack $requestStack, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        parent::__construct($requestStack);
    }

    protected function buildByRequest(): void {
        try{
            $object = $this->getRequestContent();
            $this->email = $object['email'];
            $this->username = $object['username'];
            $this->password = $object['password'];
            $this->isPublic = $object['isPublic'] === 'true';
        }
        catch (Exception $exception){

        }
    }

    public function buildEntity() : User {
        $user = new User();
        $user->setEmail($this->email)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $this->password));

        return $user;
    }
}