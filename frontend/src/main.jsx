import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import { SiteProvider } from './context/Site';
import { PrefsProvider } from './context/Prefs';
import { SmoothProvider } from './context/Smooth';
import { UIProvider } from './context/UI';
import { TransitionProvider } from './context/Transition';
import './styles.css';

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <BrowserRouter>
      <SiteProvider>
        <PrefsProvider>
          <SmoothProvider>
            <UIProvider>
              <TransitionProvider>
                <App />
              </TransitionProvider>
            </UIProvider>
          </SmoothProvider>
        </PrefsProvider>
      </SiteProvider>
    </BrowserRouter>
  </React.StrictMode>
);
