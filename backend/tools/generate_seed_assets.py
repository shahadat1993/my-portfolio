"""Generates the demo images + videos used by the database seeder.
Run once (already run for you):  python tools/generate_seed_assets.py   (needs: pip install pillow, ffmpeg for videos)"""
import os, math, random, subprocess
from PIL import Image, ImageDraw, ImageFilter, ImageFont

OUT = os.path.join(os.path.dirname(__file__), '..', 'database', 'seeders', 'assets')
IMG = os.path.join(OUT, 'images'); VID = os.path.join(OUT, 'videos')
os.makedirs(IMG, exist_ok=True); os.makedirs(VID, exist_ok=True)
random.seed(7)

def font(size, bold=True):
    for p in ['/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf' if bold else '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', 'DejaVuSans-Bold.ttf', 'arial.ttf']:
        try: return ImageFont.truetype(p, size)
        except Exception: pass
    return ImageFont.load_default()

def hex2rgb(h): h = h.lstrip('#'); return tuple(int(h[i:i+2], 16) for i in (0, 2, 4))

def gradient(w, h, c1, c2, angle=35):
    img = Image.new('RGB', (w, h)); px = img.load()
    a = math.radians(angle); ca, sa = math.cos(a), math.sin(a)
    mx = abs(w * ca) + abs(h * sa)
    for y in range(h):
        for x in range(w):
            t = ((x - w / 2) * ca + (y - h / 2) * sa) / mx + .5
            t = max(0, min(1, t))
            px[x, y] = tuple(int(c1[i] + (c2[i] - c1[i]) * t) for i in range(3))
    return img

