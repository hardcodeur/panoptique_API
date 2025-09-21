<?php 
namespace App\ApiResource\Profil;

use ApiPlatform\Metadata;
use ApiPlatform\Metadata\ApiResource;

use App\Dto\Profil\ProfilDetailDto;
use App\Dto\UserShifts\UserShiftsOutputDto;
use App\Dto\UserShifts\UseShiftMetricOutputDto;

use App\State\Profil\ProfilDetailProvider;
use App\State\UserShifts\UseShiftMetricProvider;
use App\State\UserShifts\UserShiftsProvider;

use App\Dto\Mission\MissionListDto;
use App\State\Mission\ProfilMissionListProvider;





#[ApiResource(
    operations: [
        new Metadata\Get(
            uriTemplate: '/profil',
            output: ProfilDetailDto::class,
            provider: ProfilDetailProvider::class,
            name: 'get_profil'
        ),
        new Metadata\Get(
            uriTemplate: '/profil/current-week-shifts',
            output: UserShiftsOutputDto::class,
            provider: UserShiftsProvider::class,
            name: 'profil_current_week_shifts'
        ),
        new Metadata\Get(
            uriTemplate: '/profil/metric-shift',
            output: UseShiftMetricOutputDto::class,
            provider: UseShiftMetricProvider::class,
            name: 'profil_current_month_shifts_metric'
        ),
        new Metadata\GetCollection(
            uriTemplate: '/profil/missions',
            output: MissionListDto::class,
            provider: ProfilMissionListProvider::class,
            name: 'profil_missions'
        )
    ]
)]
class Profil 
{}