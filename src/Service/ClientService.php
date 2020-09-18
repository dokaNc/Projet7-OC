<?php


namespace App\Service;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ClientService extends Service
{
    /**
     * @var string
     */
    private $repositoryName = 'clientRepository';

    /**
     * @var ExceptionService
     */
    private $exceptionService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(PhoneRepository $phoneRepository, ClientRepository $clientRepository, PaginatorInterface $paginatorInterface, ExceptionService $exceptionService, EntityManagerInterface $entityManager)
    {
        parent::__construct($phoneRepository, $clientRepository, $paginatorInterface, $exceptionService, $entityManager);

        $this->exceptionService = $exceptionService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $page
     * @return PaginationInterface
     */
    public function getAllData($page)
    {
        return $this->getAll($this->repositoryName, $page);
    }

    /**
     * @param $page
     * @return PaginationInterface
     */
    public function getAllDataByClients($page)
    {
        return $this->getAllByClients($this->repositoryName, $page);
    }

    /**
     * @param $violations
     * @param Client $client
     * @return void
     * @throws ResourceValidationException
     */
    public function addData($violations, Client $client)
    {
        $this->exceptionService->invalidJson($violations);

        $client->setName($client->getName());

        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    /**
     * @param $violations
     * @param Client $client
     * @param Client $newClient
     * @return void
     * @throws ResourceValidationException
     */
    public function updateData($violations, Client $client, Client $newClient)
    {
        $this->exceptionService->invalidJson($violations);

        $client->setName($newClient->getName());

        $this->getDoctrine()->getManager()->flush();
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