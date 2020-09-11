<?php


namespace App\Service;


use App\Entity\Phone;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PhoneService extends Service
{
    /**
     * @var string
     */
    private $repositoryName = 'phoneRepository';

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
     * @param Phone $phone
     * @param $violations
     * @throws ResourceValidationException
     */
    public function addData(Phone $phone, $violations)
    {
        $this->exceptionService->invalidJson($violations);

        $phone->setBrand($phone->getBrand());
        $phone->setColor($phone->getColor());
        $phone->setDescription($phone->getDescription());
        $phone->setModel($phone->getModel());
        $phone->setPrice($phone->getPrice());

        $this->entityManager->persist($phone);
        $this->entityManager->flush();
    }

    /**
     * @param $violations
     * @param Phone $phone
     * @param Phone $newPhone
     * @throws ResourceValidationException
     */
    public function updateData($violations, Phone $phone, Phone $newPhone)
    {
        $this->exceptionService->invalidJson($violations);

        $phone->setBrand($newPhone->getBrand());
        $phone->setModel($newPhone->getModel());
        $phone->setColor($newPhone->getColor());
        $phone->setDescription($newPhone->getDescription());
        $phone->setPrice($newPhone->getPrice());

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param Phone $phone
     * @return Phone
     */
    public function deleteData(Phone $phone)
    {
        $this->entityManager->remove($phone);
        $this->entityManager->flush();

        return $phone;
    }
}