<?php

namespace App\Console\Commands;

use App\Domains\Service\Models\ServiceCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCategoryImages extends Command
{
    protected $signature = 'categories:import-images';

    protected $description = 'Import category images from the website';

    private array $imageMap = [
        'Ø§Ù„Ø­Ù†Ø§Ø¡ Ø¨Ø§Ù„ØµØ¨ØºØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D8%AD%D9%86%D8%A7%D8%A1_%D8%A8%D8%A7%D9%84%D8%B5%D8%A8%D8%BA%D8%A9_17681978507906968.webp',
        'Ø§Ù„Ø­Ù†Ø§Ø¡ Ø§Ù„Ø¹Ø§Ø¯ÙŠØ© Ø§Ù„Ø­Ù…Ø±Ø§Ø¡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D8%AD%D9%86%D8%A7%D8%A1_%D8%A7%D9%84%D8%B9%D8%A7%D8%AF%D9%8A%D8%A9_%D8%A7%D9%84%D8%AD%D9%85%D8%B1%D8%A7%D8%A1_17677082403616414.webp',
        'Ø§Ù„Ø­Ù…Ø§Ù… Ø§Ù„Ù…ØºØ±Ø¨ÙŠ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D8%AD%D9%85%D8%A7%D9%85_%D8%A7%D9%84%D9%85%D8%BA%D8%B1%D8%A8%D9%8A_17677082601191682.webp',
        'Ø§Ù„Ù…Ø³Ø§Ø¬' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D9%85%D8%B3%D8%A7%D8%AC_17677082839481196.webp',
        'Ø§Ù„Ø£Ø¸Ø§ÙØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_17677083070462856.webp',
        'Ø§Ù„Ø´Ø¹Ø±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D8%B4%D8%B9%D8%B1_17677083331655886.webp',
        'Ø§Ù„Ø­ÙˆØ§Ø¬Ø¨ ÙˆØ§Ù„Ø¨Ø´Ø±Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A7%D9%84%D8%AD%D9%88%D8%A7%D8%AC%D8%A8_%D9%88%D8%A7%D9%84%D8%A8%D8%B4%D8%B1%D9%87_17677083486017070.webp',
        'Ø¨ÙƒØ¬Ø§Øª Ø¬Ù…Ø§Ù„ Ø²ÙˆÙ„Ù‡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/categories/%D8%A8%D9%83%D8%AC__%D8%AC%D9%85%D8%A7%D9%84_%D8%B2%D9%88%D9%84%D9%87_17677083661967342.webp',
    ];

    public function handle(): void
    {
        Storage::disk(config('filesystems.default'))->makeDirectory('categories');

        $updated = 0;
        $failed = 0;
        $notFound = 0;

        foreach ($this->imageMap as $categoryName => $imageUrl) {
            $category = ServiceCategory::where('name_ar', $categoryName)->first();

            if (! $category) {
                $this->warn("Ù„Ù… ÙŠÙØ¹Ø«Ø± Ø¹Ù„Ù‰ Ø§Ù„ÙØ¦Ø©: {$categoryName}");
                $notFound++;

                continue;
            }

            try {
                $response = Http::timeout(30)->withoutVerifying()->get($imageUrl);

                if (! $response->successful()) {
                    $this->error("ÙØ´Ù„ ØªØ­Ù…ÙŠÙ„ ØµÙˆØ±Ø©: {$categoryName}");
                    $failed++;

                    continue;
                }

                $filename = 'categories/'.Str::uuid().'.webp';

                Storage::disk(config('filesystems.default'))->put($filename, $response->body());

                if ($category->image && Storage::disk(config('filesystems.default'))->exists($category->image)) {
                    Storage::disk(config('filesystems.default'))->delete($category->image);
                }

                $category->update(['image' => $filename]);

                $this->info("âœ“ {$categoryName}");
                $updated++;
            } catch (\Exception $e) {
                $this->error("خطأ في {$categoryName}: ".$e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Ù…ÙƒØªÙ…Ù„ â€” ØªÙ… Ø§Ù„ØªØ­Ø¯ÙŠØ«: {$updated} | ÙØ´Ù„: {$failed} | ØºÙŠØ± Ù…ÙˆØ¬ÙˆØ¯: {$notFound}");
    }
}
