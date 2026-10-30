// All backend calls live here. Change VITE_API_URL in .env to point to your Laravel server.
export const API = (import.meta.env.VITE_API_URL || 'http://localhost:8000').replace(/\/$/, '');

export async function api(path, opts = {}) {
  const res = await fetch(`${API}/api${path}`, {
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
    ...opts,
  });
  const json = await res.json().catch(() => ({}));
  if (!res.ok) throw Object.assign(new Error(json.message || 'Request failed'), { status: res.status, errors: json.errors });
  return json;
}
export const post = (path, body) => api(path, { method: 'POST', body: JSON.stringify(body) });
