/**
 * Visual Processor Module
 * Processes pillar data into visual display format for the daily view
 */

import { getPbacIcon } from '../pbac-icon-mapping.js';

export class VisualProcessor {
  /**
   * Process pillar data into visual display items
   */
  static processItems(pillars) {
    const items = [];
    
    // Define pillar configuration with visual properties
    const pillarConfig = [
      { key: 'blood_loss', label: 'Blood Loss', iconSrc: '/images/grid_blood_loss.png' },
      { key: 'pain', label: 'Pain', iconSrc: '/images/grid_pain.png' },
      { key: 'impact', label: 'Impact', iconSrc: '/images/grid_impact.png' },
      { key: 'general_health', label: 'General Health', iconSrc: '/images/grid_general_health.png' },
      { key: 'mood', label: 'Mood', iconSrc: '/images/grid_mood.png' },
      { key: 'stool_urine', label: 'Stool/Urine', iconSrc: '/images/grid_urine_stool.png' },
      { key: 'sleep', label: 'Sleep', iconSrc: '/images/grid_sleep.png' },
      { key: 'diet', label: 'Diet', iconSrc: '/images/grid_diet.png' },
      { key: 'exercise', label: 'Exercise', iconSrc: '/images/grid_sport.png' },
      { key: 'sex', label: 'Sexual Health', iconSrc: '/images/grid_sex.png' },
      { key: 'notes', label: 'Notes', iconSrc: '/images/grid_notes.png' }
    ];

    pillarConfig.forEach(config => {
      const pillar = pillars[config.key];
      if (pillar && this.hasPillarData(config.key, pillar)) {
        const visualItem = this.createVisualItem(config, pillar);
        items.push(visualItem);
      }
    });

    return items;
  }

  /**
   * Create a visual item from pillar config and data
   */
  static createVisualItem(config, pillar) {
    const item = {
      key: config.key,
      label: config.label,
      iconSrc: config.iconSrc,
      pillar: pillar,
      hasData: true,
      statusColor: 'bg-green-500',
      statusText: 'Data available'
    };

    // Generate visual elements based on pillar type
    switch (config.key) {
      case 'blood_loss':
        this.processBloodLossVisuals(item, pillar);
        break;
      case 'pain':
        this.processPainVisuals(item, pillar);
        break;
      case 'impact':
        this.processImpactVisuals(item, pillar);
        break;
      case 'general_health':
        this.processEnergyVisuals(item, pillar);
        break;
      case 'mood':
        this.processMoodVisuals(item, pillar);
        break;
      case 'stool_urine':
        this.processStoolVisuals(item, pillar);
        break;
      case 'sleep':
        this.processSleepVisuals(item, pillar);
        break;
      case 'diet':
        this.processDietVisuals(item, pillar);
        break;
      case 'exercise':
        this.processExerciseVisuals(item, pillar);
        break;
      case 'sex':
        this.processSexVisuals(item, pillar);
        break;
      case 'notes':
        this.processNotesVisuals(item, pillar);
        break;
    }

    return item;
  }

  /**
   * Process blood loss visual elements
   */
  static processBloodLossVisuals(item, pillar) {
    const amount = pillar.amount || 0;
    const severity = pillar.severity || 'light';
    const spotting = pillar.flags?.spotting;

    if (spotting) {
      item.statusIcon = {
        src: '/images/spotting.png',
        alt: 'Spotting'
      };
      item.context = 'Spotting detected';
    } else {
      // Create severity icons
      const severityLevels = ['very_light', 'light', 'moderate', 'heavy', 'very_heavy'];
      item.severityIcons = severityLevels.map((level, index) => ({
        src: `/images/blood_loss_${index + 1}.png`,
        alt: level,
        active: severity === level
      }));
      item.context = `${severity.replace('_', ' ')} flow`;
    }
  }

  /**
   * Process pain visual elements
   */
  static processPainVisuals(item, pillar) {
    const value = pillar.value || 0;
    const regions = pillar.regions || [];

    // Create pain level icons (6 levels)
    item.severityIcons = [];
    for (let i = 1; i <= 6; i++) {
      const isActive = Math.ceil(value / 2) >= i;
      item.severityIcons.push({
        src: `/images/smile_${i}.png`,
        alt: `Pain level ${i}`,
        active: isActive
      });
    }

    item.context = `Pain Level: ${value}/10`;
    if (regions.length > 0) {
    }
  }

