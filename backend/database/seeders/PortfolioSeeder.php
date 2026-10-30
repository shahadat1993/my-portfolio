<?php

namespace Database\Seeders;

use App\Models\{Category, ContactMessage, Faq, Media, Post, Project, Service, Setting, Skill, Subscriber, ThemeColor, TimelineItem};
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Demo content: categories, projects (images + video + long case-study HTML), blog posts (images at the
 * start / middle / end with left / right / centre alignment), skills, services, timeline, FAQs, colours,
 * settings photos, sample messages. Safe to run again (uses updateOrCreate).
 */
class PortfolioSeeder extends Seeder
{
    /** Copy a bundled demo file into storage/app/public/seed/... and register it in the media library. */
    protected function asset(string $kind, string $file): string
    {
        $src = database_path("seeders/assets/$kind/$file");
        $dest = "seed/$kind/$file";
        $disk = Storage::disk('public');
        if (!$disk->exists($dest)) $disk->put($dest, File::get($src));
        $mime = $kind === 'videos' ? 'video/mp4' : 'image/jpeg';
        $size = @getimagesize($src);
        Media::firstOrCreate(['path' => $dest], ['type' => $kind === 'videos' ? 'video' : 'image', 'title' => pathinfo($file, PATHINFO_FILENAME), 'mime' => $mime, 'size' => filesize($src), 'width' => $size[0] ?? null, 'height' => $size[1] ?? null]);
        return $dest;
    }

    /** <figure> with alignment class understood by the rich editor + the React site. */
    protected function fig(string $file, string $align, string $caption, string $extra = ''): string
    {
        $dest = $this->asset('images', $file);
        $cls = trim('image ' . ($align ? "align-$align " : '') . $extra);
        return '<figure class="' . $cls . '"><img src="/storage/' . $dest . '" alt="' . e($caption) . '"><figcaption>' . e($caption) . '</figcaption></figure>';
    }

