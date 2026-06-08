<?php

namespace App\Console\Commands;

use App\Domains\Service\Models\Service;
use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignServiceImages extends Command
{
    protected $signature = 'services:assign-images {--dry-run : Preview without downloading} {--force : Clear existing paths and re-download all images}';

    protected $description = 'Download and assign Pexels stock images to services and categories that have no image';

    private array $photoMap = [
        'henna_pattern' => 4987381,
        'henna_mehndi' => 5602783,
        'henna_red' => 5309021,
        'hammam' => 33852650,
        'body_massage' => 5938555,
        'head_massage' => 8088603,
        'face_massage' => 6560280,
        'nail_art' => 5871831,
        'nail_removal' => 5870539,
        'hair_blowdry' => 7440133,
        'hair_styling' => 7984818,
        'hair_straightening' => 3738339,
        'hair_treatment' => 3993330,
        'scalp_care' => 8088603,
        'updo_hairstyle' => 8467969,
        'hair_mask_henna' => 3993330,
        'hair_extensions' => 3993449,
        'braiding' => 11482130,
        'eyebrow_threading' => 6135628,
        'waxing' => 4586713,
        'eyelash_tweezer' => 7755531,
        'eyelash' => 5128220,
        'face_mask' => 5069494,
        'incense' => 6954597,
        'oil_bottle' => 2814832,
        'spa_package' => 19641820,
        'incense_category' => 11434890,
    ];

    // ORDER MATTERS â€” more specific patterns first
    private array $serviceImageRules = [
        // ── الحناء بالصبغة ─────────────────────────────────
        'حناء للعروس' => 'henna_mehndi',
        'حناء برواز' => 'henna_pattern',
        'حناء شكل' => 'henna_pattern',
        'رسم يد' => 'henna_pattern',
        'نقش خفيف' => 'henna_pattern',
        'نقش من بره' => 'henna_mehndi',
        'نقش متوسط' => 'henna_mehndi',
        'نقش كثيف' => 'henna_mehndi',
        'تاتو ظهر' => 'henna_pattern',
        'عربون حجز حنه' => 'henna_pattern',
        'قمع حنه' => 'henna_pattern',

        // ── الحناء العادية الحمراء ──────────────────────────
        'حناء نشادر' => 'henna_red',
        'تقميع' => 'henna_red',
        'حنه حمرا' => 'henna_red',
        'نقش حناء' => 'henna_mehndi',
        'اقل من نص اليد' => 'henna_red',
        'نص اليد بره فقط' => 'henna_red',
        'نص اليد الوحده' => 'henna_red',
        'اليد للكوع' => 'henna_red',
        'رسم اليد الوحده' => 'henna_red',
        'الأرجل' => 'henna_red',
        'أيدي من بره' => 'henna_red',

        // ── الحمام المغربي ──────────────────────────────────
        'حمام مغربي' => 'hammam',
        'حمام دلكه' => 'body_massage',
        'حمام ملكي' => 'hammam',
        'دلكه سودانيه نص' => 'body_massage',

        // ── المساج ─────────────────────────────────────────
        'مساج رأس' => 'head_massage',
        'مساج الوجه و الرأس' => 'face_massage',
        'مساج ريلاكس' => 'body_massage',
        'مساج بالأحجار' => 'body_massage',
        'مساج بالأخشاب' => 'body_massage',
        'مساج قدمين' => 'body_massage',
        'مساج أكتاف' => 'body_massage',
        'مساج رقبة' => 'head_massage',
        'مساج يدين' => 'body_massage',
        'المساج بالحجامة' => 'body_massage',

        // ── الأظافر ─────────────────────────────────────────
        'بديكير مانكير' => 'nail_art',
        'بديكير رجل' => 'nail_art',
        'بديكير يد' => 'nail_art',
        'قص وبرد أظافر' => 'nail_art',
        'لون فرنسي' => 'nail_art',
        'لون مانكير' => 'nail_art',
        'أظافر إكريلك' => 'nail_art',
        'تركيب أظافر' => 'nail_art',
        'رسم على الأظافر' => 'nail_art',
        'إزالة لون جل' => 'nail_removal',
        'إزالة هارد جل' => 'nail_removal',
        'ازالة اظافر' => 'nail_removal',

        // ── الشعر ───────────────────────────────────────────
        'غسل شعر مع تصفيف' => 'hair_blowdry',
        'غسل شعر' => 'hair_blowdry',
        'حمام كريم' => 'hair_treatment',
        'حمام زيت' => 'hair_treatment',
        'قص أطراف' => 'hair_styling',
        'قص غرة' => 'hair_styling',
        'إستشوار ستريت' => 'hair_blowdry',
        'استشوار ستريت' => 'hair_blowdry',
        'استشوار سيراميك' => 'hair_straightening',
        'سيراميك أطراف' => 'hair_straightening',
        'سيراميك شعر' => 'hair_straightening',
        'غسل و سيراميك' => 'hair_straightening',
        'ويفي شعر' => 'hair_blowdry',
        'معالجات' => 'hair_treatment',
        'معالج لفرد' => 'hair_treatment',
        'فرد شعر' => 'hair_straightening',
        'تطهير فروه' => 'scalp_care',
        'تسريحة' => 'updo_hairstyle',
        'ماسك الحنا' => 'hair_mask_henna',
        'تضفير شعر مستعار' => 'hair_extensions',
        'فك خيوط' => 'hair_extensions',
        'تضفير' => 'braiding',
        'تركيب شعر خياطه' => 'hair_extensions',

        // ── الحواجب والبشرة ─────────────────────────────────
        'صبغة حواجب' => 'eyebrow_threading',
        'تشقير حواجب' => 'eyebrow_threading',
        'صبغة وتشقير' => 'eyebrow_threading',
        'قص حواجب' => 'eyebrow_threading',
        'حف حواجب' => 'eyebrow_threading',
        'حواجب ملقط' => 'eyebrow_threading',
        'مايكرو بليدنج' => 'eyebrow_threading',
        'إزالة شنب' => 'waxing',
        'ازالة شعر منطقة الذقن' => 'waxing',
        'حف وجه' => 'waxing',
        'ازالة رموش' => 'eyelash_tweezer',
        'تركيب رموش' => 'eyelash',
        'حف جسم' => 'waxing',
        'حف إبط' => 'waxing',
        'حف يدين' => 'waxing',
        'حف نص يدين' => 'waxing',
        'حف رجلين' => 'waxing',
        'حف ظهر' => 'waxing',
        'حف بطن' => 'waxing',
        'ماسك نضارة' => 'face_mask',
        'ماسك ترطيب' => 'face_mask',
        'ماسك نضاره' => 'face_mask',
        'ميني فيشيل' => 'face_mask',

        // ── بكجات جمال زوله ────────────────────────────────
        'بكج النضاره' => 'face_mask',
        'بكج عشاق الدلكه' => 'hammam',
        'بكج الأطفال' => 'nail_art',
        '4 جلسات ماسك الحنه' => 'hair_mask_henna',
        'بكج' => 'spa_package',

        // ── بيع بخور صندل سوداني ───────────────────────────
        'بخور' => 'incense',
        'دلكه سودانيه معطره' => 'body_massage',
        'دلكه سودانيه غير معطره' => 'body_massage',
        'دلكة فايتمينات' => 'body_massage',
        'كريم ترطيب و تصفيف' => 'hair_mask_henna',
        'زيت صندل' => 'oil_bottle',

        // ── Hair Care / Makeup / Nail / Facial (from ServiceSeeder) ──
        'قص الشعر' => 'hair_styling',
        'صبغ الشعر' => 'hair_treatment',
        'كيراتين' => 'hair_treatment',
        'مكياج سهرة' => 'face_mask',
        'مكياج عرائس' => 'face_mask',
        'مانيكير كلاسيك' => 'nail_art',
        'جل للأظافر' => 'nail_art',
        'تنظيف البشرة' => 'face_mask',
        'مساج الجسم الكامل' => 'body_massage',
        'حمام مغربي' => 'hammam',
    ];

    // Category name â†’ photo key
    private array $categoryImageRules = [
        'الحناء بالصبغة' => 'henna_mehndi',
        'الحناء العادية الحمراء' => 'henna_red',
        'الحمام المغربي' => 'hammam',
        'المساج' => 'body_massage',
        'الأظافر' => 'nail_art',
        'الشعر' => 'hair_blowdry',
        'الحواجب والبشرة' => 'eyebrow_threading',
        'بكجات جمال زوله' => 'spa_package',
        'بيع بخور صندل سوداني' => 'incense_category',
        'Hair Care' => 'hair_blowdry',
        'Makeup' => 'face_mask',
        'Nail Care' => 'nail_art',
        'Facial & Skin Care' => 'face_mask',
        'Spa & Relaxation' => 'body_massage',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force  = $this->option('force');

        $this->info($dryRun ? 'DRY RUN — no files will be downloaded.' : 'Starting image download and assignment...');

        if ($force && ! $dryRun) {
            Service::whereNotNull('image')->where('image', '!=', '')->update(['image' => null]);
            ServiceCategory::whereNotNull('image')->where('image', '!=', '')->update(['image' => null]);
            $this->info('Cleared existing image paths (--force).');
        }

        $downloaded = [];
        if (! $dryRun) {
            $downloaded = $this->downloadAllImages();
        }

        // ── Services ───────────────────────────────────────────
        $services = Service::whereNull('image')->orWhere('image', '')->get();
        $this->info("Found {$services->count()} services without images.");

        $assigned = 0;
        $skipped = 0;

        foreach ($services as $service) {
            $photoKey = $this->resolvePhotoKey($service->name_ar);

            if (! $photoKey) {
                $this->warn("  No rule for: {$service->name_ar}");
                $skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] {$service->name_ar} â†’ {$photoKey}");
                $assigned++;

                continue;
            }

            $path = $downloaded[$photoKey] ?? null;
            if (! $path) {
                $this->error("  Image not downloaded for key: {$photoKey}");
                $skipped++;

                continue;
            }

            $service->update(['image' => $path]);
            $this->line("  âœ“ {$service->name_ar}");
            $assigned++;
        }

        // â”€â”€ Categories â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $this->assignCategoryImages($dryRun, $downloaded);

        $this->newLine();
        $this->info("Done. Assigned: {$assigned} | Skipped: {$skipped}");

        return self::SUCCESS;
    }

    private function resolvePhotoKey(string $nameAr): ?string
    {
        foreach ($this->serviceImageRules as $pattern => $key) {
            if (str_contains($nameAr, $pattern)) {
                return $key;
            }
        }

        return null;
    }

    private function downloadAllImages(): array
    {
        $paths = [];

        $needed = array_unique(
            array_merge(
                array_values($this->serviceImageRules),
                array_values($this->categoryImageRules)
            )
        );

        foreach ($needed as $key) {
            if (! isset($this->photoMap[$key])) {
                continue;
            }

            $photoId = $this->photoMap[$key];
            $this->line("  Downloading [{$key}] photo #{$photoId} ...");

            $url = "https://images.pexels.com/photos/{$photoId}/pexels-photo-{$photoId}.jpeg?auto=compress&cs=tinysrgb&w=800";
            $filename = 'services/'.Str::uuid().'.jpg';

            try {
                $response = Http::timeout(30)->withoutVerifying()->get($url);

                if ($response->successful()) {
                    Storage::disk(config('filesystems.default'))->put($filename, $response->body());
                    $paths[$key] = $filename;
                    $this->info("    âœ“ Saved as {$filename}");
                } else {
                    $this->error("    âœ— HTTP {$response->status()} for photo #{$photoId}");
                }
            } catch (\Exception $e) {
                $this->error("    âœ— Error: {$e->getMessage()}");
            }
        }

        return $paths;
    }

    private function assignCategoryImages(bool $dryRun, array $downloaded): void
    {
        $categories = ServiceCategory::where(function ($q) {
            $q->whereNull('image')->orWhere('image', '');
        })->get();

        if ($categories->isEmpty()) {
            $this->newLine();
            $this->info('All categories already have images.');

            return;
        }

        $this->newLine();
        $this->info("Found {$categories->count()} categories without images.");

        foreach ($categories as $category) {
            $key = $this->categoryImageRules[$category->name_ar]
                ?? $this->categoryImageRules[$category->name_en]
                ?? null;

            if (! $key) {
                $this->warn("  No rule for category: {$category->name_ar}");

                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] {$category->name_ar} → {$key}");

                continue;
            }

            $path = $downloaded[$key] ?? null;
            if (! $path) {
                $this->error("  Image not downloaded for key: {$key}");

                continue;
            }

            $category->update(['image' => $path]);
            $this->line("  âœ“ Category: {$category->name_ar}");
        }
    }
}
