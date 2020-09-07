<?php


namespace App\Controller;

use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\UserRepository;
use App\Service\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ClientController
 * @package App\Controller
 * @Route("/api")
 */

class UserController extends AbstractController
{
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(EntityManagerInterface $entityManager,
                                UserRepository $repository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
        $this->userService = $userService;
    }

    /**
     * @Rest\Get(
     *     path = "/user/{id}",
     *     name = "app_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @param User $user
     * @return View
     */
    public function showAction(User $user)
    {
        return View::create($user, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Post(
     *    path = "/admin/add/user",
     *    name = "app_admin_add_user"
     * )
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param User $user
     * @param $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function addAction(User $user, $violations)
    {
        $this->userService->addUser($violations, $user);

        return View::create($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/user/{id}",
     *     name = "app_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @param User $user
     * @return View
     */
    public function deleteAction(User $user)
    {
        $this->userService->deleteData($user);

        return View::create($user, Response::HTTP_NO_CONTENT);
    }
}