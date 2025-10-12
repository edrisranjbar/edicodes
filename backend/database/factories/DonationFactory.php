<?php

namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Donation::class;

    /**
     * Persian first names
     *
     * @var array
     */
    private $persianFirstNames = [
        'علی', 'محمد', 'رضا', 'حسین', 'مهدی', 'احمد', 'امیر', 'حامد', 'سعید', 'مسعود',
        'فاطمه', 'زهرا', 'مریم', 'سارا', 'نرگس', 'فرزانه', 'لیلا', 'نیلوفر', 'شبنم', 'آیدا',
        'محسن', 'مصطفی', 'جواد', 'حمید', 'بهروز', 'فرهاد', 'کامران', 'سهراب', 'آرش', 'پویا',
        'سمیرا', 'سمانه', 'مهناز', 'پریسا', 'الهام', 'مهسا', 'نازنین', 'طاهره', 'زینب', 'ملیکا',
        'امین', 'سینا', 'فرید', 'بهمن', 'نیما', 'هادی', 'داود', 'یوسف', 'مجید', 'رامین'
    ];

    /**
     * Persian last names
     *
     * @var array
     */
    private $persianLastNames = [
        'احمدی', 'محمدی', 'رضایی', 'حسینی', 'کریمی', 'جعفری', 'موسوی', 'علوی', 'هاشمی', 'نوری',
        'اکبری', 'صادقی', 'مرادی', 'اسدی', 'ملکی', 'قاسمی', 'رحمانی', 'عباسی', 'یوسفی', 'فرهادی',
        'میرزایی', 'سلیمانی', 'شریفی', 'صفری', 'طاهری', 'کاظمی', 'مهدوی', 'ناصری', 'جلالی', 'سعیدی',
        'رستمی', 'امینی', 'توکلی', 'غلامی', 'رنجبر', 'حیدری', 'زارع', 'شفیعی', 'حکیمی', 'باقری',
        'نظری', 'منصوری', 'پورحسینی', 'کاویانی', 'نیکنام', 'جهانی', 'اسماعیلی', 'محبی', 'فتحی', 'رشیدی'
    ];

    /**
     * Persian donation messages
     *
     * @var array
     */
    private $persianMessages = [
        'از تلاش‌های شما برای آموزش رایگان سپاسگزارم',
        'موفق باشید، محتواهای شما عالی است',
        'ممنون از محتوای باکیفیتی که تولید می‌کنید',
        'امیدوارم با این کمک کوچک بتوانم سهمی در کار شما داشته باشم',
        'از وقتی که برای آموزش می‌گذارید متشکرم',
        'دوره‌های شما بسیار مفید و کاربردی هستند',
        'خداقوت، به کارتون ادامه بدید',
        'از شما و آموزش‌های رایگانتون ممنونم',
        'دست‌های پر برکتتون درد نکنه',
        'موفق و سربلند باشید',
        'یکی از بهترین مدرسین حوزه برنامه‌نویسی هستید',
        'عالی هستید، ممنون از اشتراک دانشتون',
        'خیلی لذت بردم از آموزش‌هاتون',
        'همیشه موفق باشید',
        'پروژه‌های متن‌باز شما خیلی کمک‌کننده هستند',
        'ازتون حمایت می‌کنیم',
        'باعث افتخارید',
        'امیدوارم روزی بتونم مثل شما باشم',
        'خدا قوت، ممنون از همه چیز',
        'سپاس از محتواهای ارزشمندتون',
        null, // Some donations without messages
        null,
        null,
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 85% paid, 10% pending, 5% failed
        $statusRandom = $this->faker->numberBetween(1, 100);
        if ($statusRandom <= 85) {
            $status = 'paid';
        } elseif ($statusRandom <= 95) {
            $status = 'pending';
        } else {
            $status = 'failed';
        }

        $isPaid = $status === 'paid';

        return [
            'name' => $this->faker->randomElement($this->persianFirstNames) . ' ' . 
                      $this->faker->randomElement($this->persianLastNames),
            'email' => $this->faker->boolean(30) ? $this->faker->safeEmail() : null, // 30% have email
            'amount' => $this->faker->randomElement([
                50000, 100000, 150000, 200000, 250000, 300000, 500000, 
                1000000, 1500000, 2000000, 5000000
            ]),
            'currency' => 'T', // Toman
            'message' => $this->faker->boolean(60) ? $this->faker->randomElement($this->persianMessages) : null,
            'transaction_id' => $isPaid ? $this->faker->regexify('[A-Z0-9]{32}') : null,
            'payment_method' => 'zarinpal',
            'card_number' => $isPaid ? $this->faker->regexify('[0-9]{4}') : null, // Last 4 digits
            'tracking_code' => $isPaid ? $this->faker->regexify('[0-9]{10}') : null,
            'ref_id' => $isPaid ? $this->faker->regexify('[0-9]{10}') : null,
            'status' => $status,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the donation is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'transaction_id' => $this->faker->regexify('[A-Z0-9]{32}'),
            'ref_id' => $this->faker->regexify('[0-9]{10}'),
            'tracking_code' => $this->faker->regexify('[0-9]{10}'),
            'card_number' => $this->faker->regexify('[0-9]{4}'),
        ]);
    }

    /**
     * Indicate that the donation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'transaction_id' => $this->faker->regexify('[A-Z0-9]{32}'),
            'ref_id' => null,
            'tracking_code' => null,
        ]);
    }

    /**
     * Indicate that the donation is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'transaction_id' => $this->faker->regexify('[A-Z0-9]{32}'),
            'ref_id' => null,
            'tracking_code' => null,
        ]);
    }

    /**
     * Indicate that the donation is anonymous.
     */
    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'ناشناس',
            'email' => null,
        ]);
    }
}

