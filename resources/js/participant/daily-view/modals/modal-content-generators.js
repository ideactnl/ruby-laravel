/**
 * Refactored Modal Content Generators
 * Clean architecture using constants, validators, JSON data, and helper utilities
 */

import { modalContentData } from '../data/index.js';
import { PillarDataValidators } from '../cards/pillar-data-validators.js';
import { ModalHelpers } from './modal-helpers.js';
import {
  BLOOD_LOSS_SEVERITY_LEVELS, BLOOD_LOSS_SEVERITY_LABELS,
  PAIN_REGION_LABELS, MOOD_ICON_MAP, MOOD_KEYS,
  DIET_ICON_MAP, DIET_KEYS, IMPACT_LIMITATION_TYPES, IMPACT_LIMITATION_LABELS,
  ENERGY_LEVEL_LABELS, SYMPTOM_LABELS, STOOL_CONSISTENCY_MAP,
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
      return '<p class="text-gray-500">No blood loss data recorded.</p>';
    }

    const amount = pillar?.amount ?? 0;
    const severity = pillar?.severity || 'none';
    const spotting = pillar?.flags?.spotting;
    const data = modalContentData.bloodLoss;

    let content = '<div class="space-y-6">';
    
    content += '<div class="text-center mb-6">';
    content += `<h3 class="text-lg font-semibold mb-3">${data.severityTitle}</h3>`;
    content += '<div class="flex justify-center items-center gap-1 mb-4">';
    
    const spottingActive = spotting;
    const spottingClasses = spottingActive ? 'opacity-100 bg-orange-100 border-2 border-orange-500 rounded-full p-1' : 'opacity-30';
    content += `<div class="${spottingClasses}"><img src="/images/spotting.png" alt="Spotting" class="w-8 h-8 object-contain"></div>`;
    
    BLOOD_LOSS_SEVERITY_LEVELS.forEach((level, index) => {
      const isActive = severity === level && !spotting;
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/blood_loss_${index + 1}.png" alt="${BLOOD_LOSS_SEVERITY_LABELS[level]}" class="w-8 h-8 object-contain"></div>`;
    });
    
    content += '</div>';
    content += '</div>';

    if (spotting) {
      const spottingContent = 
        ModalHelpers.createSectionHeader(data.spotting.title, 'orange-800') +
        ModalHelpers.createDescription(data.spotting.description, 'orange-700', 'sm');
      content += ModalHelpers.createSection('orange', 'orange', spottingContent);
    } else if (severity && severity !== 'none') {
      const severityContent = 
        ModalHelpers.createSectionHeader(data.title || 'Blood Loss Details', 'blue-800') +
        ModalHelpers.createLabelValue(data.labels.severity, BLOOD_LOSS_SEVERITY_LABELS[severity], 'blue-700') +
        ModalHelpers.createDescription(data.severityDescriptions[severity] || 'Blood loss recorded', 'blue-700', 'sm');
      content += ModalHelpers.createSection('blue', 'blue', severityContent);
    }

    const trackingContent = 
      ModalHelpers.createSectionHeader(data.trackingTitle || 'Tracking Information') +
      ModalHelpers.createLabelValue(data.labels.amountRecorded, `${amount} ml`) +
      ModalHelpers.createDescription(data.trackingInfo);
    content += ModalHelpers.createSection('gray', 'gray', trackingContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Pain modal content
   */
  static generatePainModal(pillar) {
    if (!PillarDataValidators.hasPainData(pillar)) {
      return '<p class="text-gray-500">No pain data recorded.</p>';
    }

    const value = pillar?.value ?? 0;
    const regions = pillar?.regions || [];
    const data = modalContentData.pain;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(data.title);
    content += ModalHelpers.createIconGrid();

    let currentPainIcon = Math.min(6, Math.max(1, Math.ceil(value / 2) || 1));
    for (let i = 1; i <= 6; i++) {
      const isActive = i === currentPainIcon;
      content += ModalHelpers.createIcon(`smile_${i}.png`, `Pain Level ${i}`, isActive);
    }
    content += '</div>';

    if (value > 0) {
      const painContent = 
        ModalHelpers.createSectionHeader(data.levelTitle, 'blue-800') +
        ModalHelpers.createLabelValue('Pain Level:', `${value}/10`, 'blue-700');
      content += ModalHelpers.createSection('blue', 'blue', painContent);
    }

    if (regions.length > 0) {
      let regionsContent = ModalHelpers.createSectionHeader(data.regionsTitle, 'red-800');
      regionsContent += ModalHelpers.createTwoColumnGrid('');
      regions.forEach(region => {
        const label = PAIN_REGION_LABELS[region] || region;
        regionsContent += `<div class="text-sm text-red-700">• ${label}</div>`;
      });
      regionsContent += '</div>';
      content += ModalHelpers.createSection('red', 'red', regionsContent);
    }

    const trackingContent = 
      ModalHelpers.createSectionHeader('Pain Tracking') +
      ModalHelpers.createDescription(data.trackingInfo);
    content += ModalHelpers.createSection('gray', 'gray', trackingContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Mood modal content
   */
  static generateMoodModal(pillar) {
    if (!PillarDataValidators.hasMoodData(pillar)) {
      return '<p class="text-gray-500">No mood data recorded.</p>';
    }

    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    const data = modalContentData.mood;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(data.title);
    content += ModalHelpers.createIconGrid();

    const allMoods = [...positives, ...negatives];
    MOOD_KEYS.forEach(moodKey => {
      const isActive = allMoods.includes(moodKey) || 
                      (moodKey === 'anxious' && (allMoods.includes('anxious') || allMoods.includes('stressed'))) ||
                      (moodKey === 'angry' && (allMoods.includes('angry') || allMoods.includes('irritable'))) ||
                      (moodKey === 'worthless' && (allMoods.includes('worthless') || allMoods.includes('guilty')));
      
      content += ModalHelpers.createIcon(MOOD_ICON_MAP[moodKey], moodKey, isActive);
    });
    content += '</div>';

    const balance = positives.length - negatives.length;
    content += '<div class="text-center mb-4">';
    if (balance > 0) {
      content += ModalHelpers.createStatusIndicator('Overall Mood', data.overallLabels.positive, 'success');
    } else if (balance < 0) {
      content += ModalHelpers.createStatusIndicator('Overall Mood', data.overallLabels.negative, 'warning');
    } else {
      content += ModalHelpers.createStatusIndicator('Overall Mood', data.overallLabels.balanced, 'info');
    }
    content += '</div>';

    if (positives.length > 0) {
      let positiveContent = ModalHelpers.createSectionHeader(data.positiveTitle, 'green-800');
      positives.forEach(mood => {
        positiveContent += ModalHelpers.createListItem(null, mood.charAt(0).toUpperCase() + mood.slice(1), null, 'green-700');
      });
      positiveContent += ModalHelpers.createDescription(data.positiveMessage, 'green-600', 'xs');
      content += ModalHelpers.createSection('green', 'green', positiveContent);
    }

    if (negatives.length > 0) {
      let negativeContent = ModalHelpers.createSectionHeader(data.negativeTitle, 'red-800');
      negatives.forEach(mood => {
        negativeContent += ModalHelpers.createListItem(null, mood.charAt(0).toUpperCase() + mood.slice(1), null, 'red-700');
      });
      
      if (negatives.some(mood => ['depressed', 'hopeless', 'worthless'].includes(mood))) {
        negativeContent += ModalHelpers.createWarning('If these feelings persist, consider reaching out to a mental health professional or your healthcare provider.', 'danger');
      }
      
      negativeContent += ModalHelpers.createDescription(data.negativeMessage, 'red-600', 'xs');
      content += ModalHelpers.createSection('red', 'red', negativeContent);
    }

    let insightsContent = ModalHelpers.createSectionHeader(data.trackingInsights.title);
    insightsContent += ModalHelpers.createLabelValue(
      data.trackingInsights.todaysBalance,
      `${positives.length} positive, ${negatives.length} challenging`
    );

    let balanceMessage;
    if (balance > 1) {
      balanceMessage = data.trackingInsights.balanceMessages.strongPositive;
    } else if (balance === 1) {
      balanceMessage = data.trackingInsights.balanceMessages.mostlyPositive;
    } else if (balance === 0) {
      balanceMessage = data.trackingInsights.balanceMessages.balanced;
    } else if (balance === -1) {
      balanceMessage = data.trackingInsights.balanceMessages.somewhatChallenging;
    } else {
      balanceMessage = data.trackingInsights.balanceMessages.challenging;
    }

    insightsContent += ModalHelpers.createDescription(balanceMessage, 'gray-700', 'sm');
    insightsContent += ModalHelpers.createDescription(data.trackingInsights.correlationInfo, 'gray-600', 'xs');
    content += ModalHelpers.createSection('gray', 'gray', insightsContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Diet modal content
   */
  static generateDietModal(pillar) {
    if (!PillarDataValidators.hasDietData(pillar)) {
      return '<p class="text-gray-500">No diet data recorded.</p>';
    }

    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    const neutrals = pillar?.neutrals || [];
    const data = modalContentData.diet;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(data.title);
    content += ModalHelpers.createIconGrid();

    const allDietItems = [...positives, ...negatives, ...neutrals];
    DIET_KEYS.forEach(dietKey => {
      const isActive = allDietItems.includes(dietKey);
      content += ModalHelpers.createIcon(DIET_ICON_MAP[dietKey], dietKey, isActive);
    });
    content += '</div>';
    content += `<p class="text-center text-gray-600 mb-4">${data.totalItemsLabel || 'Items tracked:'} ${allDietItems.length}/12</p>`;

    if (positives.length > 0) {
      let beneficialContent = ModalHelpers.createSectionHeader(data.beneficialTitle, 'green-800');
      positives.forEach(item => {
        const label = item.charAt(0).toUpperCase() + item.slice(1).replace('_', ' ');
        beneficialContent += ModalHelpers.createListItem(null, label, null, 'green-700');
      });
      beneficialContent += ModalHelpers.createDescription(data.beneficialSummary, 'green-600', 'xs');
      content += ModalHelpers.createSection('green', 'green', beneficialContent);
    }

    if (negatives.length > 0) {
      let negativeContent = ModalHelpers.createSectionHeader(data.negativeTitle, 'red-800');
      negatives.forEach(item => {
        const label = item.charAt(0).toUpperCase() + item.slice(1).replace('_', ' ');
        negativeContent += ModalHelpers.createListItem(null, label, null, 'red-700');
      });
      negativeContent += ModalHelpers.createDescription(data.negativeSummary, 'red-600', 'xs');
      content += ModalHelpers.createSection('red', 'red', negativeContent);
    }

    if (neutrals.length > 0) {
      let neutralContent = ModalHelpers.createSectionHeader(data.neutralTitle, 'gray-800');
      neutrals.forEach(item => {
        const label = item.charAt(0).toUpperCase() + item.slice(1).replace('_', ' ');
        neutralContent += ModalHelpers.createListItem(null, label, null, 'gray-700');
      });
      neutralContent += ModalHelpers.createDescription(data.neutralSummary, 'gray-600', 'xs');
      content += ModalHelpers.createSection('gray', 'gray', neutralContent);
    }

    const totalItems = allDietItems.length;
    const healthyRatio = positives.length / totalItems;
    
    let analysisContent = ModalHelpers.createSectionHeader('Nutritional Analysis', 'blue-800');
    
    if (healthyRatio >= 0.7) {
      analysisContent += ModalHelpers.createDescription('Excellent nutrition day! You made predominantly healthy food choices.', 'blue-700', 'sm');
    } else if (healthyRatio >= 0.5) {
      analysisContent += ModalHelpers.createDescription('Good nutrition balance. Consider increasing beneficial foods slightly.', 'blue-700', 'sm');
    } else if (healthyRatio >= 0.3) {
      analysisContent += ModalHelpers.createDescription('Room for improvement. Consider increasing healthy food choices tomorrow.', 'blue-700', 'sm');
    } else {
      analysisContent += ModalHelpers.createDescription('Focus on nutrition. Try to include more anti-inflammatory foods in your diet.', 'blue-700', 'sm');
    }
    
    analysisContent += ModalHelpers.createLabelValue('Today\'s breakdown:', `${positives.length} beneficial, ${negatives.length} to monitor, ${neutrals.length} neutral foods`, 'blue-700');
    analysisContent += ModalHelpers.createDescription('Diet plays a crucial role in managing symptoms. Anti-inflammatory foods may help reduce symptom severity, while processed foods might worsen them.', 'blue-600', 'xs');
    content += ModalHelpers.createSection('blue', 'blue', analysisContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Impact modal content
   */
  static generateImpactModal(pillar) {
    if (!PillarDataValidators.hasImpactData(pillar)) {
      return '<p class="text-gray-500">No impact data recorded.</p>';
    }

    const grade = pillar?.gradeYourDay ?? 0;
    const limitations = pillar?.limitations || [];
    const data = modalContentData.impact;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(data.title);
    content += ModalHelpers.createIconGrid();

    IMPACT_LIMITATION_TYPES.forEach((limitationType, index) => {
      const isActive = limitations.includes(limitationType);
      const iconFile = `impact_${index + 1}.png`;
      const label = IMPACT_LIMITATION_LABELS[limitationType];
      content += ModalHelpers.createIcon(iconFile, label, isActive);
    });
    content += '</div>';

    let gradeText = data.gradeLabels.difficult;
    let gradeColor = 'text-red-600';
    if (grade >= 8) {
      gradeText = data.gradeLabels.great;
      gradeColor = 'text-green-600';
    } else if (grade >= 6) {
      gradeText = data.gradeLabels.good;
      gradeColor = 'text-yellow-600';
    } else if (grade >= 4) {
      gradeText = data.gradeLabels.challenging;
      gradeColor = 'text-orange-600';
    }

    content += '<div class="text-center mb-4">';
    content += `<p class="text-lg font-medium mb-2 ${gradeColor}">${gradeText}</p>`;
    if (grade > 0) {
      content += ModalHelpers.createGradeDisplay(grade, 10, 'Daily Grade');
    }
    content += '</div>';

    if (limitations.length > 0) {
      let limitationsContent = ModalHelpers.createSectionHeader(data.limitationsTitle, 'red-800');
      limitationsContent += ModalHelpers.createSingleColumnLayout('');
      
      limitations.forEach(limitation => {
        const label = IMPACT_LIMITATION_LABELS[limitation] || limitation;
        const iconIndex = IMPACT_LIMITATION_TYPES.indexOf(limitation) + 1;
        limitationsContent += ModalHelpers.createListItem(`impact_${iconIndex}.png`, label, null, 'red-700');
      });
      
      limitationsContent += '</div>';
      content += ModalHelpers.createSection('red', 'red', limitationsContent);
    } else {
      let noLimitationsContent = ModalHelpers.createSectionHeader(data.noLimitationsTitle, 'green-800');
      noLimitationsContent += ModalHelpers.createDescription(data.noLimitationsMessage, 'green-700', 'sm');
      noLimitationsContent += ModalHelpers.createDescription(data.noLimitationsSummary, 'green-600', 'xs');
      content += ModalHelpers.createSection('green', 'green', noLimitationsContent);
    }

    let analysisContent = ModalHelpers.createSectionHeader(data.analysisTitle, 'blue-800');
    analysisContent += ModalHelpers.createLabelValue('Your Rating:', `${grade}/10 (${gradeText})`, 'blue-700');
    
    const gradeDescription = data.gradeDescriptions[grade.toString()] || data.gradeDescriptions[Math.min(10, Math.max(0, Math.round(grade))).toString()];
    if (gradeDescription) {
      analysisContent += ModalHelpers.createDescription(gradeDescription, 'blue-700', 'sm');
    }

    const impactLevel = limitations.length;
    let impactLevelText;
    if (impactLevel === 0) {
      impactLevelText = data.impactLevels.minimal;
    } else if (impactLevel <= 2) {
      impactLevelText = data.impactLevels.mild;
    } else if (impactLevel <= 4) {
      impactLevelText = data.impactLevels.moderate;
    } else if (impactLevel <= 6) {
      impactLevelText = data.impactLevels.significant;
    } else {
      impactLevelText = data.impactLevels.severe;
    }

    analysisContent += ModalHelpers.createLabelValue('Impact Level:', impactLevelText, 'blue-700');
    content += ModalHelpers.createSection('blue', 'blue', analysisContent);

    let trackingContent = ModalHelpers.createSectionHeader('Impact Tracking Insights');
    trackingContent += ModalHelpers.createLabelValue('Limitations count:', `${impactLevel} out of 11 possible areas affected`, 'gray-700');
    trackingContent += ModalHelpers.createDescription(data.trackingInfo, 'gray-600', 'xs');
    content += ModalHelpers.createSection('gray', 'gray', trackingContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Energy/General Health modal content
   */
  static generateEnergyModal(pillar) {
    if (!PillarDataValidators.hasGeneralHealthData(pillar)) {
      return '<p class="text-gray-500">No energy/health data recorded.</p>';
    }

    const energy = pillar?.energyLevel ?? 0;
    const symptoms = pillar?.symptoms || [];
    const data = modalContentData.generalHealth;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(data.title);

    const energyLabel = ENERGY_LEVEL_LABELS[energy] || 'Unknown';
    content += `<div class="text-center mb-4">`;
    content += `<p class="text-lg font-medium mb-2">${energyLabel} Energy</p>`;
    content += ModalHelpers.createGradeDisplay(energy, 5, 'Level');
    content += '</div>';

    let energyContent = ModalHelpers.createSectionHeader(data.energyTitle, 'blue-800');
    energyContent += ModalHelpers.createLabelValue('Current Level:', `${energyLabel} (${energy}/5)`, 'blue-700');
    const energyDescription = data.energyDescriptions[energy.toString()];
    if (energyDescription) {
      energyContent += ModalHelpers.createDescription(energyDescription, 'blue-700', 'sm');
    }
    content += ModalHelpers.createSection('blue', 'blue', energyContent);

    if (symptoms.length > 0) {
      let symptomsContent = ModalHelpers.createSectionHeader(data.symptomsTitle, 'red-800');
      symptoms.forEach(symptom => {
        const label = SYMPTOM_LABELS[symptom] || symptom.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        symptomsContent += ModalHelpers.createListItem(null, label, null, 'red-700');
      });
      symptomsContent += ModalHelpers.createDescription(data.multipleSymptomsWarning, 'red-600', 'xs');
      content += ModalHelpers.createSection('red', 'red', symptomsContent);
    } else {
      let noSymptomsContent = ModalHelpers.createSectionHeader(data.noSymptomsTitle, 'green-800');
      noSymptomsContent += ModalHelpers.createDescription(data.noSymptomsMessage, 'green-700', 'sm');
      noSymptomsContent += ModalHelpers.createDescription(data.noSymptomsSummary, 'green-600', 'xs');
      content += ModalHelpers.createSection('green', 'green', noSymptomsContent);
    }

    let correlationContent = ModalHelpers.createSectionHeader(data.correlationTitle, 'yellow-800');
    let correlationMessage;
    if (energy <= 2 && symptoms.length > 0) {
      correlationMessage = data.correlationMessages.lowEnergyWithSymptoms;
    } else if (energy >= 4 && symptoms.length > 0) {
      correlationMessage = data.correlationMessages.goodEnergyWithSymptoms;
    } else if (energy <= 2 && symptoms.length === 0) {
      correlationMessage = data.correlationMessages.lowEnergyNoSymptoms;
    } else if (energy >= 4 && symptoms.length === 0) {
      correlationMessage = data.correlationMessages.goodEnergyNoSymptoms;
    } else {
      correlationMessage = data.correlationMessages.moderate;
    }
    correlationContent += ModalHelpers.createDescription(correlationMessage, 'yellow-700', 'sm');
    correlationContent += ModalHelpers.createDescription(data.trackingInfo, 'yellow-600', 'xs');
    content += ModalHelpers.createSection('yellow', 'yellow', correlationContent);

    let insightsContent = ModalHelpers.createSectionHeader(data.insightsTitle, 'gray-800');
    let insightMessage;
    if (energy >= 4) {
      insightMessage = data.insightMessages.goodEnergy;
    } else if (energy <= 2) {
      insightMessage = data.insightMessages.lowEnergy;
    } else {
      insightMessage = data.insightMessages.moderate;
    }
    insightsContent += ModalHelpers.createDescription(insightMessage, 'gray-600', 'xs');
    content += ModalHelpers.createSection('gray', 'gray', insightsContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Stool/Urine modal content
   */
  static generateStoolUrineModal(pillar) {
    if (!PillarDataValidators.hasStoolUrineData(pillar)) {
      return '<p class="text-gray-500">No stool/urine data recorded.</p>';
    }

    const hasUrineBlood = pillar?.urine?.blood ?? false;
    const hasStoolBlood = pillar?.stool?.blood ?? false;
    const consistency = pillar?.stool?.consistency;
    const data = modalContentData.stoolUrine;

    let content = '<div class="space-y-6">';
    
    content += ModalHelpers.createCenteredHeader(data.title);
    content += ModalHelpers.createIconGrid();
    
    const conditions = [
      { key: 'blood', icon: 'urine_stool.png', label: 'Blood', active: hasUrineBlood || hasStoolBlood },
      { key: 'hard', icon: 'urine_stool_1.png', label: 'Hard', active: consistency === 'hard' },
      { key: 'normal', icon: 'urine_stool_2.png', label: 'Normal', active: consistency === 'normal' },
      { key: 'soft', icon: 'urine_stool_3.png', label: 'Soft', active: consistency === 'soft' },
      { key: 'watery', icon: 'urine_stool_4.png', label: 'Watery', active: consistency === 'watery' },
      { key: 'something_else', icon: 'urine_stool_5.png', label: 'Other', active: consistency === 'something_else' },
      { key: 'no_stool', icon: 'urine_stool_6.png', label: 'No Stool', active: consistency === 'no_stool' }
    ];

    conditions.forEach(condition => {
      content += ModalHelpers.createIcon(condition.icon, condition.label, condition.active);
    });
    content += '</div>';

    content += '<div class="text-center mb-4">';
    if (hasUrineBlood || hasStoolBlood) {
      content += ModalHelpers.createStatusIndicator('Status', 'Blood Detected', 'error');
    } else if (consistency) {
      const statusColors = {
        'hard': 'text-orange-600', 'normal': 'text-green-600', 'soft': 'text-yellow-600',
        'watery': 'text-blue-600', 'something_else': 'text-purple-600', 'no_stool': 'text-gray-600'
      };
      const colorClass = statusColors[consistency] || 'text-gray-600';
      content += `<p class="${colorClass} font-medium">Status: ${consistency.charAt(0).toUpperCase() + consistency.slice(1).replace('_', ' ')}</p>`;
    }
    content += '</div>';

    if (hasUrineBlood || hasStoolBlood) {
      let bloodContent = ModalHelpers.createSectionHeader(data.bloodDetectionTitle, 'red-800');
      
      if (hasUrineBlood && hasStoolBlood) {
        bloodContent += ModalHelpers.createDescription(data.bloodMessages.both, 'red-700', 'sm');
      } else if (hasUrineBlood) {
        bloodContent += ModalHelpers.createDescription(data.bloodMessages.urine, 'red-700', 'sm');
      } else if (hasStoolBlood) {
        bloodContent += ModalHelpers.createDescription(data.bloodMessages.stool, 'red-700', 'sm');
      }
      
      bloodContent += ModalHelpers.createWarning(data.bloodWarning, 'danger');
      content += ModalHelpers.createSection('red', 'red', bloodContent);
    }

    if (consistency) {
      let consistencyContent = ModalHelpers.createSectionHeader(data.consistencyTitle, 'blue-800');
      const description = data.consistencyDescriptions[consistency];
      const label = consistency.charAt(0).toUpperCase() + consistency.slice(1).replace('_', ' ');
      
      consistencyContent += ModalHelpers.createLabelValue('Stool Type:', label, 'blue-700');
      if (description) {
        consistencyContent += ModalHelpers.createDescription(description, 'blue-600', 'xs');
      }
      content += ModalHelpers.createSection('blue', 'blue', consistencyContent);
    }

    let trackingContent = ModalHelpers.createSectionHeader(data.trackingTitle, 'gray-800');
    
    if (hasUrineBlood || hasStoolBlood) {
      trackingContent += ModalHelpers.createDescription('Blood detection requires medical attention for proper evaluation and treatment.', 'gray-600', 'xs');
    } else if (consistency === 'normal') {
      trackingContent += ModalHelpers.createDescription(data.normalMessage, 'gray-600', 'xs');
    } else if (consistency === 'hard') {
      trackingContent += ModalHelpers.createDescription('Hard stools may indicate constipation. Consider increasing fiber and water intake.', 'gray-600', 'xs');
    } else if (consistency === 'watery') {
      trackingContent += ModalHelpers.createDescription('Persistent diarrhea may require dietary adjustments or medical evaluation.', 'gray-600', 'xs');
    }
    
    trackingContent += ModalHelpers.createDescription(data.trackingInfo, 'gray-600', 'xs');
    content += ModalHelpers.createSection('gray', 'gray', trackingContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Sleep modal content
   */
  static generateSleepModal(pillar) {
    if (!PillarDataValidators.hasSleepData(pillar)) {
      return '<p class="text-gray-500">No sleep data recorded.</p>';
    }

    const hours = pillar?.calculatedHours ?? 0;
    const fellAsleep = pillar?.fellAsleep;
    const wokeUp = pillar?.wokeUp;
    const troubleAsleep = pillar?.troubleAsleep ?? false;
    const wakeUpDuringNight = pillar?.wakeUpDuringNight ?? false;
    const tiredRested = pillar?.tiredRested ?? false;
    const data = modalContentData.sleep;

    let content = '<div class="space-y-6">';
    content += ModalHelpers.createCenteredHeader(data.title, null, 'sleep.png');

    let quality = data.qualityLabels.good;
    let qualityColor = 'text-green-600';
    if (hours < 6 || hours > 10 || troubleAsleep || wakeUpDuringNight || !tiredRested) {
      if (hours < 4 || (troubleAsleep && wakeUpDuringNight && !tiredRested)) {
        quality = data.qualityLabels.poor;
        qualityColor = 'text-red-600';
      } else {
        quality = data.qualityLabels.okay;
        qualityColor = 'text-yellow-600';
      }
    }

    content += '<div class="text-center mb-4">';
    content += `<p class="${qualityColor} font-medium mb-2">${quality}</p>`;
    content += `<p class="text-gray-600">${hours.toFixed(1)} hours</p>`;
    content += '</div>';

    if (fellAsleep && wokeUp) {
      let scheduleContent = ModalHelpers.createSectionHeader(data.scheduleTitle, 'blue-800');
      scheduleContent += `<div class="text-sm text-blue-700">Fell asleep: ${fellAsleep}</div>`;
      scheduleContent += `<div class="text-sm text-blue-700">Woke up: ${wokeUp}</div>`;
      content += ModalHelpers.createSection('blue', 'blue', scheduleContent);
    }

    const issues = [];
    if (troubleAsleep) issues.push('trouble_asleep');
    if (wakeUpDuringNight) issues.push('wake_up_during_night');
    if (!tiredRested) issues.push('not_tired_rested');

    if (issues.length > 0) {
      let issuesContent = ModalHelpers.createSectionHeader(data.issuesTitle, 'red-800');
      
      issues.forEach(issue => {
        const label = SLEEP_ISSUE_LABELS[issue];
        issuesContent += ModalHelpers.createListItem(null, label, null, 'red-700');
      });
      
      issuesContent += ModalHelpers.createDescription(data.poorSleepWarning, 'red-600', 'xs');
      content += ModalHelpers.createSection('red', 'red', issuesContent);
    } else {
      let qualityContent = ModalHelpers.createSectionHeader(data.noIssuesTitle, 'green-800');
      qualityContent += ModalHelpers.createDescription('Excellent! No sleep issues reported tonight.', 'green-700', 'sm');
      qualityContent += ModalHelpers.createDescription(data.qualityMessage, 'green-600', 'xs');
      content += ModalHelpers.createSection('green', 'green', qualityContent);
    }

    let analysisContent = ModalHelpers.createSectionHeader(data.analysisTitle, 'blue-800');
    
    let durationMessage;
    if (hours >= 7 && hours <= 9) {
      durationMessage = data.durationMessages.optimal;
    } else if (hours < 6) {
      durationMessage = data.durationMessages.short;
    } else if (hours > 10) {
      durationMessage = data.durationMessages.long;
    } else {
      durationMessage = data.durationMessages.borderline;
    }
    
    analysisContent += ModalHelpers.createDescription(durationMessage, 'blue-700', 'sm');
    analysisContent += ModalHelpers.createLabelValue('Sleep Quality Score:', `${quality} based on duration and reported issues`, 'blue-700');
    analysisContent += ModalHelpers.createDescription(data.trackingInfo, 'blue-600', 'xs');
    content += ModalHelpers.createSection('blue', 'blue', analysisContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Exercise modal content
   */
  static generateExerciseModal(pillar) {
    if (!PillarDataValidators.hasExerciseData(pillar)) {
      return '<p class="text-gray-500">No exercise data recorded.</p>';
    }

    const hasExercise = pillar?.any ?? false;
    const levels = pillar?.levels || [];
    const impacts = pillar?.impacts || [];
    const data = modalContentData.exercise;

    let content = '<div class="space-y-6">';
    content += ModalHelpers.createCenteredHeader(data.title);
    
    content += ModalHelpers.createIconGrid();
    
    const allExerciseItems = [...levels, ...impacts];
    
    const exerciseIconMapping = {
      'high_impact': 'exercise_type_1.png',
      'precision_exercise': 'exercise_type_2.png'
    };
    
    Object.entries(exerciseIconMapping).forEach(([exerciseType, iconFile]) => {
      const isActive = allExerciseItems.includes(exerciseType);
      const label = EXERCISE_TYPE_LABELS[exerciseType] || exerciseType;
      content += ModalHelpers.createIcon(iconFile, label, isActive);
    });
    content += '</div>';

    content += '<div class="text-center mb-4">';
    if (hasExercise) {
      content += ModalHelpers.createStatusIndicator('Status', 'Exercise Completed', 'success');
    } else {
      content += ModalHelpers.createStatusIndicator('Status', 'Rest Day', 'info');
    }
    content += '</div>';

    if (hasExercise) {
      if (levels.length > 0) {
        let durationContent = ModalHelpers.createSectionHeader(data.durationTitle, 'green-800');
        
        levels.forEach(level => {
          const label = EXERCISE_DURATION_LABELS[level] || level;
          durationContent += ModalHelpers.createListItem(null, label, null, 'green-700');
        });
        
        content += ModalHelpers.createSection('green', 'green', durationContent);
      }

      if (impacts.length > 0) {
        let typesContent = ModalHelpers.createSectionHeader(data.typesTitle, 'blue-800');
        
        impacts.forEach(impact => {
          const label = EXERCISE_TYPE_LABELS[impact] || impact;
          typesContent += ModalHelpers.createListItem(null, label, null, 'blue-700');
        });
        
        content += ModalHelpers.createSection('blue', 'blue', typesContent);
      }

      let benefitsContent = ModalHelpers.createSectionHeader(data.benefitsTitle, 'green-800');
      
      if (levels.includes('greater_sixty')) {
        benefitsContent += ModalHelpers.createDescription(data.benefitMessages.extended, 'green-700', 'sm');
      } else if (levels.includes('thirty_to_sixty')) {
        benefitsContent += ModalHelpers.createDescription(data.benefitMessages.moderate, 'green-700', 'sm');
      } else if (levels.includes('less_thirty')) {
        benefitsContent += ModalHelpers.createDescription(data.benefitMessages.light, 'green-700', 'sm');
      }
      
      benefitsContent += ModalHelpers.createDescription(data.exerciseBenefits, 'green-600', 'xs');
      content += ModalHelpers.createSection('green', 'green', benefitsContent);
    } else {
      let restContent = ModalHelpers.createSectionHeader(data.noExerciseTitle, 'gray-800');
      restContent += ModalHelpers.createDescription(data.noExerciseMessage, 'gray-700', 'sm');
      restContent += ModalHelpers.createDescription(data.noExerciseAdvice, 'gray-600', 'xs');
      content += ModalHelpers.createSection('gray', 'gray', restContent);
    }

    let insightsContent = ModalHelpers.createSectionHeader(data.insightsTitle, 'blue-800');
    
    if (hasExercise) {
      insightsContent += ModalHelpers.createLabelValue('Activity Summary:', `${levels.length} duration level(s), ${impacts.length} exercise type(s)`, 'blue-700');
      insightsContent += ModalHelpers.createDescription(data.trackingBenefits, 'blue-600', 'xs');
    } else {
      insightsContent += ModalHelpers.createLabelValue('Status:', 'No exercise recorded today', 'blue-700');
      insightsContent += ModalHelpers.createDescription(data.restDayBenefits, 'blue-600', 'xs');
    }
    
    content += ModalHelpers.createSection('blue', 'blue', insightsContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Sexual Health modal content
   */
  static generateSexModal(pillar) {
    if (!PillarDataValidators.hasSexData(pillar)) {
      return '<p class="text-gray-500">No sexual health data recorded.</p>';
    }

    const today = pillar?.today ?? false;
    const avoided = pillar?.avoided ?? false;
    const satisfied = pillar?.satisfied ?? false;
    const issues = pillar?.issues || [];
    const data = modalContentData.sexualHealth;

    let content = '<div class="space-y-6">';
    content += ModalHelpers.createCenteredHeader(data.title, null, 'sex.png');

    content += '<div class="text-center mb-4">';
    if (today) {
      if (satisfied) {
        content += ModalHelpers.createStatusIndicator('Status', data.statusMessages.satisfied, 'success');
      } else {
        content += ModalHelpers.createStatusIndicator('Status', data.statusMessages.unsatisfied, 'warning');
      }
    } else if (avoided) {
      content += ModalHelpers.createStatusIndicator('Status', data.statusMessages.avoided, 'warning');
    } else {
      content += ModalHelpers.createStatusIndicator('Status', data.statusMessages.noActivity, 'info');
    }
    content += '</div>';

    if (today) {
      let activityContent = ModalHelpers.createSectionHeader(data.activityTitle, 'green-800');
      activityContent += ModalHelpers.createDescription(satisfied ? 'Satisfying experience reported' : 'Experience was not satisfying', 'green-700', 'sm');
      content += ModalHelpers.createSection('green', 'green', activityContent);
    }

    if (avoided) {
      let avoidanceContent = ModalHelpers.createSectionHeader(data.avoidanceTitle, 'orange-800');
      avoidanceContent += ModalHelpers.createDescription(data.avoidanceMessage, 'orange-700', 'sm');
      content += ModalHelpers.createSection('orange', 'orange', avoidanceContent);
    }

    if (issues.length > 0) {
      let issuesContent = ModalHelpers.createSectionHeader(data.issuesTitle, 'red-800');
      
      issues.forEach(issue => {
        const label = SEX_ISSUE_LABELS[issue] || issue;
        issuesContent += ModalHelpers.createListItem(null, label, null, 'red-700');
      });
      
      issuesContent += ModalHelpers.createWarning(data.issuesWarning, 'danger');
      content += ModalHelpers.createSection('red', 'red', issuesContent);
    }

    let insightsContent = ModalHelpers.createSectionHeader(data.insightsTitle, 'blue-800');
    
    let experienceMessage;
    if (today && satisfied) {
      experienceMessage = data.experienceMessages.positive;
    } else if (today && !satisfied) {
      experienceMessage = data.experienceMessages.negative;
    } else if (avoided) {
      experienceMessage = data.experienceMessages.avoided;
    } else {
      experienceMessage = data.experienceMessages.noActivity;
    }
    
    insightsContent += ModalHelpers.createDescription(experienceMessage, 'blue-700', 'sm');
    insightsContent += ModalHelpers.createDescription(data.trackingInfo, 'blue-600', 'xs');
    content += ModalHelpers.createSection('blue', 'blue', insightsContent);

    content += '</div>';
    return content;
  }

  /**
   * Generate Notes modal content
   */
  static generateNotesModal(pillar) {
    if (!PillarDataValidators.hasNotesData(pillar)) {
      return '<p class="text-gray-500">No notes recorded.</p>';
    }

    const text = pillar?.text || '';
    const hasNote = pillar?.hasNote ?? false;
    const data = modalContentData.notes;

    let content = '<div class="space-y-6">';
    content += ModalHelpers.createCenteredHeader(data.title, null, 'grid_notes.png');

    if (hasNote && text) {
      let noteContent = ModalHelpers.createSectionHeader(data.contentTitle, 'blue-800');
      noteContent += `<div class="bg-white rounded p-3 border">`;
      noteContent += ModalHelpers.formatTextWithBreaks(text);
      noteContent += `</div>`;

      content += ModalHelpers.createSection('blue', 'blue', noteContent);
    } else {
      let noNoteContent = ModalHelpers.createSectionHeader(data.noNoteTitle, 'gray-800');
      noNoteContent += ModalHelpers.createDescription('No personal notes recorded for today.', 'gray-700', 'sm');
      content += ModalHelpers.createSection('gray', 'gray', noNoteContent);
    }

    let valueContent = ModalHelpers.createSectionHeader(data.valueTitle, 'green-800');
    
    if (hasNote && text) {
      valueContent += ModalHelpers.createDescription(data.valueMessages.withNote, 'green-700', 'sm');
      valueContent += ModalHelpers.createDescription(data.benefitMessages.withNote, 'green-600', 'xs');
    } else {
      valueContent += ModalHelpers.createDescription(data.valueMessages.withoutNote, 'green-700', 'sm');
      valueContent += ModalHelpers.createDescription(data.benefitMessages.withoutNote, 'green-600', 'xs');
    }
    
    content += ModalHelpers.createSection('green', 'green', valueContent);

    let tipsContent = ModalHelpers.createSectionHeader(data.tipsTitle, 'blue-800');
    data.tips.forEach(tip => {
      tipsContent += `<p class="text-sm text-blue-700 mb-1">• ${tip}</p>`;
    });
    content += ModalHelpers.createSection('blue', 'blue', tipsContent);

    content += '</div>';
    return content;
  }

  /**
   * Main method to generate modal content based on pillar type
   */
  static generateModalContent(pillar, pillarType) {
    try {
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
          return `<p class="text-gray-500">Modal content for ${pillarType} is not yet implemented.</p>`;
      }
    } catch (error) {
      console.error(`Error generating modal content for ${pillarType}:`, error);
      return `<p class="text-red-500">Error loading modal content. Please try again.</p>`;
    }
  }
}
