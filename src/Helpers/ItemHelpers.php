<?php

namespace App\Helpers;

use App\DTO\FruitDTO;
use App\DTO\ItemData;
use App\DTO\VegetableDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ItemHelpers
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public static function createItemDTOFromRequest(Request $request): ItemData
    {
        $data = json_decode($request->getContent(), true);

        $itemDTO = new ItemData();
        $itemDTO->name = $data['name'];
        $itemDTO->type = $data['type'];
        $itemDTO->unit = $data['unit'];
        $itemDTO->quantity = $data['quantity'];

        return $itemDTO;
    }

    /**
     * Helper method to create a consistent JSON response.
     */
    public function createJsonResponse($data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $jsonData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, $statusCode, [], true);
    }

    /**
     * Helper method to create standardized error responses.
     */
    public function createErrorResponse(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }

    public function createFruitDTOFromRequest(Request $request): FruitDTO
    {
        $data = json_decode($request->getContent(), true);

        $fruitDTO = new FruitDTO();
        $fruitDTO->name = $data['name'];
        $fruitDTO->weight = $data['weight'];

        return $fruitDTO;
    }

    public function createVegetableDTOFromRequest(Request $request): VegetableDTO
    {
        $data = json_decode($request->getContent(), true);

        $vegetableDTO = new VegetableDTO();
        $vegetableDTO->name = $data['name'];
        $vegetableDTO->weight = $data['weight'];

        return $vegetableDTO;
    }
}