  /**
   * Process mood visual elements
   */
  static processMoodVisuals(item, pillar) {
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    
    const balance = positives.length - negatives.length;
    let moodIcon = 'mood_1.png'; // calm
    let moodText = 'Balanced';
    
    if (balance > 1) {
      moodIcon = 'mood_2.png'; // happy
      moodText = 'Positive';
    } else if (balance < -1) {
      moodIcon = 'mood_7.png'; // sad
      moodText = 'Challenging';
    }

    item.statusIcon = {
      src: `/images/${moodIcon}`,
      alt: 'Overall mood'
    };
    
    item.context = `${moodText} Day`;
  }

  /**
   * Process other pillar types with basic visuals
   */
  static processImpactVisuals(item, pillar) {
    const grade = pillar.gradeYourDay || 0;
    const limitations = pillar.limitations || [];
    
    item.statusIcon = {
      src: '/images/grid_impact.png',
      alt: 'Impact'
    };
    item.context = `Grade: ${grade}/10`;
    if (limitations.length > 0) {
    }
  }

  static processEnergyVisuals(item, pillar) {
    const energy = pillar.energyLevel || 0;
    const symptoms = pillar.symptoms || [];
    
    item.statusIcon = {
      src: `/images/general_health_${Math.min(energy, 4)}.png`,
      alt: 'Energy level'
    };
    item.context = `Energy: ${energy}/5`;
    if (symptoms.length > 0) {
    }
  }

  static processStoolVisuals(item, pillar) {
    item.statusIcon = {
      src: '/images/grid_urine_stool.png',
      alt: 'Stool/Urine'
    };
    item.context = 'Recorded';
  }

  static processSleepVisuals(item, pillar) {
    const hours = pillar.calculatedHours || 0;
    
    item.statusIcon = {
      src: '/images/grid_sleep.png',
      alt: 'Sleep'
    };
    item.context = `${hours} hours`;
  }

  static processDietVisuals(item, pillar) {
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    const neutrals = pillar.neutrals || [];
    
    item.statusIcon = {
      src: '/images/grid_diet.png',
      alt: 'Diet'
    };
    item.context = 'Diet tracked';
  }

  static processExerciseVisuals(item, pillar) {
    const levels = pillar.levels || [];
    
    item.statusIcon = {
      src: '/images/grid_sport.png',
      alt: 'Exercise'
    };
    
    if (levels.includes('greater_sixty')) {
      item.context = '>60 minutes';
    } else if (levels.includes('thirty_to_sixty')) {
      item.context = '30-60 minutes';
    } else if (levels.includes('less_thirty')) {
      item.context = '<30 minutes';
    } else {
      item.context = 'Exercise completed';
    }
  }

  static processSexVisuals(item, pillar) {
    item.statusIcon = {
      src: '/images/grid_sex.png',
      alt: 'Sexual Health'
    };
    
    if (pillar.avoided) {
      item.context = 'Avoided';
    } else if (pillar.satisfied) {
      item.context = 'Satisfied';
    } else {
      item.context = 'Activity recorded';
    }
  }

  static processNotesVisuals(item, pillar) {
    item.statusIcon = {
      src: '/images/grid_notes.png',
      alt: 'Notes'
    };
    item.context = 'Note recorded';
  }

  /**
   * Check if pillar has meaningful data
   */
  static hasPillarData(key, pillar) {
    if (!pillar) return false;

    switch (key) {
      case 'blood_loss':
        return pillar.amount > 0 || pillar.flags?.spotting;
      case 'pain':
        return pillar.value > 0;
      case 'impact':
        return pillar.gradeYourDay !== undefined || (pillar.limitations && pillar.limitations.length > 0);
      case 'general_health':
        return pillar.energyLevel > 0 || (pillar.symptoms && pillar.symptoms.length > 0);
      case 'mood':
        return (pillar.positives && pillar.positives.length > 0) || (pillar.negatives && pillar.negatives.length > 0);
      case 'stool_urine':
        return pillar.urine?.blood || pillar.stool?.blood || pillar.stool?.consistency;
      case 'sleep':
        return pillar.calculatedHours > 0;
      case 'diet':
        return (pillar.positives && pillar.positives.length > 0) || 
               (pillar.negatives && pillar.negatives.length > 0) || 
               (pillar.neutrals && pillar.neutrals.length > 0);
      case 'exercise':
        return pillar.any === true;
      case 'sex':
        return pillar.today === true || pillar.avoided === true;
      case 'notes':
        return pillar.hasNote === true;
      default:
        return false;
    }
  }
}
