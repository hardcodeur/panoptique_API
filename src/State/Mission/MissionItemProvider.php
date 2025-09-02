<?php

namespace App\State\Mission;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mission\MissionDetailDto;
use App\Repository\MissionRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MissionItemProvider implements ProviderInterface
{
    public function __construct(
        private MissionRepository $repository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de l'utilisateur par ID
        $id = $uriVariables['id'] ?? null;
        $item = $this->repository->find($id);
        
        if (!$item) {
            throw new NotFoundHttpException('Mission non trouvé');
        }
                
        // Création du DTO
        return new MissionDetailDto(
            $item->getId(),
            $item->getStart(),
            $item->getEnd(),
            $item->getCustomer()->getId(),
            $item->getTeam()->getId(),
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
        );
    }
    
}