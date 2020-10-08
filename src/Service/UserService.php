<?php


namespace App\Service;

use App\Entity\User;
use App\Exception\ResourceValidationException;
use App\Repository\ClientRepository;
use App\Repository\PhoneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserService extends Service
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
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var PaginatorInterface
     */
    private $paginatorInterface;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(PhoneRepository $phoneRepository,
                                ClientRepository $clientRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                UserRepository $userRepository,
                                PaginatorInterface $paginatorInterface,
                                ExceptionService $exceptionService,
                                EntityManagerInterface $entityManager,
                                Security $security,
                                SerializerInterface $serializer)
    {
        parent::__construct($phoneRepository, $clientRepository, $userRepository, $paginatorInterface, $exceptionService, $entityManager);

        $this->exceptionService = $exceptionService;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->security = $security;
        $this->paginatorInterface = $paginatorInterface;
        $this->serializer = $serializer;
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
            $this->userRepository->findAll(),
            $page,
            10
        );

        $data = $serializer->serialize($info, 'json',
            SerializationContext::create()->setGroups(array('Default', 'one')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param $page
     * @param PaginatorInterface $paginator
     * @param ClientRepository $clientRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function getAllDataClient($page, PaginatorInterface $paginator, ClientRepository $clientRepository, SerializerInterface $serializer)
    {
        $info = $paginator->paginate(
            $clientRepository->findBy(
                ['id' => $this->getUser()->getClients()]
            ),
            $page,
            10
        );

        $data = $serializer->serialize($info, 'json',
            SerializationContext::create()->setGroups(array('Default', 'user')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param User $user
     * @param int $id
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function getData(User $user, int $id, SerializerInterface $serializer)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($userClient = $this->getUser()->getClients() === $user->getClients()) {
                $info = $this->userRepository->findBy(['id' => $id]);

                $data = $serializer->serialize($info, 'json',
                    SerializationContext::create()->setGroups(array('Default', 'unique')));

                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');

                return new Response($data, 200, [
                    'Content-Type' => 'application/json'
                ]);
            } else {
                throw $this->createAccessDeniedException();
            }
        }

            if ($this->isGranted('ROLE_SUPERADMIN')) {
                $info = $this->userRepository->findBy(['id' => $id]);

                $data = $serializer->serialize($info, 'json',
                    SerializationContext::create()->setGroups(array('Default', 'unique')));

                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');

                return new Response($data, 200, [
                    'Content-Type' => 'application/json'
                ]);
            } else {
                throw $this->createAccessDeniedException();
            }
    }

    /**
     * @param User $user
     * @param Request $request
     * @param ExceptionService $exceptionService
     * @param $violations
     * @param ClientRepository $clientRepository
     * @param SerializerInterface $serializer
     * @return Response
     * @throws ResourceValidationException
     */
    public function addUser(User $user, Request $request,
                            ExceptionService $exceptionService,
                            $violations,
                            ClientRepository $clientRepository,
                            SerializerInterface $serializer)
    {
        $exceptionService->invalidJson($violations);

        $client_id = $this->getUser()->getClients();
        $values = json_decode($request->getContent());

        $user->getEmail();
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, (
            $user->getPassword()
            ))
        );

        if ($this->isGranted('ROLE_ADMIN')) {
            $user->setClients($this->getUser()->getClients());
        } elseif ($this->isGranted('ROLE_SUPERADMIN')) {
            $client = $clientRepository->findOneBy(['id' => isset($values->client_id) ? $values->client_id : $client_id]);
            $user->setClients($client);
        } else {
            throw $this->createAccessDeniedException();
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $data = $serializer->serialize($user, 'json',
            SerializationContext::create()->setGroups(array('Default', 'unique')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return new Response($data, 201, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param User $user
     * @return User
     */
    public function deleteData(User $user)
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser()->getClients() === $user->getClients()) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            } else {
                throw $this->createAccessDeniedException();
            }
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $user;



    }
}