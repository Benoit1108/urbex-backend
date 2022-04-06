<?php

namespace App\Controller;


use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register( UserDto $user, EntityManagerInterface $entityManager): Response
    {
        $newUser = null;
        if (!empty($user) && $user->isBuild()) {
            $newUser = $user->buildEntity();
            try{
                $entityManager->persist($newUser);
                $entityManager->flush();
            } catch (OptimisticLockException|ORMException $exception) {

            }
        }
        return new Response(json_encode($newUser), '201', ['content-type' => 'application/json']);
    }

    /**
     * @Route("/api/users", name="api_users", methods={"GET"})
     */
    public function getAllUser(UserRepository $userRepository) : Response
    {
        return new Response(json_encode($userRepository->findAll()), '200', ['content-type' => 'application/json']);
    }
}