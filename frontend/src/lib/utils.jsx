import { Children, cloneElement, isValidElement } from 'react';

export const cls = (...a) => a.filter(Boolean).join(' ');
export const tick = (() => {
  let ac;
  return (f = 520, on = true) => {
    if (!on) return;
    try {
      ac = ac || new (window.AudioContext || window.webkitAudioContext)();
      const o = ac.createOscillator(), g = ac.createGain();
      o.type = 'square'; o.frequency.value = f; g.gain.value = 0.02;
      o.connect(g); g.connect(ac.destination); o.start();
      g.gain.exponentialRampToValueAtTime(0.0001, ac.currentTime + 0.06); o.stop(ac.currentTime + 0.07);
    } catch {}
  };
})();

/** <SplitChars>Hello <em>world</em></SplitChars> → every letter in its own span (3D flip-in animation). */
export function SplitChars({ children }) {
  return Children.map(children, (c, i) => {
    if (typeof c === 'string') {
      return c.split(/(\s+)/).filter(Boolean).map((w, k) =>
        /^\s+$/.test(w) ? ' ' : (
          <span className="wd" key={`${i}-${k}`}>{[...w].map((ch, j) => <span className="c3" key={j}>{ch}</span>)}</span>
        ));
    }
    if (isValidElement(c)) return cloneElement(c, { key: i }, <SplitChars>{c.props.children}</SplitChars>);
    return c;
  });
}

/** Every word in its own span (scroll-scrub reveal). */
export function SplitWords({ children }) {
  return Children.map(children, (c, i) => {
    if (typeof c === 'string') {
      return c.split(/(\s+)/).filter(Boolean).map((w, k) => (/^\s+$/.test(w) ? ' ' : <span className="w" key={`${i}-${k}`}>{w}</span>));
    }
    if (isValidElement(c)) return cloneElement(c, { key: i }, <SplitWords>{c.props.children}</SplitWords>);
    return c;
  });
}

/** Adds ids to <h2> in rich-text HTML and returns { html, toc }. */
export function withToc(html = '') {
  if (!html) return { html: '', toc: [] };
  const doc = new DOMParser().parseFromString(`<div id="r">${html}</div>`, 'text/html');
  const toc = [];
  doc.querySelectorAll('#r h2').forEach((h, i) => { h.id = `sec-${i}`; toc.push({ id: h.id, text: h.textContent }); });
  return { html: doc.getElementById('r').innerHTML, toc };
}
