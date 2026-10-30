import { useState } from 'react';
import TLink from '../components/TLink';
import HeroCanvas from '../components/HeroCanvas';
import VizCanvas from '../components/VizCanvas';
import { CtaBand, K, Marquee } from '../components/Bits';
import { SplitWords } from '../lib/utils';
import { gsap } from '../lib/gsap';
import { useGsap, revealRv, scrubWords, countUp, introDelay } from '../hooks/useGsap';
import { useApi, useTitle } from '../hooks/useApi';
import { useSite } from '../context/Site';
import { usePrefs } from '../context/Prefs';

const TABS = [
  { n: 'Build', l: 'Structure', h: 'Give an idea structure.', p: 'Products and interfaces that connect real needs to working software.', a: 'Explore the products ↗', u: '/projects' },
  { n: 'Teach', l: 'Clarity', h: 'Make it clear.', p: 'Notes and walkthroughs that help other developers grow.', a: 'Read the notes ↗', u: '/blogs' },
  { n: 'Connect', l: 'Connection', h: 'Build the right thing, together.', p: 'Working closely with founders and teams to ship ideas.', a: 'Start a conversation ↗', u: '/contact' },
];
const LABELS = ['Three disciplines', 'Structure', 'Clarity', 'Connection'];

export default function Home() {
  const { settings: s, skills } = useSite();
  const { motion, prefs, isDark } = usePrefs();
  const { data: projects } = useApi('/projects');
  const [tab, setTab] = useState(0);        // which Build/Teach/Connect text is shown
  const [mode, setMode] = useState(0);      // 0 = dot wave until the visitor clicks a tab
  useTitle(`${s.site_name} — ${s.site_tagline}`);

  const featured = projects ? (projects.filter((p) => p.featured).length ? projects.filter((p) => p.featured) : projects).slice(0, 3) : [];
  const stats = [1, 2, 3].map((i) => ({ v: s[`stat${i}_value`], suf: s[`stat${i}_suffix`], l: s[`stat${i}_label`] }));
  const words = skills.length ? skills.map((k) => k.name.replace(/\s*\(.*\)/, '')) : ['Laravel', 'React', 'Expo', 'MySQL', 'GSAP'];

  const ref = useGsap((root) => {
    gsap.set('#nm .c', { yPercent: 115, opacity: 0, rotate: 6 });
    gsap.timeline({ delay: introDelay() })
      .from('#hcv', { opacity: 0, scale: 1.08, duration: 1.6, ease: 'power3.out' })
      .to('#nm .c', { yPercent: 0, opacity: 1, rotate: 0, duration: 1.1, stagger: 0.04, ease: 'power4.out' }, 0.1)
      .from('#nm em,#hk,.hs,.hb>*,.lab,.hf>*', { y: 24, opacity: 0, stagger: 0.06, duration: 0.8 }, 0.5)
      .from('.stats>div', { y: 30, opacity: 0, stagger: 0.1 }, 0.9);
    gsap.timeline({ scrollTrigger: { trigger: '#hero', scrub: 1, start: 'top top', end: 'bottom top' } })
      .to('.hm', { yPercent: -25, opacity: 0.15 }, 0).to('.lab', { opacity: 0 }, 0);
    countUp(root); scrubWords(root); revealRv(root);
    gsap.from('.vz', { clipPath: 'inset(0 0 100% 0)', duration: 1.2, ease: 'power4.out', scrollTrigger: { trigger: '.vz', start: 'top 85%' } });
    gsap.from('.tabs button', { y: 30, opacity: 0, stagger: 0.1, scrollTrigger: { trigger: '.tabs', start: 'top 92%' } });
  }, []);
  const workRef = useGsap(() => {
    gsap.from('.pr', { y: 60, opacity: 0, stagger: 0.12, scrollTrigger: { trigger: '#fp', start: 'top 85%' } });
  }, [featured.length]);

  const t = TABS[tab];
  return (
    <div className="pg" ref={ref}>
      <div id="stage" className="dk">
        <section id="hero" style={{ paddingBottom: 0 }}>
          <HeroCanvas photo={s.hero_photo_cors || s.hero_photo} motion={motion} />
          <div className="lab l1">● 01 / Presence<b>+</b>Engineering<br />Design<br />Motion</div>
          <div className="lab r1">{s.location}<br />{s.hero_available === '1' && '● Available for work'}</div>
          <div className="hm">
            <p className="mono" id="hk">{s.hero_kicker}</p>
            <h1 id="nm">{[...s.hero_name].map((c, i) => <span className="c" key={i}>{c}</span>)} <em>{s.hero_accent}</em></h1>
            <p className="hs">{s.hero_text}</p>
            <div className="hb">
              <TLink className="pbtn mag" to="/projects" data-c="VIEW">{s.hero_cta} <span>↓</span></TLink>
              <TLink className="ulk" to="/contact">Start a conversation ↗</TLink>
            </div>
          </div>
          <div className="hf mono"><span>Scroll to discover ↓</span><span>Building for the web since {s.since_year}</span><span>{s.location}</span></div>
        </section>
        <div className="stats">
          {stats.map((x, i) => (
            <div key={i}><b><i data-n={x.v} style={{ fontStyle: 'normal' }}>{motion ? 0 : x.v}</i><sup>{x.suf}</sup></b><span className="mono">{x.l}</span></div>
          ))}
          <div><b style={{ fontSize: '1.6rem', paddingTop: 8 }}>The work. <em>Real impact.</em></b><TLink className="mono" to="/projects">View projects ↗</TLink></div>
        </div>
      </div>

      <section id="pres">
        <K n="01" left="The person behind the work" right="3 disciplines, one perspective" />
        <div className="pgr">
          <div className="vz"><VizCanvas mode={mode} accent={prefs.accent} dark={isDark} /><small className="mono" id="vl">{LABELS[mode]}</small></div>
          <div className="pt2">
            <h2 className="ws"><SplitWords>Technology is the starting point. <em>People are the point.</em></SplitWords></h2>
            <p>I connect engineering, design and business.</p>
            <p className="m">Since {s.since_year} I’ve been building and learning across the product, the interface and the server — shipping complete apps for real people.</p>
            <TLink className="ulk" to="/about">Read my story ↗</TLink>
          </div>
        </div>
        <div className="tabs">{TABS.map((x, i) => <button key={x.n} className={i === tab ? 'on' : ''} onClick={() => { setTab(i); setMode(i + 1); }}><span>{x.n}</span><span>↗</span></button>)}</div>
        <div className="tx sw" key={tab}><h3>{t.h}</h3><p>{t.p}</p><TLink className="ulk" to={t.u}>{t.a}</TLink></div>
      </section>

      <section id="work" ref={workRef}>
        <K n="02" left="Selected work" right="Built, tested, put into the world" />
        <h2 className="rv">Ideas are a start. <em>This is the work.</em></h2>
        <div className="pl" id="fp">
          {featured.map((p, i) => (
            <TLink key={p.slug} className="pr" to={`/projects/${p.slug}`} data-p={`0${i + 1}`}>
              <small>0{i + 1}</small><h3>{p.title}</h3><span className="t mono">{p.tags.slice(0, 3).join(' · ')}</span>
            </TLink>
          ))}
        </div>
      </section>

      <Marquee words={words} />
      <section className="cta2">
        <K n="03" left="Let’s make it matter" right="Open for projects" />
        <h2 className="rv">Something great starts with <em>a hello.</em></h2><br />
        <TLink className="cta mag rv" to="/contact" data-c="HI">Let’s connect ↗</TLink>
      </section>
    </div>
  );
}
