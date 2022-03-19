<?php

namespace App\Dto;

class AuthDto extends AbstractDto {

    public string $email = '';

    public string $password = '';

    protected function buildByRequest(): void {
        $object = $this->getRequestContent();
        $this->email = $object['email'];
        $this->password = $object['password'];
    }
}