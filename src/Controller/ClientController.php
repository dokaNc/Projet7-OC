<?php


namespace App\Controller;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Service\ClientService;
use App\Service\ExceptionService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ClientController
 * @package App\Controller
 * @Route("/api")
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
     * @param $page
     * @return View
     */
    public function listAction($page)
    {
        $data = $this->clientService->getAllData($page);

        return View::create($data, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/client/{id}",
     *     name = "app_client_show",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     * @param Client $client
     * @return View
     */
    public function showAction(Client $client)
    {
        return View::create($client, Response::HTTP_ACCEPTED);
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
     * @param Client $client
     * @param $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function createAction(Client $client, $violations)
    {
        $this->clientService->addData($violations, $client);

        return View::create($client, Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_client_show',
                ['id' => $client->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
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
     * @param Client $client
     * @param Client $newClient
     * @param $violations
     * @return mixed
     * @throws ResourceValidationException
     */
    public function updateAction(Client $client, Client $newClient, $violations)
    {
        $this->clientService->updateData($violations, $client, $newClient);

        return View::create($client, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/client/{id}",
     *     name = "app_client_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     * @param Client $client
     * @return View
     */
    public function deleteAction(Client $client)
    {
        $this->clientService->deleteData($client);

        return View::create($client, Response::HTTP_NO_CONTENT);
    }
}