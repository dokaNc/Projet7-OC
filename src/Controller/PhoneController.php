<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;


class PhoneController extends AbstractController
{
    private $entityManager;

    private $repository;

    public function __construct(EntityManagerInterface $entityManager, PhoneRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Rest\Get(
     *     path = "/phones",
     *     name = "app_phone_list",
     *     requirements = {"id"="\d+"}
     * )
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return View
     */
    public function listAction(PaginatorInterface $paginator, Request $request)
    {
        $phone = $paginator->paginate(
            $this->repository->findAllVisibleQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return View::create($phone, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/phones/{id}",
     *     name = "app_phone_show",
     *     requirements = {"id"="\d+"}
     * )
     * @param Phone $phone
     * @return View
     */
    public function showAction(Phone $phone)
    {
        return View::create($phone, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Post(
     *    path = "/phones",
     *    name = "app_phone_create"
     * )
     * @ParamConverter("phone", converter="fos_rest.request_body")
     * @param Phone $phone
     * @return View
     */
    public function createAction(Phone $phone)
    {

        $this->entityManager->persist($phone);
        $this->entityManager->flush();

        return View::create($phone, Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_phone_show',
                ['id' => $phone->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
    }

}