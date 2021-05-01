<?php

namespace Database\Factories;

use App\Models\EmailList;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $top_domains = null;
        for ($i = 0; $i < $this->faker->numberBetween(10,500); $i++) {
            $top_domains[$this->faker->domainName()] = $this->faker->numberBetween(100, 100000);
        }

        $free_emails = null;
        for ($i = 0; $i < $this->faker->numberBetween(2,50); $i++) {
            $free_emails[$this->faker->freeEmailDomain()] = $this->faker->numberBetween(100, 10000);
        }

        $status = $this->faker->randomElement(['unconfirmed', 'active', 'processing', 'error', 'deleted']);

        return [
            'name' => $this->faker->word,
            'status' => $status,
            'notify' => $this->faker->boolean,

            'free_mails_stat' => json_encode($free_emails),
            'top_domains' => json_encode($top_domains),

            'finish_date' => $status === 'active' ? $this->faker->dateTimeBetween('-1 years') : null,
            'list_db_status' => $this->faker->randomElement(['unknown', 'parsing', 'pre-processing', 'generating', 'generated', 'completed', 'flushing', 'error']),

        ];
    }
}
