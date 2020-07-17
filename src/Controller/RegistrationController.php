<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Rest\Post(
     *    path = "/register",
     *    name = "app_register_create"
     * )
     * @ParamConverter(
     *     "newUser",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Register" }
     *     }
     * )
     * @param User $newUser
     * @param Request $request
     * @return View
     */
    public function registerAction(User $newUser, Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!is_null($user)) {
            return View::create([
                'message' => 'User already exist'
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();

        $user->setEmail($newUser->getEmail());
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return View::create($user, Response::HTTP_CREATED);
    }
}