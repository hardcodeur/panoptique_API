<?php
namespace App\State\Team;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Team\TeamDetailDto;
use App\Repository\TeamRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamItemProvider implements ProviderInterface
{
    public function __construct(
        private TeamRepository $teamRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de l'utilisateur par ID
        $id = $uriVariables['id'] ?? null;
        $team = $this->teamRepository->find($id);
        
        if (!$team) {
            throw new NotFoundHttpException('Equipe non trouvé');
        }
                
        // Création du DTO
        return new TeamDetailDto(
            $team->getId(),
            $team->getName(),
        );
    }
    
}