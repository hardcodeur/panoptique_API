<?php 

namespace App\State\Team;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// dto
use App\Dto\Team\TeamCreateDto;
use App\Dto\Team\TeamUpdateDto;
use App\Dto\Team\TeamDetailDto;
// doctrine
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TeamRepository;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class TeamProcessor implements ProcessorInterface
{   
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamRepository $teamRepository,
        private ValidatorInterface $validator
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($data instanceof TeamCreateDto) {
            return $this->handleCreate($data);
        }
        
        if ($data instanceof TeamUpdateDto) {
            $userId = $uriVariables['id'] ?? null;
            return $this->handleUpdate($userId,$data);
        }

        if ($operation instanceof Delete && $data instanceof Team) {
            $this->handleDelete($data);
            return null;
        }

        return $data;
    }

    public function handleCreate(TeamCreateDto $data){

        $team = new Team;
        $team->setName($data->getTeamName());

        # Validation of my entity 
        $violations = $this->validator->validate($team);
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return new TeamDetailDto(
            $team->getId(),
            $team->getName(),
        );

    }

    public function handleUpdate(int $teamId,TeamUpdateDto $data){

        $team = $this->teamRepository->find($teamId);

        if(!$team){
            throw new NotFoundHttpException(sprintf("L'équipe avec l'ID : %d n'existe pas", $teamId));
        }

        # Validation of my entity 
        $violations = $this->validator->validate($team);
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        if($data->getTeamName() !== null){
            $team->setName($data->getTeamName());
        }

        $this->entityManager->flush();

        return new TeamDetailDto(
            $team->getId(),
            $team->getName(),
        );
        
    }


    public function handleDelete(Team $team){
        // Select user in team and empty field team and delete user
        $teamUsers=$team->getUsers();
        foreach($teamUsers as $user){
            $user->setTeam(null);
            $user->setIsDeleted(true);
        }

        // Delete team
        $team->setIsDeleted(true);
        $this->entityManager->flush();
    }

}