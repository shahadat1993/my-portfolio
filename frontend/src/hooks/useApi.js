import { useEffect, useState } from 'react';
import { api } from '../api';

/** const { data, loading, error } = useApi('/projects') */
export function useApi(path) {
  const [state, setState] = useState({ data: null, loading: true, error: null });
  useEffect(() => {
    let alive = true;
    setState({ data: null, loading: true, error: null });
    api(path).then((j) => alive && setState({ data: j.data ?? j, loading: false, error: null }))
      .catch((e) => alive && setState({ data: null, loading: false, error: e }));
    return () => { alive = false; };
  }, [path]);
  return state;
}

export function useTitle(t) {
  useEffect(() => { if (t) document.title = t; }, [t]);
}
