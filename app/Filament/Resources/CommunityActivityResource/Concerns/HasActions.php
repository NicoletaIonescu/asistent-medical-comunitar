<?php

declare(strict_types=1);

namespace App\Filament\Resources\CommunityActivityResource\Concerns;

use App\Enums\CommunityActivityType;
use App\Filament\Actions\ActionGroup;
use App\Filament\Resources\CommunityActivityResource;
use App\Models\CommunityActivity;
use Filament\Pages\Actions\CreateAction;

trait HasActions
{
    protected function getActions(): array
    {
        return [
            ActionGroup::make([

                CreateAction::make('campaign')
                    ->form(CommunityActivityResource::getCampaignEditFormSchema())
                    ->using(function (array $data) {
                        $data['type'] = CommunityActivityType::CAMPAIGN;

                        CommunityActivity::create($data);
                    })
                    ->label(__('community_activity.action.create_campaign'))
                    ->modalHeading(__('community_activity.action.create_campaign'))
                    ->groupedIcon(null)
                    ->disableCreateAnother(),

                CreateAction::make('environment')
                    ->form(CommunityActivityResource::getEnvironmentEditFormSchema())
                    ->using(function (array $data) {
                        $data['type'] = CommunityActivityType::ENVIRONMENT;

                        CommunityActivity::create($data);
                    })
                    ->label(__('community_activity.action.create_environment'))
                    ->modalHeading(__('community_activity.action.create_environment'))
                    ->groupedIcon(null)
                    ->disableCreateAnother(),

                CreateAction::make('admin')
                    ->form(CommunityActivityResource::getAdministrativeEditFormSchema())
                    ->using(function (array $data) {
                        $data['type'] = CommunityActivityType::ADMINISTRATIVE;

                        CommunityActivity::create($data);
                    })
                    ->label(__('community_activity.action.create_administrative'))
                    ->modalHeading(__('community_activity.action.create_administrative'))
                    ->groupedIcon(null)
                    ->disableCreateAnother(),

            ]),
        ];
    }
}