def blobs(img, colors, n=5):
    w, h = img.size
    layer = Image.new('RGBA', img.size, (0, 0, 0, 0)); d = ImageDraw.Draw(layer)
    for i in range(n):
        r = random.randint(int(h * .25), int(h * .6)); x = random.randint(-r // 2, w + r // 2); y = random.randint(-r // 2, h + r // 2)
        d.ellipse([x - r, y - r, x + r, y + r], fill=colors[i % len(colors)] + (110,))
    layer = layer.filter(ImageFilter.GaussianBlur(h // 7))
    img.paste(layer, (0, 0), layer)
    return img

def mock_window(img, title, accent, scale=.62, dark=True):
    w, h = img.size; ww, wh = int(w * scale), int(h * scale * 1.05)
    x0, y0 = (w - ww) // 2, int(h * .17)
    sh = Image.new('RGBA', img.size, (0, 0, 0, 0)); ImageDraw.Draw(sh).rounded_rectangle([x0 + 10, y0 + 30, x0 + ww + 10, y0 + wh + 30], 22, fill=(0, 0, 0, 120))
    img.paste(sh.filter(ImageFilter.GaussianBlur(22)), (0, 0), sh.filter(ImageFilter.GaussianBlur(22)))
    d = ImageDraw.Draw(img, 'RGBA'); bg = (14, 14, 18, 245) if dark else (250, 250, 252, 245); fg = (255, 255, 255) if dark else (20, 20, 25)
    d.rounded_rectangle([x0, y0, x0 + ww, y0 + wh], 22, fill=bg)
    for i, c in enumerate([(255, 95, 86), (255, 189, 46), (39, 201, 63)]): d.ellipse([x0 + 22 + i * 24, y0 + 18, x0 + 36 + i * 24, y0 + 32], fill=c)
    d.rounded_rectangle([x0 + 110, y0 + 14, x0 + ww - 30, y0 + 36], 10, fill=(255, 255, 255, 25) if dark else (0, 0, 0, 18))
    d.text((x0 + 36, y0 + 64), title, font=font(max(26, h // 14)), fill=fg)
    d.rounded_rectangle([x0 + 36, y0 + 64 + h // 11, x0 + 36 + ww // 4, y0 + 64 + h // 11 + 26], 13, fill=accent + (255,))
    for r in range(3):
        for c in range(3):
            cx = x0 + 36 + c * (ww - 72) // 3; cy = y0 + int(wh * .50) + r * int(wh * .17)
            d.rounded_rectangle([cx, cy, cx + (ww - 72) // 3 - 14, cy + int(wh * .14)], 14, fill=(255, 255, 255, 22) if dark else (0, 0, 0, 14))
            d.rounded_rectangle([cx + 14, cy + 14, cx + 14 + (ww - 72) // 8, cy + 28], 7, fill=accent + (200,))
    return img

def save(img, name, q=82): img.convert('RGB').save(os.path.join(IMG, name), 'JPEG', quality=q, optimize=True)

P = [('bluebee-platform', 'BlueBee', '#ff6a3d', '#7c5cff'), ('bluebee-mobile', 'BlueBee App', '#7c5cff', '#00d1b2'), ('fashionbd-store', 'FashionBd', '#ff4fa3', '#f5b400'),
     ('admin-suite', 'Admin Suite', '#4da3ff', '#7c5cff'), ('shopbd-app', 'ShopBD', '#00d1b2', '#4da3ff'), ('portfolio-cms', 'Portfolio CMS', '#d4ff3a', '#ff6a3d')]
for slug, name, a, b in P:
    ca, cb = hex2rgb(a), hex2rgb(b)
    for i, suffix in enumerate(['', '-1', '-2']):
        img = gradient(1200, 750, ca if i % 2 == 0 else cb, cb if i % 2 == 0 else ca, 30 + i * 40)
        blobs(img, [ca, cb, (255, 255, 255)]); mock_window(img, name + ('' if i == 0 else ' · screen %d' % i), ca if i % 2 else cb, scale=.64 - i * .04, dark=(i != 2))
        save(img, 'p-%s%s.jpg' % (slug, suffix))

B = [('laravel-api-web-mobile', 'One Laravel API', '#7c5cff', '#4da3ff'), ('gsap-scrolltrigger-patterns', 'ScrollTrigger', '#ff6a3d', '#f5b400'), ('spatie-rbac-without-headaches', 'Spatie RBAC', '#ff4fa3', '#7c5cff'),
     ('react-native-vs-web-one-api', 'RN vs Web', '#00d1b2', '#4da3ff'), ('dark-mode-that-isnt-flat', 'Dark mode', '#d4ff3a', '#00d1b2'), ('study-system-fullstack-devops', 'Study system', '#f5b400', '#ff4fa3')]
for slug, name, a, b in B:
    img = gradient(1400, 800, hex2rgb(a), hex2rgb(b), 25); blobs(img, [hex2rgb(a), hex2rgb(b), (255, 255, 255)], 6)
    d = ImageDraw.Draw(img, 'RGBA'); d.text((80, 520), name, font=font(110), fill=(255, 255, 255, 240)); d.rounded_rectangle([80, 680, 360, 700], 10, fill=(255, 255, 255, 190))
    save(img, 'b-%s.jpg' % slug)
INL = [('#ff6a3d', '#7c5cff', 'Diagram'), ('#00d1b2', '#4da3ff', 'Workflow'), ('#ff4fa3', '#f5b400', 'Preview'), ('#7c5cff', '#00d1b2', 'Result'), ('#d4ff3a', '#ff6a3d', 'Notes'), ('#4da3ff', '#ff4fa3', 'Summary')]
for i, (a, b, t) in enumerate(INL, 1):
    img = gradient(1000, 700, hex2rgb(a), hex2rgb(b), 50 + i * 20); blobs(img, [hex2rgb(a), hex2rgb(b)], 4); mock_window(img, t, hex2rgb(b if i % 2 else a), .7, dark=bool(i % 2))
    save(img, 'inline-%d.jpg' % i)

# portrait placeholders (dark, studio-like silhouette) for hero + about
def portrait(name, tint):
    w, h = 900, 1100; img = Image.new('RGB', (w, h), (6, 6, 8)); d = ImageDraw.Draw(img, 'RGBA')
    glow = Image.new('RGBA', (w, h), (0, 0, 0, 0)); ImageDraw.Draw(glow).ellipse([w * .1, h * .05, w * .9, h * .7], fill=tint + (90,)); img.paste(glow.filter(ImageFilter.GaussianBlur(120)), (0, 0), glow.filter(ImageFilter.GaussianBlur(120)))
    d.ellipse([w * .05, h * .72, w * .95, h * 1.5], fill=(40, 40, 46, 255))
    d.rectangle([w * .42, h * .56, w * .58, h * .78], fill=(150, 150, 156, 255))
    d.ellipse([w * .3, h * .17, w * .7, h * .62], fill=(210, 210, 216, 255))
    d.pieslice([w * .28, h * .12, w * .72, h * .46], 180, 360, fill=(18, 18, 22, 255))
    for x in (.41, .59): d.ellipse([w * x - 16, h * .38, w * x + 16, h * .405], fill=(30, 30, 34, 255))
    d.arc([w * .43, h * .47, w * .57, h * .54], 20, 160, fill=(60, 60, 66, 255), width=6)
    save(img.filter(ImageFilter.GaussianBlur(1.2)), name, 86)
portrait('hero.jpg', (255, 106, 61)); portrait('about.jpg', (124, 92, 255))

# demo videos
def video(name, c0, c1):
    cmd = ['ffmpeg', '-y', '-v', 'error', '-f', 'lavfi', '-i', 'gradients=s=854x480:d=6:r=24:speed=0.03:c0=%s:c1=%s:nb_colors=2' % (c0, c1), '-c:v', 'libx264', '-pix_fmt', 'yuv420p', '-crf', '30', '-preset', 'veryfast', '-movflags', '+faststart', os.path.join(VID, name)]
    try: subprocess.run(cmd, check=True)
    except Exception as e: print('video skipped:', e)
video('demo-1.mp4', '0xff6a3d', '0x7c5cff'); video('demo-2.mp4', '0x00d1b2', '0x4da3ff')
print('assets generated')
