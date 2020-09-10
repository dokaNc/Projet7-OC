<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\UserRepository;
use App\Service\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use http\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 * @package App\Controller
 * @Route("/api")
 */

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
    /**
     * @var RegisterService
     */
    private $registerService;

    public function __construct(UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $entityManager,
                                RegisterService $registerService)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->registerService = $registerService;
    }

    /**
     * @Rest\Post(
     *    path = "/register",
     *    name = "app_register_create"
     * )
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @param User $user
     * @param $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function registerAction(User $user, $violations)
    {
        $this->registerService->addData($violations, $user);

        return View::create($user, Response::HTTP_OK);
    }
}
