/**
 * Translation Utilities for JavaScript
 * Provides helper functions for accessing translations in JavaScript
 */

/**
 * Get translation with fallback
 * @param {string} key - Translation key
 * @param {string} fallback - Fallback text if translation not found
 * @returns {string} Translated text or fallback
 */
export function __(key, fallback = key) {
  // Try general translations first
  if (window.translations && window.translations[key]) {
    return window.translations[key];
  }
  
  // Try card status translations
  if (window.cardStatusTranslations && window.cardStatusTranslations[key]) {
    return window.cardStatusTranslations[key];
  }
  
  // Try tooltip translations
  if (window.tooltipTranslations && window.tooltipTranslations[key]) {
    return window.tooltipTranslations[key];
  }
  
  // Try modal translations
  if (window.modalTranslations && window.modalTranslations[key]) {
    return window.modalTranslations[key];
  }
  
  // Try health domain translations
  if (window.healthDomainTranslations && window.healthDomainTranslations[key]) {
    return window.healthDomainTranslations[key];
  }
  
  // Return fallback if not found
  return fallback;
}

/**
 * Get health domain translation
 * @param {string} domain - Health domain key
 * @param {string} fallback - Fallback text
 * @returns {string} Translated domain name
 */
export function getDomainTranslation(domain, fallback = domain) {
  return window.healthDomainTranslations?.[domain] || fallback;
}

/**
 * Get modal translation
 * @param {string} key - Modal translation key
 * @param {string} fallback - Fallback text
 * @returns {string} Translated modal text
 */
export function getModalTranslation(key, fallback = key) {
  return window.modalTranslations?.[key] || fallback;
}

/**
 * Get card status translation
 * @param {string} key - Card status translation key
 * @param {string} fallback - Fallback text
 * @returns {string} Translated card status text
 */
export function getCardStatusTranslation(key, fallback = key) {
  return window.cardStatusTranslations?.[key] || fallback;
}

/**
 * Get tooltip translation
 * @param {string} key - Tooltip translation key
 * @param {string} fallback - Fallback text
 * @returns {string} Translated tooltip text
 */
export function getTooltipTranslation(key, fallback = key) {
  return window.tooltipTranslations?.[key] || fallback;
}

/**
 * Get current locale
 * @returns {string} Current locale (en, nl)
 */
export function getCurrentLocale() {
  return window.appLocale || 'en';
}

/**
 * Check if current locale is Dutch
 * @returns {boolean} True if Dutch locale
 */
export function isDutch() {
  return getCurrentLocale() === 'nl';
}

/**
 * Get locale for date formatting
 * @returns {string} Locale string for Intl APIs (en-US, nl-NL)
 */
export function getDateLocale() {
  return isDutch() ? 'nl-NL' : 'en-US';
}

/**
 * Format date with current locale
 * @param {Date|string} date - Date to format
 * @param {object} options - Intl.DateTimeFormat options
 * @returns {string} Formatted date string
 */
export function formatDate(date, options = {}) {
  try {
    const d = date instanceof Date ? date : new Date(date);
    return d.toLocaleDateString(getDateLocale(), options);
  } catch (e) {
    return date.toString();
  }
}
