import React from 'react';
import { createRoot } from 'react-dom/client'; // Ubah ini
import App from './App';
import './index.css'; // Pastikan ini ada untuk mengimpor Tailwind CSS

const container = document.getElementById('root');
const root = createRoot(container); // Ubah ini

root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);