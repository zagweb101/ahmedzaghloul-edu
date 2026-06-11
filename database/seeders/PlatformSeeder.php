<?php

namespace Database\Seeders;

use App\Enums\AccessLevel;
use App\Enums\SkillLevel;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LiveEvent;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $paths = [
            ['اساسيات التصوير الفوتوغرافي', 'photography-basics', SkillLevel::Beginner, AccessLevel::Free, 1],
            ['اساسيات الاضاءة', 'lighting-basics', SkillLevel::Beginner, AccessLevel::Member, 2],
            ['احتراف الاضاءة', 'advanced-lighting', SkillLevel::Professional, AccessLevel::Premium, 3],
            ['تصوير الاعراس', 'wedding-photography', SkillLevel::Intermediate, AccessLevel::Member, 4],
            ['تصوير الفاشون', 'fashion-photography', SkillLevel::Intermediate, AccessLevel::Member, 5],
            ['تصوير البيوتي', 'beauty-photography', SkillLevel::Professional, AccessLevel::Premium, 6],
            ['تصوير الاطعمة', 'food-photography', SkillLevel::Intermediate, AccessLevel::Member, 7],
            ['الفيديو', 'video', SkillLevel::Intermediate, AccessLevel::Member, 8],
        ];

        foreach ($paths as [$title, $slug, $level, $accessLevel, $sortOrder]) {
            $path = LearningPath::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'description' => 'مسار تطبيقي يساعد المصور على التعلم خطوة بخطوة.',
                    'level' => $level,
                    'access_level' => $accessLevel,
                    'sort_order' => $sortOrder,
                    'is_published' => true,
                ],
            );

            foreach ($this->lessonTitlesFor($slug) as $index => $lessonTitle) {
                Lesson::updateOrCreate(
                    [
                        'learning_path_id' => $path->id,
                        'slug' => 'lesson-' . ($index + 1),
                    ],
                    [
                        'title' => $lessonTitle,
                        'summary' => 'درس تطبيقي قصير يوضح الفكرة بخطوات عملية.',
                        'duration_minutes' => 12 + $index,
                        'access_level' => $index === 0 ? AccessLevel::Free : $accessLevel,
                        'sort_order' => $index + 1,
                        'is_published' => true,
                    ],
                );
            }
        }

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'مجاني',
                'price_cents' => 0,
                'billing_period' => 'free',
                'description' => 'دروس مختارة ومقتطفات لعرض قوة المنصة.',
                'features' => ['دروس مجانية', 'مقتطفات لايف', 'معاينة فعاليات'],
                'sort_order' => 1,
            ],
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'monthly'],
            [
                'name' => 'شهري',
                'price_cents' => 9900,
                'billing_period' => 'month',
                'description' => 'كل المحتوى والمجتمع واللايفات.',
                'features' => ['كل المسارات', 'المجتمع', 'اللايفات', 'ملفات PDF'],
                'is_featured' => true,
                'sort_order' => 2,
            ],
        );

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'yearly'],
            [
                'name' => 'سنوي',
                'price_cents' => 99000,
                'billing_period' => 'year',
                'description' => 'أفضل قيمة للمصور الجاد.',
                'features' => ['كل مميزات الشهري', 'سعر أوفر', 'أرشيف اللايفات', 'أولوية في الورش'],
                'sort_order' => 3,
            ],
        );

        LiveEvent::updateOrCreate(
            ['slug' => 'first-live'],
            [
                'title' => 'لايف تعريفي: كيف تبدأ رحلتك في التصوير',
                'description' => 'جلسة تعريفية عن طريقة التعلم داخل المنصة.',
                'location' => 'اونلاين',
                'capacity' => 100,
                'access_level' => AccessLevel::Free,
                'is_published' => true,
            ],
        );
    }

    /**
     * @return array<int, string>
     */
    private function lessonTitlesFor(string $pathSlug): array
    {
        return match ($pathSlug) {
            'photography-basics' => [
                'فهم التعريض ببساطة',
                'اختيار العدسة المناسبة',
                'تكوين الصورة قبل الضغط على الغالق',
            ],
            'lighting-basics' => [
                'قراءة اتجاه الضوء',
                'الضوء الناعم والضوء الحاد',
                'إضاءة بورتريه بمصدر واحد',
            ],
            'advanced-lighting' => [
                'بناء إضاءة درامية',
                'مزج الفلاش مع الضوء الطبيعي',
                'تحليل إعدادات إضاءة احترافية',
            ],
            'wedding-photography' => [
                'خطة تغطية يوم الزفاف',
                'لقطات لا يجب أن تفوتك',
                'إدارة الوقت أثناء الحفل',
            ],
            'fashion-photography' => [
                'تحضير فكرة جلسة فاشون',
                'التواصل مع الموديل والفريق',
                'اختيار الزوايا المناسبة',
            ],
            'beauty-photography' => [
                'إضاءة البشرة بتفاصيل نظيفة',
                'اختيار العدسة والزوايا',
                'ملاحظات مهمة قبل الريتاتش',
            ],
            'food-photography' => [
                'تجهيز مشهد تصوير الطعام',
                'إضاءة بسيطة للمنيوهات',
                'التكوين في تصوير الأطعمة',
            ],
            default => [
                'إعدادات الفيديو الأساسية',
                'الصوت والإضاءة في الفيديو',
                'تصوير لقطة ثابتة ونظيفة',
            ],
        };
    }
}
