<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public $exampleCompanyTitle = [
        'Apple',
        'Twitter',
        'Facebook',
        'Fedex',
    ];

    public $exampleCompanyDescription = [
        'Our company is the best company in the world. We are the leading provider of products and services in our industry. Our team is made up of the most talented and dedicated professionals in the world. We are committed to excellence and innovation, and we are always looking for new ways to improve our products and services. If you are looking for a challenging and rewarding career, we would love to hear from you!',
        'We are a fast-growing company that is revolutionizing the way people work and live. Our team is made up of the most talented and dedicated professionals in the world. We are committed to excellence and innovation, and we are always looking for new ways to improve our products and services. If you are looking for a challenging and rewarding career, we would love to hear from you!',
        'Our company is a global leader in the industry. We are dedicated to providing the best products and services to our customers. Our team is made up of the most talented and dedicated professionals in the world. We are committed to excellence and innovation, and we are always looking for new ways to improve our products and services. If you are looking for a challenging and rewarding career, we would love to hear from you!',
        'We are a dynamic and innovative company that is changing the world. Our team is made up of the most talented and dedicated professionals in the world. We are committed to excellence and innovation, and we are always looking for new ways to improve our products and services. If you are looking for a challenging and rewarding career, we would love to hear from you!',
        'Our company is a leader in the industry. We are dedicated to providing the best products and services to our customers. Our team is made up of the most talented and dedicated professionals in the world. We are committed to excellence and innovation, and we are always looking for new ways to improve our products and services. If you are looking for a challenging and rewarding career, we would love to hear from you!',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->exampleCompanyTitle[array_rand($this->exampleCompanyTitle)],
            'description' => $this->exampleCompanyDescription[array_rand($this->exampleCompanyDescription)],
            'rating' => $this->faker->numberBetween(1, 5),
            'website' => $this->faker->url,
            'employes' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
