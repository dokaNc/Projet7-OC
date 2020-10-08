<?php


namespace App\Controller;

use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Service\ExceptionService;
use App\Service\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security as OAS;

/**
 * Class ClientController
 * @package App\Controller
 * @Route("/api")
 *
 * @OAS(name="Bearer")
 * @OA\Tag(name="User")
 * @OA\Response(
 *      response="401",
 *      description="JWT Token",
 *      @OA\JsonContent(
 *           @OA\Property(property="code", type="integer", example="401"),
 *           @OA\Property(property="messsage", type="string", example="JWT Token not found / Invalid JWT Token")
 *      )
 * )
 * @OA\Response(
 *      response="403",
 *      description="Access",
 *      @OA\JsonContent(
 *           @OA\Property(property="status", type="integer", example="403"),
 *           @OA\Property(property="messsage", type="string", example="Access denied")
 *      )
 * )
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
     *
     * @OA\Response(
     *      response="200",
     *      description="List Users",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="email"),
     *                  @OA\Property(type="object", property="_links",
     *                      @OA\Property(type="object", property="self",
     *                          @OA\Property(type="string", property="href")
     *                      )
     *                  )
     *          )
     *      )
     * )
     * @OA\Response(
     *      response="404",
     *      description="Not Found",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="404"),
     *           @OA\Property(property="messsage", type="string", example="Ressource not found")
     *      )
     * )
     *
     * @param $page
     * @param PaginatorInterface $paginator
     * @param ClientRepository $clientRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function listAction($page, PaginatorInterface $paginator, ClientRepository $clientRepository, SerializerInterface $serializer)
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->userService->getAllDataClient($page, $paginator, $clientRepository, $serializer);
        } elseif ($this->isGranted('ROLE_SUPERADMIN')) {
            return $this->userService->getAllData($page, $paginator, $clientRepository, $serializer);
        }
    }

    /**
     * @Rest\Get(
     *     path = "/user/{id}",
     *     name = "app_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     *
     * @OA\Response(
     *      response="200",
     *      description="Details User",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="email"),
     *          @OA\Property(type="object", property="_clients",
     *                  @OA\Property(type="string", property="name"),
     *                      @OA\Property(type="object", property="_links",
     *                          @OA\Property(type="object", property="self",
     *                              @OA\Property(type="string", property="href"),
     *                      ),
     *                  ),
     *          ),
     *          @OA\Property(type="object", property="_links",
     *              @OA\Property(type="object", property="self",
     *                  @OA\Property(type="string", property="href")
     *              )
     *          )
     *      )
     * )
     * @OA\Response(
     *      response="404",
     *      description="Not Found",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="404"),
     *           @OA\Property(property="messsage", type="string", example="Ressource not found")
     *      )
     * )
     *
     * @param int $id
     * @param User $user
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function showAction(int $id, User $user, SerializerInterface $serializer)
    {
        return $this->userService->getData($user, $id, $serializer);
    }

    /**
     * @Rest\Post(
     *    path = "/user",
     *    name = "app_user_create"
     * )
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     *
     * @OA\Response(
     *      response="201",
     *      description="Add User",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="email"),
     *          @OA\Property(type="object", property="_clients",
     *                  @OA\Property(type="string", property="name"),
     *                      @OA\Property(type="object", property="_links",
     *                          @OA\Property(type="object", property="self",
     *                              @OA\Property(type="string", property="href"),
     *                      ),
     *                  ),
     *          ),
     *          @OA\Property(type="object", property="_links",
     *              @OA\Property(type="object", property="self",
     *                  @OA\Property(type="string", property="href")
     *              )
     *          )
     *      )
     * )
     * @OA\RequestBody(
     *     request="Add User",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="email"),
     *          @OA\Property(type="string", property="password"),
     *          @OA\Property(type="integer", property="client_id"),
     *     )
     * )
     *
     * @param User $user
     * @param Request $request
     * @param ExceptionService $exceptionService
     * @param $violations
     * @param ClientRepository $clientRepository
     * @param SerializerInterface $serializer
     * @return Response
     * @throws ResourceValidationException
     */
    public function addAction(User $user, Request $request, ExceptionService $exceptionService, $violations, ClientRepository $clientRepository, SerializerInterface $serializer)
    {
        return $this->userService->addUser($user, $request, $exceptionService, $violations, $clientRepository, $serializer);
    }

    /**
     * @Rest\Delete(
     *     path = "/user/{id}",
     *     name = "app_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPERADMIN')")
     *
     * @OA\Response(
     *      response="204",
     *      description="Delete User",
     * )
     * @OA\Response(
     *      response="404",
     *      description="Not Found",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="404"),
     *           @OA\Property(property="messsage", type="string", example="Ressource not found")
     *      )
     * )
     *
     * @param User $user
     * @return View
     */
    public function deleteAction(User $user)
    {
        $this->userService->deleteData($user);

        return View::create($user, Response::HTTP_NO_CONTENT);
    }
}