/**
 * Modal Manager Module
 * Handles modal state and content generation
 */

import { ModalContentGenerators } from './modal-content-generators.js';

export class ModalManager {
  constructor(component) {
    this.component = component;
  }

  /**
   * Open modal with domain-specific content
   */
  openDomainModal(item) {
    this.component.modalData = item;
    this.component.modalContent = this.generateModalContent(item);
    this.component.showModal = true;
    document.body.style.overflow = 'hidden';
  }

  /**
   * Close modal and restore state
   */
  closeModal() {
    this.component.showModal = false;
    this.component.modalData = null;
    this.component.modalContent = '';
    document.body.style.overflow = '';
  }

  /**
   * Generate modal content based on pillar type
   */
  generateModalContent(item) {
    return ModalContentGenerators.generateModalContent(item.pillar, item.key);
  }
}
