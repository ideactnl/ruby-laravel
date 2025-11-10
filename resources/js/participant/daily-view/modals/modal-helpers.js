/**
 * Modal Helper Utilities
 * Reusable helper functions for generating consistent modal HTML content
 */

export class ModalHelpers {
  
  /**
   * Create a section container with consistent styling and extra spacing
   */
  static createSection(bgColor, borderColor, content) {
    return `<div class="bg-${bgColor}-50 border border-${borderColor}-200 rounded-lg p-4 mb-6">${content}</div>`;
  }

  /**
   * Create a section header with consistent styling
   */
  static createSectionHeader(title, textColor = 'gray-800') {
    return `<h4 class="font-medium text-${textColor} mb-2">${title}</h4>`;
  }

  /**
   * Create a label-value pair with consistent styling
   */
  static createLabelValue(label, value, textColor = 'gray-700') {
    return `<p class="text-sm text-${textColor} mb-1"><strong>${label}</strong> ${value}</p>`;
  }

  /**
   * Create description text with consistent styling
   */
  static createDescription(text, textColor = 'gray-600', size = 'xs') {
    return `<p class="text-${size} text-${textColor} mb-2">${text}</p>`;
  }

  /**
   * Create an icon grid container - responsive flex layout with extra spacing
   */
  static createIconGrid() {
    return '<div class="flex flex-wrap justify-center items-center gap-1 sm:gap-2 mb-8 px-2">';
  }

  /**
   * Create an icon with active/inactive styling - responsive design
   */
  static createIcon(iconPath, altText, isActive = false) {
    const containerClasses = isActive 
      ? 'w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-blue-100 border-2 border-blue-500 rounded-full p-1 flex items-center justify-center' 
      : 'w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 opacity-30 rounded-full p-1 flex items-center justify-center';
    const imgClasses = 'w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 object-contain';
    return `<div class="${containerClasses}"><img src="/images/${iconPath}" alt="${altText}" class="${imgClasses}"></div>`;
  }

  /**
   * Create a list item with icon and description
   */
  static createListItem(iconPath, label, description = null, textColor = 'gray-700') {
    let content = `<div class="mb-3">`;
    content += `<div class="flex items-center gap-2 text-sm text-${textColor} font-medium">`;
    if (iconPath) {
      content += `<img src="/images/${iconPath}" alt="${label}" class="w-4 h-4 object-contain">`;
    }
    content += `<span>${label}</span>`;
    content += `</div>`;
    if (description) {
      content += this.createDescription(description, textColor, 'xs');
    }
    content += `</div>`;
    return content;
  }

  /**
   * Create a status indicator with color coding
   */
  static createStatusIndicator(status, message, type = 'info') {
    const colorMap = {
      'success': 'green',
      'warning': 'yellow', 
      'error': 'red',
      'info': 'blue'
    };
    const color = colorMap[type] || 'gray';
    const statusText = status ? `${status}: ` : '';
    return `<p class="text-${color}-600 font-medium">${statusText}${message}</p>`;
  }

  /**
   * Create a main modal container
   */
  static createModalContainer(content) {
    return `<div class="space-y-4">${content}</div>`;
  }

  /**
   * Create a centered header section with extra bottom spacing
   */
  static createCenteredHeader(title, subtitle = null, iconPath = null) {
    let content = '<div class="text-center mb-8">';
    content += `<h3 class="text-lg font-semibold mb-3">${title}</h3>`;
    if (iconPath) {
      content += `<img src="/images/${iconPath}" alt="${title}" class="w-16 h-16 object-contain mx-auto mb-2">`;
    }
    if (subtitle) {
      content += `<p class="text-gray-600">${subtitle}</p>`;
    }
    content += '</div>';
    return content;
  }

  /**
   * Create a grade/rating display
   */
  static createGradeDisplay(grade, maxGrade, label, color = 'gray-600') {
    return `<p class="text-${color}">${label}: ${grade}/${maxGrade}</p>`;
  }

  /**
   * Create a warning message
   */
  static createWarning(message, type = 'warning') {
    const icon = type === 'danger' ? '⚠️' : 'ℹ️';
    const colorClass = type === 'danger' ? 'text-red-700' : 'text-yellow-700';
    return `<p class="text-xs ${colorClass} font-medium mt-3">${icon} ${message}</p>`;
  }

  /**
   * Create a two-column grid layout
   */
  static createTwoColumnGrid(content) {
    return `<div class="grid grid-cols-2 gap-2">${content}</div>`;
  }

  /**
   * Create a single column layout
   */
  static createSingleColumnLayout(content) {
    return `<div class="grid grid-cols-1 gap-2">${content}</div>`;
  }

  /**
   * Create a flex container with items
   */
  static createFlexContainer(content, alignment = 'center', gap = '2') {
    return `<div class="flex justify-${alignment} items-center gap-${gap}">${content}</div>`;
  }

  /**
   * Safely escape HTML content
   */
  static escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Format text with line breaks preserved
   */
  static formatTextWithBreaks(text) {
    return `<p class="whitespace-pre-wrap leading-relaxed">${this.escapeHtml(text)}</p>`;
  }
}
