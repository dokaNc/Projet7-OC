<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\UserRepository;
use App\Service\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class SecurityController
 * @package App\Controller
 * @Route("/api")
 */

class SecurityController extends AbstractController
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
     *
     * @OA\Response(
     *      response="201",
     *      description="Register Account",
     *      @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *                ref=@Model(type=User::class))
     *      )
     * )
     * @OA\Response(
     *      response="500",
     *      description="Invalid Email",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="400"),
     *           @OA\Property(property="messsage", type="string", example="The JSON sent contains invalid data. Field 'email': This value is not a valid email address.")
     *      )
     * )
     * @OA\RequestBody(
     *     request="Register Account",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="email"),
     *          @OA\Property(type="string", property="password"),
     *     )
     * )
     * @OA\Tag(name="Security")
     *
     * @param User $user
     * @param $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function registerAction(User $user, $violations)
    {
        $this->registerService->addData($violations, $user);

        return View::create($user, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Post(
     *    path = "/login_check",
     *    name = "app_login"
     * )
     *
     * @OA\Response(
     *      response="200",
     *      description="Login Account",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="token"),
     *     )
     * )
     * @OA\Response(
     *      response="401",
     *      description="Invalid Credentials",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="401 Unauthorized"),
     *           @OA\Property(property="messsage", type="string", example="Bad credentials, please verify that your email / password are correctly set")
     *      )
     * )
     * @OA\RequestBody(
     *     request="Register Account",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="email"),
     *          @OA\Property(type="string", property="password"),
     *     )
     * )
     * @OA\Tag(name="Security")
     *
     */
    public function login()
    {
    }
}
