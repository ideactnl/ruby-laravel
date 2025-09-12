import './bootstrap';

import './participant/dashboard';
import './participant/daily-view';
import './participant/export';

if (window.Alpine) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.Alpine.start());
  } else {
    window.Alpine.start();
  }
}