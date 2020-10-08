<?php


namespace App\Controller;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Service\ClientService;
use App\Service\ExceptionService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class ClientController
 * @package App\Controller
 * @Route("/api")
 *
 * @Security(name="Bearer")
 * @OA\Tag(name="Client")
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
 *
 */
class ClientController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ClientRepository
     */
    private $repository;
    /**
     * @var ExceptionService
     */
    private $exceptionService;
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(EntityManagerInterface $entityManager,
                                ClientRepository $repository,
                                ExceptionService $exceptionService,
                                ClientService $clientService)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->exceptionService = $exceptionService;
        $this->clientService = $clientService;
    }

    /**
     * @Rest\Get(
     *     path = "/clients/{page<\d+>?1}",
     *     name = "app_clients_list",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @OA\Response(
     *      response="200",
     *      description="List Clients",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="name"),
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
     * @param $page
     * @param PaginatorInterface $paginator
     * @param ClientRepository $clientRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function listAction($page, PaginatorInterface $paginator, ClientRepository $clientRepository, SerializerInterface $serializer)
    {
        return $this->clientService->getAllData($page, $paginator, $clientRepository, $serializer);

    }

    /**
     * @Rest\Get(
     *     path = "/client/{id}",
     *     name = "app_client_show",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @OA\Response(
     *      response="200",
     *      description="Detail Clients with Users linked",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="name"),
     *          @OA\Property(type="array", property="users",
     *              @OA\Items(
     *                  @OA\Property(type="integer", property="id"),
     *                  @OA\Property(type="string", property="email"),
     *                      @OA\Property(type="object", property="_links",
     *                          @OA\Property(type="object", property="self",
     *                              @OA\Property(type="string", property="href"),
     *                      ),
     *                  ),
     *          ),
     *     ),
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
     * @param Client $client
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function showAction(int $id, Client $client, SerializerInterface $serializer)
    {
        return $this->clientService->getData($id, $serializer);
    }

    /**
     * @Rest\Post(
     *    path = "/client",
     *    name = "app_client_create"
     * )
     * @ParamConverter(
     *     "client",
     *     converter="fos_rest.request_body"
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @OA\Response(
     *      response="201",
     *      description="Add Client",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="name"),
     *                  @OA\Property(type="object", property="_links",
     *                      @OA\Property(type="object", property="self",
     *                          @OA\Property(type="string", property="href")
     *                      )
     *                  )
     *          )
     *      )
     * )
     * @OA\Response(
     *     response=415,
     *     description="Unsupported Media Type",
     *     @Model(type=Client::class)
     * )
     * @OA\RequestBody(
     *     request="Add new Client",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="name"),
     *     )
     * )
     *
     * @param Client $client
     * @param ExceptionService $exceptionService
     * @param $violations
     * @param SerializerInterface $serializer
     * @return Response
     * @throws ResourceValidationException
     */
    public function createAction(Client $client, ExceptionService $exceptionService, $violations, SerializerInterface $serializer)
    {
        return $this->clientService->addData($client, $exceptionService, $violations, $serializer);
    }

    /**
     * @Rest\Put(
     *     path = "/client/{id}",
     *     name = "app_client_update",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     * @ParamConverter(
     *     "newClient",
     *     converter="fos_rest.request_body"
     * )
     *
     * @OA\Response(
     *      response="200",
     *      description="Update Client",
     *      @OA\JsonContent(
     *          @OA\Property(type="integer", property="id"),
     *          @OA\Property(type="string", property="name"),
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
     * @OA\RequestBody(
     *     request="Update Client",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="name"),
     *     )
     * )
     *
     * @param Client $client
     * @param Client $newClient
     * @param ExceptionService $exceptionService
     * @param $violations
     * @param SerializerInterface $serializer
     * @return Response
     * @throws ResourceValidationException
     */
    public function updateAction(Client $client, Client $newClient,
                                 ExceptionService $exceptionService,
                                 $violations,
                                 SerializerInterface $serializer)
    {
        return $this->clientService->updateData($client, $newClient, $exceptionService, $violations, $serializer);
    }

    /**
     * @Rest\Delete(
     *     path = "/client/{id}",
     *     name = "app_client_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @OA\Response(
     *      response="204",
     *      description="Delete Client",
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
     * @param Client $client
     * @return View
     */
    public function deleteAction(Client $client)
    {
        $this->clientService->deleteData($client);

        return View::create($client, Response::HTTP_NO_CONTENT);
    }
}