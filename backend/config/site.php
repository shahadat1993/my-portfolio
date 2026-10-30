<?php
/*
 | Site settings schema. Every field here appears in Admin → Site Settings
 | and is exposed to the React frontend through GET /api/bootstrap
 | (unless 'public' => false).
 | types: text, textarea, email, url, number, toggle, select, image
 */
return [
 'frontend_url' => trim(explode(',', env('FRONTEND_URL', 'http://localhost:5173'))[0]),
 'groups' => [
  'general' => ['label'=>'General','icon'=>'bx-cog','fields'=>[
    ['key'=>'site_name','label'=>'Site name','type'=>'text','default'=>'Shahadat Dev'],
    ['key'=>'site_tagline','label'=>'Tagline','type'=>'text','default'=>'Full-stack developer · Laravel · React'],
    ['key'=>'location','label'=>'Location','type'=>'text','default'=>'Chattogram, Bangladesh'],
    ['key'=>'since_year','label'=>'Building since (year)','type'=>'number','default'=>'2021'],
    ['key'=>'footer_text','label'=>'Footer line','type'=>'text','default'=>'Built with intent · always evolving'],
  ]],
  'hero' => ['label'=>'Home hero','icon'=>'bx-home-circle','fields'=>[
    ['key'=>'hero_kicker','label'=>'Small line above name','type'=>'text','default'=>'A full-stack perspective on the web'],
    ['key'=>'hero_name','label'=>'Name (big text)','type'=>'text','default'=>'Shahadat'],
    ['key'=>'hero_accent','label'=>'Accent word (italic, coloured)','type'=>'text','default'=>'Dev'],
    ['key'=>'hero_text','label'=>'Hero paragraph','type'=>'textarea','default'=>'I build products, ship full-stack apps and craft interfaces that feel alive.'],
    ['key'=>'hero_cta','label'=>'Main button label','type'=>'text','default'=>'Explore my work'],
    ['key'=>'hero_available','label'=>'Show "Available for work"','type'=>'toggle','default'=>'1'],
    ['key'=>'hero_photo','label'=>'Hero portrait photo','type'=>'image','help'=>'Used for the pixel-scanline portrait. Use a face photo with dark/plain background, portrait ratio.'],
  ]],
  'stats' => ['label'=>'Home stats','icon'=>'bx-bar-chart-alt-2','fields'=>[
    ['key'=>'stat1_value','label'=>'Stat 1 number','type'=>'number','default'=>'30'],['key'=>'stat1_suffix','label'=>'Stat 1 suffix','type'=>'text','default'=>'+'],['key'=>'stat1_label','label'=>'Stat 1 label','type'=>'text','default'=>'Projects shipped'],
    ['key'=>'stat2_value','label'=>'Stat 2 number','type'=>'number','default'=>'5'],['key'=>'stat2_suffix','label'=>'Stat 2 suffix','type'=>'text','default'=>'y'],['key'=>'stat2_label','label'=>'Stat 2 label','type'=>'text','default'=>'Building for the web'],
    ['key'=>'stat3_value','label'=>'Stat 3 number','type'=>'number','default'=>'12'],['key'=>'stat3_suffix','label'=>'Stat 3 suffix','type'=>'text','default'=>'+'],['key'=>'stat3_label','label'=>'Stat 3 label','type'=>'text','default'=>'Clients served'],
  ]],
  'about' => ['label'=>'About page','icon'=>'bx-user','fields'=>[
    ['key'=>'about_lead','label'=>'Lead paragraph','type'=>'textarea','default'=>'I’m a full-stack developer who ships whole products — API, admin panel, web and mobile apps — with an eye for motion and detail.'],
    ['key'=>'about_p1','label'=>'Paragraph 2','type'=>'textarea','default'=>'I work with Laravel on the back end, React and React Native on the front, and I care about clean architecture, secure roles and interfaces that feel alive.'],
    ['key'=>'about_p2','label'=>'Paragraph 3','type'=>'textarea','default'=>'Right now I’m growing into DevOps and cloud so I can take products from idea to infrastructure — and build a small web agency around that craft.'],
    ['key'=>'about_photo','label'=>'About photo','type'=>'image'],
    ['key'=>'fact_focus','label'=>'Fact: Focus','type'=>'text','default'=>'Full-stack · Mobile · Motion'],
    ['key'=>'fact_experience','label'=>'Fact: Experience','type'=>'text','default'=>'5 years building'],
    ['key'=>'fact_status','label'=>'Fact: Status','type'=>'text','default'=>'Open for projects'],
  ]],
  'contact' => ['label'=>'Contact & email','icon'=>'bx-envelope','fields'=>[
    ['key'=>'contact_email','label'=>'Receive messages at (comma separated)','type'=>'text','default'=>'hello@example.com','public'=>false,'help'=>'Every contact-form message is emailed here (needs MAIL_* in .env).'],
    ['key'=>'public_email','label'=>'Public email shown on site','type'=>'email','default'=>'hello@example.com'],
    ['key'=>'phone','label'=>'Phone / WhatsApp','type'=>'text','default'=>''],
    ['key'=>'availability_text','label'=>'Availability text','type'=>'text','default'=>'Open for new projects'],
    ['key'=>'auto_reply','label'=>'Send auto-reply to the visitor','type'=>'toggle','default'=>'1','public'=>false],
    ['key'=>'auto_reply_subject','label'=>'Auto-reply subject','type'=>'text','default'=>'Thanks for reaching out!','public'=>false],
    ['key'=>'auto_reply_body','label'=>'Auto-reply message','type'=>'textarea','default'=>"Hi {name},\n\nThanks for your message — I’ve received it and will reply within one working day.\n\n— Shahadat",'public'=>false,'help'=>'Use {name} for the visitor’s name.'],
    ['key'=>'sheet_webhook_url','label'=>'Google Sheet webhook URL (Apps Script)','type'=>'url','default'=>'','public'=>false,'help'=>'Paste the Web App URL of the Apps Script from docs/google-apps-script.gs. Leave empty to disable.'],
    ['key'=>'sheet_secret','label'=>'Google Sheet secret token','type'=>'text','default'=>'','public'=>false,'help'=>'Must match SECRET in the Apps Script.'],
  ]],
  'social' => ['label'=>'Social links','icon'=>'bx-link-alt','fields'=>[
    ['key'=>'github_url','label'=>'GitHub','type'=>'url','default'=>'https://github.com/shahadat1993'],
    ['key'=>'linkedin_url','label'=>'LinkedIn','type'=>'url','default'=>''],
    ['key'=>'facebook_url','label'=>'Facebook','type'=>'url','default'=>''],
    ['key'=>'twitter_url','label'=>'X / Twitter','type'=>'url','default'=>''],
    ['key'=>'instagram_url','label'=>'Instagram','type'=>'url','default'=>''],
    ['key'=>'youtube_url','label'=>'YouTube','type'=>'url','default'=>''],
  ]],
  'appearance' => ['label'=>'Appearance','icon'=>'bx-palette','fields'=>[
    ['key'=>'default_theme','label'=>'Default theme for new visitors','type'=>'select','options'=>['dark'=>'Dark','light'=>'Light','auto'=>'Follow device'],'default'=>'dark','help'=>'Accent colours are managed in Appearance → Theme colours.'],
    ['key'=>'default_font','label'=>'Default heading font','type'=>'select','options'=>['serif'=>'Serif (elegant)','sans'=>'Sans (modern)'],'default'=>'serif'],
  ]],
]];
