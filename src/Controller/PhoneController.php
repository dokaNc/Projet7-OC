<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Exception\ResourceValidationException;
use App\Repository\PhoneRepository;
use App\Service\PhoneService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class PhoneController
 * @package App\Controller
 * @Route("/api")
 */

class PhoneController extends AbstractController
{
    private $entityManager;

    private $repository;

    private $phoneService;

    public function __construct(EntityManagerInterface $entityManager,
                                PhoneRepository $repository,
                                PhoneService $phoneService)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->phoneService = $phoneService;
    }

    /**
     * @Rest\Get(
     *     path = "/phones/{page<\d+>?1}",
     *     name = "app_phone_list",
     *     requirements = {"id"="\d+"}
     * )
     * @param $page
     * @return View
     */
    public function listAction($page)
    {
        $data = $this->phoneService->getAllData($page);
        return View::create($data,Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Get(
     *     path = "/phone/{id}",
     *     name = "app_phone_show",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     * @param Phone $phone
     * @return View
     */
    public function showAction(Phone $phone)
    {
        return View::create($phone, Response::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Post(
     *    path = "/phone",
     *    name = "app_phone_create"
     * )
     * @ParamConverter(
     *     "phone",
     *     converter="fos_rest.request_body"
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     * @param Phone $phone
     * @param $violations
     * @return View
     * @throws ResourceValidationException
     */
    public function createAction(Phone $phone, $violations)
    {
        $this->phoneService->addData($phone, $violations);

        return View::create($phone, Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_phone_show',
                ['id' => $phone->getId(), UrlGeneratorInterface::ABSOLUTE_URL])
            ]);
    }

    /**
     * @Rest\Put(
     *     path = "/phone/{id}",
     *     name = "app_phone_update",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_SUPERADMIN")
     * @ParamConverter("newPhone",
     *     converter="fos_rest.request_body"
     * )
     * @param Phone $phone
     * @param Phone $newPhone
     * @param $violations
     * @return mixed
     * @throws ResourceValidationException
     */
    public function updateAction(Phone $phone, Phone $newPhone, $violations)
    {
        $this->phoneService->updateData($violations, $phone, $newPhone);

        return View::create($phone, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/phone/{id}",
     *     name = "app_phone_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @IsGranted("ROLE_ADMIN")
     * @param Phone $phone
     * @return View
     */
    public function deleteAction(Phone $phone)
    {
        $this->entityManager->remove($phone);
        $this->entityManager->flush();

        return View::create($phone, Response::HTTP_NO_CONTENT);
    }

}