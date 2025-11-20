/**
 * Card Icon Generators
 * Generates severity icon arrays with active highlighting for daily view cards
 * Each pillar type returns: { severityIcons: [...], statusIcon: {...} }
 */

import {
  BLOOD_LOSS_SEVERITY_LEVELS,
  IMPACT_LIMITATION_TYPES,
  MOOD_ICON_MAP,
  MOOD_KEYS,
  DIET_ICON_MAP,
  DIET_KEYS,
  EXERCISE_LEVELS,
  SYMPTOM_ICON_MAP,
  SYMPTOM_KEYS
} from './pillar-constants.js';

export class CardIconGenerators {

  /**
   * Blood Loss Icons - Spotting + 5 severity levels
   */
  static getBloodLossIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const severity = pillar.severity || 'none';
    const spotting = pillar.flags?.spotting;

    const severityIcons = [];

    severityIcons.push({
      src: '/images/spotting.png',
      alt: 'spotting',
      active: spotting
    });

    BLOOD_LOSS_SEVERITY_LEVELS.forEach((level, index) => {
      severityIcons.push({
        src: `/images/blood_loss_${index + 1}.png`,
        alt: level,
        active: severity === level && !spotting
      });
    });

    const statusIcon = {
      src: spotting ? '/images/spotting.png' : `/images/blood_loss_${Math.max(1, BLOOD_LOSS_SEVERITY_LEVELS.indexOf(severity) + 1)}.png`,
      alt: 'Blood Loss'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * Pain Icons - 6 pain level icons (smile faces)
   */
  static getPainIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const value = pillar.value ?? 0;

    let currentPainIcon = 1;
    if (value >= 9) currentPainIcon = 6;
    else if (value >= 7) currentPainIcon = 5;
    else if (value >= 5) currentPainIcon = 4;
    else if (value >= 3) currentPainIcon = 3;
    else if (value >= 2) currentPainIcon = 2;
    else currentPainIcon = 1;

    const severityIcons = [];
    for (let i = 1; i <= 6; i++) {
      severityIcons.push({
        src: `/images/smile_${i}.png`,
        alt: `Pain level ${i}`,
        active: i === currentPainIcon
      });
    }

    const statusIcon = {
      src: `/images/smile_${currentPainIcon}.png`,
      alt: 'Pain Level'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * Impact Icons - 11 limitation types
   */
  static getImpactIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const limitations = pillar.limitations || [];

    const severityIcons = IMPACT_LIMITATION_TYPES.map((type, index) => ({
      src: `/images/impact_${index + 1}.png`,
      alt: type.replace(/_/g, ' '),
      active: limitations.includes(type)
    }));

    let primaryIcon = '/images/impact_1.png';
    if (limitations.includes('used_medication')) {
      primaryIcon = '/images/impact_1.png';
    } else if (limitations.includes('missed_work')) {
      primaryIcon = '/images/impact_2.png';
    } else if (limitations.includes('missed_school')) {
      primaryIcon = '/images/impact_3.png';
    } else if (limitations.includes('could_not_sport')) {
      primaryIcon = '/images/impact_4.png';
    } else if (limitations.includes('missed_social_activities')) {
      primaryIcon = '/images/impact_5.png';
    } else if (limitations.includes('missed_leisure_activities')) {
      primaryIcon = '/images/impact_6.png';
    } else if (limitations.includes('had_to_sit_more')) {
      primaryIcon = '/images/impact_7.png';
    } else if (limitations.includes('had_to_lie_down')) {
      primaryIcon = '/images/impact_8.png';
    } else if (limitations.includes('had_to_stay_longer_in_bed')) {
      primaryIcon = '/images/impact_9.png';
    } else if (limitations.includes('could_not_do_unpaid_work')) {
      primaryIcon = '/images/impact_10.png';
    } else if (limitations.includes('other')) {
      primaryIcon = '/images/impact_11.png';
    }

    const statusIcon = {
      src: primaryIcon,
      alt: 'Daily Impact'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * General Health Icons - Show symptom icons
   */
  static getGeneralHealthIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const symptoms = pillar.symptoms || [];
    const energy = pillar.energyLevel;

    const severityIcons = [];
    SYMPTOM_KEYS.forEach(symptomKey => {
      const isActive = symptoms.includes(symptomKey);
      const iconSrc = `/images/${SYMPTOM_ICON_MAP[symptomKey] || 'general_health.png'}`;
      severityIcons.push({
        src: iconSrc,
        alt: symptomKey,
        active: isActive
      });
    });

    const energyIconMap = {
      [-2]: '/images/sleep.png',
      [-1]: '/images/general_health_1.png',
      [0]: '/images/general_health_2.png',
      [1]: '/images/general_health_3.png',
      [2]: '/images/general_health_4.png'
    };

    const statusIcon = {
      src: energyIconMap[energy] || '/images/general_health.png',
      alt: 'Energy Level'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * Mood Icons - 12 mood states
   */
  static getMoodIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    const allMoods = [...positives, ...negatives];

    const severityIcons = MOOD_KEYS.map(moodKey => {
      const isActive = allMoods.includes(moodKey) ||
        (moodKey === 'anxious' && allMoods.includes('anxious_stressed')) ||
        (moodKey === 'angry' && allMoods.includes('angry_irritable')) ||
        (moodKey === 'worthless' && allMoods.includes('worthless_guilty')) ||
        (moodKey === 'depressed' && allMoods.includes('depressed_sad_down')) ||
        (moodKey === 'hopes' && allMoods.includes('hopes'));

      return {
        src: `/images/${MOOD_ICON_MAP[moodKey]}`,
        alt: moodKey,
        active: isActive
      };
    });

    const balance = positives.length - negatives.length;
    let moodIcon = 'mood_1.png';
    if (balance > 1) {
      moodIcon = 'mood_2.png';
    } else if (balance < -1) {
      moodIcon = 'mood_7.png';
    }

    const statusIcon = {
      src: `/images/${moodIcon}`,
      alt: 'Mood State'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * Stool/Urine Icons - Blood detection + 6 consistency types
   */
  static getStoolUrineIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const hasUrineBlood = pillar.urine?.blood ?? false;
    const hasStoolBlood = pillar.stool?.blood ?? false;
    const consistency = pillar.stool?.consistency;

    const severityIcons = [
      { src: '/images/urine_stool.png', alt: 'Blood in stool/urine', active: hasUrineBlood || hasStoolBlood },
      { src: '/images/urine_stool_1.png', alt: 'Hard', active: consistency === 'hard' },
      { src: '/images/urine_stool_2.png', alt: 'Normal', active: consistency === 'normal' },
      { src: '/images/urine_stool_3.png', alt: 'Soft', active: consistency === 'soft' },
      { src: '/images/urine_stool_4.png', alt: 'Watery', active: consistency === 'watery' },
      { src: '/images/urine_stool_5.png', alt: 'Something else', active: consistency === 'something_else' },
      { src: '/images/urine_stool_6.png', alt: 'No stool', active: consistency === 'no_stool' }
    ];

    // Determine primary icon
    let primaryIcon = '/images/urine_stool_2.png';
    if (hasUrineBlood || hasStoolBlood) {
      primaryIcon = '/images/urine_stool.png';
    } else if (consistency) {
      const consistencyMap = {
        'hard': '/images/urine_stool_1.png',
        'normal': '/images/urine_stool_2.png',
        'soft': '/images/urine_stool_3.png',
        'watery': '/images/urine_stool_4.png',
        'something_else': '/images/urine_stool_5.png',
        'no_stool': '/images/urine_stool_6.png'
      };
      primaryIcon = consistencyMap[consistency] || primaryIcon;
    }

    const statusIcon = {
      src: primaryIcon,
      alt: 'Stool/Urine Status'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * Sleep Icons - Simple sleep icon (no severity range)
   */
  static getSleepIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const statusIcon = {
      src: '/images/sleep.png',
      alt: 'Sleep Quality'
    };

    return { severityIcons: [], statusIcon };
  }

  /**
   * Diet Icons - 12 diet item types
   */
  static getDietIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    const neutrals = pillar.neutrals || [];
    const allDietItems = [...positives, ...negatives, ...neutrals];

    const severityIcons = DIET_KEYS.map(dietKey => ({
      src: `/images/${DIET_ICON_MAP[dietKey]}`,
      alt: dietKey,
      active: allDietItems.includes(dietKey)
    }));

    const statusIcon = {
      src: '/images/grid_diet.png',
      alt: 'Diet Quality'
    };

    return { severityIcons, statusIcon };
  }

  /**
   * Exercise Icons - Duration levels and types
   */
  static getExerciseIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const hasDuration = pillar.levels && pillar.levels.length > 0;
    const hasImpact = pillar.impacts && (pillar.impacts.includes('high_impact') || pillar.impacts.includes('low_impact'));
    const isType1Active = hasDuration || hasImpact;
    
    const hasPrecisionExercise = pillar.impacts && pillar.impacts.includes('relaxation_exercise');
    const isType2Active = hasPrecisionExercise;

    const severityIcons = [
      {
        src: '/images/exercise_type_1.png',
        alt: 'Exercise Type 1 (High Impact / General)',
        active: isType1Active
      },
      {
        src: '/images/exercise_type_2.png',
        alt: 'Exercise Type 2 (Relaxation / Precision)',
        active: isType2Active
      }
    ];

    const statusIcon = {
      src: '/images/sport.png',
      alt: 'Exercise Activity'
    };

    return { severityIcons, statusIcon };
  }


  /**
   * Sex Icons - Simple sex icon (no severity range)
   */
  static getSexIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const statusIcon = {
      src: '/images/sex.png',
      alt: 'Sexual Health'
    };

    return { severityIcons: [], statusIcon };
  }

  /**
   * Notes Icons - Simple notes icon (no severity range)
   */
  static getNotesIcons(pillar) {
    if (!pillar) return { severityIcons: [], statusIcon: null };

    const statusIcon = {
      src: '/images/grid_notes.png',
      alt: 'Notes'
    };

    return { severityIcons: [], statusIcon };
  }
}
