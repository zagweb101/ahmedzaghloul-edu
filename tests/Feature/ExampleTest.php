<?php

namespace Tests\Feature;

use Database\Seeders\PlatformSeeder;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\LiveEventRegistrationNotification;
use App\Notifications\LiveEventReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(PlatformSeeder::class);

        $response = $this->get('/');

        $response
            ->assertStatus(200)
            ->assertSee('بيت المصور')
            ->assertSee('المسارات');
    }

    public function test_public_learning_pages_are_available(): void
    {
        $this->seed(PlatformSeeder::class);

        $this->get('/learning-paths')
            ->assertStatus(200)
            ->assertSee('اساسيات التصوير الفوتوغرافي');

        $this->get('/learning-paths/photography-basics')
            ->assertStatus(200)
            ->assertSee('فهم التعريض ببساطة');
    }

    public function test_public_community_events_and_plan_pages_are_available(): void
    {
        $this->seed(PlatformSeeder::class);

        $this->get('/community')
            ->assertStatus(200)
            ->assertSee('المجتمع');

        $this->get('/live-events')
            ->assertStatus(200)
            ->assertSee('لايف تعريفي');

        $this->get('/subscription-plans')
            ->assertStatus(200)
            ->assertSee('شهري');
    }

    public function test_user_can_register_and_reach_dashboard(): void
    {
        $this->seed(PlatformSeeder::class);

        $response = $this->post('/register', [
            'name' => 'مصور جديد',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('لوحة العضو');
    }

    public function test_user_can_login(): void
    {
        $this->seed(PlatformSeeder::class);

        User::create([
            'name' => 'مستخدم مسجل',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email' => 'member@example.com',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_admin_area_is_protected_and_admin_can_create_learning_path(): void
    {
        $this->seed(PlatformSeeder::class);

        $member = User::create([
            'name' => 'عضو عادي',
            'email' => 'member-only@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => false,
        ]);

        $admin = User::create([
            'name' => 'مدير',
            'email' => 'admin-test@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $this->actingAs($member)
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertStatus(200)
            ->assertSee('إدارة المنصة');

        $this->actingAs($admin)
            ->post('/admin/learning-paths', [
                'title' => 'تصوير البورتريه',
                'slug' => 'portrait-photography',
                'description' => 'مسار جديد لتعليم تصوير البورتريه.',
                'level' => 'intermediate',
                'access_level' => 'member',
                'sort_order' => 20,
                'is_published' => '1',
            ])
            ->assertRedirect('/admin/learning-paths');

        $this->assertDatabaseHas('learning_paths', [
            'slug' => 'portrait-photography',
        ]);
    }

    public function test_admin_can_create_lesson_inside_learning_path(): void
    {
        $this->seed(PlatformSeeder::class);

        $admin = User::create([
            'name' => 'مدير الدروس',
            'email' => 'lesson-admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $path = \App\Models\LearningPath::where('slug', 'photography-basics')->firstOrFail();

        $this->actingAs($admin)
            ->post("/admin/learning-paths/{$path->id}/lessons", [
                'title' => 'إعدادات الكاميرا العملية',
                'slug' => 'practical-camera-settings',
                'summary' => 'شرح عملي لإعدادات الكاميرا.',
                'duration_minutes' => 18,
                'access_level' => 'member',
                'sort_order' => 10,
                'is_published' => '1',
            ])
            ->assertRedirect("/admin/learning-paths/{$path->id}/lessons");

        $this->assertDatabaseHas('lessons', [
            'learning_path_id' => $path->id,
            'slug' => 'practical-camera-settings',
        ]);
    }

    public function test_authenticated_user_can_publish_post_and_comment(): void
    {
        $user = User::create([
            'name' => 'عضو المجتمع',
            'email' => 'community@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post('/community', [
                'title' => 'كيف أحسن إضاءة الصورة؟',
                'body' => 'جربت مصدر ضوء واحد وأحتاج رأيكم.',
                'category' => 'question',
            ])
            ->assertRedirect('/community');

        $post = \App\Models\CommunityPost::where('title', 'كيف أحسن إضاءة الصورة؟')->firstOrFail();

        $this->actingAs($user)
            ->post("/community/{$post->id}/comments", [
                'body' => 'هذا تعليق تجريبي.',
            ])
            ->assertRedirect('/community');

        $this->assertDatabaseHas('community_comments', [
            'community_post_id' => $post->id,
            'body' => 'هذا تعليق تجريبي.',
        ]);
    }

    public function test_paid_lesson_is_locked_without_subscription(): void
    {
        $this->seed(PlatformSeeder::class);

        $user = User::create([
            'name' => 'عضو مجاني',
            'email' => 'free-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->get('/learning-paths/lighting-basics/lessons/lesson-2')
            ->assertStatus(200)
            ->assertSee('هذا الدرس للمشتركين');
    }

    public function test_subscribed_user_can_complete_lesson(): void
    {
        $this->seed(PlatformSeeder::class);

        $user = User::create([
            'name' => 'عضو مشترك',
            'email' => 'paid-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = \App\Models\SubscriptionPlan::where('slug', 'monthly')->firstOrFail();
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'access_level' => 'member',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $lesson = \App\Models\Lesson::whereHas('learningPath', fn ($query) => $query->where('slug', 'lighting-basics'))
            ->where('slug', 'lesson-2')
            ->firstOrFail();

        $this->actingAs($user)
            ->get('/learning-paths/lighting-basics/lessons/lesson-2')
            ->assertStatus(200)
            ->assertSee('تحديد كمكتمل');

        $this->actingAs($user)
            ->post('/learning-paths/lighting-basics/lessons/lesson-2/complete')
            ->assertRedirect();

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    public function test_admin_can_grant_subscription_to_user(): void
    {
        $this->seed(PlatformSeeder::class);

        $admin = User::create([
            'name' => 'مدير العضويات',
            'email' => 'subscriptions-admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $member = User::create([
            'name' => 'عضو جديد',
            'email' => 'new-subscriber@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = \App\Models\SubscriptionPlan::where('slug', 'yearly')->firstOrFail();

        $this->actingAs($admin)
            ->post("/admin/users/{$member->id}/subscription", [
                'subscription_plan_id' => $plan->id,
                'access_level' => 'premium',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $member->id,
            'subscription_plan_id' => $plan->id,
            'access_level' => 'premium',
            'status' => 'active',
        ]);
    }

    public function test_user_can_like_and_unlike_community_post(): void
    {
        $author = User::create([
            'name' => 'صاحب البوست',
            'email' => 'post-author@example.com',
            'password' => Hash::make('password123'),
        ]);

        $member = User::create([
            'name' => 'عضو متفاعل',
            'email' => 'liking-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $post = $author->communityPosts()->create([
            'title' => 'تجربة إضاءة جديدة',
            'body' => 'شاركوني رأيكم في النتيجة.',
            'category' => 'showcase',
        ]);

        $this->actingAs($member)
            ->post("/community/{$post->id}/like")
            ->assertRedirect();

        $this->assertDatabaseHas('community_post_likes', [
            'community_post_id' => $post->id,
            'user_id' => $member->id,
        ]);

        $this->actingAs($member)->post("/community/{$post->id}/like");

        $this->assertDatabaseMissing('community_post_likes', [
            'community_post_id' => $post->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_admin_can_hide_and_pin_community_post(): void
    {
        $admin = User::create([
            'name' => 'مشرف المجتمع',
            'email' => 'community-admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $post = $admin->communityPosts()->create([
            'title' => 'تحدي الأسبوع',
            'body' => 'شارك أفضل صورة بورتريه لديك.',
            'category' => 'challenge',
        ]);

        $this->actingAs($admin)
            ->patch("/admin/community/{$post->id}/pinned")
            ->assertRedirect();

        $this->assertTrue($post->fresh()->is_pinned);

        $this->actingAs($admin)
            ->patch("/admin/community/{$post->id}/published")
            ->assertRedirect();

        $this->assertFalse($post->fresh()->is_published);
    }

    public function test_user_can_upload_community_post_image(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'مصور المجتمع',
            'email' => 'image-uploader@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post('/community', [
                'title' => 'نتيجة تصوير اليوم',
                'body' => 'جربت إضاءة ناعمة على البورتريه.',
                'category' => 'showcase',
                'image' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect('/community');

        $post = \App\Models\CommunityPost::where('title', 'نتيجة تصوير اليوم')->firstOrFail();

        $this->assertNotNull($post->image_path);
        Storage::disk('public')->assertExists($post->image_path);
    }

    public function test_admin_can_upload_lesson_pdf_and_subscriber_can_download_it(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->seed(PlatformSeeder::class);

        $admin = User::create([
            'name' => 'مدير الملفات',
            'email' => 'files-admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $subscriber = User::create([
            'name' => 'عضو بملفات',
            'email' => 'files-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = \App\Models\SubscriptionPlan::where('slug', 'monthly')->firstOrFail();
        UserSubscription::create([
            'user_id' => $subscriber->id,
            'subscription_plan_id' => $plan->id,
            'access_level' => 'member',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $path = \App\Models\LearningPath::where('slug', 'photography-basics')->firstOrFail();

        $this->actingAs($admin)
            ->post("/admin/learning-paths/{$path->id}/lessons", [
                'title' => 'ملفات الدرس العملية',
                'slug' => 'lesson-files',
                'summary' => 'درس مع ملف PDF مرفوع.',
                'thumbnail' => UploadedFile::fake()->create('thumb.jpg', 100, 'image/jpeg'),
                'pdf_file' => UploadedFile::fake()->create('guide.pdf', 120, 'application/pdf'),
                'duration_minutes' => 15,
                'access_level' => 'member',
                'sort_order' => 99,
                'is_published' => '1',
            ])
            ->assertRedirect("/admin/learning-paths/{$path->id}/lessons");

        $lesson = \App\Models\Lesson::where('slug', 'lesson-files')->firstOrFail();

        Storage::disk('public')->assertExists($lesson->thumbnail_path);
        Storage::disk('local')->assertExists($lesson->pdf_path);

        $this->actingAs($subscriber)
            ->get('/learning-paths/photography-basics/lessons/lesson-files/pdf')
            ->assertOk();

        $freeUser = User::create([
            'name' => 'عضو بدون اشتراك',
            'email' => 'no-subscriber@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($freeUser)
            ->get('/learning-paths/photography-basics/lessons/lesson-files/pdf')
            ->assertForbidden();
    }

    public function test_user_can_update_dashboard_avatar(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'عضو بصورة',
            'email' => 'avatar-user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->from('/dashboard')
            ->post('/dashboard/avatar', [
                'avatar' => UploadedFile::fake()->create('avatar.png', 100, 'image/png'),
            ])
            ->assertRedirect('/dashboard');

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_admin_can_update_and_delete_learning_path_media(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->seed(PlatformSeeder::class);

        $admin = User::create([
            'name' => 'مدير التعديل',
            'email' => 'edit-admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $path = \App\Models\LearningPath::where('slug', 'photography-basics')->firstOrFail();
        $oldCover = 'learning-paths/covers/old.jpg';
        Storage::disk('public')->put($oldCover, 'cover');
        $path->update(['cover_image_path' => $oldCover]);

        $this->actingAs($admin)
            ->put("/admin/learning-paths/{$path->id}", [
                'title' => $path->title,
                'slug' => $path->slug,
                'description' => $path->description,
                'cover_image' => UploadedFile::fake()->create('new-cover.jpg', 100, 'image/jpeg'),
                'level' => $path->level->value,
                'access_level' => $path->access_level->value,
                'sort_order' => $path->sort_order,
                'is_published' => '1',
            ])
            ->assertRedirect('/admin/learning-paths');

        $path->refresh();
        Storage::disk('public')->assertMissing($oldCover);
        $this->assertNotSame($oldCover, $path->cover_image_path);

        $lesson = $path->lessons()->firstOrFail();
        $lesson->update([
            'thumbnail_path' => 'lessons/thumbnails/old.jpg',
            'pdf_path' => 'lessons/pdfs/old.pdf',
        ]);
        Storage::disk('public')->put($lesson->thumbnail_path, 'thumb');
        Storage::disk('local')->put($lesson->pdf_path, 'pdf');

        $this->actingAs($admin)
            ->delete("/admin/learning-paths/{$path->id}/lessons/{$lesson->id}")
            ->assertRedirect("/admin/learning-paths/{$path->id}/lessons");

        Storage::disk('public')->assertMissing($lesson->thumbnail_path);
        Storage::disk('local')->assertMissing($lesson->pdf_path);
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    public function test_community_gallery_lists_posts_with_images(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'مصور المعرض',
            'email' => 'gallery-user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $post = $user->communityPosts()->create([
            'title' => 'لقطة ضوء طبيعي',
            'body' => 'تجربة إضاءة ناعمة.',
            'category' => 'showcase',
        ]);

        $imagePath = 'community/gallery-photo.jpg';
        Storage::disk('public')->put($imagePath, 'image');
        $post->images()->create(['image_path' => $imagePath, 'sort_order' => 1]);
        $post->syncCoverImage();

        $this->get('/community/gallery')
            ->assertStatus(200)
            ->assertSee('معرض صور الأعضاء')
            ->assertSee('لقطة ضوء طبيعي');

        $this->get('/community/gallery?category=showcase')
            ->assertStatus(200)
            ->assertSee('لقطة ضوء طبيعي');

        $this->get('/community/gallery?category=gear')
            ->assertStatus(200)
            ->assertDontSee('لقطة ضوء طبيعي');
    }

    public function test_user_can_publish_edit_and_delete_post_with_multiple_images(): void
    {
        Storage::fake('public');

        $author = User::create([
            'name' => 'صاحب البوست',
            'email' => 'multi-image-author@example.com',
            'password' => Hash::make('password123'),
        ]);

        $otherUser = User::create([
            'name' => 'عضو آخر',
            'email' => 'other-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($author)
            ->post('/community', [
                'title' => 'جلسة تصوير متعددة',
                'body' => 'ثلاث زوايا مختلفة من نفس الجلسة.',
                'category' => 'showcase',
                'images' => [
                    UploadedFile::fake()->create('one.jpg', 100, 'image/jpeg'),
                    UploadedFile::fake()->create('two.jpg', 100, 'image/jpeg'),
                    UploadedFile::fake()->create('three.jpg', 100, 'image/jpeg'),
                ],
            ])
            ->assertRedirect('/community');

        $post = \App\Models\CommunityPost::where('title', 'جلسة تصوير متعددة')->firstOrFail();
        $this->assertCount(3, $post->images);

        $this->actingAs($otherUser)
            ->get("/community/posts/{$post->id}/edit")
            ->assertForbidden();

        $imageToRemove = $post->images()->orderBy('sort_order')->firstOrFail();

        $this->actingAs($author)
            ->put("/community/posts/{$post->id}", [
                'title' => 'جلسة محدثة',
                'body' => 'عدلت العنوان وحذفت صورة واحدة.',
                'category' => 'showcase',
                'remove_image_ids' => [$imageToRemove->id],
                'images' => [
                    UploadedFile::fake()->create('four.jpg', 100, 'image/jpeg'),
                ],
            ])
            ->assertRedirect('/community');

        $post->refresh();
        $this->assertSame('جلسة محدثة', $post->title);
        $this->assertCount(3, $post->images);
        Storage::disk('public')->assertMissing($imageToRemove->image_path);

        $this->actingAs($author)
            ->delete("/community/{$post->id}")
            ->assertRedirect('/community');

        $this->assertDatabaseMissing('community_posts', ['id' => $post->id]);
        $this->assertDatabaseCount('community_post_images', 0);
    }

    public function test_user_can_comment_with_image_and_post_owner_gets_notification(): void
    {
        Storage::fake('public');

        $author = User::create([
            'name' => 'صاحب البوست',
            'email' => 'notify-author@example.com',
            'password' => Hash::make('password123'),
        ]);

        $commenter = User::create([
            'name' => 'المعلّق',
            'email' => 'notify-commenter@example.com',
            'password' => Hash::make('password123'),
        ]);

        $post = $author->communityPosts()->create([
            'title' => 'بوست للتنبيهات',
            'body' => 'محتوى البوست.',
            'category' => 'question',
        ]);

        $this->actingAs($commenter)
            ->post("/community/{$post->id}/comments", [
                'body' => 'تعليق مع صورة',
                'image' => UploadedFile::fake()->create('comment.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect('/community');

        $comment = \App\Models\CommunityComment::where('community_post_id', $post->id)->firstOrFail();
        $this->assertNotNull($comment->image_path);
        Storage::disk('public')->assertExists($comment->image_path);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $author->id,
            'notifiable_type' => User::class,
        ]);

        $this->assertSame(1, $author->fresh()->notifications()->count());
        $this->assertSame(0, $commenter->fresh()->notifications()->count());
    }

    public function test_like_sends_notification_but_self_like_does_not(): void
    {
        $author = User::create([
            'name' => 'صاحب الإعجاب',
            'email' => 'like-author@example.com',
            'password' => Hash::make('password123'),
        ]);

        $member = User::create([
            'name' => 'معجب',
            'email' => 'like-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $post = $author->communityPosts()->create([
            'title' => 'بوست للإعجاب',
            'body' => 'محتوى.',
            'category' => 'showcase',
        ]);

        $this->actingAs($author)
            ->post("/community/{$post->id}/like")
            ->assertRedirect();

        $this->assertSame(0, $author->fresh()->notifications()->count());

        $this->actingAs($member)
            ->post("/community/{$post->id}/like")
            ->assertRedirect();

        $notification = $author->fresh()->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('like', $notification->data['action']);
        $this->assertSame($member->id, $notification->data['actor_id']);

        $this->actingAs($member)
            ->post("/community/{$post->id}/like")
            ->assertRedirect();

        $this->assertSame(1, $author->fresh()->notifications()->count());
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::create([
            'name' => 'مستلم الإشعار',
            'email' => 'notification-reader@example.com',
            'password' => Hash::make('password123'),
        ]);

        $actor = User::create([
            'name' => 'المرسل',
            'email' => 'notification-sender@example.com',
            'password' => Hash::make('password123'),
        ]);

        $post = $user->communityPosts()->create([
            'title' => 'بوستي',
            'body' => 'محتوى.',
            'category' => 'feedback',
        ]);

        $user->notify(new \App\Notifications\CommunityInteractionNotification(
            action: 'comment',
            post: $post,
            actor: $actor,
            preview: 'تعليق تجريبي',
        ));

        $notification = $user->fresh()->notifications()->firstOrFail();

        $this->actingAs($user)
            ->post("/notifications/{$notification->id}/read")
            ->assertRedirect(route('community.index') . '#post-' . $post->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_register_for_and_cancel_live_event(): void
    {
        Notification::fake();

        $this->seed(PlatformSeeder::class);

        $user = User::create([
            'name' => 'حاضر اللايف',
            'email' => 'event-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $event = \App\Models\LiveEvent::where('slug', 'first-live')->firstOrFail();

        $this->actingAs($user)
            ->post("/live-events/{$event->id}/register")
            ->assertRedirect();

        $this->assertDatabaseHas('live_event_registrations', [
            'live_event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
        ]);

        Notification::assertSentTo($user, LiveEventRegistrationNotification::class);

        $this->actingAs($user)
            ->delete("/live-events/{$event->id}/register")
            ->assertRedirect();

        $this->assertDatabaseHas('live_event_registrations', [
            'live_event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_live_event_reminder_command_notifies_registered_users_once(): void
    {
        Notification::fake();

        $user = User::create([
            'name' => 'متابع اللايف',
            'email' => 'reminder-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $event = \App\Models\LiveEvent::create([
            'title' => 'لايف قريب',
            'slug' => 'soon-live',
            'description' => 'لايف خلال الساعات القادمة.',
            'starts_at' => now()->addHours(6),
            'location' => 'اونلاين',
            'access_level' => 'free',
            'is_published' => true,
        ]);

        $registration = \App\Models\LiveEventRegistration::create([
            'live_event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
        ]);

        $this->artisan('live-events:send-reminders')->assertSuccessful();

        Notification::assertSentTo($user, LiveEventReminderNotification::class);
        $this->assertNotNull($registration->fresh()->reminder_sent_at);

        Notification::fake();

        $this->artisan('live-events:send-reminders')->assertSuccessful();
        Notification::assertNothingSent();
    }

    public function test_user_can_checkout_paid_plan_and_admin_can_approve_order(): void
    {
        Notification::fake();

        $this->seed(PlatformSeeder::class);

        $member = User::create([
            'name' => 'مشترك جديد',
            'email' => 'checkout-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $admin = User::create([
            'name' => 'مدير المدفوعات',
            'email' => 'payments-admin@example.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        $plan = \App\Models\SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        $this->actingAs($member)
            ->post('/subscription-plans/monthly/checkout', [
                'customer_note' => 'تحويل بنكي تجريبي',
            ])
            ->assertRedirect();

        $order = \App\Models\SubscriptionOrder::where('user_id', $member->id)->firstOrFail();
        $this->assertTrue($order->isPending());

        $this->actingAs($admin)
            ->post("/admin/subscription-orders/{$order->id}/approve")
            ->assertRedirect();

        $this->assertTrue($order->fresh()->isPaid());
        $this->assertTrue($member->fresh()->canAccess(\App\Enums\AccessLevel::Member));
        Notification::assertSentTo($member, \App\Notifications\SubscriptionActivatedNotification::class);
    }

    public function test_demo_payment_driver_activates_subscription_immediately(): void
    {
        Notification::fake();
        config(['payments.driver' => 'demo']);

        $this->seed(PlatformSeeder::class);

        $member = User::create([
            'name' => 'مشترك فوري',
            'email' => 'demo-checkout@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($member)
            ->post('/subscription-plans/yearly/checkout')
            ->assertRedirect();

        $order = \App\Models\SubscriptionOrder::where('user_id', $member->id)->firstOrFail();
        $this->assertTrue($order->isPaid());
        $this->assertTrue($member->fresh()->canAccess(\App\Enums\AccessLevel::Premium));
    }

    public function test_tap_payment_driver_redirects_to_checkout_url(): void
    {
        config([
            'payments.driver' => 'tap',
            'payments.tap.secret_key' => 'sk_test_fake',
            'payments.tap.api_url' => 'https://api.tap.company/v2',
        ]);

        Http::fake([
            'https://api.tap.company/v2/charges' => Http::response([
                'id' => 'chg_test123',
                'transaction' => [
                    'url' => 'https://tap.company/checkout/chg_test123',
                ],
            ], 200),
        ]);

        $this->seed(PlatformSeeder::class);

        $member = User::create([
            'name' => 'مشترك تاب',
            'email' => 'tap-checkout@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($member)
            ->post('/subscription-plans/monthly/checkout')
            ->assertRedirect('https://tap.company/checkout/chg_test123');

        $order = \App\Models\SubscriptionOrder::where('user_id', $member->id)->firstOrFail();
        $this->assertSame('chg_test123', $order->gateway_charge_id);
        $this->assertSame('https://tap.company/checkout/chg_test123', $order->checkout_url);
        $this->assertTrue($order->isPending());
    }

    public function test_tap_webhook_marks_order_as_paid(): void
    {
        Notification::fake();

        $this->seed(PlatformSeeder::class);

        $member = User::create([
            'name' => 'مشترك ويبهوك',
            'email' => 'tap-webhook@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = \App\Models\SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        $order = \App\Models\SubscriptionOrder::create([
            'user_id' => $member->id,
            'subscription_plan_id' => $plan->id,
            'reference' => 'ORD-TAPTEST1',
            'amount_cents' => $plan->price_cents,
            'currency' => $plan->currency,
            'status' => 'pending',
            'payment_driver' => 'tap',
            'gateway_charge_id' => 'chg_webhook1',
        ]);

        $this->post('/payments/tap/webhook', [
            'id' => 'chg_webhook1',
            'status' => 'CAPTURED',
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ])->assertOk();

        $this->assertTrue($order->fresh()->isPaid());
        $this->assertTrue($member->fresh()->canAccess(\App\Enums\AccessLevel::Member));
        Notification::assertSentTo($member, \App\Notifications\SubscriptionActivatedNotification::class);
    }

    public function test_lesson_page_includes_seo_metadata(): void
    {
        $this->seed(PlatformSeeder::class);

        $path = \App\Models\LearningPath::where('slug', 'photography-basics')->firstOrFail();
        $lesson = $path->lessons()->where('is_published', true)->firstOrFail();

        $this->get(route('lessons.show', [$path, $lesson]))
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('LearningResource', false)
            ->assertSee(route('lessons.show', [$path, $lesson]), false);
    }

    public function test_learning_path_page_includes_seo_metadata(): void
    {
        $this->seed(PlatformSeeder::class);

        $path = \App\Models\LearningPath::where('slug', 'photography-basics')->firstOrFail();
        $path->update([
            'seo_description' => 'وصف مخصص لتحسين محركات البحث لهذا المسار.',
            'seo_keywords' => 'تصوير,فوتوغرافي,تعليم',
        ]);

        $this->get(route('learning-paths.show', $path))
            ->assertOk()
            ->assertSee('وصف مخصص لتحسين محركات البحث لهذا المسار.', false)
            ->assertSee('تصوير,فوتوغرافي,تعليم', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('BreadcrumbList', false)
            ->assertSee(route('learning-paths.show', $path), false);
    }

    public function test_sitemap_is_available(): void
    {
        $this->seed(PlatformSeeder::class);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('learning-paths.index'), false);
    }

    public function test_community_notification_uses_mail_when_enabled(): void
    {
        config(['platform.mail_notifications' => true]);

        $author = User::create([
            'name' => 'صاحب البريد',
            'email' => 'mail-author@example.com',
            'password' => Hash::make('password123'),
        ]);

        $commenter = User::create([
            'name' => 'المعلّق بالبريد',
            'email' => 'mail-commenter@example.com',
            'password' => Hash::make('password123'),
        ]);

        $post = $author->communityPosts()->create([
            'title' => 'بوست للبريد',
            'body' => 'محتوى.',
            'category' => 'question',
        ]);

        Notification::fake();

        app(\App\Services\CommunityNotifier::class)->comment($post, $commenter, 'تعليق يصل بالبريد');

        Notification::assertSentTo(
            $author,
            \App\Notifications\CommunityInteractionNotification::class,
            fn ($notification, $channels) => in_array('mail', $channels, true)
                && in_array('database', $channels, true),
        );
    }
}
