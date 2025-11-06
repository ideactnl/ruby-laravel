/**
 * Refactored Modal Content Generators - Refactored for multilingual support
 * Clean architecture using constants, validators, and helper utilities
 */

import { PillarDataValidators } from '../cards/pillar-data-validators.js';
import { ModalHelpers } from './modal-helpers.js';
import { getModalTranslation } from '../../utils/translations.js';
import {
  BLOOD_LOSS_SEVERITY_LEVELS, BLOOD_LOSS_SEVERITY_LABELS,
  PAIN_REGION_LABELS, MOOD_ICON_MAP, MOOD_KEYS, MOOD_LABELS,
  DIET_ICON_MAP, DIET_KEYS, DIET_LABELS, IMPACT_LIMITATION_TYPES, IMPACT_LIMITATION_LABELS,
  ENERGY_LEVEL_LABELS, SYMPTOM_LABELS, SYMPTOM_ICON_MAP, SYMPTOM_KEYS, STOOL_CONSISTENCY_MAP, STOOL_URINE_LABELS,
  EXERCISE_DURATION_LABELS, EXERCISE_TYPE_LABELS,
  SLEEP_QUALITY_LABELS, SLEEP_ISSUE_LABELS,
  SEX_ISSUE_LABELS, SEX_STATUS_LABELS
} from '../cards/pillar-constants.js';

export class ModalContentGenerators {

