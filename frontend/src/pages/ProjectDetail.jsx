import { useParams } from 'react-router-dom';
import TLink from '../components/TLink';
import { CtaBand, K, Prose } from '../components/Bits';
import { SplitChars } from '../lib/utils';
import { gsap } from '../lib/gsap';
import { useGsap, flipTitle, revealRv } from '../hooks/useGsap';
import { useApi, useTitle } from '../hooks/useApi';

export default function ProjectDetail() {
  const { slug } = useParams();
  const { data: p, loading, error } = useApi(`/projects/${slug}`);
  useTitle(p ? p.seo_title || p.title : undefined);

  const ref = useGsap((root) => {
    flipTitle(root); revealRv(root);
    gsap.from('.cover', { rotationX: 20, scale: 0.85, opacity: 0, transformPerspective: 1100, duration: 1.3, ease: 'power4.out', delay: 0.3, clearProps: 'transform,opacity' });
    gsap.from('.gp', { rotationY: -35, opacity: 0, transformPerspective: 1000, stagger: 0.15, duration: 1, ease: 'power3.out', scrollTrigger: { trigger: '.gal', start: 'top 85%' }, clearProps: 'transform,opacity' });
  }, [p?.slug]);

  if (loading) return <div className="pg"><section className="pt"><span className="mono">Loading…</span></section></div>;
  if (error || !p) return <div className="pg"><section className="pt"><h1>Not found</h1><p className="lead">This project does not exist. <TLink className="ulk" to="/projects">← All projects</TLink></p></section></div>;

  return (
    <div className="pg" ref={ref}>
      <section className="pt">
        <TLink className="ulk" to="/projects">← All projects</TLink><div style={{ height: 28 }} />
        <span className="mono">(Case study) · {p.year}</span>
        <h1><SplitChars>{p.title}</SplitChars></h1>
        <div className="meta rv">
          <div><small className="mono">Role</small>{p.role || '—'}</div><div><small className="mono">Year</small>{p.year || '—'}</div>
          <div><small className="mono">Status</small>{p.status || '—'}</div><div><small className="mono">Type</small>{p.category_name || '—'}</div>
        </div>
      </section>

      <section style={{ paddingTop: 20 }}>
        <div className="cover tilt" data-k=".4">
          <div className="cd">
            <div className="gl" />
            {p.cover ? <img src={p.cover} alt={p.title} style={{ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'cover' }} /> : <b className="init ly" data-d="26">{p.code}</b>}
          </div>
        </div>
      </section>

      {p.video && (
        <section style={{ paddingTop: 0 }}>
          <div className="vid rv">{p.video_kind === 'embed' ? <iframe src={p.video} title="Project video" allow="accelerometer; autoplay; encrypted-media; picture-in-picture" allowFullScreen /> : <video src={p.video} poster={p.cover} controls playsInline />}</div>
        </section>
      )}

      <section>
        <K n="01" left="Overview" right={p.status} />
        <div className="two">
          <h2 className="rv">{p.summary}</h2>
          <div>
            <Prose html={p.overview} />
            <div className="chips rv">{p.tags.map((t) => <span key={t}>{t}</span>)}</div>
            <div style={{ display: 'flex', gap: 14, flexWrap: 'wrap', marginTop: 28 }}>
              {p.live_url && <a className="pbtn" href={p.live_url} target="_blank" rel="noreferrer">Live site ↗</a>}
              {p.repo_url && <a className="pbtn" href={p.repo_url} target="_blank" rel="noreferrer">Source code ↗</a>}
            </div>
          </div>
        </div>
      </section>

      <section>
        <K n="02" left="Approach" right="Challenge · Solution · Results" />
        <div className="three">
          <div className="rv"><h3>Challenge</h3><Prose html={p.challenge} /></div>
          <div className="rv"><h3>Solution</h3><Prose html={p.solution} /></div>
          <div className="rv"><h3>Results</h3><ul>{p.results.map((r) => <li key={r}>{r}</li>)}</ul></div>
        </div>
      </section>

      {p.features.length > 0 && (
        <section>
          <K n="03" left="Key features" right={`${p.features.length} highlights`} />
          <div className="flist">{p.features.map((f, i) => <div className="rv" key={f}><small>0{i + 1}</small>{f}</div>)}</div>
        </section>
      )}

      {p.gallery.length > 0 && (
        <section>
          <K n="04" left="Screens" right="Hover the panels" />
          <div className="gal">{p.gallery.map((g, i) => <figure className="gp tilt" data-s="1" data-k=".8" key={i}><img src={g.src} alt={g.caption || p.title} loading="lazy" />{g.caption && <figcaption>{g.caption}</figcaption>}</figure>)}</div>
        </section>
      )}

      {p.content && <section><K n="05" left="Full case study" right="The details" /><Prose html={p.content} /></section>}

      <section><TLink className="nextp rv" to={`/projects/${p.next.slug}`}><small className="mono">Next project</small><h2>{p.next.title} →</h2></TLink></section>
      <CtaBand />
    </div>
  );
}
