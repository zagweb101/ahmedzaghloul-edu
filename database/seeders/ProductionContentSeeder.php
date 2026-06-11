<?php

namespace Database\Seeders;

use App\Enums\AccessLevel;
use App\Models\BlogPost;
use App\Models\LiveEvent;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class ProductionContentSeeder extends Seeder
{
    /**
     * Safe to run on production: uses updateOrCreate only.
     */
    public function run(): void
    {
        SubscriptionPlan::where('slug', 'free')->update([
            'seo_title' => 'الخطة المجانية — بيت المصور',
            'seo_description' => 'ابدأ مجانًا في بيت المصور: دروس مختارة ومقتطفات لايف ومعاينة فعاليات.',
            'seo_keywords' => 'تصوير مجاني,تعليم تصوير,بيت المصور',
        ]);

        SubscriptionPlan::where('slug', 'monthly')->update([
            'seo_title' => 'الاشتراك الشهري — بيت المصور',
            'seo_description' => 'اشتراك شهري يفتح كل المسارات والمجتمع واللايفات وملفات PDF للمصورين.',
            'seo_keywords' => 'اشتراك شهري,تعليم تصوير,لايفات تصوير',
        ]);

        SubscriptionPlan::where('slug', 'yearly')->update([
            'seo_title' => 'الاشتراك السنوي — بيت المصور',
            'seo_description' => 'أفضل قيمة للمصور الجاد: كل مميزات الشهري بسعر أوفر وأرشيف لايفات.',
            'seo_keywords' => 'اشتراك سنوي,أكاديمية تصوير,بيت المصور',
        ]);

        LiveEvent::updateOrCreate(
            ['slug' => 'first-live'],
            [
                'title' => 'لايف تعريفي: كيف تبدأ رحلتك في التصوير',
                'description' => 'جلسة تعريفية عن طريقة التعلم داخل المنصة.',
                'seo_description' => 'لايف مجاني تعريفي يشرح كيف تبدأ رحلتك في التصوير داخل بيت المصور.',
                'seo_keywords' => 'لايف تصوير,تعليم تصوير,بيت المصور',
                'location' => 'اونلاين',
                'stream_url' => 'https://www.youtube.com/embed/live_stream?channel=UCplaceholder',
                'starts_at' => now()->addDays(7),
                'capacity' => 100,
                'access_level' => AccessLevel::Free,
                'is_published' => true,
            ],
        );

        BlogPost::updateOrCreate(
            ['slug' => 'photography-basics-guide'],
            [
                'title' => 'دليل المبتدئين: كيف تبدأ في التصوير الفوتوغرافي',
                'excerpt' => 'خطوات عملية لفهم الكاميرا والتعريض والتكوين قبل الانتقال للمحتوى المتقدم.',
                'body' => "ابدأ بفهم ثلاثة عناصر أساسية: التعريض، العدسة، والتكوين.\n\nالتعريض يتحكم في كمية الضوء التي تصل للحساس. العدسة تحدد زاوية الرؤية وعزل الخلفية. التكوين يحدد أين يجلس المشاهد داخل الصورة.\n\nفي بيت المصور نربط هذه المفاهيم بمسارات عملية وتحديات أسبوعية داخل المجتمع.",
                'seo_description' => 'دليل عربي للمبتدئين يشرح أساسيات التصوير الفوتوغرافي بخطوات عملية.',
                'seo_keywords' => 'تصوير فوتوغرافي,مبتدئين,تعليم تصوير',
                'author_name' => 'أحمد زغلول',
                'published_at' => now()->subDays(2),
                'is_published' => true,
            ],
        );

        BlogPost::updateOrCreate(
            ['slug' => 'natural-light-portrait-tips'],
            [
                'title' => '5 نصائح سريعة لتصوير بورتريه بضوء طبيعي',
                'excerpt' => 'كيف تستفيد من النافذة والظل الناعم لصورة بورتريه نظيفة بدون إضاءة معقدة.',
                'body' => "1. ضع وجه الموديل باتجاه مصدر الضوء وليس خلفه.\n2. ابحث عن ظل ناعم قريب من نافذة كبيرة.\n3. استخدم خلفية بسيطة بعيدة قليلًا عن الموديل.\n4. راقب لمعان الجلد في العينين (catchlight).\n5. التقط عدة لقطات بزوايا مختلفة قبل إنهاء الجلسة.\n\nطبّق أحد هذه النصائح ثم شارك نتيجتك داخل مجتمع بيت المصور.",
                'seo_description' => 'نصائح عملية لتصوير بورتريه بضوء طبيعي للمبتدئين والمتوسطين.',
                'seo_keywords' => 'بورتريه,ضوء طبيعي,تصوير',
                'author_name' => 'أحمد زغلول',
                'published_at' => now()->subDay(),
                'is_published' => true,
            ],
        );

        BlogPost::updateOrCreate(
            ['slug' => 'why-community-matters-for-photographers'],
            [
                'title' => 'لماذا المجتمع مهم للمصور المتطور؟',
                'excerpt' => 'التعلم وحده لا يكفي — التطبيق والنقد البنّاء والتحديات هي ما يثبت المهارة.',
                'body' => "المصور الذي يتعلم بدون تطبيق يبقى في مرحلة المعرفة النظرية.\n\nداخل بيت المصور نجمع بين الدروس والتحديات الأسبوعية ومساحة آمنة لطرح الأسئلة ومشاركة الأعمال.\n\nابدأ بمشاركة صورة واحدة هذا الأسبوع مع وصف قصير لما حاولت تطبيقه.",
                'seo_description' => 'لماذا يحتاج المصور لمجتمع تطبيقي بجانب الدروس التعليمية.',
                'seo_keywords' => 'مجتمع مصورين,تعليم تصوير,تطبيق عملي',
                'author_name' => 'أحمد زغلول',
                'published_at' => now(),
                'is_published' => true,
            ],
        );
    }
}
