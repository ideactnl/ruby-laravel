/**
 * PBAC Icons Base Configuration
 */

import { getTooltipTranslation } from '../utils/translations.js';

// Base path for all PBAC icons
export const ICON_BASE_PATH = '/images/';

/**
 * Create icon result object
 */
export function createIconResult(iconFile, label) {
  return {
    src: `${ICON_BASE_PATH}${iconFile}`,
    label
  };
}

/**
 * Get translated tooltip
 */
export function getTranslatedTooltip(key) {
  return getTooltipTranslation(key);
}
