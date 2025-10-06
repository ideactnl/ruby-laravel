/**
 * PBAC Icons Base Configuration
 */

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
