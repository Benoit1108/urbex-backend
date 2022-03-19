<?php

namespace App\Controller;

use App\Dto\AuthDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\JwtUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function index(UserRepository $userRepository, ?AuthDto $authDto): Response
    {
        $status = Response::HTTP_NOT_FOUND;
        $data = ['message' => 'Invalid credentials'];

        if (!empty($authDto) && $authDto->isBuild()) {
            $user = $userRepository->findOneBy(['email' => $authDto->email]);
            if ($user != null && password_verify($authDto->password, $user->getPassword()) === true) {
                $status = Response::HTTP_OK;
                $data = [
                    'message' => "Authentication Successfully",
                    'token' => JwtUtils::generateAccessToken($user)
                ];
            }
        }
        return new Response(json_encode($data), $status, ['content-type' => 'application/json']);
    }
}
