<?php

declare(strict_types=1);

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Contracts\Pages\WithSidebar;
use App\Enums\Beneficiary\Type;
use App\Filament\Resources\BeneficiaryResource;
use App\Filament\Resources\BeneficiaryResource\Concerns;
use App\Forms\Components\Badge;
use App\Forms\Components\Card;
use App\Forms\Components\Household;
use App\Forms\Components\Location;
use App\Forms\Components\Subsection;
use App\Forms\Components\Value;
use App\Models\Beneficiary;
use App\Models\Intervention;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Form;
use Filament\Resources\Pages\ViewRecord;

class OverviewBeneficiary extends ViewRecord implements WithSidebar
{
    use Concerns\HasActions;
    use Concerns\HasRecordBreadcrumb;
    use Concerns\HasSidebar;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->full_name ?? __('field.has_unknown_identity');
    }

    public function getBreadcrumb(): string
    {
        return $this->getRecord()->full_name;
    }

    protected function form(Form $form): Form
    {
        return match ($this->getRecord()->type) {
            Type::REGULAR => static::getRegularBeneficiaryForm($form),
            Type::OCASIONAL => static::getOcasionalBeneficiaryForm($form),
        };
    }

    protected static function getRegularBeneficiaryForm(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Card::make()
                    ->header(__('beneficiary.section.personal_data'))
                    ->columns(2)
                    ->columnSpan([
                        'xl' => 1,
                    ])
                    ->schema([
                        Badge::make('status')
                            ->content(fn (Beneficiary $record) => $record->status?->label())
                            ->color(fn (Beneficiary $record) => $record->status?->color())
                            ->columnSpanFull(),

                        Value::make('id')
                            ->label(__('field.beneficiary_id'))
                            ->content(fn (Beneficiary $record) => $record->id),

                        Value::make('integrated')
                            ->label(__('field.integrated'))
                            ->content('Placeholder content'),

                        Household::make()
                            ->withoutSubsection()
                            ->columns(2)
                            ->columnSpanFull(),

                        Value::make('age')
                            ->label(__('field.age'))
                            ->content(fn (Beneficiary $record) => $record->age),

                        Value::make('gender')
                            ->label(__('field.gender'))
                            ->content(fn (Beneficiary $record) => $record->gender?->label()),

                        Value::make('address')
                            ->label(__('field.address'))
                            ->content(fn (Beneficiary $record) => $record->full_address)
                            ->columnSpanFull(),

                        Value::make('phone')
                            ->label(__('field.phone'))
                            ->content(fn (Beneficiary $record) => $record->phone)
                            ->columnSpanFull(),

                    ]),

                Card::make()
                    ->header(__('beneficiary.section.active_interventions'))
                    ->columnSpan([
                        'xl' => 2,
                    ])
                    ->schema([

                    ]),
            ]);
    }

    protected static function getOcasionalBeneficiaryForm(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Card::make()
                    ->header(__('beneficiary.section.personal_data'))
                    ->columnSpan(1)
                    ->schema([
                        Badge::make('type')
                            ->content(fn (Beneficiary $record) => $record->type?->label())
                            ->color(fn (Beneficiary $record) => $record->type?->color()),

                        Subsection::make()
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->visible(fn (Beneficiary $record) => $record->has_unknown_identity)
                            ->schema([
                                Value::make('has_unknown_identity')
                                    ->label(__('field.has_unknown_identity'))
                                    ->empty()
                                    ->columnSpanFull(),
                            ]),

                        Subsection::make()
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->hidden(fn (Beneficiary $record) => $record->has_unknown_identity)
                            ->schema([
                                Value::make('first_name')
                                    ->label(__('field.first_name'))
                                    ->content(fn (Beneficiary $record) => $record->first_name),

                                Value::make('last_name')
                                    ->label(__('field.last_name'))
                                    ->content(fn (Beneficiary $record) => $record->last_name),

                                Value::make('gender')
                                    ->label(__('field.gender'))
                                    ->content(fn (Beneficiary $record) => $record->gender?->label()),

                                Value::make('cnp')
                                    ->label(__('field.cnp'))
                                    ->content(fn (Beneficiary $record) => $record->cnp)
                                    ->fallback(__('field.does_not_have_cnp')),
                            ]),

                        Household::make(),

                        static::getLocationSubsection(),

                        Subsection::make()
                            ->icon('heroicon-o-lightning-bolt')
                            ->schema([
                                Repeater::make('interventions')
                                    ->relationship()
                                    ->label(__('intervention.label.plural'))
                                    ->columns(2)
                                    ->extraAttributes(['class' => '[ul]:divide-y'])
                                    ->schema([
                                        Value::make('reason')
                                            ->label(__('field.intervention_reason'))
                                            ->content(fn (Intervention $record) => $record->reason),

                                        Value::make('date')
                                            ->label(__('field.date'))
                                            ->content(fn (Intervention $record) => $record->date),

                                        Value::make('services')
                                            ->label(__('field.services'))
                                            ->content(fn (Intervention $record) => $record->services->join(', ')),
                                    ]),
                            ]),

                        Subsection::make()
                            ->icon('heroicon-o-annotation')
                            ->schema([
                                Value::make('notes')
                                    ->label(__('field.beneficiary_notes'))
                                    ->extraAttributes(['class' => 'prose max-w-none'])
                                    ->content(fn (Beneficiary $record) => $record->notes),
                            ]),
                    ]),
            ]);
    }

    private static function getLocationSubsection(): Subsection
    {
        return Subsection::make()
            ->icon('heroicon-o-location-marker')
            ->columns(2)
            ->schema([
                Location::make(),

                Value::make('address')
                    ->label(__('field.address'))
                    ->content(fn (Beneficiary $record) => $record->address),

                Value::make('phone')
                    ->label(__('field.phone'))
                    ->content(fn (Beneficiary $record) => $record->phone),
            ]);
    }
}
