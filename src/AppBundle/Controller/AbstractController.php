<?php

namespace AppBundle\Controller;

use AppBundle\Enum\RESTResponseEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController extends Controller
{
    /**
     * @param int $code
     * @param array $messages
     * @return JsonResponse
     */
    public function getJsonErrorResponse(int $code = 0, array $messages) : JsonResponse
    {
        return new JsonResponse([
            'code'      => $code,
            'error'     => $messages
        ], $code);
    }

    /**
     * @param mixed $response
     * @return JsonResponse
     */
    public function getJsonResponse($response) : JsonResponse
    {
        return new JsonResponse([
            'code'      => RESTResponseEnum::OK,
            'response'  => $response
        ], RESTResponseEnum::OK);
    }
}
