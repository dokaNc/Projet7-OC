<?php


namespace App\Controller;

use App\Entity\User;
use App\EventSubscriber\ExceptionSubscriber;
use App\Exception\ResourceValidationException;
use App\Repository\UserRepository;
use App\Service\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
     *     path = "/users/{page<\d+>?1}",
     *     name = "app_users_list",
     *     requirements = {"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     * @param $page
     * @return View
     */
    public function listAction($page)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $data = $this->userService->getAllDataClient($page);
        } elseif ($this->isGranted('ROLE_SUPERADMIN')) {
            $data = $this->userService->getAllData($page);
        }

        return View::create($data, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/user/{id}",
     *     name = "app_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     * @param int $id
     * @param User $user
     * @return View
     */
    public function showAction(int $id, User $user)
    {
        $this->userService->getData($user, $id);

        return View::create($user, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Post(
     *    path = "/user",
     *    name = "app_add_user"
     * )
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     * @param User $user
     * @param $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function addAction(User $user, $violations)
    {
        $this->userService->addUser($user, $violations);

        return View::create($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/user/{id}",
     *     name = "app_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     * @param User $user
     * @return View
     */
    public function deleteAction(User $user)
    {
        $this->userService->deleteData($user);

        return View::create($user, Response::HTTP_NO_CONTENT);
    }
}