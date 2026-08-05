<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
// use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->unique(true);
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->regexify('\\+91[6-9]\\d{9}'),
            'image' => 'image/placeholder.jpg',  // Placeholder image path
            'DOB' => $this->faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'age' => $this->faker->numberBetween(18, 50),
            'gender' => $this->faker->randomElement(array_column(GenderEnum::cases(), 'value')),
            'password' => Hash::make('password'),  // Default password
            'address' => $this->faker->address(),
            'country' => 'USA',
            'state' => 'California',
            'city' => 'Los Angeles',
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced']),
            'designation' => $this->faker->jobTitle(),
            'qualification' => $this->faker->randomElement([
                'Bachelor of Science in Computer Science',
                'Master of Business Administration',
                'Diploma in IT',
                'PhD in Physics'
            ]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
