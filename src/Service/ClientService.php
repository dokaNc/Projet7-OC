<?php


namespace App\Service;


use App\Entity\Client;
use App\Exception\ResourceValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Service\ExceptionService;

class ClientService extends Service
{
    /**
     * @var string
     */
    private $repositoryName = 'clientRepository';

    /**
     * @var \App\Service\ExceptionService
     */
    private $exceptionService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ExceptionService $exceptionService,
                                EntityManagerInterface $entityManager)
    {
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
     * @param $data
     * @param $violations
     * @throws ResourceValidationException
     */
    public function addData($data, $violations)
    {
        return $this->add($data, $violations);
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
        return $this->update();
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