import { CtaBand, Cards, Chips, Faqs, K, PROCESS } from '../components/Bits';
import Acc from '../components/Accordion';
import { SplitChars, SplitWords } from '../lib/utils';
import { gsap } from '../lib/gsap';
import { useGsap, flipTitle, revealRv, scrubWords, countUp } from '../hooks/useGsap';
import { useTitle } from '../hooks/useApi';
import { useSite } from '../context/Site';
import { usePrefs } from '../context/Prefs';

const TOOLS = ['Laravel', 'React', 'React Native', 'Expo', 'MySQL', 'REST APIs', 'Sanctum', 'Spatie', 'Tailwind', 'Bootstrap', 'GSAP', 'Vite', 'Git', 'Linux'];

export default function About() {
  const { settings: s, services, skills, timeline, faqs } = useSite();
  const { motion } = usePrefs();
  useTitle(`About — ${s.site_name}`);
  const stats = [[s.stat1_value, s.stat1_suffix, s.stat1_label], [s.stat2_value, s.stat2_suffix, s.stat2_label], [s.stat3_value, s.stat3_suffix, s.stat3_label], [100, '%', 'Responsive builds']];

  const ref = useGsap((root) => {
    flipTitle(root); revealRv(root); scrubWords(root); countUp(root);
    gsap.from('.hcard', { scale: 0.8, rotationY: -40, opacity: 0, transformPerspective: 1000, duration: 1.3, ease: 'power4.out', clearProps: 'transform,opacity' });
    gsap.from('.stats>div', { y: 40, opacity: 0, stagger: 0.1, scrollTrigger: { trigger: '.stats', start: 'top 90%' } });
    root.querySelectorAll('.sk i').forEach((i) => gsap.from(i, { '--p': '0%', duration: 1.4, ease: 'power3.out', scrollTrigger: { trigger: i, start: 'top 92%' } }));
    gsap.to('.tl .f', { scaleY: 1, ease: 'none', scrollTrigger: { trigger: '.tl', scrub: true, start: 'top 70%', end: 'bottom 70%' } });
    root.querySelectorAll('.tl .s').forEach((el) => gsap.from(el, { x: -50, opacity: 0, scrollTrigger: { trigger: el, start: 'top 88%' } }));
  }, []);

  return (
    <div className="pg" ref={ref}>
      <section className="pt"><span className="mono">(About)</span><h1><SplitChars>Hello, I’m <em>{s.hero_name}.</em></SplitChars></h1></section>
      <section style={{ paddingTop: 30 }}>
        <div className="ab">
          <div className="tilt hcard" data-k="1">
            <div className="cd" style={{ background: 'linear-gradient(160deg,#7c5cff,var(--a))' }}>
              <div className="gl" />
              {s.about_photo ? <img src={s.about_photo} alt={s.hero_name} style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} /> : <b className="init ly" data-d="22">{s.hero_name.slice(0, 2).toUpperCase()}</b>}
              <span className="chip c1 ly" data-d="40">{s.location.split(',')[0]}</span>
              <span className="chip c3 ly" data-d="30">Since {s.since_year}</span>
            </div>
          </div>
          <div>
            <p className="ws"><SplitWords>{s.about_lead}</SplitWords></p>
            <p className="rv">{s.about_p1}</p>
            <p className="rv">{s.about_p2}</p>
            <div className="facts rv">
              <div><small className="mono">Based in</small>{s.location}</div><div><small className="mono">Focus</small>{s.fact_focus}</div>
              <div><small className="mono">Experience</small>{s.fact_experience}</div><div><small className="mono">Status</small>{s.fact_status}</div>
            </div>
          </div>
        </div>
      </section>

      <section>
        <K n="01" left="What I do" right={`${services.length} ways I help`} />
        {services.map((v, i) => <Acc key={v.title} n={`0${i + 1}`} q={v.title} a={v.description}><div className="tg2">{v.tags.map((t) => <span key={t}>{t}</span>)}</div></Acc>)}
      </section>

      <div className="dk stats">
        {stats.map(([v, suf, l], i) => <div key={i}><b><i data-n={v} style={{ fontStyle: 'normal' }}>{motion ? 0 : v}</i><sup>{suf}</sup></b><span className="mono">{l}</span></div>)}
      </div>

      <section>
        <K n="02" left="Toolbox" right="What I use every day" />
        <div className="sk">{skills.map((k) => <div key={k.name}><span>{k.name} <b>{k.level}%</b></span><i style={{ '--p': `${k.level}%` }} /></div>)}</div>
        <Chips items={TOOLS} />
      </section>

      <section><K n="03" left="How I work" right="Four calm steps" /><Cards items={PROCESS} /></section>

      <section>
        <K n="04" left="Journey" right="Started with code. Kept asking why." />
        <div className="tl"><div className="f" />
          {timeline.map((t) => <div className="s" key={t.title}><small className="mono">{t.label}</small><h3>{t.title}</h3><p>{t.description}</p></div>)}
        </div>
      </section>

      <section><K n="05" left="Questions" right="Good to know" /><Faqs items={faqs.filter((f) => f.scope === 'about')} /></section>
      <CtaBand />
    </div>
  );
}
