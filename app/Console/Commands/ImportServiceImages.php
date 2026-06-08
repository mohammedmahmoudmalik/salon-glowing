<?php

namespace App\Console\Commands;

use App\Domains\Service\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportServiceImages extends Command
{
    protected $signature = 'services:import-images';

    protected $description = 'Import service images from the website';

    private array $imageMap = [
        'Ø­Ù†Ø§Ø¡ Ø¨Ø±ÙˆØ§Ø²' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D8%A8%D8%B1%D9%88%D8%A7%D8%B2_17697092383288856.webp.150x150_q100_crop.webp',
        'Ø­Ù†Ø§Ø¡ Ø´ÙƒÙ„ ÙˆØ³Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D8%B4%D9%83%D9%84_%D9%88%D8%B3%D8%B7_17697092463603254.webp.150x150_q100_crop.webp',
        'Ø­Ù†Ø§Ø¡ Ø´ÙƒÙ„ Ø¹Ø§Ù„ÙŠ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D8%B4%D9%83%D9%84_%D8%B9%D8%A7%D9%84%D9%8A_17697092410872094.webp.150x150_q100_crop.webp',
        'Ø­Ù†Ø§Ø¡ Ù„Ù„Ø¹Ø±ÙˆØ³' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D9%84%D9%84%D8%B9%D8%B1%D9%88%D8%B3_17681912389787540.webp.150x150_q100_crop.webp',
        'Ø­Ù†Ø§Ø¡ Ù„Ù„Ø¹Ø±ÙˆØ³ VIP' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D9%84%D9%84%D8%B9%D8%B1%D9%88%D8%B3_17681403968258186.webp.150x150_q100_crop.webp',
        'Ø±Ø³Ù… ÙŠØ¯ Ø¨Ù†Ø§Øª ØµØ¨ØºØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B1%D8%B3%D9%85_%D9%8A%D8%AF_%D8%A8%D9%86%D8%A7%D8%AA_%D8%B5%D8%A8%D8%BA%D8%A9_17697092555563764.webp.150x150_q100_crop.webp',
        'Ø±Ø³Ù… ÙŠØ¯ Ø£Ø·ÙØ§Ù„ ØµØ¨ØºØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B1%D8%B3%D9%85_%D9%8A%D8%AF_%D8%A3%D8%B7%D9%81%D8%A7%D9%84_%D8%B5%D8%A8%D8%BA%D8%A9_17697092572213786.webp.150x150_q100_crop.webp',
        'Ø­Ù†Ø§Ø¡ Ù†Ø´Ø§Ø¯Ø± Ø´ÙƒÙ„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D8%AD%D9%85%D8%B1%D8%A7%D8%A1_%D9%86%D8%B4%D8%A7%D8%AF%D8%B1_17681407670270770.webp.150x150_q100_crop.webp',
        'Ø­Ù†Ø§Ø¡ Ù†Ø´Ø§Ø¯Ø± Ø³Ø§Ø¯Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%86%D8%A7%D8%A1_%D9%86%D8%B4%D8%A7%D8%AF%D8%B1_%D8%B3%D8%A7%D8%AF%D8%A9_17697092395700664.webp.150x150_q100_crop.webp',
        'Ù†Ù‚Ø´ Ø­Ù†Ø§Ø¡ Ø®ÙÙŠÙ Ø§Ù„ÙƒÙ Ù…Ù† Ø¨Ø±Ù‡ ÙÙ‚Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%86%D9%82%D8%B4_%D8%AD%D9%86%D8%A7%D8%A1_%D8%A7%D9%84%D9%83%D9%81_%D9%85%D9%86_%D8%A8%D8%B1%D9%87_%D9%81%D9%82%D8%B7_17697092454483888.webp.150x150_q100_crop.webp',
        'Ù†Ù‚Ø´ Ø­Ù†Ø§Ø¡ Ø£ØµØ§Ø¨Ø¹ Ù…Ø¹ Ø§Ù„ÙƒÙ Ù…Ù† Ø¨Ø±Ù‡ ÙˆÙ…Ù† Ø¬ÙˆÙ‡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%86%D9%82%D8%B4_%D8%AD%D9%86%D8%A7%D8%A1_%D8%A7%D9%84%D9%83%D9%81_%D9%85%D9%86_%D8%A8%D8%B1%D9%87_%D9%88%D9%85%D9%86_%D8%AC%D9%88%D9%87_17697092498221410.webp.150x150_q100_crop.webp',
        'Ø§Ù‚Ù„ Ù…Ù† Ù†Øµ Ø§Ù„ÙŠØ¯ Ø¨Ø±Ù‡ ÙˆØ¬ÙˆÙ‡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%86%D8%B5_%D8%A7%D9%84%D9%8A%D8%AF_%D8%A8%D8%B1%D9%87_%D9%88%D8%AC%D9%88%D9%87_17697092510330916.webp.150x150_q100_crop.webp',
        'Ù†Øµ Ø§Ù„ÙŠØ¯ Ø¨Ø±Ù‡ ÙÙ‚Ø· Ù†Ù‚Ø´ Ù…Ù„ÙŠØ§Ù†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%86%D8%B5_%D8%A7%D9%84%D9%8A%D8%AF_%D8%A8%D8%B1%D9%87_%D9%81%D9%82%D8%B7_17697092580157082.webp.150x150_q100_crop.webp',
        'Ø§Ù„ÙŠØ¯ Ù„Ù„ÙƒÙˆØ¹ Ù…Ù† Ø¨Ø±Ù‡ ÙÙ‚Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A7%D9%84%D9%8A%D8%AF_%D9%84%D9%84%D9%83%D9%88%D8%B9_%D9%85%D9%86_%D8%A8%D8%B1%D9%87_%D9%81%D9%82%D8%B7_17697092549951244.webp.150x150_q100_crop.webp',
        'Ø±Ø³Ù… Ø§Ù„ÙŠØ¯ Ø§Ù„ÙˆØ­Ø¯Ù‡ Ù„Ù„ÙƒÙˆØ¹ Ù…Ù† Ø¨Ø±Ù‡ ÙˆØ¬ÙˆÙ‡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A7%D9%84%D9%8A%D8%AF_%D9%84%D9%84%D9%83%D9%88%D8%B9_%D9%85%D9%86_%D8%A8%D8%B1%D9%87_%D9%88%D8%AC%D9%88%D9%87_17697092599827786.webp.150x150_q100_crop.webp',
        'Ø§Ù„Ø£Ø±Ø¬Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A7%D9%84%D8%A3%D8%B1%D8%AC%D9%84_17697092610364302.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ù…ØºØ±Ø¨ÙŠ ØªÙ‚Ù„ÙŠØ¯ÙŠ Ø¹Ø§Ø¯ÙŠ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D9%85%D8%BA%D8%B1%D8%A8%D9%8A_%D8%AA%D9%82%D9%84%D9%8A%D8%AF%D9%8A_%D8%B9%D8%A7%D8%AF%D9%8A_17697092356687308.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ø¯Ù„ÙƒÙ‡ Ø³ÙˆØ¯Ø§Ù†ÙŠØ© Ø¬Ø³Ù… ÙƒØ§Ù…Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D8%AF%D9%84%D9%83%D9%87_%D8%B3%D9%88%D8%AF%D8%A7%D9%86%D9%8A%D8%A9_17697092400318988.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ù…ØºØ±Ø¨ÙŠ Ù…Ø¹ Ø³ÙƒØ±Ø§Ø¨ ÙƒØ§Ù…Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D9%85%D8%BA%D8%B1%D8%A8%D9%8A_%D9%85%D8%B9_%D8%B3%D9%83%D8%B1%D8%A7%D8%A8_%D9%83%D8%A7%D9%85%D9%84_17697092442451454.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ù…ØºØ±Ø¨ÙŠ Ø³ÙˆØ¯Ø§Ù†ÙŠ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D9%85%D8%BA%D8%B1%D8%A8%D9%8A_%D8%B3%D9%88%D8%AF%D8%A7%D9%86%D9%8A_17697092485529612.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ù…Ù„ÙƒÙŠ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D9%85%D9%84%D9%83%D9%8A_17697092537154388.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ø±ÙŠÙ„Ø§ÙƒØ³ Ø¹Ø§Ø¯ÙŠ 30 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D8%B1%D9%8A%D9%84%D8%A7%D9%83%D8%B3_%D8%B9%D8%A7%D8%AF%D9%8A_30_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092377624450.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ø±ÙŠÙ„Ø§ÙƒØ³ Ø¹Ø§Ø¯ÙŠ 60 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D8%B1%D9%8A%D9%84%D8%A7%D9%83%D8%B3_%D8%B9%D8%A7%D8%AF%D9%8A_60_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092414777712.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ø¨Ø§Ù„Ø£Ø­Ø¬Ø§Ø± 60 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D8%A8%D8%A7%D9%84%D8%A3%D8%AD%D8%AC%D8%A7%D8%B1_60_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092438707924.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ø¨Ø§Ù„Ø£Ø®Ø´Ø§Ø¨ 60 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D8%A8%D8%A7%D9%84%D8%A3%D8%AE%D8%B4%D8%A7%D8%A8_60_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092502312030.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ù‚Ø¯Ù…ÙŠÙ† ÙÙ‚Ø· 30 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D9%82%D8%AF%D9%85%D9%8A%D9%86_%D9%81%D9%82%D8%B7_30_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092506015926.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ù‚Ø¯Ù…ÙŠÙ† ÙÙ‚Ø· 10 Ø¯Ù‚Ø§Ø¦Ù‚' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D9%82%D8%AF%D9%85%D9%8A%D9%86_%D9%81%D9%82%D8%B7_10_%D8%AF%D9%82%D8%A7%D8%A6%D9%82_17697092541135408.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ø£ÙƒØªØ§Ù 30 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D8%A3%D9%83%D8%AA%D8%A7%D9%81_30_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092575845270.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ Ø±Ù‚Ø¨Ø© 30 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D8%B1%D9%82%D8%A8%D8%A9_30_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092592610586.webp.150x150_q100_crop.webp',
        'Ù…Ø³Ø§Ø¬ ÙŠØ¯ÙŠÙ† Ø£Ùˆ Ø±Ø¬Ù„ÙŠÙ† 30 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B3%D8%A7%D8%AC_%D9%8A%D8%AF%D9%8A%D9%86_%D9%81%D9%82%D8%B7_%D8%A3%D9%88_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_%D9%81%D9%82%D8%B7_30_%D8%AF%D9%82%D9%8A%D9%82%D8%A9_17697092604751752.webp.150x150_q100_crop.webp',
        'Ø§Ù„Ù…Ø³Ø§Ø¬ Ø¨Ø§Ù„Ø­Ø¬Ø§Ù…Ø© 60 Ø¯Ù‚ÙŠÙ‚Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A7%D9%84%D9%85%D8%B3%D8%A7%D8%AC_%D8%A8%D8%A7%D9%84%D8%AD%D8%AC%D8%A7%D9%85%D8%A9__60_%D8%AF%D9%82%D9%8A%D9%82%D9%87_17697092644526394.webp.150x150_q100_crop.webp',
        'Ø¨Ø¯ÙŠÙƒÙŠØ± Ù…Ø§Ù†ÙƒÙŠØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D8%AF%D9%8A%D9%83%D9%8A%D8%B1_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_17697092387344486.webp.150x150_q100_crop.webp',
        'Ø¨Ø¯ÙŠÙƒÙŠØ± Ø±Ø¬Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D8%AF%D9%8A%D9%83%D9%8A%D8%B1_%D8%B1%D8%AC%D9%84_17697092405919198.webp.150x150_q100_crop.webp',
        'Ø¨Ø¯ÙŠÙƒÙŠØ± ÙŠØ¯' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D8%AF%D9%8A%D9%83%D9%8A%D8%B1_%D9%8A%D8%AF_17697092459302568.webp.150x150_q100_crop.webp',
        'Ù‚Øµ ÙˆØ¨Ø±Ø¯ Ø£Ø¸Ø§ÙØ± ÙŠØ¯ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%82%D8%B5_%D9%88%D8%A8%D8%B1%D8%AF_%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_%D9%8A%D8%AF%D9%8A%D9%86_17697092468276320.webp.150x150_q100_crop.webp',
        'Ù‚Øµ ÙˆØ¨Ø±Ø¯ Ø£Ø¸Ø§ÙØ± Ø±Ø¬Ù„ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%82%D8%B5_%D9%88%D8%A8%D8%B1%D8%AF_%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_17697092531766398.webp.150x150_q100_crop.webp',
        'Ù„ÙˆÙ† ÙØ±Ù†Ø³ÙŠ ÙŠØ¯ÙŠÙ† Ø£Ùˆ Ø±Ø¬Ù„ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%84%D9%88%D9%86_%D9%81%D8%B1%D9%86%D8%B3%D9%8A_%D9%8A%D8%AF%D9%8A%D9%86_%D9%81%D9%82%D8%B7_%D8%A3%D9%88_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_%D9%81%D9%82%D8%B7_17697092560776054.webp.150x150_q100_crop.webp',
        'Ù„ÙˆÙ† Ù…Ø§Ù†ÙƒÙŠØ± Ø¹Ø§Ø¯ÙŠ ÙŠØ¯ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%84%D9%88%D9%86_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_%D8%B9%D8%A7%D8%AF%D9%8A_%D9%8A%D8%AF%D9%8A%D9%86_17697092565492072.webp.150x150_q100_crop.webp',
        'Ù„ÙˆÙ† Ù…Ø§Ù†ÙƒÙŠØ± Ø¹Ø§Ø¯ÙŠ Ø±Ø¬Ù„ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%84%D9%88%D9%86_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_%D8%B9%D8%A7%D8%AF%D9%8A_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_17697092588388760.webp.150x150_q100_crop.webp',
        'Ù„ÙˆÙ† Ù…Ø§Ù†ÙƒÙŠØ± Ø¬Ù„ ÙŠØ¯ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%84%D9%88%D9%86_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_%D8%AC%D9%84_%D9%8A%D8%AF%D9%8A%D9%86_17697092624917604.webp.150x150_q100_crop.webp',
        'Ù„ÙˆÙ† Ù…Ø§Ù†ÙƒÙŠØ± Ø¬Ù„ Ø±Ø¬Ù„ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%84%D9%88%D9%86_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_%D8%AC%D9%84_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_17697092636518772.webp.150x150_q100_crop.webp',
        'ØªØ±ÙƒÙŠØ¨ Ø£Ø¸Ø§ÙØ± Ù…Ø¹ Ù„ÙˆÙ† (Extention)' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AA%D8%B1%D9%83%D9%8A%D8%A8_%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_%D9%85%D8%B9_%D9%84%D9%88%D9%86_extention_17697092655005352.webp.150x150_q100_crop.webp',
        'Ø£Ø¸Ø§ÙØ± Ø¥ÙƒØ±ÙŠÙ„Ùƒ Ø£Ùˆ ÙØ§ÙŠØ¨Ø± Ø¬Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_%D8%A5%D9%83%D8%B1%D9%8A%D9%84%D9%83_%D8%A3%D9%88_%D9%81%D8%A7%D9%8A%D8%A8%D8%B1_%D8%AC%D9%84_17697092674419652.webp.150x150_q100_crop.webp',
        'Ø¥Ø²Ø§Ù„Ø© Ù„ÙˆÙ† Ø¬Ù„ ÙŠØ¯ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B2%D8%A7%D9%84%D8%A9_%D9%84%D9%88%D9%86_%D8%AC%D9%84_%D9%8A%D8%AF%D9%8A%D9%86_17697092682023864.webp.150x150_q100_crop.webp',
        'Ø¥Ø²Ø§Ù„Ø© Ù‡Ø§Ø±Ø¯ Ø¬Ù„ Ø£Ø¸Ø§ÙØ± ÙŠØ¯ÙŠÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B2%D8%A7%D9%84%D8%A9__%D9%87%D8%A7%D8%B1%D8%AF_%D8%AC%D9%84_%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_%D9%8A%D8%AF%D9%8A%D9%86_17697092698466236.webp.150x150_q100_crop.webp',
        'Ø¨Ø¯ÙŠÙƒÙŠØ± Ù…Ø§Ù†ÙƒÙŠØ± Ø£Ø·ÙØ§Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D8%AF%D9%8A%D9%83%D9%8A%D8%B1_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_%D8%A3%D8%B7%D9%81%D8%A7%D9%84_17697092708408850.webp.150x150_q100_crop.webp',
        'Ù„ÙˆÙ† Ù…Ø§Ù†ÙƒÙŠØ± ÙŠØ¯ÙŠÙ† Ø£Ø·ÙØ§Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%84%D9%88%D9%86_%D9%85%D8%A7%D9%86%D9%83%D9%8A%D8%B1_%D9%8A%D8%AF%D9%8A%D9%86_%D8%A3%D8%B7%D9%81%D8%A7%D9%84_17697092713300002.webp.150x150_q100_crop.webp',
        'ØªØ±ÙƒÙŠØ¨ Ø£Ø¸Ø§ÙØ± Ù„Ù„Ø£Ø·ÙØ§Ù„ Ù…Ø¹ Ù„ÙˆÙ†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AA%D8%B1%D9%83%D9%8A%D8%A8_%D8%A3%D8%B8%D8%A7%D9%81%D8%B1_%D9%84%D9%84%D8%A3%D8%B7%D9%81%D8%A7%D9%84_%D9%85%D8%B9_%D9%84%D9%88%D9%86_17697092734359688.webp.150x150_q100_crop.webp',
        'ØºØ³Ù„ Ø´Ø¹Ø±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%BA%D8%B3%D9%84_%D8%B4%D8%B9%D8%B1_17697092390946550.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… ÙƒØ±ÙŠÙ… Ø¹Ø§Ø¯ÙŠ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D9%83%D8%B1%D9%8A%D9%85_%D8%B9%D8%A7%D8%AF%D9%8A_17697092425139132.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… ÙƒØ±ÙŠÙ… VIP' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D9%83%D8%B1%D9%8A%D9%85_vip_17697092450324910.webp.150x150_q100_crop.webp',
        'Ù‚Øµ Ø£Ø·Ø±Ø§Ù' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%82%D8%B5_%D8%A3%D8%B7%D8%B1%D8%A7%D9%81_17697092490105974.webp.150x150_q100_crop.webp',
        'Ù‚Øµ ØºØ±Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%82%D8%B5_%D8%BA%D8%B1%D8%A9_17697092527104594.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ø²ÙŠØª Ø¨Ø±ÙˆÙ„ Ø§Ù„Ø­Ø¯ÙŠØ¯ Ù„Ù„Ø´Ø¹Ø± Ø§Ù„Ù‚ØµÙŠØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D8%B2%D9%8A%D8%AA_%D8%A8%D8%B1%D9%88%D9%84_%D8%A7%D9%84%D8%AD%D8%AF%D9%8A%D8%AF_%D9%84%D9%84%D8%B4%D8%B9%D8%B1_%D8%A7%D9%84%D9%82%D8%B5%D9%8A%D8%B1_17697092568295814.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ø²ÙŠØª Ø¨Ø±ÙˆÙ„ Ø§Ù„Ø­Ø¯ÙŠØ¯ Ù„Ù„Ø´Ø¹Ø± Ø§Ù„ÙˆØ³Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D8%B2%D9%8A%D8%AA_%D8%A8%D8%B1%D9%88%D9%84_%D8%A7%D9%84%D8%AD%D8%AF%D9%8A%D8%AF_%D9%84%D9%84%D8%B4%D8%B9%D8%B1_%D8%A7%D9%84%D9%88%D8%B3%D8%B7_17697092595740888.webp.150x150_q100_crop.webp',
        'Ø­Ù…Ø§Ù… Ø²ÙŠØª Ø¨Ø±ÙˆÙ„ Ø§Ù„Ø­Ø¯ÙŠØ¯ Ù„Ù„Ø´Ø¹Ø± Ø§Ù„Ø·ÙˆÙŠÙ„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%85%D8%A7%D9%85_%D8%B2%D9%8A%D8%AA_%D8%A8%D8%B1%D9%88%D9%84_%D8%A7%D9%84%D8%AD%D8%AF%D9%8A%D8%AF_%D9%84%D9%84%D8%B4%D8%B9%D8%B1_%D8%A7%D9%84%D8%B7%D9%88%D9%8A%D9%84_17697092615245008.webp.150x150_q100_crop.webp',
        'Ø¥Ø³ØªØ´ÙˆØ§Ø± Ø³ØªØ±ÙŠØª Ù‚ØµÙŠØ± Ø¬Ø¯Ø§' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B3%D8%AA%D8%B4%D9%88%D8%A7%D8%B1_%D8%B3%D8%AA%D8%B1%D9%8A%D8%AA_%D9%82%D8%B5%D9%8A%D8%B1_%D8%AC%D8%AF%D8%A7_17697092633092176.webp.150x150_q100_crop.webp',
        'Ø§Ø³ØªØ´ÙˆØ§Ø± Ø³ØªØ±ÙŠØª Ù‚ØµÙŠØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B3%D8%AA%D8%B4%D9%88%D8%A7%D8%B1_%D8%B3%D8%AA%D8%B1%D9%8A%D8%AA_%D9%82%D8%B5%D9%8A%D8%B1_17697092648078654.webp.150x150_q100_crop.webp',
        'Ø§Ø³ØªØ´ÙˆØ§Ø± Ø³ØªØ±ÙŠØª ÙˆØ³Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B3%D8%AA%D8%B4%D9%88%D8%A7%D8%B1_%D8%B3%D8%AA%D8%B1%D9%8A%D8%AA_%D9%88%D8%B3%D8%B7_17697092670249804.webp.150x150_q100_crop.webp',
        'Ø¥Ø³ØªØ´ÙˆØ§Ø± Ø³ØªØ±ÙŠØª ÙˆØ³Ø· ÙƒØ«ÙŠÙ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B3%D8%AA%D8%B4%D9%88%D8%A7%D8%B1_%D8%B3%D8%AA%D8%B1%D9%8A%D8%AA_%D9%88%D8%B3%D8%B7_%D9%83%D8%AB%D9%8A%D9%81_17697092678273940.webp.150x150_q100_crop.webp',
        'Ø¥Ø³ØªØ´ÙˆØ§Ø± Ø³ØªØ±ÙŠØª Ø·ÙˆÙŠÙ„ Ø®ÙÙŠÙ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B3%D8%AA%D8%B4%D9%88%D8%A7%D8%B1_%D8%B3%D8%AA%D8%B1%D9%8A%D8%AA_%D8%B7%D9%88%D9%8A%D9%84_%D8%AE%D9%81%D9%8A%D9%81_17697092690875922.webp.150x150_q100_crop.webp',
        'Ø¥Ø³ØªØ´ÙˆØ§Ø± Ø³ØªØ±ÙŠØª Ø·ÙˆÙŠÙ„ ÙƒØ«ÙŠÙ' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B3%D8%AA%D8%B4%D9%88%D8%A7%D8%B1_%D8%B3%D8%AA%D8%B1%D9%8A%D8%AA_%D8%B7%D9%88%D9%8A%D9%84_%D9%83%D8%AB%D9%8A%D9%81_17697092705754962.webp.150x150_q100_crop.webp',
        'Ø³ÙŠØ±Ø§Ù…ÙŠÙƒ Ø´Ø¹Ø± Ù‚ØµÙŠØ± Ø¬Ø¯Ø§' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B3%D9%8A%D8%B1%D8%A7%D9%85%D9%8A%D9%83_%D8%B4%D8%B9%D8%B1_%D9%82%D8%B5%D9%8A%D8%B1_%D8%AC%D8%AF%D8%A7_17697092718238222.webp.150x150_q100_crop.webp',
        'Ø³ÙŠØ±Ø§Ù…ÙŠÙƒ Ø´Ø¹Ø± Ù‚ØµÙŠØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B3%D9%8A%D8%B1%D8%A7%D9%85%D9%8A%D9%83_%D8%B4%D8%B9%D8%B1_%D9%82%D8%B5%D9%8A%D8%B1_17697092740309432.webp.150x150_q100_crop.webp',
        'Ø³ÙŠØ±Ø§Ù…ÙŠÙƒ Ø´Ø¹Ø± ÙˆØ³Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B3%D9%8A%D8%B1%D8%A7%D9%85%D9%8A%D9%83_%D8%B4%D8%B9%D8%B1_%D9%88%D8%B3%D8%B7_17697092756156194.webp.150x150_q100_crop.webp',
        'Ø³ÙŠØ±Ø§Ù…ÙŠÙƒ Ø´Ø¹Ø± Ø·ÙˆÙŠÙ„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B3%D9%8A%D8%B1%D8%A7%D9%85%D9%8A%D9%83_%D8%B4%D8%B9%D8%B1_%D8%B7%D9%88%D9%8A%D9%84_17697092764253452.webp.150x150_q100_crop.webp',
        'Ø³ÙŠØ±Ø§Ù…ÙŠÙƒ Ø´Ø¹Ø± Ø·ÙˆÙŠÙ„ Ø¬Ø¯Ø§' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B3%D9%8A%D8%B1%D8%A7%D9%85%D9%8A%D9%83_%D8%B4%D8%B9%D8%B1_%D8%B7%D9%88%D9%8A%D9%84_%D8%AC%D8%AF%D8%A7_17697092776204812.webp.150x150_q100_crop.webp',
        'ÙˆÙŠÙÙŠ Ø´Ø¹Ø± Ù‚ØµÙŠØ± Ø¬Ø¯Ø§' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%88%D9%8A%D9%81%D9%8A_%D8%B4%D8%B9%D8%B1_%D9%82%D8%B5%D9%8A%D8%B1_%D8%AC%D8%AF%D8%A7_17697092800524998.webp.150x150_q100_crop.webp',
        'ÙˆÙŠÙÙŠ Ø´Ø¹Ø± Ù‚ØµÙŠØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%88%D9%8A%D9%81%D9%8A_%D8%B4%D8%B9%D8%B1_%D9%82%D8%B5%D9%8A%D8%B1_17697092804847162.webp.150x150_q100_crop.webp',
        'ÙˆÙŠÙÙŠ Ø´Ø¹Ø± ÙˆØ³Ø·' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%88%D9%8A%D9%81%D9%8A_%D8%B4%D8%B9%D8%B1_%D9%88%D8%B3%D8%B7_17697092812568230.webp.150x150_q100_crop.webp',
        'ÙˆÙŠÙÙŠ Ø´Ø¹Ø± Ø·ÙˆÙŠÙ„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%88%D9%8A%D9%81%D9%8A_%D8%B4%D8%B9%D8%B1_%D8%B7%D9%88%D9%8A%D9%84_17697092822190616.webp.150x150_q100_crop.webp',
        'ÙˆÙŠÙÙŠ Ø´Ø¹Ø± Ø·ÙˆÙŠÙ„ Ø¬Ø¯Ø§' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%88%D9%8A%D9%81%D9%8A_%D8%B4%D8%B9%D8%B1_%D8%B7%D9%88%D9%8A%D9%84_%D8%AC%D8%AF%D8%A7_17697092831471580.webp.150x150_q100_crop.webp',
        'Ù…Ø¹Ø§Ù„Ø¬Ø§Øª (Ø¨Ø±ÙˆØªÙŠÙ†)' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%B9%D8%A7%D9%84%D8%AC%D8%A7%D8%AA_%D8%A8%D8%B1%D9%88%D8%AA%D9%8A%D9%86_17697092889311936.webp.150x150_q100_crop.webp',
        'ØµØ¨ØºØ© Ø­ÙˆØ§Ø¬Ø¨' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B5%D8%A8%D8%BA%D8%A9_%D8%AD%D9%88%D8%A7%D8%AC%D8%A8_17697092366583658.webp.150x150_q100_crop.webp',
        'ØªØ´Ù‚ÙŠØ± Ø­ÙˆØ§Ø¬Ø¨' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AA%D8%B4%D9%82%D9%8A%D8%B1_%D8%AD%D9%88%D8%A7%D8%AC%D8%A8_17697092419323854.webp.150x150_q100_crop.webp',
        'ØµØ¨ØºØ© ÙˆØªØ´Ù‚ÙŠØ±' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%B5%D8%A8%D8%BA%D8%A9_%D9%88%D8%AA%D8%B4%D9%82%D9%8A%D8%B1_17697092446195750.webp.150x150_q100_crop.webp',
        'Ù‚Øµ Ø­ÙˆØ§Ø¬Ø¨' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%82%D8%B5_%D8%AD%D9%88%D8%A7%D8%AC%D8%A8_17697092481080436.webp.150x150_q100_crop.webp',
        'Ù…Ø§ÙŠÙƒØ±Ùˆ Ø¨Ù„ÙŠØ¯Ù†Ø¬ Ø­ÙˆØ§Ø¬Ø¨' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%A7%D9%8A%D9%83%D8%B1%D9%88_%D8%A8%D9%84%D9%8A%D8%AF%D9%86%D8%AC_%D8%AD%D9%88%D8%A7%D8%AC%D8%A8_17697092584169344.webp.150x150_q100_crop.webp',
        'Ø¥Ø²Ø§Ù„Ø© Ø´Ù†Ø¨ ÙÙ‚Ø· Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A5%D8%B2%D8%A7%D9%84%D8%A9_%D8%B4%D9%86%D8%A8_%D9%81%D9%82%D8%B7_%D8%B4%D9%85%D8%B9_17697092640501716.webp.150x150_q100_crop.webp',
        'Ø­Ù ÙˆØ¬Ù‡ ÙƒØ§Ù…Ù„ Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%88%D8%AC%D9%87_%D9%83%D8%A7%D9%85%D9%84_%D8%B4%D9%85%D8%B9_17697092658451036.webp.150x150_q100_crop.webp',
        'Ø­Ù ÙˆØ¬Ù‡ ÙƒØ§Ù…Ù„ Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%88%D8%AC%D9%87_%D9%83%D8%A7%D9%85%D9%84_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092687326484.webp.150x150_q100_crop.webp',
        'Ø­Ù ÙˆØ¬Ù‡ ÙƒØ§Ù…Ù„ Ø´ÙØ±Ø©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%88%D8%AC%D9%87_%D9%83%D8%A7%D9%85%D9%84_%D8%B4%D9%81%D8%B1%D8%A9_17697092694525742.webp.150x150_q100_crop.webp',
        'ØªØ±ÙƒÙŠØ¨ Ø±Ù…ÙˆØ´ Ø¹Ø§Ø¯ÙŠØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AA%D8%B1%D9%83%D9%8A%D8%A8_%D8%B1%D9%85%D9%88%D8%B4_%D8%B9%D8%A7%D8%AF%D9%8A%D8%A9_17697092723219550.webp.150x150_q100_crop.webp',
        'ØªØ±ÙƒÙŠØ¨ Ø±Ù…ÙˆØ´ Ø­Ø¨Ù‡ Ø­Ø¨Ù‡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AA%D8%B1%D9%83%D9%8A%D8%A8_%D8%B1%D9%85%D9%88%D8%B4_%D8%AD%D8%A8%D9%87_%D8%AD%D8%A8%D9%87_17697092702137756.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¬Ø³Ù… ÙƒØ§Ù…Ù„ Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%AC%D8%B3%D9%85_%D9%83%D8%A7%D9%85%D9%84_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092728343750.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¬Ø³Ù… ÙƒØ§Ù…Ù„ Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%AC%D8%B3%D9%85_%D9%83%D8%A7%D9%85%D9%84_%D8%B4%D9%85%D8%B9_17697092744696626.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¬Ø³Ù… Ø­Ù„Ø§ÙˆØ© Ø¨Ø¯ÙˆÙ† Ø¸Ù‡Ø± ÙˆØ¨Ø·Ù†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%AC%D8%B3%D9%85_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_%D8%A8%D8%AF%D9%88%D9%86_%D8%B8%D9%87%D8%B1_%D9%88%D8%A8%D8%B7%D9%86_17697092748946982.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¬Ø³Ù… Ø´Ù…Ø¹ Ø¨Ø¯ÙˆÙ† Ø¸Ù‡Ø± ÙˆØ¨Ø·Ù†' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%AC%D8%B3%D9%85__%D8%B4%D9%85%D8%B9_%D8%A8%D8%AF%D9%88%D9%86_%D8%B8%D9%87%D8%B1_%D9%88%D8%A8%D8%B7%D9%86_17697092752320630.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¥Ø¨Ø· Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%A5%D8%A8%D8%B7_%D8%B4%D9%85%D8%B9_17697092759725390.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¥Ø¨Ø· Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%A5%D8%A8%D8%B7_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092788519886.webp.150x150_q100_crop.webp',
        'Ø­Ù ÙŠØ¯ÙŠÙ† ÙƒØ§Ù…Ù„Ø© Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%8A%D8%AF%D9%8A%D9%86_%D9%83%D8%A7%D9%85%D9%84%D8%A9_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_%D9%85%D8%B9_%D8%A7%D9%84%D8%A5%D8%A8%D8%B7_17697092769012446.webp.150x150_q100_crop.webp',
        'Ø­Ù ÙŠØ¯ÙŠÙ† ÙƒØ§Ù…Ù„Ø© Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%8A%D8%AF%D9%8A%D9%86_%D9%83%D8%A7%D9%85%D9%84%D8%A9_%D8%B4%D9%85%D8%B9_%D8%A8%D8%AF%D9%88%D9%86_%D8%A7%D9%84%D8%A5%D8%A8%D8%B7_17697092808264316.webp.150x150_q100_crop.webp',
        'Ø­Ù Ù†Øµ ÙŠØ¯ÙŠÙ† Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%86%D8%B5_%D9%8A%D8%AF%D9%8A%D9%86_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092818670862.webp.150x150_q100_crop.webp',
        'Ø­Ù Ù†Øµ ÙŠØ¯ÙŠÙ† Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D9%86%D8%B5_%D9%8A%D8%AF%D9%8A%D9%86_%D8%B4%D9%85%D8%B9_17697092825873632.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø±Ø¬Ù„ÙŠÙ† ÙƒØ§Ù…Ù„Ø© Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_%D9%83%D8%A7%D9%85%D9%84%D8%A9_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092837039868.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø±Ø¬Ù„ÙŠÙ† Ù†Øµ Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_%D9%86%D8%B5_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092842949364.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø±Ø¬Ù„ÙŠÙ† ÙƒØ§Ù…Ù„Ø© Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_%D9%83%D8%A7%D9%85%D9%84%D8%A9_%D8%B4%D9%85%D8%B9_17697092852781928.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø±Ø¬Ù„ÙŠÙ† Ù†Øµ Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%B1%D8%AC%D9%84%D9%8A%D9%86_%D9%86%D8%B5_%D8%B4%D9%85%D8%B9_17697092859212842.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¸Ù‡Ø± Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%B8%D9%87%D8%B1_%D8%B4%D9%85%D8%B9_17697092875106078.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¸Ù‡Ø± Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%B8%D9%87%D8%B1_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092884463760.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¨Ø·Ù† Ø´Ù…Ø¹' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%A8%D8%B7%D9%86_%D8%B4%D9%85%D8%B9_17697092899803590.webp.150x150_q100_crop.webp',
        'Ø­Ù Ø¨Ø·Ù† Ø­Ù„Ø§ÙˆØ©' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%AD%D9%81_%D8%A8%D8%B7%D9%86_%D8%AD%D9%84%D8%A7%D9%88%D8%A9_17697092868225642.webp.150x150_q100_crop.webp',
        'Ù…Ø§Ø³Ùƒ Ù†Ø¶Ø§Ø±Ø© Ùˆ ØªØ±Ø·ÙŠØ¨ Ù„Ù„ÙˆØ¬Ù‡' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D9%85%D8%A7%D8%B3%D9%83_%D9%86%D8%B6%D8%A7%D8%B1%D8%A9_%D8%B7%D8%A8%D9%8A%D8%B9%D9%8A_%D9%84%D9%84%D8%A8%D8%B4%D8%B1%D8%A9_17697092908486332.webp.150x150_q100_crop.webp',
        'Ø¨ÙƒØ¬ 1' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D9%83%D8%AC_1_17697092360883834.webp.150x150_q100_crop.webp',
        'Ø¨ÙƒØ¬ 2' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D9%83%D8%AC_2_17697092429732742.webp.150x150_q100_crop.webp',
        'Ø¨ÙƒØ¬ 3' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D9%83%D8%AC_3_17697092435546082.webp.150x150_q100_crop.webp',
        'Ø¨ÙƒØ¬ 4' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D9%83%D8%AC_4_17697092494019852.webp.150x150_q100_crop.webp',
        'Ø¨ÙƒØ¬ Ø§Ù„Ø£Ø·ÙØ§Ù„' => 'https://d8aaen7rph5y9.cloudfront.net/app/mediafiles/t_1767705983_l7631n/items/%D8%A8%D9%83%D8%AC_%D8%A7%D9%84%D8%A3%D8%B7%D9%81%D8%A7%D9%84_17697092514411814.webp.150x150_q100_crop.webp',
    ];

    public function handle(): void
    {
        Storage::disk(config('filesystems.default'))->makeDirectory('services');

        $updated = 0;
        $failed = 0;
        $notFound = 0;

        foreach ($this->imageMap as $serviceName => $imageUrl) {
            $service = Service::where('name_ar', $serviceName)->first();

            if (! $service) {
                $this->warn("Ù„Ù… ÙŠÙØ¹Ø«Ø± Ø¹Ù„Ù‰ Ø§Ù„Ø®Ø¯Ù…Ø©: {$serviceName}");
                $notFound++;

                continue;
            }

            try {
                $response = Http::timeout(30)->withoutVerifying()->get($imageUrl);

                if (! $response->successful()) {
                    $this->error("ÙØ´Ù„ ØªØ­Ù…ÙŠÙ„ ØµÙˆØ±Ø©: {$serviceName}");
                    $failed++;

                    continue;
                }

                $extension = 'webp';
                $filename = 'services/'.Str::uuid().'.'.$extension;

                Storage::disk(config('filesystems.default'))->put($filename, $response->body());

                if ($service->image && Storage::disk(config('filesystems.default'))->exists($service->image)) {
                    Storage::disk(config('filesystems.default'))->delete($service->image);
                }

                $service->update(['image' => $filename]);

                $this->info("âœ“ {$serviceName}");
                $updated++;
            } catch (\Exception $e) {
                $this->error("خطأ في {$serviceName}: ".$e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Ù…ÙƒØªÙ…Ù„ â€” ØªÙ… Ø§Ù„ØªØ­Ø¯ÙŠØ«: {$updated} | ÙØ´Ù„: {$failed} | ØºÙŠØ± Ù…ÙˆØ¬ÙˆØ¯: {$notFound}");
    }
}
