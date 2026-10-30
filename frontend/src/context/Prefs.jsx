import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { useSite } from './Site';

const Ctx = createContext(null);
export const usePrefs = () => useContext(Ctx);
const KEY = 'pf-prefs-v1';
const lum = (hex) => { const n = parseInt(hex.slice(1), 16); return 0.299 * (n >> 16) + 0.587 * ((n >> 8) & 255) + 0.114 * (n & 255); };

export function PrefsProvider({ children }) {
  const site = useSite();
  const [saved, setSaved] = useState(() => { try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch { return {}; } });
  const reduced = useMemo(() => window.matchMedia?.('(prefers-reduced-motion: reduce)').matches, []);

  // defaults come from the admin dashboard (Site settings + Theme colours); a visitor's own choice wins
  const defAccent = site.colors.find((c) => c.is_default)?.hex || site.colors[0]?.hex || '#ff6a3d';
  const prefs = {
    theme: saved.theme || site.settings.default_theme || 'dark',
    accent: saved.accent || defAccent,
    font: saved.font || site.settings.default_font || 'serif',
    motion: saved.motion ?? true,
    smooth: saved.smooth ?? true,
    cursor: saved.cursor ?? true,
    sound: saved.sound ?? false,
  };
  const motion = prefs.motion && !reduced;

  useEffect(() => { try { localStorage.setItem(KEY, JSON.stringify(saved)); } catch {} }, [saved]);
  useEffect(() => {
    const r = document.documentElement;
    if (prefs.theme === 'auto') r.removeAttribute('data-theme'); else r.dataset.theme = prefs.theme;
    r.style.setProperty('--a', prefs.accent);
    r.style.setProperty('--ai', lum(prefs.accent) > 140 ? '#0e0e10' : '#ffffff');
    r.dataset.ft = prefs.font;
    r.classList.toggle('st', !motion);
    document.body.classList.toggle('nocur', !prefs.cursor);
  }, [prefs.theme, prefs.accent, prefs.font, prefs.cursor, motion]);

  const set = useCallback((k, v) => setSaved((s) => ({ ...s, [k]: v })), []);
  const reset = useCallback(() => setSaved({}), []);
  const isDark = prefs.theme === 'dark' || (prefs.theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
  const toggleTheme = () => set('theme', isDark ? 'light' : 'dark');

  return <Ctx.Provider value={{ prefs, motion, set, reset, isDark, toggleTheme }}>{children}</Ctx.Provider>;
}
