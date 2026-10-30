import { createContext, useContext, useState } from 'react';

const Ctx = createContext(null);
export const useUI = () => useContext(Ctx);

/** Which overlay is open: menu / search / settings. */
export function UIProvider({ children }) {
  const [menu, setMenu] = useState(false);
  const [search, setSearch] = useState(false);
  const [settings, setSettings] = useState(false);
  const closeAll = () => { setMenu(false); setSearch(false); setSettings(false); };
  return <Ctx.Provider value={{ menu, setMenu, search, setSearch, settings, setSettings, closeAll }}>{children}</Ctx.Provider>;
}