  /**
   * Generate Blood Loss modal content
   */
  static generateBloodLossModal(pillar) {
    if (!PillarDataValidators.hasBloodLossData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No blood loss data recorded.'}</p>`;
    }

    const severity = pillar?.severity || 'none';
    const flags = pillar?.flags || {};
    const spotting = flags.spotting;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_blood_loss_title') || 'Blood Loss Details');

    content += '<div class="text-center mb-6">';
    content += `<h3 class="text-lg font-semibold mb-3">${getModalTranslation('modal_blood_loss_severity_title') || 'Blood Loss Severity'}</h3>`;
    content += '<div class="flex justify-center items-center gap-1 mb-4">';

    const spottingActive = spotting;
    const spottingClasses = spottingActive ? 'opacity-100 bg-orange-100 border-2 border-orange-500 rounded-full p-1' : 'opacity-30';
    content += `<div class="${spottingClasses}"><img src="/images/spotting.png" alt="${getModalTranslation('modal_spotting_title') || 'Spotting'}" class="w-8 h-8 object-contain"></div>`;

    BLOOD_LOSS_SEVERITY_LEVELS.forEach((level, index) => {
      const isActive = severity === level && !spotting;
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      const label = BLOOD_LOSS_SEVERITY_LABELS[level] ? BLOOD_LOSS_SEVERITY_LABELS[level]() : level;
      content += `<div class="${classes}"><img src="/images/blood_loss_${index + 1}.png" alt="${label}" class="w-8 h-8 object-contain"></div>`;
    });

    content += '</div>';
    content += '</div>';

    if (spotting) {
      const spottingContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_spotting_title') || 'Spotting Detected', 'orange-800');
      content += ModalHelpers.createSection('orange', 'orange', spottingContent);
    } else if (severity && severity !== 'none') {
      const severityLabel = BLOOD_LOSS_SEVERITY_LABELS[severity] ? BLOOD_LOSS_SEVERITY_LABELS[severity]() : severity;
      const severityContent = 
        ModalHelpers.createSectionHeader(getModalTranslation('modal_blood_loss_severity_title') || 'Blood Loss Severity', 'blue-800') +
        ModalHelpers.createLabelValue(getModalTranslation('modal_blood_loss_severity') || 'Severity:', severityLabel, 'blue-700');
      content += ModalHelpers.createSection('blue', 'blue', severityContent);
    }

    // Display blood loss flags/indicators
    const activeFlags = [];
    if (flags.bloodClots) activeFlags.push(getModalTranslation('modal_blood_loss_blood_clots') || 'Blood clots');
    if (flags.doubleProtection) activeFlags.push(getModalTranslation('modal_blood_loss_double_protection') || 'Double protection');
    if (flags.leakedClothes) activeFlags.push(getModalTranslation('modal_blood_loss_leaked_clothes') || 'Leaked through clothes');
    if (flags.changedProducts) activeFlags.push(getModalTranslation('modal_blood_loss_changed_products') || 'Changed products frequently');
    if (flags.wokeUpAtNight) activeFlags.push(getModalTranslation('modal_blood_loss_woke_up_night') || 'Woke up at night');

    if (activeFlags.length > 0) {
      let flagsContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_blood_loss_indicators') || 'Because of your blood loss, you…', 'red-800');
      flagsContent += '<ul class="list-disc list-inside space-y-1 text-sm text-red-700">';
      activeFlags.forEach(flag => {
        flagsContent += `<li>${flag}</li>`;
      });
      flagsContent += '</ul>';
      content += ModalHelpers.createSection('red', 'red', flagsContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Pain modal content
   */
  static generatePainModal(pillar) {
    if (!PillarDataValidators.hasPainData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No pain data recorded.'}</p>`;
    }

    const value = pillar?.value ?? 0;
    const regions = pillar?.regions || [];
    const during = pillar?.during || [];

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_pain_title') || 'Pain Assessment');

    content += '<div class="text-center mb-6">';
    content += `<h3 class="text-lg font-semibold mb-3">${getModalTranslation('modal_pain_level_title') || 'Pain Level'}</h3>`;
    content += '<div class="flex justify-center items-center gap-1 mb-4">';

    let currentPainIcon = Math.min(6, Math.max(1, Math.ceil(value / 2) || 1));
    for (let i = 1; i <= 6; i++) {
      const isActive = i === currentPainIcon;
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/smile_${i}.png" alt="Pain Level ${i}" class="w-8 h-8 object-contain"></div>`;
    }
    content += '</div>';
    content += '</div>';

    if (value > 0) {
      const painContent =
        ModalHelpers.createSectionHeader(getModalTranslation('modal_pain_level_title') || 'Pain Level', 'blue-800') +
        ModalHelpers.createLabelValue(getModalTranslation('modal_pain_level') || 'Pain Level:', `${value}/10`, 'blue-700');
      content += ModalHelpers.createSection('blue', 'blue', painContent);
    }

    if (during.length > 0) {
      let duringContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_pain_during_title') || 'Pain experienced during', 'purple-800');
      duringContent += '<ul class="list-disc list-inside space-y-1 text-sm text-purple-700">';
      during.forEach(activity => {
        const labelKey = `pain_during_${activity}`;
        const label = PAIN_REGION_LABELS[labelKey] ? PAIN_REGION_LABELS[labelKey]() : activity;
        duringContent += `<li>${label}</li>`;
      });
      duringContent += '</ul>';
      content += ModalHelpers.createSection('purple', 'purple', duringContent);
    }

    if (regions.length > 0) {
      let regionsContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_pain_regions_title') || 'Affected Regions', 'red-800');
      regionsContent += '<ul class="list-disc list-inside space-y-1 text-sm text-red-700">';
      regions.forEach(region => {
        const label = PAIN_REGION_LABELS[region] ? PAIN_REGION_LABELS[region]() : region;
        regionsContent += `<li>${label}</li>`;
      });
      regionsContent += '</ul>';
      content += ModalHelpers.createSection('red', 'red', regionsContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Mood modal content
   */
  static generateMoodModal(pillar) {
    if (!PillarDataValidators.hasMoodData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No mood data recorded.'}</p>`;
    }

    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_mood_title') || 'Mood Assessment');

    content += '<div class="text-center mb-6">';
    content += '<div class="flex flex-wrap justify-center items-center gap-2 mb-4 max-w-md mx-auto">';

    const allMoods = [...positives, ...negatives];
    MOOD_KEYS.forEach(moodKey => {
      const isActive = allMoods.includes(moodKey) ||
        (moodKey === 'anxious' && (allMoods.includes('anxious') || allMoods.includes('stressed'))) ||
        (moodKey === 'angry' && (allMoods.includes('angry') || allMoods.includes('irritable'))) ||
        (moodKey === 'worthless' && (allMoods.includes('worthless') || allMoods.includes('guilty')));

      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-2' : 'opacity-30';
      const label = MOOD_LABELS[moodKey] ? MOOD_LABELS[moodKey]() : moodKey;
      content += `<div class="${classes}"><img src="/images/${MOOD_ICON_MAP[moodKey]}" alt="${label}" class="w-10 h-10 object-contain"></div>`;
    });
    content += '</div>';
    content += '</div>';

    const balance = positives.length - negatives.length;
    content += '<div class="text-center mb-4">';
    if (balance > 0) {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_overall') || 'Overall', getModalTranslation('modal_mood_positive_day') || 'Positive Day', 'success');
    } else if (balance < 0) {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_overall') || 'Overall', getModalTranslation('modal_mood_challenging_day') || 'Challenging Day', 'warning');
    } else {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_overall') || 'Overall', getModalTranslation('modal_mood_balanced_day') || 'Balanced Day', 'info');
    }
    content += '</div>';

    if (positives.length > 0) {
      let positiveContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_mood_positive_title') || 'Positive Emotions Experienced', 'green-800');
      positiveContent += '<ul class="list-disc list-inside text-green-700 text-sm">';
      positives.forEach(mood => {
        const label = MOOD_LABELS[mood] ? MOOD_LABELS[mood]() : mood;
        positiveContent += `<li>${label}</li>`;
      });
      positiveContent += '</ul>';
      content += ModalHelpers.createSection('green', 'green', positiveContent);
    }

    if (negatives.length > 0) {
      let negativeContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_mood_negative_title') || 'Challenging Emotions Experienced', 'red-800');
      negativeContent += '<ul class="list-disc list-inside text-red-700 text-sm">';
      negatives.forEach(mood => {
        const label = MOOD_LABELS[mood] ? MOOD_LABELS[mood]() : mood;
        negativeContent += `<li>${label}</li>`;
      });
      negativeContent += '</ul>';
      content += ModalHelpers.createSection('red', 'red', negativeContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Diet modal content
   */
  static generateDietModal(pillar) {
    if (!PillarDataValidators.hasDietData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No diet data recorded.'}</p>`;
    }

    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    const neutrals = pillar?.neutrals || [];

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_diet_title') || 'Diet Items');

    content += '<div class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-4 justify-items-center mb-6 max-w-2xl mx-auto">';
    const allDietItems = [...positives, ...negatives, ...neutrals];
    DIET_KEYS.forEach(dietKey => {
      const isActive = allDietItems.includes(dietKey);
      const label = DIET_LABELS[dietKey] ? DIET_LABELS[dietKey]() : dietKey;
      const containerClasses = isActive 
        ? 'bg-red-100 border-2 border-red-500 rounded-full p-2 sm:p-3 opacity-100' 
        : 'opacity-30';
      content += `
        <div class="flex flex-col items-center">
          <div class="${containerClasses}">
            <img src="/images/${DIET_ICON_MAP[dietKey]}" alt="${label}" class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 object-contain">
          </div>
          <span class="text-xs text-gray-700 text-center mt-1 sm:mt-2 max-w-[70px] sm:max-w-20 leading-tight hyphens-auto" style="word-break: break-word; overflow-wrap: anywhere;">${label}</span>
        </div>
      `;
    });
    content += '</div>';

    content += '</div>';
    return content;
  }

  /**
   * Generate Impact modal content
   */
  static generateImpactModal(pillar) {
    if (!PillarDataValidators.hasImpactData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No impact data recorded.'}</p>`;
    }

    const grade = pillar?.gradeYourDay ?? 0;
    const complaints = pillar?.complaints ?? 0;
    const limitations = pillar?.limitations || [];
    const medications = pillar?.medications || {};
    const medList = medications.list || [];
    const medEffective = medications.effective;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_impact_title') || 'Daily impact & limitations');

    // Day Grade Section
    let gradeText = getModalTranslation('modal_impact_horrible_day') || 'Horrible day';
    let gradeColor = 'text-red-600';
    if (grade >= 8) {
      gradeText = getModalTranslation('modal_impact_perfect_day') || 'Perfect day';
      gradeColor = 'text-green-600';
    } else if (grade >= 5) {
      gradeText = getModalTranslation('modal_impact_normal_day') || 'Normal day';
      gradeColor = 'text-yellow-600';
    }

    content += '<div class="text-center mb-4">';
    content += `<h3 class="text-lg font-semibold mb-2">${getModalTranslation('modal_impact_your_day') || 'Your day was:'}</h3>`;
    content += `<p class="text-lg font-medium mb-2 ${gradeColor}">${gradeText}</p>`;
    if (grade > 0) {
      content += ModalHelpers.createGradeDisplay(grade, 10, getModalTranslation('modal_daily_grade') || 'Daily Grade');
    }
    content += '</div>';

    // Complaints Section
    if (complaints > 0) {
      let complaintsText = getModalTranslation('modal_impact_complaints_nothing') || 'couldn\'t do anything';
      let complaintsColor = 'text-red-600';
      if (complaints >= 7) {
        complaintsText = getModalTranslation('modal_impact_complaints_usual') || 'could do as much as usual';
        complaintsColor = 'text-green-600';
      } else if (complaints >= 4) {
        complaintsText = getModalTranslation('modal_impact_complaints_half') || 'could do about half';
        complaintsColor = 'text-yellow-600';
      }

      content += '<div class="text-center mb-4">';
      content += `<h3 class="text-lg font-semibold mb-2">${getModalTranslation('modal_impact_complaints_title') || 'Because of your complaints you…'}</h3>`;
      content += `<p class="text-lg font-medium mb-2 ${complaintsColor}">${complaintsText}</p>`;
      content += ModalHelpers.createGradeDisplay(complaints, 10, getModalTranslation('modal_level') || 'Level');
      content += '</div>';
    }

    // Medications Section
    if (medList.length > 0) {
      let medContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_impact_medications_title') || 'Medications taken', 'blue-800');
      medContent += '<ul class="list-disc list-inside space-y-1 text-sm text-blue-700">';
      medList.forEach(med => {
        const label = getModalTranslation(`modal_impact_med_${med}`) || med;
        medContent += `<li>${label}</li>`;
      });
      medContent += '</ul>';
      
      if (medEffective !== null && medEffective !== undefined) {
        let effectiveText = getModalTranslation('modal_impact_med_not_effective') || 'Not effective at all';
        if (medEffective >= 7) {
          effectiveText = getModalTranslation('modal_impact_med_very_effective') || 'Very effective';
        } else if (medEffective >= 4) {
          effectiveText = getModalTranslation('modal_impact_med_effective') || 'Effective';
        }
        medContent += `<p class="text-sm text-blue-700 mt-3"><strong>${getModalTranslation('modal_impact_med_effectiveness') || 'Effectiveness'}:</strong> ${effectiveText}</p>`;
      }
      
      content += ModalHelpers.createSection('blue', 'blue', medContent);
    }

    // Limitations Section
    if (limitations.length > 0) {
      let limitationsContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_impact_limitations_title') || 'Daily Limitations:', 'red-800');
      limitationsContent += '<div class="space-y-2">';
      limitations.forEach(limitation => {
        const label = IMPACT_LIMITATION_LABELS[limitation] ? IMPACT_LIMITATION_LABELS[limitation]() : limitation;
        const iconIndex = IMPACT_LIMITATION_TYPES.indexOf(limitation) + 1;
        limitationsContent += `
          <div class="flex items-center gap-2">
            <img src="/images/impact_${iconIndex}.png" alt="${label}" class="w-6 h-6 object-contain flex-shrink-0">
            <span class="text-sm text-red-700">${label}</span>
          </div>
        `;
      });
      limitationsContent += '</div>';
      content += ModalHelpers.createSection('red', 'red', limitationsContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Energy/General Health modal content
   */
  static generateEnergyModal(pillar) {
    if (!PillarDataValidators.hasGeneralHealthData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No energy/health data recorded.'}</p>`;
    }

    const energy = pillar?.energyLevel ?? 0;
    const symptoms = pillar?.symptoms || [];

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_general_health_title') || 'General Health & Energy');

    const energyLabel = ENERGY_LEVEL_LABELS[energy] ? ENERGY_LEVEL_LABELS[energy]() : getModalTranslation('card_general_health_unknown') || 'Unknown';
    content += `<div class="text-center mb-4">`;
    content += `<p class="text-lg font-medium mb-2">${energyLabel}</p>`;
    content += ModalHelpers.createGradeDisplay(energy, 5, getModalTranslation('modal_level') || 'Level');
    content += '</div>';

    content += ModalHelpers.createSectionHeader(getModalTranslation('modal_general_health_symptoms_title') || 'Symptoms Experienced', 'gray-800');
    content += '<div class="grid grid-cols-3 sm:grid-cols-4 gap-2 sm:gap-4 justify-items-center mb-6 max-w-2xl mx-auto">';
    
    SYMPTOM_KEYS.forEach(symptomKey => {
      const isActive = symptoms.includes(symptomKey);
      const label = SYMPTOM_LABELS[symptomKey] ? SYMPTOM_LABELS[symptomKey]() : symptomKey;
      const icon = SYMPTOM_ICON_MAP[symptomKey] || 'general_health.png';
      const containerClasses = isActive 
        ? 'bg-red-100 border-2 border-red-500 rounded-full p-2 sm:p-3 opacity-100' 
        : 'opacity-30';
      
      content += `
        <div class="flex flex-col items-center">
          <div class="${containerClasses}">
            <img src="/images/${icon}" alt="${label}" class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 object-contain">
          </div>
          <span class="text-xs text-gray-700 text-center mt-1 sm:mt-2 max-w-[70px] sm:max-w-20 leading-tight hyphens-auto" style="word-break: break-word; overflow-wrap: anywhere;">${label}</span>
        </div>
      `;
    });
    
    content += '</div>';

    let energyContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_general_health_energy_title') || 'Energy Assessment', 'blue-800');
    energyContent += ModalHelpers.createLabelValue(getModalTranslation('modal_general_health_current_level') || 'Current level:', `${energyLabel} (${energy}/5)`, 'blue-700');
    content += ModalHelpers.createSection('blue', 'blue', energyContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Stool/Urine modal content
   */
  static generateStoolUrineModal(pillar) {
    if (!PillarDataValidators.hasStoolUrineData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No stool/urine data recorded.'}</p>`;
    }

    const hasUrineBlood = pillar?.urine?.blood ?? false;
    const hasStoolBlood = pillar?.stool?.blood ?? false;
    const consistency = pillar?.stool?.consistency;
    const somethingElseText = pillar?.stool?.somethingElseText;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_stool_urine_title') || 'Stool & Urine Conditions');

    content += '<div class="text-center mb-6">';
    content += '<div class="flex flex-wrap justify-center items-center gap-2 mb-4 max-w-md mx-auto">';

    const conditions = [
      { key: 'blood', icon: 'urine_stool.png', label: STOOL_URINE_LABELS['blood'] ? STOOL_URINE_LABELS['blood']() : 'Blood', active: hasUrineBlood || hasStoolBlood },
      { key: 'hard', icon: 'urine_stool_1.png', label: STOOL_URINE_LABELS['hard'] ? STOOL_URINE_LABELS['hard']() : 'Hard', active: consistency === 'hard' },
      { key: 'normal', icon: 'urine_stool_2.png', label: STOOL_URINE_LABELS['normal'] ? STOOL_URINE_LABELS['normal']() : 'Normal', active: consistency === 'normal' },
      { key: 'soft', icon: 'urine_stool_3.png', label: STOOL_URINE_LABELS['soft'] ? STOOL_URINE_LABELS['soft']() : 'Soft', active: consistency === 'soft' },
      { key: 'watery', icon: 'urine_stool_4.png', label: STOOL_URINE_LABELS['watery'] ? STOOL_URINE_LABELS['watery']() : 'Watery', active: consistency === 'watery' },
      { key: 'something_else', icon: 'urine_stool_5.png', label: STOOL_URINE_LABELS['something_else'] ? STOOL_URINE_LABELS['something_else']() : 'Other', active: consistency === 'something_else' },
      { key: 'no_stool', icon: 'urine_stool_6.png', label: STOOL_URINE_LABELS['no_stool'] ? STOOL_URINE_LABELS['no_stool']() : 'No Stool', active: consistency === 'no_stool' }
    ];

    conditions.forEach(condition => {
      const classes = condition.active ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-2' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/${condition.icon}" alt="${condition.label}" class="w-10 h-10 object-contain"></div>`;
    });
    content += '</div>';
    content += '</div>';

    content += '<div class="text-center mb-4">';
    if (hasUrineBlood || hasStoolBlood) {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_stool_urine_status') || 'Status', getModalTranslation('modal_stool_urine_blood_detected') || 'Blood Detected', 'error');
    } else if (consistency) {
      const statusColors = {
        'hard': 'text-orange-600', 'normal': 'text-green-600', 'soft': 'text-yellow-600',
        'watery': 'text-blue-600', 'something_else': 'text-purple-600', 'no_stool': 'text-gray-600'
      };
      const colorClass = statusColors[consistency] || 'text-gray-600';
      const label = STOOL_URINE_LABELS[consistency] ? STOOL_URINE_LABELS[consistency]() : consistency;
      content += `<p class="${colorClass} font-medium">${getModalTranslation('modal_stool_urine_status') || 'Status'}: ${label}</p>`;
    }
    content += '</div>';

    if (hasUrineBlood || hasStoolBlood) {
      let bloodContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_stool_urine_blood_detection_title') || 'Blood Detection', 'red-800');
      if (hasUrineBlood) {
        bloodContent += ModalHelpers.createListItem('urine_stool.png', STOOL_URINE_LABELS['blood'] ? STOOL_URINE_LABELS['blood']() : 'Blood in urine', null, 'red-700');
      }
      if (hasStoolBlood) {
        bloodContent += ModalHelpers.createListItem('urine_stool.png', getModalTranslation('modal_stool_urine_blood_in_stool') || 'Blood in stool', null, 'red-700');
      }
      content += ModalHelpers.createSection('red', 'red', bloodContent);
    }

    if (consistency) {
      let consistencyContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_stool_urine_consistency_title') || 'Stool Consistency', 'blue-800');
      const label = STOOL_URINE_LABELS[consistency] ? STOOL_URINE_LABELS[consistency]() : consistency;
      const consistencyData = STOOL_CONSISTENCY_MAP[consistency];
      if (consistencyData) {
        consistencyContent += ModalHelpers.createListItem(consistencyData.icon.replace('/images/', ''), label, null, 'blue-700');
      }
      
      // Show "something else" text if provided
      if (consistency === 'something_else' && somethingElseText) {
        consistencyContent += `<p class="text-sm text-blue-700 mt-2 italic">"${somethingElseText}"</p>`;
      }
      
      content += ModalHelpers.createSection('blue', 'blue', consistencyContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Sleep modal content
   */
  static generateSleepModal(pillar) {
    if (!PillarDataValidators.hasSleepData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No sleep data recorded.'}</p>`;
    }

    const hours = pillar?.calculatedHours ?? 0;
    const fellAsleep = pillar?.fellAsleep;
    const wokeUp = pillar?.wokeUp;
    const troubleAsleep = pillar?.troubleAsleep ?? false;
    const wakeUpDuringNight = pillar?.wakeUpDuringNight ?? false;
    const tiredRested = pillar?.tiredRested ?? false;
    const isWorkSchoolDay = pillar?.isWorkSchoolDay ?? false;
    const isFreeDay = pillar?.isFreeDay ?? false;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_sleep_title') || 'Sleep Quality', null, 'sleep.png');

    let quality = SLEEP_QUALITY_LABELS['good'] ? SLEEP_QUALITY_LABELS['good']() : 'Good Sleep';
    let qualityColor = 'text-green-600';
    if (hours < 6 || hours > 10 || troubleAsleep || wakeUpDuringNight || !tiredRested) {
      if (hours < 4 || (troubleAsleep && wakeUpDuringNight && !tiredRested)) {
        quality = SLEEP_QUALITY_LABELS['poor'] ? SLEEP_QUALITY_LABELS['poor']() : 'Poor Sleep';
        qualityColor = 'text-red-600';
      } else {
        quality = SLEEP_QUALITY_LABELS['okay'] ? SLEEP_QUALITY_LABELS['okay']() : 'Okay Sleep';
        qualityColor = 'text-yellow-600';
      }
    }

    content += '<div class="text-center mb-4">';
    content += `<p class="${qualityColor} font-medium mb-2">${quality}</p>`;
    content += `<p class="text-gray-600">${hours.toFixed(1)} ${getModalTranslation('modal_sleep_hours') || 'hours'}</p>`;
    
    if (isWorkSchoolDay || isFreeDay) {
      const dayType = isWorkSchoolDay ? 
        (getModalTranslation('modal_sleep_work_school_day') || 'Work/School day') :
        (getModalTranslation('modal_sleep_free_day') || 'Free day');
      content += `<p class="text-sm text-gray-500 mt-2">${dayType}</p>`;
    }
    content += '</div>';

    if (fellAsleep && wokeUp) {
      let scheduleContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_sleep_schedule_title') || 'Sleep Schedule', 'blue-800');
      scheduleContent += `<div class="text-sm text-blue-700">${getModalTranslation('modal_sleep_fell_asleep') || 'Fell asleep:'} ${fellAsleep}</div>`;
      scheduleContent += `<div class="text-sm text-blue-700">${getModalTranslation('modal_sleep_woke_up') || 'Woke up:'} ${wokeUp}</div>`;
      content += ModalHelpers.createSection('blue', 'blue', scheduleContent);
    }

    const issues = [];
    if (troubleAsleep) issues.push('trouble_asleep');
    if (wakeUpDuringNight) issues.push('wake_up_during_night');
    if (!tiredRested) issues.push('not_tired_rested');

    if (issues.length > 0) {
      let issuesContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_sleep_issues_title') || 'Sleep Challenges', 'red-800');

      issues.forEach(issue => {
        const label = SLEEP_ISSUE_LABELS[issue] ? SLEEP_ISSUE_LABELS[issue]() : issue;
        issuesContent += ModalHelpers.createListItem(null, label, null, 'red-700');
      });

      content += ModalHelpers.createSection('red', 'red', issuesContent);
    } else {
      let qualityContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_sleep_no_issues_title') || 'Quality Sleep', 'green-800');
      content += ModalHelpers.createSection('green', 'green', qualityContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Exercise modal content
   */
  static generateExerciseModal(pillar) {
    if (!PillarDataValidators.hasExerciseData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No exercise data recorded.'}</p>`;
    }

    const hasExercise = pillar?.any ?? false;
    const levels = pillar?.levels || [];
    const impacts = pillar?.impacts || [];

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_exercise_title') || 'Exercise Activity');

    content += '<div class="text-center mb-6">';
    content += '<div class="flex justify-center items-center gap-8 mb-4">';

    const hasDuration = levels && levels.length > 0;
    const hasImpact = impacts && (impacts.includes('high_impact') || impacts.includes('low_impact'));
    const isType1Active = hasDuration || hasImpact;

    const hasPrecisionExercise = impacts && impacts.includes('relaxation_exercise');
    const isType2Active = hasPrecisionExercise;

    const exerciseIcons = [
      { file: 'exercise_type_1.png', label: EXERCISE_TYPE_LABELS.high_impact ? EXERCISE_TYPE_LABELS.high_impact() : 'High Impact', active: isType1Active },
      { file: 'exercise_type_2.png', label: EXERCISE_TYPE_LABELS.relaxation_exercise ? EXERCISE_TYPE_LABELS.relaxation_exercise() : 'Relaxation Exercise', active: isType2Active }
    ];

    exerciseIcons.forEach(({ file, label, active }) => {
      const classes = active ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-3' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/${file}" alt="${label}" class="w-12 h-12 object-contain"></div>`;
    });

    content += '</div>';
    content += '</div>';

    content += '<div class="text-center mb-4">';
    if (hasExercise) {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_exercise_status') || 'Status:', getModalTranslation('modal_exercise_completed') || 'Exercise Completed', 'success');
    } else {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_exercise_status') || 'Status:', getModalTranslation('modal_exercise_rest_day') || 'Rest Day', 'info');
    }
    content += '</div>';

    if (hasExercise && levels.length > 0) {
      let durationContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_exercise_duration_title') || 'Duration', 'green-800');
      levels.forEach(level => {
        const label = EXERCISE_DURATION_LABELS[level] ? EXERCISE_DURATION_LABELS[level]() : level;
        durationContent += ModalHelpers.createListItem(null, label, null, 'green-700');
      });
      content += ModalHelpers.createSection('green', 'green', durationContent);
    }

    if (hasExercise && impacts.length > 0) {
      let typesContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_exercise_activity_types_title') || 'Activity Types', 'blue-800');
      impacts.forEach(impact => {
        const label = EXERCISE_TYPE_LABELS[impact] ? EXERCISE_TYPE_LABELS[impact]() : impact;
        typesContent += ModalHelpers.createListItem(null, label, null, 'blue-700');
      });
      content += ModalHelpers.createSection('blue', 'blue', typesContent);
    }

    if (!hasExercise) {
      let restContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_exercise_no_exercise_title') || 'Rest Day', 'gray-800');
      content += ModalHelpers.createSection('gray', 'gray', restContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Sexual Health modal content
   */
  static generateSexModal(pillar) {
    if (!PillarDataValidators.hasSexData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No sexual health data recorded.'}</p>`;
    }

    const today = pillar?.today ?? false;
    const avoided = pillar?.avoided ?? false;
    const satisfied = pillar?.satisfied ?? false;
    const issues = pillar?.issues || [];

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_sexual_health_title') || 'Sexual Health');

    content += '<div class="text-center mb-4">';
    if (today) {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_sexual_health_status') || 'Status:', getModalTranslation('modal_sexual_health_had_sex_today') || 'I had sex today.', 'success');
    } else if (avoided) {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_sexual_health_status') || 'Status:', getModalTranslation('modal_sexual_health_avoided_sex') || 'I avoided sex because of pain complaints.', 'warning');
    } else {
      content += ModalHelpers.createStatusIndicator(getModalTranslation('modal_sexual_health_status') || 'Status:', getModalTranslation('modal_sexual_health_no_activity') || 'No activity', 'info');
    }
    content += '</div>';

    if (today) {
      let activityContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_sexual_health_activity_title') || 'Activity Details', 'green-800');
      const statusLabel = satisfied ? 
        (SEX_STATUS_LABELS['satisfied'] ? SEX_STATUS_LABELS['satisfied']() : 'Satisfying experience') :
        (SEX_STATUS_LABELS['unsatisfied'] ? SEX_STATUS_LABELS['unsatisfied']() : 'Unsatisfying experience');
      activityContent += ModalHelpers.createListItem(null, statusLabel, null, satisfied ? 'green-700' : 'orange-700');
      content += ModalHelpers.createSection('green', 'green', activityContent);
    }

    if (issues.length > 0) {
      let issuesContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_sexual_health_issues_title') || 'Health Concerns', 'red-800');
      issues.forEach(issue => {
        const label = SEX_ISSUE_LABELS[issue] ? SEX_ISSUE_LABELS[issue]() : issue;
        issuesContent += ModalHelpers.createListItem(null, label, null, 'red-700');
      });
      content += ModalHelpers.createSection('red', 'red', issuesContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate Notes modal content
   */
  static generateNotesModal(pillar) {
    if (!PillarDataValidators.hasNotesData(pillar)) {
      return `<p class="text-gray-500">${getModalTranslation('modal_no_data_recorded') || 'No notes recorded.'}</p>`;
    }

    const text = pillar?.text || '';
    const hasNote = pillar?.hasNote ?? false;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(getModalTranslation('modal_notes_title') || 'Personal Notes', null, 'grid_notes.png');

    if (hasNote && text) {
      let noteContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_notes_content_title') || 'Your Personal Note', 'blue-800');
      noteContent += `<div class="bg-white rounded p-3 border text-gray-700">`;
      noteContent += ModalHelpers.formatTextWithBreaks ? ModalHelpers.formatTextWithBreaks(text) : text.replace(/\n/g, '<br>');
      noteContent += `</div>`;
      content += ModalHelpers.createSection('blue', 'blue', noteContent);
    } else {
      let noNoteContent = ModalHelpers.createSectionHeader(getModalTranslation('modal_notes_no_notes_title') || 'No Notes Today', 'gray-800');
      content += ModalHelpers.createSection('gray', 'gray', noNoteContent);
    }

    content += '</div>';
    return content;
  }

  /**
   * Generate modal content based on pillar type
   */
  static generateModalContent(pillar, pillarType) {
    switch (pillarType) {
      case 'blood_loss':
        return this.generateBloodLossModal(pillar);
      case 'pain':
        return this.generatePainModal(pillar);
      case 'mood':
        return this.generateMoodModal(pillar);
      case 'diet':
        return this.generateDietModal(pillar);
      case 'impact':
        return this.generateImpactModal(pillar);
      case 'general_health':
      case 'energy':
        return this.generateEnergyModal(pillar);
      case 'stool_urine':
      case 'stool':
        return this.generateStoolUrineModal(pillar);
      case 'sleep':
        return this.generateSleepModal(pillar);
      case 'exercise':
        return this.generateExerciseModal(pillar);
      case 'sex':
        return this.generateSexModal(pillar);
      case 'notes':
        return this.generateNotesModal(pillar);
      default:
        return '<p class="text-gray-500">No data available for this category.</p>';
    }
  }
}
