<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Beneficiary\IDType;
use App\Enums\Beneficiary\Status;
use App\Enums\Beneficiary\Type;
use App\Enums\Gender;
use App\Models\Appointment;
use App\Models\Beneficiary;
use App\Models\Catagraphy;
use App\Models\City;
use App\Models\Family;
use App\Models\Intervention;
use App\Models\Intervention\InterventionableCase;
use App\Models\Intervention\InterventionableIndividualService;
use App\Models\Intervention\OcasionalIntervention;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Beneficiary>
 */
class BeneficiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nurse_id' => User::factory(),
            'family_id' => Family::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'prior_name' => fake()->boolean(25) ? fake()->lastName() : null,
            'type' => fake()->randomElement(Type::values()),
            'integrated' => fake()->boolean(),
            'gender' => fake()->randomElement(Gender::values()),
            'date_of_birth' => fake()->date(),
        ];
    }

    public function configure(): static
    {
        return $this
            ->state(function (array $attributes) {
                if (Type::OCASIONAL->is($attributes['type'])) {
                    return [];
                }

                $status = fake()->randomElement(Status::values());

                return [
                    'status' => $status,
                    'reason_removed' => Status::REMOVED->is($status) ? fake()->sentence() : null,
                ];
            })
            ->afterCreating(function (Beneficiary $beneficiary) {
                if ($beneficiary->isOcasional()) {
                    OcasionalIntervention::factory()
                        ->for($beneficiary)
                        ->count(fake()->randomDigitNotNull())
                        ->create();
                }

                if ($beneficiary->isRegular()) {
                    Catagraphy::factory()
                        ->recycle($beneficiary->nurse)
                        ->for($beneficiary)
                        ->disability()
                        ->reproductiveHealth()
                        ->withNotes()
                        ->create();

                    InterventionableIndividualService::factory()
                        ->count(fake()->randomDigitNotNull())
                        ->has(
                            Intervention::factory()
                                ->withVulnerability()
                                ->recycle($beneficiary),
                            'intervention'
                        )
                        ->create();

                    InterventionableCase::factory()
                        ->count(fake()->randomDigitNotNull())
                        ->has(
                            Intervention::factory()
                                ->withVulnerability()
                                ->recycle($beneficiary)
                                ->afterCreating(function (Intervention $case) {
                                    InterventionableIndividualService::factory()
                                        ->count(fake()->randomDigitNotNull())
                                        ->has(
                                            Intervention::factory([
                                                'parent_id' => $case->id,
                                                'beneficiary_id' => $case->beneficiary_id,
                                            ]),
                                            'intervention'
                                        )
                                        ->create();
                                }),
                            'intervention'
                        )
                        ->create();

                    $interventions = Intervention::query()
                        ->where('interventionable_type', 'individual_service')
                        ->whereBeneficiary($beneficiary)
                        ->get();

                    Appointment::factory()
                        ->recycle($beneficiary->nurse)
                        ->for($beneficiary)
                        ->count(fake()->randomDigitNotNull())
                        ->afterCreating(function (Appointment $appointment) use ($interventions) {
                            if (! fake()->boolean()) {
                                return;
                            }

                            $appointment->interventions()->saveMany(
                                $interventions->random(fake()->numberBetween(1, $interventions->count()))
                            );
                        })
                        ->create();
                }
            });
    }

    public function withCNP(): static
    {
        return $this->state(fn (array $attributes) => [
            'cnp' => fake()->cnp(
                dateOfBirth: $attributes['date_of_birth'],
            ),
        ]);
    }

    public function withID(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_type' => fake()->randomElement(IDType::values()),
            'id_serial' => fake()->lexify('??'),
            'id_number' => fake()->numerify('######'),
        ]);
    }

    public function withAddress(): static
    {
        return $this->state(function (array $attributes) {
            $city = City::query()->inRandomOrder()->first();

            return [
                'address' => fake()->address(),
                'county_id' => $city->county_id,
                'city_id' => $city->id,
            ];
        });
    }

    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => fake()->paragraphs(asText: true),
        ]);
    }
}
