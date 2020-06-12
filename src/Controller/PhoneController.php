<?php

namespace App\Controller;

use App\Entity\Phone;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PhoneController extends AbstractController
{
    /**
     * @Get(
     *     path = "/phones/{id}",
     *     name = "app_phone_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function showAction(Phone $phone)
    {
        return $phone;
    }

    /**
     * @Post(
     *     path = "/phones",
     *     name = "app_phone_create",
     * )
     * @View(StatusCode=201)
     * @ParamConverter("phone", converter="fos_rest.request_body")
     */
    public function createAction(Phone $phone)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($phone);
        $em->flush();
    }

}