    public function run(): void
    {
        /* ---------- categories ---------- */
        $cat = [];
        foreach ([['project', 'Web', 'web'], ['project', 'Mobile', 'mobile'], ['project', 'Admin', 'admin'],
                  ['blog', 'Architecture', 'architecture'], ['blog', 'Motion', 'motion'], ['blog', 'Laravel', 'laravel'], ['blog', 'Mobile', 'mobile'], ['blog', 'Design', 'design'], ['blog', 'Career', 'career']] as $i => [$type, $name, $slug]) {
            $cat[$type . ':' . $slug] = Category::updateOrCreate(['type' => $type, 'slug' => $slug], ['name' => $name, 'sort_order' => $i])->id;
        }

        /* ---------- theme colours (managed in Admin → Theme colours) ---------- */
        foreach ([['Ember', '#ff6a3d', true], ['Lime', '#d4ff3a'], ['Violet', '#7c5cff'], ['Mint', '#00d1b2'], ['Rose', '#ff4fa3'], ['Sky', '#4da3ff'], ['Amber', '#f5b400']] as $i => $c) {
            ThemeColor::updateOrCreate(['hex' => $c[1]], ['name' => $c[0], 'is_default' => $c[2] ?? false, 'sort_order' => $i]);
        }

        /* ---------- site settings (photos; text defaults live in config/site.php) ---------- */
        Setting::put('hero_photo', $this->asset('images', 'hero.jpg'));
        Setting::put('about_photo', $this->asset('images', 'about.jpg'));

        /* ---------- skills / services / timeline / faqs ---------- */
        foreach ([['Laravel / PHP', 90], ['React / Vite', 85], ['React Native', 78], ['MySQL', 82], ['GSAP / Motion', 75], ['DevOps (learning)', 45]] as $i => [$n, $l]) Skill::updateOrCreate(['name' => $n], ['level' => $l, 'sort_order' => $i]);
        foreach ([
            ['Full-stack web apps', 'End-to-end products with Laravel and React: database design, REST APIs, authentication, dashboards and polished interfaces.', ['Laravel', 'React', 'MySQL']],
            ['Mobile apps', 'Cross-platform React Native (Expo) apps that share one API with the web version.', ['React Native', 'Expo', 'Sanctum']],
            ['Admin panels and dashboards', 'Role-based admin panels with Spatie permissions, media libraries and reporting.', ['Spatie RBAC', 'Sneat', 'Charts']],
            ['Motion and interfaces', 'GSAP and ScrollTrigger experiences, design systems and responsive layouts that feel alive.', ['GSAP', 'Tailwind', 'Design systems']],
        ] as $i => [$t, $d, $tags]) Service::updateOrCreate(['title' => $t], ['description' => $d, 'tags' => $tags, 'sort_order' => $i, 'is_active' => true]);
        foreach ([['2021', 'Learning by building.', 'PHP, JavaScript and databases — first real projects.'], ['2024', 'Shipping for clients.', 'E-commerce platforms, APIs and dashboards in production.'],
                  ['Today', 'Whole products, end to end.', 'Web, mobile and admin systems built together.'], ['Next', 'Agency & cloud.', 'DevOps, scale, and a team to build bigger things.']] as $i => [$l, $t, $d])
            TimelineItem::updateOrCreate(['label' => $l, 'title' => $t], ['description' => $d, 'sort_order' => $i]);
        foreach ([
            ['about', 'Do you work with clients outside Bangladesh?', 'Yes. I work remotely and collaborate across time zones with clear updates and written specs.'],
            ['about', 'Which stack do you use?', 'Laravel and MySQL on the back end, React or React Native on the front, with Tailwind or Bootstrap for UI and GSAP for motion.'],
            ['about', 'Can you take a project from idea to launch?', 'Yes — planning, design, API, admin, front end, mobile app and deployment.'],
            ['about', 'How do you handle changes?', 'Work is split into small phases with a review after each, so changes stay cheap and nothing surprises you.'],
            ['contact', 'How quickly do you reply?', 'Usually within one working day.'],
            ['contact', 'Do you sign NDAs?', 'Yes, happy to sign one before you share details.'],
            ['contact', 'How is pricing decided?', 'After a short discovery call I scope the work into phases and share a clear estimate for each.'],
            ['contact', 'Do you offer maintenance?', 'Yes, monthly retainers cover fixes, updates and small features.'],
        ] as $i => [$s, $q, $a]) Faq::updateOrCreate(['scope' => $s, 'question' => $q], ['answer' => $a, 'sort_order' => $i]);

        /* ---------- projects ---------- */
        $video1 = $this->asset('videos', 'demo-1.mp4');
        $video2 = $this->asset('videos', 'demo-2.mp4');
        $projects = [
            ['bluebee-platform', 'BlueBee Platform', 'Bb', 'web', '2026', 'Full-stack developer', 'Live', 'B2B multi-vendor reselling e-commerce with reseller pricing and a full admin.', ['Laravel', 'MySQL', 'REST', 'Sanctum'], 'BlueBee is a B2B reselling platform where vendors list products and resellers sell them under their own margins.', 'Multiple vendors, reseller-specific pricing and a heavy admin had to live inside one coherent system.', 'An API-first Laravel architecture with role-based access, a per-reseller pricing engine and a themed storefront.', ['3 user roles', 'Per-reseller pricing', 'Full admin with RBAC'], ['Multi-vendor catalogue', 'Reseller price rules', 'Order and payout tracking', 'Role-based admin', 'Responsive storefront'], true, $video1],
            ['bluebee-mobile', 'BlueBee Mobile', 'Mb', 'mobile', '2026', 'Mobile developer', 'In progress', 'Customer and reseller app consuming the Laravel API.', ['React Native', 'Expo', 'Sanctum'], 'The mobile companion for BlueBee — customers browse and order, resellers manage their catalogue and orders on the go.', 'Reuse the same API for web and mobile without duplicating business logic.', 'An Expo app with a shared design language, token auth and offline-friendly lists.', ['iOS + Android', 'One shared API', 'Reseller tools on mobile'], ['Browse and order', 'Reseller dashboard', 'Push-ready architecture', 'Secure token auth', 'Smooth native navigation'], true, $video2],
            ['fashionbd-store', 'FashionBd Store', 'Fb', 'web', '2026', 'Full-stack developer', 'Live', 'Phase-by-phase fashion store built with Blade and Bootstrap.', ['Laravel', 'Blade', 'Bootstrap'], 'A full fashion e-commerce build delivered in phases — storefront first, then cart, checkout and admin.', 'Ship a complete store fast while keeping the codebase clean enough to grow.', 'Blade and Bootstrap for both frontend and admin, built phase by phase with reusable components.', ['Storefront + admin', 'Phased delivery', 'Reusable components'], ['Catalogue and filters', 'Cart and checkout', 'Order management', 'Coupons and banners', 'Admin dashboard'], true, null],
            ['admin-suite', 'Admin Suite', 'Ad', 'admin', '2025', 'Back-end developer', 'Reusable kit', 'Role-based admin panels with Spatie permissions and media library.', ['Spatie RBAC', 'Sneat', 'Sanctum'], 'A reusable admin foundation for client projects — roles, permissions, media and settings out of the box.', 'Every project needs an admin, and rebuilding it each time wastes weeks.', 'A Sneat-based Laravel admin kit with Spatie roles, a media library and a clean settings module.', ['Reusable kit', 'Granular permissions', 'Faster project starts'], ['Roles and permissions', 'Media library', 'Site settings', 'Powerful data tables', 'Clean, themeable UI'], false, null],
            ['shopbd-app', 'ShopBD App', 'Sb', 'mobile', '2025', 'Mobile developer', 'Shipped', 'E-commerce mobile app with cart, orders and wishlist.', ['Expo', 'React Native', 'REST'], 'A shopping app built with React Native and Expo on top of a Laravel REST API.', 'Keep the app fast and simple while handling carts, orders and wishlists reliably.', 'Lean screens, cached lists and a clear state layer that syncs with the API.', ['Fast browsing', 'Persistent cart', 'Order tracking'], ['Product lists and search', 'Cart and wishlist', 'Order history', 'Profile and addresses', 'Clean navigation'], false, null],
            ['portfolio-cms', 'Portfolio CMS', 'Pf', 'web', '2026', 'Designer + developer', 'This site', 'This very site — React front end with a Laravel admin-driven backend.', ['React', 'GSAP', 'Laravel'], 'A motion-first portfolio where every project, post and page is managed from a Laravel admin.', 'Rich scroll animation and 3D interaction without sacrificing performance or editability.', 'React + GSAP front end, Laravel REST API and an admin to manage content, SEO and media.', ['Admin-managed content', 'Fast and responsive', 'Motion that supports content'], ['3D project ring', 'Blog with detail pages', 'Dark and light themes', 'Accent colour settings', 'Search everywhere'], false, null],
        ];
        foreach ($projects as $i => [$slug, $title, $code, $c, $year, $role, $status, $summary, $tags, $overview, $challenge, $solution, $results, $features, $featured, $video]) {
            $cover = $this->asset('images', "p-$slug.jpg");
            $g1 = $this->asset('images', "p-$slug-1.jpg");
            $g2 = $this->asset('images', "p-$slug-2.jpg");
            $content =
                '<h2>Goals</h2><p>' . e($overview) . ' The brief was simple to say and hard to build: make it fast, make it easy to manage, and make it feel premium on every device.</p>' .
                '<ul><li>One clear experience for each type of user</li><li>Everything editable without touching code</li><li>A foundation that can keep growing</li></ul>' .
                $this->fig("p-$slug-1.jpg", 'left', "$title — main interface") .
                '<h2>How it works</h2><p>' . e($solution) . ' The interface talks to a versioned REST API, so the same business rules serve every screen. Validation, permissions and pricing live on the server; the client only renders what it is allowed to see.</p>' .
                '<p>Early on I wrote the data model on paper, then migrations, then the API contract, and only then the screens. That order kept rework low and made every later feature a small, safe step.</p>' .
                '<h3>Tech stack</h3><table><thead><tr><th>Layer</th><th>Choice</th><th>Why</th></tr></thead><tbody>' .
                '<tr><td>API</td><td>Laravel</td><td>Fast to build, great tooling, strong validation</td></tr><tr><td>Database</td><td>MySQL</td><td>Reliable relational data and reporting</td></tr><tr><td>Interface</td><td>' . e(implode(', ', array_slice($tags, 0, 2))) . '</td><td>Matches the project and the team</td></tr></tbody></table>' .
                $this->fig("p-$slug-2.jpg", 'right', "$title — detail screen") .
                '<h2>Challenges along the way</h2><p>' . e($challenge) . ' I solved it by splitting the work into small, reviewable phases and demoing after each one, so decisions stayed cheap to change.</p>' .
                '<blockquote>Good products are built in small steps, and every step ships something real.</blockquote>' .
                '<h2>What I learned</h2><p>Plan the data first, keep the API boring, and spend the saved time on details users feel: loading states, empty states, keyboard support and motion that explains instead of decorates.</p>' .
                $this->fig("p-$slug.jpg", 'center', "$title — overview", 'img-md');
            Project::updateOrCreate(['slug' => $slug], [
                'title' => $title, 'short_code' => $code, 'category_id' => $cat["project:$c"], 'summary' => $summary, 'cover_image' => $cover, 'hero_video' => $video,
                'role' => $role, 'client' => null, 'year' => $year, 'status' => $status, 'tags' => $tags, 'results' => $results, 'features' => $features,
                'gallery' => [['src' => $g1, 'caption' => "$title — screen 1"], ['src' => $g2, 'caption' => "$title — screen 2"], ['src' => $cover, 'caption' => "$title — overview"]],
                'overview' => '<p>' . e($overview) . '</p><p>The project combines a clean API, a role-aware admin and an interface built for real users — delivered in phases so every release is usable.</p>',
                'challenge' => '<p>' . e($challenge) . '</p>', 'solution' => '<p>' . e($solution) . '</p><ul><li>Versioned REST API</li><li>Role-based permissions</li><li>Reusable UI components</li></ul>',
                'content' => $content, 'is_featured' => $featured, 'is_published' => true, 'sort_order' => $i, 'published_at' => Carbon::now()->subDays(60 - $i * 7),
            ]);
        }

        /* ---------- blog posts: images at start / middle / end, aligned left / right / centre ---------- */
        $posts = [
            ['laravel-api-web-mobile', 'How I structure one Laravel API for web and mobile', 'architecture', 'One API, two clients — the structure, versioning and auth setup I reuse on every project.', ['Laravel', 'API', 'Sanctum'], 8, 12, true,
             ['inline-1.jpg', 'left', 'The contract sits in the middle of everything'], ['inline-2.jpg', 'right', 'Web, mobile and admin share one source of truth'], ['inline-3.jpg', 'center', 'The finished structure'],
             [['Start with the contract', ['Before touching controllers I write down the JSON shape of every resource. Web and mobile read the same contract, so nothing changes silently.', 'A contract also makes estimates honest: you can see every endpoint a screen needs before writing a single line of UI.']],
              ['One API, versioned routes', ['Everything lives under /api/v1. Sanctum tokens serve mobile, while the web app uses the same endpoints. Controllers stay thin; resources and form requests do the shaping and validation.', 'When a breaking change is unavoidable, a new /v2 route group ships next to the old one and both live for a while.']],
              ['Keep the admin separate', ['The admin panel is its own consumer with its own permissions. Spatie roles decide what each token can do, so the same domain logic serves customers, resellers and staff safely.']],
              ['What I would do again', ['Resources for every response, form requests for every input, feature tests for every permission boundary. It is not exciting, and that is exactly why it works.']]],
             "Route::prefix('v1')->middleware('auth:sanctum')->group(function () {\n    Route::apiResource('products', ProductController::class);\n    Route::post('orders', [OrderController::class, 'store']);\n});"],
            ['gsap-scrolltrigger-patterns', 'GSAP ScrollTrigger patterns I use on every project', 'motion', 'Scrub, pin, batch — a small toolbox of scroll animation patterns that stay fast.', ['GSAP', 'ScrollTrigger', 'Motion'], 6, 2, false,
             ['inline-4.jpg', 'right', 'Scrub ties animation progress to the scrollbar'], ['inline-5.jpg', 'left', 'One pinned moment per page is plenty'], ['inline-6.jpg', 'center', 'Cleaning up keeps pages smooth'],
             [['Scrub only what needs it', ['Scrubbing ties an animation to scroll position. Use it for hero parallax and text reveals, and let everything else play once.', 'Add a small amount of smoothing so motion feels physical instead of mechanical.']],
              ['Pin sparingly', ['Pinned sections feel cinematic but cost attention. One pinned moment per page is plenty, and it should always have a reason.']],
              ['Batch the boring stuff', ['ScrollTrigger.batch reveals lists of cards with a gentle stagger and one observer — far cheaper than a trigger for every element.']],
              ['Always clean up', ['Wrap page animations in a context and revert it when the page changes. It prevents stacked triggers and layout glitches.']]],
             "gsap.to('.hero h1', {\n  yPercent: -30,\n  scrollTrigger: { trigger: '.hero', scrub: 1 }\n});"],
            ['spatie-rbac-without-headaches', 'Spatie RBAC without the headaches', 'laravel', 'Roles, permissions and seeders that stay readable as your admin panel grows.', ['Laravel', 'Spatie', 'RBAC'], 7, 28, false,
             ['inline-2.jpg', 'center', 'Roles are bundles of permissions'], ['inline-1.jpg', 'left', 'Gate every action, not every page'], ['inline-5.jpg', 'right', 'Seed once, same everywhere'],
             [['Roles are labels, permissions are rules', ['Check permissions in code, never roles. A role is just a bundle you can reshuffle later without touching a single controller.']],
              ['Seed everything', ['Keep permissions in a seeder so every environment matches. A new feature adds a permission and one line in the seeder.', 'Run the seeder on deploy and you will never debug "works on my machine" access problems again.']],
              ['Test the boundaries', ['Write a few feature tests that prove a restricted user gets a 403. They catch leaks long before a client does.']]],
             "\$admin = Role::findByName('admin');\n\$admin->givePermissionTo('products.update');"],
            ['react-native-vs-web-one-api', 'React Native vs web: sharing one API', 'mobile', 'What to share between your web and mobile apps — and what to keep separate.', ['React Native', 'Expo', 'API'], 5, 5, false,
             ['inline-3.jpg', 'right', 'Same API, different clients'], ['inline-4.jpg', 'left', 'Design tokens travel between apps'], ['inline-6.jpg', 'center', 'One auth flow for both'],
             [['Share the API, not the UI', ['The contract, validation rules and auth flow are shared. Screens are not — each platform deserves its own native feel.']],
              ['Design tokens travel', ['Colours, spacing and type scales can live in one place and feed both apps, keeping the brand consistent.']],
              ['Handle auth once', ['Token auth with Sanctum works for both clients. Store the token securely and refresh it in one small module.']]],
             "const res = await fetch(API + '/v1/products', {\n  headers: { Authorization: 'Bearer ' + token },\n});"],
            ['dark-mode-that-isnt-flat', 'Designing dark mode that doesn’t feel flat', 'design', 'Layered surfaces, soft borders and one confident accent colour.', ['Design', 'CSS', 'Dark mode'], 4, 19, false,
             ['inline-5.jpg', 'center', 'Surface layers create depth'], ['inline-1.jpg', 'right', 'Hairline borders do the heavy lifting'], ['inline-2.jpg', 'left', 'One accent, used sparingly'],
             [['Not pure black', ['Pure black next to white text vibrates. A very dark neutral with a slightly lighter card colour feels calmer and more premium.']],
              ['Layer your surfaces', ['Use two or three surface tones and hairline borders. Depth comes from contrast between layers, not shadows.']],
              ['Let the accent breathe', ['One accent colour, used sparingly, guides the eye. Make it themeable so users can pick their own.']]],
             ":root[data-theme=\"dark\"] {\n  --bg: #060607;\n  --card: #111114;\n  --line: #ffffff1f;\n}"],
            ['study-system-fullstack-devops', 'My daily study system for full-stack and DevOps', 'career', 'How I split a long study day between building, learning and reviewing.', ['Career', 'Learning', 'DevOps'], 6, 2, false,
             ['inline-6.jpg', 'left', 'Blocks beat to-do lists'], ['inline-3.jpg', 'right', 'Build while you learn'], ['inline-4.jpg', 'center', 'Review weekly'],
             [['Time-box the day', ['I split the day into focused blocks — one for new concepts, one for building, one for review — so nothing crowds out the rest.']],
              ['Build while you learn', ['Every new topic goes straight into a small project. Reading alone fades; shipping something sticks.']],
              ['Review weekly', ['Once a week I list what I built, what broke and what to learn next. That list becomes the plan for the following week.']]],
             ''],
        ];
        foreach ($posts as $i => [$slug, $title, $c, $excerpt, $tags, $min, $daysAgo, $featured, $start, $mid, $end, $sections, $code]) {
            $html = $this->fig(...$start);
            foreach ($sections as $k => [$h, $paras]) {
                $html .= '<h2>' . e($h) . '</h2>';
                foreach ($paras as $p) $html .= '<p>' . e($p) . '</p>';
                if ($k === 1) $html .= $this->fig(...$mid) . ($code ? '<pre class="language-php"><code>' . e($code) . '</code></pre>' : '');
            }
            $html .= '<blockquote>Small, consistent steps beat big, rare leaps.</blockquote><h2>Wrapping up</h2><p>None of this is magic — it is a handful of habits applied every time. Pick one, use it on your next project, and tell me how it goes.</p>' . $this->fig(...$end) . '<p>Thanks for reading!</p>';
            Post::updateOrCreate(['slug' => $slug], [
                'title' => $title, 'category_id' => $cat["blog:$c"], 'excerpt' => $excerpt, 'cover_image' => $this->asset('images', "b-$slug.jpg"), 'content' => $html, 'tags' => $tags,
                'author_name' => 'Shahadat', 'read_time' => $min, 'views' => 40 + $i * 17, 'is_featured' => $featured, 'is_published' => true, 'published_at' => Carbon::now()->subDays($daysAgo + $i * 3),
            ]);
        }

        /* ---------- sample inbox + subscribers ---------- */
        if (!ContactMessage::exists()) {
            foreach ([
                ['Amina Rahman', 'amina@example.com', 'Web app', '$2k – $5k', 'Hi! We need an e-commerce site with a custom admin panel and a mobile app later. Can we talk this week?', 'new'],
                ['Daniel Kim', 'daniel@example.com', 'Animation / UI', '$500 – $2k', 'Loved your portfolio animations. Could you build a landing page with similar scroll effects for our startup?', 'read'],
                ['Tanvir Hasan', 'tanvir@example.com', 'Admin panel', 'Under $500', 'Looking for a quick Laravel admin dashboard for our small shop. What would you need from us?', 'replied'],
            ] as $i => [$n, $e, $t, $b, $m, $s]) ContactMessage::create(['name' => $n, 'email' => $e, 'project_type' => $t, 'budget' => $b, 'message' => $m, 'status' => $s, 'mail_sent' => true, 'created_at' => now()->subDays($i * 3 + 1), 'updated_at' => now()->subDays($i * 3 + 1)]);
        }
        foreach (['reader1@example.com', 'reader2@example.com'] as $mail) Subscriber::firstOrCreate(['email' => $mail]);
    }
}
