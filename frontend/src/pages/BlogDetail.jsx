import { useEffect, useMemo, useState } from 'react';
import { useParams } from 'react-router-dom';
import TLink from '../components/TLink';
import { CtaBand, K, Prose } from '../components/Bits';
import { SplitChars, withToc } from '../lib/utils';
import { useGsap, flipTitle, revealRv } from '../hooks/useGsap';
import { gsap } from '../lib/gsap';
import { useApi, useTitle } from '../hooks/useApi';
import { useScroll } from '../context/Smooth';

export default function BlogDetail() {
  const { slug } = useParams();
  const { data: p, loading, error } = useApi(`/posts/${slug}`);
  const scroll = useScroll();
  const [active, setActive] = useState(0);
  const [copied, setCopied] = useState(false);
  useTitle(p ? p.seo_title || p.title : undefined);
  const { html, toc } = useMemo(() => withToc(p?.content), [p]);

  // highlight the section currently on screen in the "On this page" list
  useEffect(() => {
    if (!toc.length) return undefined;
    const f = () => { let a = 0; toc.forEach((t, i) => { const el = document.getElementById(t.id); if (el && el.getBoundingClientRect().top < innerHeight * 0.4) a = i; }); setActive(a); };
    addEventListener('scroll', f, { passive: true }); f();
    return () => removeEventListener('scroll', f);
  }, [toc]);

  const ref = useGsap((root) => {
    flipTitle(root); revealRv(root);
    gsap.from('.hcover', { scale: 0.92, opacity: 0, duration: 1.2, ease: 'power3.out', delay: 0.3 });
    gsap.from('.toc', { x: -40, opacity: 0, duration: 0.9, delay: 0.5 });
  }, [p?.slug]);

  if (loading) return <div className="pg"><section className="pt"><span className="mono">Loading…</span></section></div>;
  if (error || !p) return <div className="pg"><section className="pt"><h1>Not found</h1><p className="lead">This article does not exist. <TLink className="ulk" to="/blogs">← All posts</TLink></p></section></div>;

  return (
    <div className="pg" ref={ref}>
      <section className="pt">
        <TLink className="ulk" to="/blogs">← All posts</TLink><div style={{ height: 28 }} />
        <span className="mono">{p.category_name} · {p.date} · {p.minutes} min read</span>
        <h1 className="art-t"><SplitChars>{p.title}</SplitChars></h1>
        {p.excerpt && <p className="lead">{p.excerpt}</p>}
      </section>
      <section className="art" style={{ paddingTop: 20 }}>
        <aside className="toc">
          <small className="mono">On this page</small>
          {toc.map((t, i) => <a key={t.id} href={`#${t.id}`} className={i === active ? 'on' : ''} onClick={(e) => { e.preventDefault(); scroll.to(document.getElementById(t.id), -100); }}>{t.text}</a>)}
          <button className="rs" onClick={() => { navigator.clipboard?.writeText(location.href); setCopied(true); }}>{copied ? 'Link copied ✓' : 'Copy link'}</button>
        </aside>
        <article>
          {p.cover && <img className="hcover" src={p.cover} alt={p.title} />}
          <Prose html={html} />
          {p.tags.length > 0 && <div className="chips">{p.tags.map((t) => <span key={t}>{t}</span>)}</div>}
          <div className="author rv" style={{ marginTop: 50 }}><b>{(p.author || 'S')[0]}</b><div><strong>{p.author}</strong><p>Full-stack developer. Writes about Laravel, React, motion and shipping products.</p></div></div>
        </article>
      </section>
      {p.related.length > 0 && (
        <section>
          <K n="→" left="Keep reading" right="Related articles" />
          {p.related.map((x) => <TLink key={x.slug} className="bl rv" to={`/blogs/${x.slug}`}><small>{x.date.toUpperCase()}</small><h3>{x.title}</h3><small>{x.category_name?.toUpperCase()} · {x.minutes} MIN</small></TLink>)}
        </section>
      )}
      <CtaBand />
    </div>
  );
}
