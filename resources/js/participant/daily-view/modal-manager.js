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
    const pillar = item.pillar;
    
    switch(item.key) {
      case 'blood_loss':
        return ModalContentGenerators.generateBloodLossModal(pillar);
      case 'pain':
        return ModalContentGenerators.generatePainModal(pillar);
      case 'impact':
        return ModalContentGenerators.generateImpactModal(pillar);
      case 'general_health':
        return ModalContentGenerators.generateEnergyModal(pillar);
      case 'mood':
        return ModalContentGenerators.generateMoodModal(pillar);
      case 'stool_urine':
        return ModalContentGenerators.generateStoolModal(pillar);
      case 'sleep':
        return ModalContentGenerators.generateSleepModal(pillar);
      case 'diet':
        return ModalContentGenerators.generateDietModal(pillar);
      case 'exercise':
        return ModalContentGenerators.generateExerciseModal(pillar);
      case 'sex':
        return ModalContentGenerators.generateSexModal(pillar);
      case 'notes':
        return ModalContentGenerators.generateNotesModal(pillar);
      default:
        return '<p>No detailed information available.</p>';
    }
  }
}
