<?php


namespace App\Service;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Repository\PhoneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;

class ClientService extends Service
{
    /**
     * @var ExceptionService
     */
    private $exceptionService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    public function __construct(PhoneRepository $phoneRepository, ClientRepository $clientRepository, UserRepository $userRepository, PaginatorInterface $paginatorInterface, ExceptionService $exceptionService, EntityManagerInterface $entityManager)
    {
        parent::__construct($phoneRepository, $clientRepository, $userRepository, $paginatorInterface, $exceptionService, $entityManager);

        $this->exceptionService = $exceptionService;
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param $page
     * @param PaginatorInterface $paginator
     * @param ClientRepository $clientRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function getAllData($page, PaginatorInterface $paginator, ClientRepository $clientRepository, SerializerInterface $serializer)
    {
        $info = $paginator->paginate(
            $clientRepository->findAll(),
            $page,
            10
        );

        $data = $serializer->serialize($info, 'json',
            SerializationContext::create()->setGroups(array('Default', 'client')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param int $id
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function getData(int $id, SerializerInterface $serializer)
    {
        $info = $this->clientRepository->findBy(['id' => $id]);

        $data = $serializer->serialize($info, 'json',
            SerializationContext::create()->setGroups(array('Default', 'user')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param Client $client
     * @param ExceptionService $exceptionService
     * @param $violations
     * @param SerializerInterface $serializer
     * @return Response
     * @throws ResourceValidationException
     */
    public function addData(Client $client,
                            ExceptionService $exceptionService,
                            $violations,
                            SerializerInterface $serializer)
    {
        $exceptionService->invalidJson($violations);

        $client->getName();

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $data = $serializer->serialize($client, 'json',
            SerializationContext::create()->setGroups(array('Default', 'client')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 201, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @param Client $client
     * @param Client $newClient
     * @param ExceptionService $exceptionService
     * @param $violations
     * @param SerializerInterface $serializer
     * @return Response
     * @throws ResourceValidationException
     */
    public function updateData(Client $client, Client $newClient,
                               ExceptionService $exceptionService,
                               $violations,
                               SerializerInterface $serializer)
    {
        $exceptionService->invalidJson($violations);

        $client->setName($newClient->getName());

        $this->getDoctrine()->getManager()->flush();

        $data = $serializer->serialize($client, 'json',
            SerializationContext::create()->setGroups(array('Default', 'client')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @param Client $client
     * @return Client
     */
    public function deleteData(Client $client)
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return $client;
    }
}