import { createContext, useContext, useEffect, useState } from 'react';
import { api, API } from '../api';

const Ctx = createContext(null);
export const useSite = () => useContext(Ctx);

export function SiteProvider({ children }) {
  const [site, setSite] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => { api('/bootstrap').then(setSite).catch((e) => setError(e)); }, []);

  if (error) {
    return (
      <div className="err-screen">
        <div>
          <h1 style={{ fontFamily: 'var(--sf)', fontWeight: 400, fontSize: '3rem' }}>Backend not reachable</h1>
          <p style={{ marginTop: 14, color: '#8a8a94' }}>
            The site could not load data from <code>{API}</code>.<br />
            Start Laravel with <code>php artisan serve</code> and check <code>VITE_API_URL</code> in <code>frontend/.env</code>.
          </p>
        </div>
      </div>
    );
  }
  if (!site) return <div className="loading-screen">…</div>;
  return <Ctx.Provider value={site}>{children}</Ctx.Provider>;
}
