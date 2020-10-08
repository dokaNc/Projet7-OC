<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Exception\ResourceValidationException;
use App\Repository\PhoneRepository;
use App\Service\PhoneService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

/**
 * Class PhoneController
 * @package App\Controller
 * @Route("/api")
 *
 * @Security(name="Bearer")
 * @OA\Tag(name="Phone")
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
     *     path="/phones/{page<\d+>?1}",
     *     name = "app_phone_list",
     *     requirements = {"id"="\d+"}
     * )
     *
     * @OA\Response(
     *      response="200",
     *      description="List Phones",
     *      @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *                ref=@Model(type=Phone::class))
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
     * @return View
     */
    public function listAction($page)
    {
        $data = $this->phoneService->getAllData($page);

        return View::create($data,Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path = "/phone/{id}",
     *     name = "app_phone_show",
     *     requirements = {"id"="\d+"}
     * )
     *
     * @OA\Response(
     *      response="200",
     *      description="Detail Phone",
     *      @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *                ref=@Model(type=Phone::class))
     *      )
     * )
     * @OA\Response(
     *      response="404",
     *      description="Not found",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="404"),
     *           @OA\Property(property="messsage", type="string", example="Ressource not found")
     *      )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="Phone")
     *
     * @param Phone $phone
     * @return View
     */
    public function showAction(Phone $phone)
    {
        return View::create($phone, Response::HTTP_OK);
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
     *
     * @OA\Response(
     *      response="201",
     *      description="Add Phone",
     *      @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *                ref=@Model(type=Phone::class))
     *      )
     * )
     * @OA\RequestBody(
     *     request="Add new Phone",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="brand"),
     *          @OA\Property(type="string", property="model"),
     *          @OA\Property(type="string", property="color"),
     *          @OA\Property(type="string", property="description"),
     *          @OA\Property(type="integer", property="price")
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="Phone")
     *
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
     *
     * @OA\Response(
     *      response="200",
     *      description="Update Phone",
     *      @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *                ref=@Model(type=Phone::class))
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
     *     request="Update Phone",
     *     required=true,
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="brand"),
     *          @OA\Property(type="string", property="model"),
     *          @OA\Property(type="string", property="color"),
     *          @OA\Property(type="string", property="description"),
     *          @OA\Property(type="integer", property="price")
     *     )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="Phone")
     *
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
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @OA\Response(
     *      response="204",
     *      description="Delete Phone",
     * )
     * @OA\Response(
     *      response="404",
     *      description="Not Found",
     *      @OA\JsonContent(
     *           @OA\Property(property="status", type="integer", example="404"),
     *           @OA\Property(property="messsage", type="string", example="Ressource not found")
     *      )
     * )
     * @Security(name="Bearer")
     * @OA\Tag(name="Phone")
     *
     * @param Phone $phone
     * @return View
     */
    public function deleteAction(Phone $phone)
    {
        $this->phoneService->deleteData($phone);

        return View::create($phone, Response::HTTP_NO_CONTENT);
    }

}