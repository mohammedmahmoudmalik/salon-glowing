<?php

namespace App\Domains\Demo\Actions;

use App\Domains\Admin\Models\Setting;
use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\Models\User;
use App\Domains\Booking\Enums\BookingStatus;
use App\Domains\Booking\Models\Booking;
use App\Domains\Booking\Models\BookingItem;
use App\Domains\Offer\Enums\DiscountType;
use App\Domains\Offer\Models\Offer;
use App\Domains\Review\Models\Review;
use App\Domains\Service\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateDemoDataAction
{
    // ── Country-specific data ────────────────────────────────────────────────

    private const NAMES = [
        'SA' => [
            'first' => ['نورة','سارة','لينا','ريم','هند','منى','غادة','أسماء','رانا','نادية',
                        'وفاء','لمياء','أمل','شروق','دلال','رهف','جود','مها','ضحى','ألاء',
                        'سلوى','نهال','ربى','ابتسام','حنان'],
            'last'  => ['الغامدي','العمري','السهلي','المالكي','الزهراني','القحطاني',
                        'الدوسري','الشهري','العتيبي','الحربي','البقمي','الجهني',
                        'الصاعدي','السلمي','العنزي'],
        ],
        'AE' => [
            'first' => ['مريم','شيخة','لطيفة','موزة','فاطمة','عائشة','نورة','منى','سلمى',
                        'حصة','ميثاء','بدور','ريم','ولاء','خلود','سارة','ميرة','عزة',
                        'ليلى','حمدة','أمل','هند','رزان','شما','وفاء'],
            'last'  => ['المهيري','الكتبي','البلوشي','الشحي','النعيمي','الزعابي',
                        'الهاملي','العامري','المنصوري','الرميثي','الحمادي','الفلاسي',
                        'النيادي','الظاهري','الحوسني'],
        ],
        'EG' => [
            'first' => ['نهى','دينا','رانيا','ياسمين','سمر','مروة','هبة','شيرين','نرمين',
                        'إيمان','آية','سحر','مي','لمياء','شيماء','منى','أمل','نجوى',
                        'فاطمة','غادة','ريهام','إسراء','نيرة','نسمة','لارا'],
            'last'  => ['محمد','أحمد','علي','حسن','حسين','عبدالله','إبراهيم','عمر',
                        'يوسف','خليل','سلامة','عبدالرحمن','جمال','فاروق','عثمان'],
        ],
    ];

    private const PHONE_PREFIXES = [
        'SA' => ['0501','0502','0503','0504','0505','0506','0507','0508','0509','0550','0551','0552','0553','0554','0555','0556','0557','0558','0559'],
        'AE' => ['0501','0502','0503','0504','0505','0506','0507','0508','0509','0551','0552','0553','0554','0555','0556','0557','0558'],
        'EG' => ['010','011','012','015'],
    ];

    private const PHONE_LENGTHS = ['SA' => 7, 'AE' => 7, 'EG' => 8];

    private const OFFER_TITLES = [
        ['ar' => 'خصم الربيع',          'en' => 'Spring Offer'],
        ['ar' => 'عروض الصيف',          'en' => 'Summer Deals'],
        ['ar' => 'خصم يوم الجمعة',      'en' => 'Friday Discount'],
        ['ar' => 'عرض العميلة الجديدة', 'en' => 'New Customer Offer'],
        ['ar' => 'عروض نهاية الشهر',    'en' => 'End of Month Sale'],
        ['ar' => 'الباقة المميزة',       'en' => 'Premium Package'],
        ['ar' => 'خصم خاص',             'en' => 'Special Discount'],
        ['ar' => 'عرض الأسبوع',         'en' => 'Weekly Deal'],
        ['ar' => 'خصم العيد',           'en' => 'Holiday Offer'],
        ['ar' => 'عرض الصداقة',         'en' => 'Friendship Offer'],
    ];

    private const REVIEW_COMMENTS = [
        5 => ['خدمة ممتازة! سأعود مجدداً.','تجربة رائعة جداً، شكراً.','سعيدة جداً بالنتيجة.','أنصح الجميع بهذا الصالون.','الأفضل في المنطقة.','خدمة احترافية من الدرجة الأولى.'],
        4 => ['خدمة جيدة وسريعة.','تجربة جيدة بشكل عام.','راضية عن الخدمة.','جيدة وسأعود مرة أخرى.','خدمة لائقة وسعر مناسب.'],
        3 => ['كانت مقبولة لكن يمكن تحسينها.','خدمة عادية.','كانت التجربة متوسطة.'],
    ];

    // ── Booking status distribution (index → [status, daysOffset range]) ───

    private const STATUS_POOLS = [
        ['status' => BookingStatus::Completed,  'min' => -150, 'max' => -4],
        ['status' => BookingStatus::Completed,  'min' => -150, 'max' => -4],
        ['status' => BookingStatus::Completed,  'min' => -150, 'max' => -4],
        ['status' => BookingStatus::Cancelled,  'min' => -90,  'max' => -1],
        ['status' => BookingStatus::NoShow,     'min' => -60,  'max' => -3],
        ['status' => BookingStatus::Confirmed,  'min' => -10,  'max' =>  7],
        ['status' => BookingStatus::Pending,    'min' =>   1,  'max' => 30],
        ['status' => BookingStatus::Pending,    'min' =>   1,  'max' => 30],
    ];

    private const TIME_SLOTS = [
        '09:00','09:30','10:00','10:30','11:00','11:30','12:00','13:00',
        '14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30',
        '18:00','18:30','19:00',
    ];

    // ── Entry point ──────────────────────────────────────────────────────────

    public function execute(int $customerCount, int $bookingCount, int $offerCount): array
    {
        $services = Service::active()->get();

        if ($services->isEmpty()) {
            throw new \RuntimeException('no_services');
        }

        $country = Setting::get('salon_country', 'SA');
        $buffer  = (int) Setting::get('booking_buffer_minutes', 10);

        $stats = ['customers' => 0, 'bookings' => 0, 'offers' => 0, 'reviews' => 0];

        DB::transaction(function () use ($customerCount, $bookingCount, $offerCount, $services, $country, $buffer, &$stats) {

            // ── 1. Customers ─────────────────────────────────────────────────
            $customers = $this->generateCustomers($customerCount, $country);
            $stats['customers'] = count($customers);

            // ── 2. Bookings ──────────────────────────────────────────────────
            $completedBookings = [];

            for ($i = 0; $i < $bookingCount; $i++) {
                $customer   = $customers[array_rand($customers)];
                $pool       = self::STATUS_POOLS[$i % count(self::STATUS_POOLS)];
                $status     = $pool['status'];
                $days       = rand($pool['min'], $pool['max']);
                $date       = Carbon::today()->addDays($days)->toDateString();
                $slot       = self::TIME_SLOTS[array_rand(self::TIME_SLOTS)];
                $svcSample  = $services->random(min(rand(1, 2), $services->count()));
                $totalDur   = $svcSample->sum('duration_minutes');
                $totalPrice = $svcSample->sum('price');
                $endTime    = Carbon::createFromFormat('H:i', $slot)->addMinutes($totalDur + $buffer)->format('H:i');

                $booking = Booking::create([
                    'customer_id'    => $customer->id,
                    'booking_date'   => $date,
                    'start_time'     => $slot,
                    'end_time'       => $endTime,
                    'status'         => $status,
                    'buffer_minutes' => $buffer,
                    'total_price'    => $totalPrice,
                    'confirmed_at'   => in_array($status, [BookingStatus::Confirmed, BookingStatus::Completed])
                                        ? Carbon::parse($date)->subDays(1) : null,
                    'cancelled_at'   => $status === BookingStatus::Cancelled
                                        ? Carbon::parse($date)->subHours(rand(4, 48)) : null,
                ]);

                foreach ($svcSample as $svc) {
                    BookingItem::create([
                        'booking_id'       => $booking->id,
                        'service_id'       => $svc->id,
                        'price'            => $svc->price,
                        'duration_minutes' => $svc->duration_minutes,
                    ]);
                }

                if ($status === BookingStatus::Completed) {
                    $completedBookings[] = ['booking' => $booking, 'service' => $svcSample->first(), 'customer' => $customer];
                }

                $stats['bookings']++;
            }

            // ── 3. Reviews (≈65% of completed bookings) ──────────────────────
            foreach ($completedBookings as $item) {
                if (rand(1, 100) > 65) {
                    continue;
                }

                $rating = $this->weightedRating();

                Review::create([
                    'booking_id'  => $item['booking']->id,
                    'customer_id' => $item['customer']->id,
                    'service_id'  => $item['service']->id,
                    'rating'      => $rating,
                    'comment'     => self::REVIEW_COMMENTS[$rating][array_rand(self::REVIEW_COMMENTS[$rating])],
                    'is_hidden'   => false,
                ]);

                $stats['reviews']++;
            }

            // ── 4. Offers ─────────────────────────────────────────────────────
            $usedTitles = [];

            for ($i = 0; $i < $offerCount; $i++) {
                $titleData    = $this->pickUnused(self::OFFER_TITLES, $usedTitles);
                $usedTitles[] = $titleData['ar'];
                $isPercentage = rand(0, 1);
                $svcForOffer  = $services->random(min(rand(1, 3), $services->count()));
                $hasExpiry    = rand(0, 1);

                $offer = Offer::create([
                    'title_ar'       => $titleData['ar'],
                    'title_en'       => $titleData['en'],
                    'description_ar' => 'احصلي على خصم مميز على خدماتنا.',
                    'description_en' => 'Get a special discount on our services.',
                    'discount_type'  => $isPercentage ? DiscountType::Percentage : DiscountType::Fixed,
                    'discount_value' => $isPercentage ? rand(10, 40) : rand(20, 100),
                    'starts_at'      => Carbon::today()->subDays(rand(0, 10)),
                    'ends_at'        => $hasExpiry ? Carbon::today()->addDays(rand(14, 90)) : null,
                    'is_active'      => true,
                ]);

                $offer->services()->attach($svcForOffer->pluck('id'));
                $stats['offers']++;
            }
        });

        return $stats;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function generateCustomers(int $count, string $country): array
    {
        $names    = self::NAMES[$country]    ?? self::NAMES['SA'];
        $prefixes = self::PHONE_PREFIXES[$country] ?? self::PHONE_PREFIXES['SA'];
        $phoneLen = self::PHONE_LENGTHS[$country]  ?? 7;
        $customers = [];

        for ($i = 0; $i < $count; $i++) {
            $firstName = $names['first'][array_rand($names['first'])];
            $lastName  = $names['last'][array_rand($names['last'])];
            $fullName  = $firstName . ' ' . $lastName;
            $uid       = Str::random(6);
            $phone     = $prefixes[array_rand($prefixes)] . $this->randomDigits($phoneLen);

            // Ensure uniqueness
            while (User::where('phone', $phone)->exists()) {
                $phone = $prefixes[array_rand($prefixes)] . $this->randomDigits($phoneLen);
            }

            $user = User::create([
                'name'      => $fullName,
                'email'     => null,
                'phone'     => $phone,
                'password'  => Hash::make('demo_' . $uid),
                'is_active' => true,
            ]);
            $user->syncRoles('customer');

            $customer = Customer::create(['user_id' => $user->id]);
            $customers[] = $customer;
        }

        return $customers;
    }

    private function randomDigits(int $length): string
    {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= rand(0, 9);
        }

        return $digits;
    }

    private function weightedRating(): int
    {
        $n = rand(1, 100);
        if ($n <= 10) {
            return 3;
        }
        if ($n <= 45) {
            return 4;
        }

        return 5;
    }

    private function pickUnused(array $pool, array $used): array
    {
        $available = array_filter($pool, fn ($item) => ! in_array($item['ar'], $used));

        return $available
            ? $available[array_rand($available)]
            : $pool[array_rand($pool)];
    }
}
