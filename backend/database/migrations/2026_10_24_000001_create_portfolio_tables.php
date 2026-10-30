<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) { $t->boolean('is_admin')->default(false)->after('password'); });

        Schema::create('settings', function (Blueprint $t) { $t->id(); $t->string('key')->unique(); $t->longText('value')->nullable(); $t->timestamps(); });

        Schema::create('theme_colors', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('hex', 9); $t->boolean('is_default')->default(false); $t->unsignedInteger('sort_order')->default(0); $t->timestamps();
        });

        Schema::create('media', function (Blueprint $t) {
            $t->id(); $t->string('path'); $t->string('type', 12)->default('image'); $t->string('title')->nullable(); $t->string('alt')->nullable();
            $t->string('mime')->nullable(); $t->unsignedBigInteger('size')->default(0); $t->unsignedInteger('width')->nullable(); $t->unsignedInteger('height')->nullable(); $t->timestamps();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id(); $t->string('type', 12)->index(); $t->string('name'); $t->string('slug'); $t->unsignedInteger('sort_order')->default(0); $t->timestamps();
            $t->unique(['type', 'slug']);
        });

        Schema::create('projects', function (Blueprint $t) {
            $t->id(); $t->string('slug')->unique(); $t->string('title'); $t->string('short_code', 6)->nullable();
            $t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->text('summary')->nullable(); $t->string('cover_image')->nullable(); $t->string('hero_video')->nullable();
            $t->string('role')->nullable(); $t->string('client')->nullable(); $t->string('year', 10)->nullable(); $t->string('status')->nullable();
            $t->string('live_url')->nullable(); $t->string('repo_url')->nullable();
            $t->json('tags')->nullable(); $t->json('results')->nullable(); $t->json('features')->nullable(); $t->json('gallery')->nullable();
            $t->longText('overview')->nullable(); $t->longText('challenge')->nullable(); $t->longText('solution')->nullable(); $t->longText('content')->nullable();
            $t->string('seo_title')->nullable(); $t->string('seo_description', 400)->nullable();
            $t->boolean('is_featured')->default(false); $t->boolean('is_published')->default(true); $t->unsignedInteger('sort_order')->default(0);
            $t->timestamp('published_at')->nullable(); $t->timestamps();
        });

        Schema::create('posts', function (Blueprint $t) {
            $t->id(); $t->string('slug')->unique(); $t->string('title');
            $t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->text('excerpt')->nullable(); $t->string('cover_image')->nullable(); $t->longText('content')->nullable(); $t->json('tags')->nullable();
            $t->string('author_name')->nullable(); $t->unsignedSmallInteger('read_time')->default(1); $t->unsignedInteger('views')->default(0);
            $t->string('seo_title')->nullable(); $t->string('seo_description', 400)->nullable();
            $t->boolean('is_featured')->default(false); $t->boolean('is_published')->default(true); $t->timestamp('published_at')->nullable(); $t->timestamps();
        });

        Schema::create('skills', function (Blueprint $t) { $t->id(); $t->string('name'); $t->unsignedTinyInteger('level')->default(80); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('services', function (Blueprint $t) { $t->id(); $t->string('title'); $t->text('description')->nullable(); $t->json('tags')->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->boolean('is_active')->default(true); $t->timestamps(); });
        Schema::create('timeline_items', function (Blueprint $t) { $t->id(); $t->string('label'); $t->string('title'); $t->text('description')->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('faqs', function (Blueprint $t) { $t->id(); $t->string('scope', 12)->default('about'); $t->string('question'); $t->text('answer'); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });

        Schema::create('contact_messages', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email'); $t->string('project_type')->nullable(); $t->string('budget')->nullable(); $t->text('message');
            $t->string('status', 12)->default('new'); $t->string('ip', 45)->nullable(); $t->string('user_agent')->nullable();
            $t->boolean('mail_sent')->default(false); $t->boolean('sheet_synced')->default(false); $t->text('note')->nullable(); $t->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $t) { $t->id(); $t->string('email')->unique(); $t->string('ip', 45)->nullable(); $t->timestamps(); });
    }

    public function down(): void
    {
        foreach (['subscribers','contact_messages','faqs','timeline_items','services','skills','posts','projects','categories','media','theme_colors','settings'] as $t) Schema::dropIfExists($t);
        Schema::table('users', function (Blueprint $t) { $t->dropColumn('is_admin'); });
    }
};
