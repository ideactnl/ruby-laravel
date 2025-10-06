/**
 * Card Generators Module
 * Creates visual card objects for each PBAC pillar type
 */

export class CardGenerators {
  constructor(component) {
    this.component = component;
  }

  /**
   * Generate all cards from pillars data
   */
  generateCards(pillars) {
    if (!pillars || typeof pillars !== 'object') {
      return [];
    }
    
    const allCards = [
      this.createBloodLossCard(pillars.blood_loss),
      this.createPainCard(pillars.pain),
      this.createImpactCard(pillars.impact),
      this.createEnergyCard(pillars.general_health),
      this.createMoodCard(pillars.mood),
      this.createStoolCard(pillars.stool_urine),
      this.createSleepCard(pillars.sleep),
      this.createDietCard(pillars.diet),
      this.createExerciseCard(pillars.exercise),
      this.createSexCard(pillars.sex),
      this.createNotesCard(pillars.notes),
    ];
    
    // Filter out cards with no data
    return allCards.filter(card => card !== null);
  }

  createBloodLossCard(pillar) {
    const amount = pillar?.amount ?? 0;
    const severity = pillar?.severity || 'none';
    const spotting = pillar?.flags?.spotting;
    
    // Hide card if no blood loss data
    if (!pillar || (amount === 0 && !spotting)) {
      return null;
    }
    
    let statusColor = 'bg-gray-300';
    let statusText = 'No data';
    let context = '';
    
    if (amount > 0) {
      if (spotting) {
        statusColor = 'bg-orange-400';
        statusText = 'Spotting detected';
        context = 'Spotting';
      } else if (severity === 'very_heavy') {
        statusColor = 'bg-red-600';
        statusText = 'Very heavy';
        context = 'Very Heavy';
      } else if (severity === 'heavy') {
        statusColor = 'bg-red-500';
        statusText = 'Heavy';
        context = 'Heavy';
      } else if (severity === 'moderate') {
        statusColor = 'bg-yellow-500';
        statusText = 'Moderate';
        context = 'Moderate';
      } else {
        statusColor = 'bg-green-400';
        statusText = 'Light';
        context = 'Light';
      }
    }
    
    // Create severity icons array
    const severityIcons = [];
    const severityLevels = ['very_light', 'light', 'moderate', 'heavy', 'very_heavy'];
    
    // Add spotting icon first
    severityIcons.push({
      src: '/images/spotting.png',
      alt: 'spotting',
      active: spotting
    });
    
    // Add regular severity level icons
    severityLevels.forEach((level, index) => {
      severityIcons.push({
        src: `/images/blood_loss_${index + 1}.png`,
        alt: level,
        active: severity === level && !spotting // Only active if not spotting
      });
    });

    return {
      key: 'blood_loss',
      label: 'Blood Loss',
      iconSrc: spotting ? '/images/spotting.png' : `/images/blood_loss_${Math.max(1, severityLevels.indexOf(severity) + 1)}.png`,
      context: context,
      statusColor: statusColor,
      statusText: statusText,
      severityIcons: severityIcons,
      statusIcon: {
        src: spotting ? '/images/spotting.png' : `/images/blood_loss_${Math.max(1, severityLevels.indexOf(severity) + 1)}.png`,
        alt: 'Blood Loss'
      },
      additionalInfo: spotting ? 'Spotting detected' : '',
      tooltip: this.component.getBloodLossTooltip(pillar),
      pillar: pillar
    };
  }

  createPainCard(pillar) {
    const value = pillar?.value ?? 0;
    const regions = pillar?.regions || [];
    
    // Hide card if no pain data
    if (!pillar || value === 0) {
      return null;
    }
    
    let statusColor = 'bg-gray-300';
    let statusText = 'No pain';
    let context = '';
    let progressColor = 'bg-gray-400';
    
    if (value > 0) {
      if (value >= 8) {
        statusColor = 'bg-red-600';
        statusText = 'Severe pain';
        context = 'Severe';
        progressColor = 'bg-red-500';
      } else if (value >= 6) {
        statusColor = 'bg-orange-500';
        statusText = 'Moderate pain';
        context = 'Moderate';
        progressColor = 'bg-orange-400';
      } else if (value >= 4) {
        statusColor = 'bg-yellow-500';
        statusText = 'Mild pain';
        context = 'Mild';
        progressColor = 'bg-yellow-400';
      } else {
        statusColor = 'bg-green-400';
        statusText = 'Light pain';
        context = 'Light';
        progressColor = 'bg-green-300';
      }
    }
    
    // Create pain level icons (6 levels)
    // Pain mapping: 0-1→1, 2→2, 3-4→3, 5-6→4, 7-8→5, 9-10→6
    const severityIcons = [];
    let currentPainIcon = 1;
    if (value >= 9) currentPainIcon = 6;
    else if (value >= 7) currentPainIcon = 5;
    else if (value >= 5) currentPainIcon = 4;
    else if (value >= 3) currentPainIcon = 3;
    else if (value >= 2) currentPainIcon = 2;
    else currentPainIcon = 1;
    
    for (let i = 1; i <= 6; i++) {
      severityIcons.push({
        src: `/images/smile_${i}.png`,
        alt: `Pain level ${i}`,
        active: i === currentPainIcon
      });
    }

    // Format regions for display
    const regionLabels = {
      'left_umbilical': 'Left Umbilical',
      'right_umbilical': 'Right Umbilical', 
      'left_iliac': 'Left Iliac',
      'right_iliac': 'Right Iliac',
      'hypogastric': 'Hypogastric',
      'epigastric': 'Epigastric',
      'left_hypochondriac': 'Left Hypochondriac',
      'right_hypochondriac': 'Right Hypochondriac',
      'umbilical': 'Umbilical',
      'back': 'Back',
      'pelvis': 'Pelvis',
      'legs': 'Legs'
    };
    
    const formattedRegions = regions.map(region => regionLabels[region] || region).join(', ');
    
    return {
      key: 'pain',
      label: 'Pain',
      iconSrc: `/images/smile_${currentPainIcon}.png`,
      context: context, // Use descriptive text instead of numbers
      statusColor: statusColor,
      statusText: statusText,
      severityIcons: severityIcons,
      statusIcon: {
        src: `/images/smile_${currentPainIcon}.png`,
        alt: 'Pain Level'
      },
      additionalInfo: formattedRegions || '',
      tooltip: this.component.getPainTooltip(pillar),
      pillar: pillar
    };
  }

  createImpactCard(pillar) {
    const grade = pillar?.gradeYourDay ?? null;
    const limitations = pillar?.limitations || [];
    
    // Hide card if no impact data
    if (!pillar || (grade === null && limitations.length === 0)) {
      return null;
    }
    
    let statusColor = 'bg-gray-300';
    let statusText = 'No impact';
    let context = '';
    
    if (grade !== null) {
      if (grade >= 8) {
        statusColor = 'bg-green-500';
        statusText = 'Great day';
        context = 'Great';
      } else if (grade >= 6) {
        statusColor = 'bg-yellow-400';
        statusText = 'Good day';
        context = 'Good';
      } else if (grade >= 4) {
        statusColor = 'bg-orange-400';
        statusText = 'Challenging day';
        context = 'Challenging';
      } else {
        statusColor = 'bg-red-500';
        statusText = 'Difficult day';
        context = 'Difficult';
      }
    }

    // Format limitations for display
    const limitationLabels = {
      'used_medication': 'Used Medication',
      'missed_work': 'Missed Work',
      'missed_school': 'Missed School',
      'could_not_sport': 'Could Not Exercise',
      'missed_social_activities': 'Missed Social Activities',
      'missed_leisure_activities': 'Missed Leisure Activities',
      'had_to_sit_more': 'Had to Sit More',
      'had_to_lie_down': 'Had to Lie Down',
      'had_to_stay_longer_in_bed': 'Stayed Longer in Bed',
      'could_not_do_unpaid_work': 'Could Not Do Unpaid Work',
      'other': 'Other Limitations'
    };
    
    const formattedLimitations = limitations
      .filter(limitation => limitation && limitation !== '_' && limitation.trim() !== '')
      .map(limitation => limitationLabels[limitation] || limitation.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))
      .join(', ');

    // Determine primary impact icon based on limitations
    let primaryIcon = '/images/impact_1.png'; // Default to medication icon
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

    return {
      key: 'impact',
      label: 'Impact',
      iconSrc: primaryIcon,
      context: context, // Use descriptive text instead of numbers
      statusColor: statusColor,
      statusText: statusText,
      statusIcon: {
        src: primaryIcon,
        alt: 'Daily Impact'
      },
      additionalInfo: formattedLimitations || '',
      tooltip: this.component.getImpactTooltip(pillar),
      pillar: pillar
    };
  }

  createEnergyCard(pillar) {
    const energy = pillar?.energyLevel ?? 0;
    const symptoms = pillar?.symptoms || [];
    
    // Hide card if no energy data
    if (!pillar || energy === 0) {
      return null;
    }
    
    let statusColor = 'bg-gray-300';
    let statusText = 'No energy data';
    let context = '';
    
    if (energy > 0) {
      const energyLabels = ['', 'Very Low', 'Low', 'Moderate', 'Good', 'High'];
      context = energyLabels[energy] || 'Unknown';
      
      // Adjust status based on energy level and symptoms
      if (energy >= 5) {
        statusColor = symptoms.length > 0 ? 'bg-yellow-500' : 'bg-green-500';
        statusText = symptoms.length > 0 ? 'High energy with symptoms' : 'High energy';
      } else if (energy >= 4) {
        statusColor = symptoms.length > 0 ? 'bg-yellow-500' : 'bg-green-500';
        statusText = symptoms.length > 0 ? 'Good energy with symptoms' : 'Good energy';
      } else if (energy >= 3) {
        statusColor = 'bg-yellow-400';
        statusText = 'Moderate energy';
      } else if (energy >= 2) {
        statusColor = 'bg-orange-400';
        statusText = 'Low energy';
      } else {
        statusColor = 'bg-red-400';
        statusText = 'Very low energy';
      }
    }

    // Format symptoms for display
    const symptomLabels = {
      'fatigue': 'Fatigue',
      'headache': 'Headache',
      'nausea': 'Nausea',
      'nauseous': 'Nauseous',
      'dizziness': 'Dizziness',
      'dizzy': 'Dizzy',
      'weakness': 'Weakness',
      'joint_pain': 'Joint Pain',
      'muscle_pain': 'Muscle Pain',
      'fever': 'Fever',
      'chills': 'Chills',
      'sweating': 'Sweating',
      'bloated': 'Bloated',
      'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
      'acne': 'Acne',
      'muscle_joint_pain': 'Muscle/Joint Pain',
      'headache_migraine': 'Headache/Migraine'
    };
    
    const formattedSymptoms = symptoms
      .filter(symptom => symptom && symptom !== '_' && symptom.trim() !== '')
      .map(symptom => symptomLabels[symptom] || symptom.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))
      .join(', ');
    
    // Create energy level icons (5 levels)
    const severityIcons = [];
    for (let i = 1; i <= 5; i++) {
      const iconSrc = i === 1 ? '/images/sleep.png' : `/images/general_health_${i - 1}.png`;
      severityIcons.push({
        src: iconSrc,
        alt: `Energy level ${i}`,
        active: i === energy
      });
    }
    
    return {
      key: 'general_health',
      label: 'General Health',
      iconSrc: energy === 1 ? '/images/sleep.png' : `/images/general_health_${Math.min(energy - 1, 4)}.png`,
      context: context, // Use descriptive text instead of numbers
      statusColor: statusColor,
      statusText: statusText,
      severityIcons: severityIcons,
      statusIcon: {
        src: energy === 1 ? '/images/sleep.png' : `/images/general_health_${Math.min(energy - 1, 4)}.png`,
        alt: 'Energy Level'
      },
      additionalInfo: formattedSymptoms || '',
      tooltip: this.component.getEnergyTooltip(pillar),
      pillar: pillar
    };
  }

  createMoodCard(pillar) {
    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    
    // Hide card if no mood data
    if (!pillar || (positives.length === 0 && negatives.length === 0)) {
      return null;
    }
    
    const balance = positives.length - negatives.length;
    let statusColor = 'bg-gray-300';
    let statusText = 'Balanced';
    let context = 'Balanced';
    let moodIcon = 'mood_1.png'; // calm
    
    if (balance > 1) {
      statusColor = 'bg-green-500';
      statusText = 'Positive day';
      context = 'Positive';
      moodIcon = 'mood_2.png'; // happy
    } else if (balance < -1) {
      statusColor = 'bg-red-400';
      statusText = 'Challenging day';
      context = 'Challenging';
      moodIcon = 'mood_7.png'; // sad
    }

    // Create mood icons based on actual mood states
    const severityIcons = [];
    const allMoods = [...positives, ...negatives];
    const moodIconMap = {
      'calm': 'mood_1.png',
      'happy': 'mood_2.png',
      'excited': 'mood_3.png',
      'anxious': 'mood_4.png',
      'stressed': 'mood_4.png',
      'ashamed': 'mood_5.png',
      'angry': 'mood_6.png',
      'irritable': 'mood_6.png',
      'sad': 'mood_7.png',
      'mood_swings': 'mood_8.png',
      'worthless': 'mood_9.png',
      'guilty': 'mood_9.png',
      'overwhelmed': 'mood_10.png',
      'hopeless': 'mood_11.png',
      'depressed': 'mood_12.png'
    };

    // Add all 12 mood icons with active ones highlighted
    const moodKeys = ['calm', 'happy', 'excited', 'anxious', 'ashamed', 'angry', 'sad', 'mood_swings', 'worthless', 'overwhelmed', 'hopeless', 'depressed'];
    
    moodKeys.forEach(moodKey => {
      const isActive = allMoods.includes(moodKey) || 
                      (moodKey === 'anxious' && (allMoods.includes('anxious') || allMoods.includes('stressed'))) ||
                      (moodKey === 'angry' && (allMoods.includes('angry') || allMoods.includes('irritable'))) ||
                      (moodKey === 'worthless' && (allMoods.includes('worthless') || allMoods.includes('guilty')));
      
      severityIcons.push({
        src: `/images/${moodIconMap[moodKey]}`,
        alt: moodKey,
        active: isActive
      });
    });

    return {
      key: 'mood',
      label: 'Mood',
      iconSrc: `/images/${moodIcon}`,
      context: context,
      statusColor: statusColor,
      statusText: statusText,
      severityIcons: severityIcons,
      statusIcon: {
        src: `/images/${moodIcon}`,
        alt: 'Mood State'
      },
      additionalInfo: `${positives.length} positive, ${negatives.length} negative`,
      tooltip: this.component.getMoodTooltip(pillar),
      pillar: pillar
    };
  }

  // Placeholder methods for other card types
  createStoolCard(pillar) {
    if (!pillar) return null;
    
    const hasUrineBlood = pillar?.urine?.blood ?? false;
    const hasStoolBlood = pillar?.stool?.blood ?? false;
    const bloodInStool = hasUrineBlood || hasStoolBlood;
    const consistency = pillar?.stool?.consistency;
    
    let statusColor = 'bg-gray-300';
    let statusText = 'No data';
    let context = 'Recorded';
    let primaryIcon = '/images/urine_stool_2.png'; // default normal
    
    if (bloodInStool) {
      statusColor = 'bg-red-500';
      statusText = 'Blood detected';
      if (hasUrineBlood && hasStoolBlood) {
        context = 'Blood in urine & stool';
      } else if (hasUrineBlood) {
        context = 'Blood in urine';
      } else {
        context = 'Blood in stool';
      }
      primaryIcon = '/images/urine_stool.png';
    } else if (consistency) {
      const consistencyMap = {
        'hard': { color: 'bg-orange-400', text: 'Hard stool', icon: '/images/urine_stool_1.png' },
        'normal': { color: 'bg-green-400', text: 'Normal', icon: '/images/urine_stool_2.png' },
        'soft': { color: 'bg-yellow-400', text: 'Soft stool', icon: '/images/urine_stool_3.png' },
        'watery': { color: 'bg-blue-400', text: 'Watery', icon: '/images/urine_stool_4.png' },
        'something_else': { color: 'bg-purple-400', text: 'Other', icon: '/images/urine_stool_5.png' },
        'no_stool': { color: 'bg-gray-400', text: 'No stool', icon: '/images/urine_stool_6.png' }
      };
      
      if (consistencyMap[consistency]) {
        statusColor = consistencyMap[consistency].color;
        statusText = consistencyMap[consistency].text;
        context = consistencyMap[consistency].text;
        primaryIcon = consistencyMap[consistency].icon;
      }
    }
    
    // Create severity icons for stool conditions
    const severityIcons = [
      { src: '/images/urine_stool.png', alt: 'Blood in stool/urine', active: hasUrineBlood || hasStoolBlood },
      { src: '/images/urine_stool_1.png', alt: 'Hard', active: consistency === 'hard' },
      { src: '/images/urine_stool_2.png', alt: 'Normal', active: consistency === 'normal' },
      { src: '/images/urine_stool_3.png', alt: 'Soft', active: consistency === 'soft' },
      { src: '/images/urine_stool_4.png', alt: 'Watery', active: consistency === 'watery' },
      { src: '/images/urine_stool_5.png', alt: 'Something else', active: consistency === 'something_else' },
      { src: '/images/urine_stool_6.png', alt: 'No stool', active: consistency === 'no_stool' }
    ];
    
    return {
      key: 'stool_urine',
      label: 'Stool/Urine',
      iconSrc: primaryIcon,
      context: context,
      statusColor: statusColor,
      statusText: statusText,
      severityIcons: severityIcons,
      statusIcon: {
        src: primaryIcon,
        alt: 'Stool/Urine Status'
      },
      tooltip: this.component.getStoolTooltip(pillar),
      pillar: pillar
    };
  }

  createSleepCard(pillar) {
    const hours = pillar?.calculatedHours ?? 0;
    
    if (!pillar || hours === 0) return null;
    
    return {
      key: 'sleep',
      label: 'Sleep',
      iconSrc: '/images/sleep.png',
      context: `${hours} hours`,
      statusColor: 'bg-indigo-400',
      statusText: 'Sleep tracked',
      statusIcon: {
        src: '/images/sleep.png',
        alt: 'Sleep Quality'
      },
      tooltip: this.component.getSleepTooltip(pillar),
      pillar: pillar
    };
  }

  createDietCard(pillar) {
    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    const neutrals = pillar?.neutrals || [];
    
    if (!pillar || (positives.length === 0 && negatives.length === 0 && neutrals.length === 0)) {
      return null;
    }
    
    // Create diet icons based on consumed items
    const severityIcons = [];
    const allDietItems = [...positives, ...negatives, ...neutrals];
    const dietIconMap = {
      'vegetables': 'diet_1.png',
      'fruit': 'diet_2.png',
      'potato_rice_bread': 'diet_3.png',
      'dairy_products': 'diet_4.png',
      'nuts_tofu_tempe': 'diet_5.png',
      'egg': 'diet_6.png',
      'fish': 'diet_7.png',
      'meat': 'diet_8.png',
      'snacks': 'diet_9.png',
      'water': 'diet_10.png',
      'coffee': 'diet_11.png',
      'alcohol': 'diet_12.png'
    };

    // Add all 12 diet icons with consumed ones highlighted
    const dietKeys = ['vegetables', 'fruit', 'potato_rice_bread', 'dairy_products', 'nuts_tofu_tempe', 'egg', 'fish', 'meat', 'snacks', 'water', 'coffee', 'alcohol'];
    
    dietKeys.forEach(dietKey => {
      const isActive = allDietItems.includes(dietKey);
      severityIcons.push({
        src: `/images/${dietIconMap[dietKey]}`,
        alt: dietKey,
        active: isActive
      });
    });
    
    return {
      key: 'diet',
      label: 'Diet',
      iconSrc: '/images/grid_diet.png',
      context: 'Diet tracked',
      statusColor: 'bg-green-400',
      statusText: 'Items recorded',
      severityIcons: severityIcons,
      statusIcon: {
        src: '/images/grid_diet.png',
        alt: 'Diet Quality'
      },
      additionalInfo: `${positives.length + negatives.length + neutrals.length} items`,
      tooltip: this.component.getDietTooltip(pillar),
      pillar: pillar
    };
  }

  createExerciseCard(pillar) {
    if (!pillar || !pillar.any) return null;
    
    const levels = pillar.levels || [];
    const types = pillar.types || [];
    
    // Duration context
    let context = 'Exercise completed';
    if (levels.includes('greater_sixty')) {
      context = '>60 minutes';
    } else if (levels.includes('thirty_to_sixty')) {
      context = '30-60 minutes';
    } else if (levels.includes('less_thirty')) {
      context = '<30 minutes';
    }
    
    // Exercise type labels
    const typeLabels = {
      'high_impact': 'High Impact',
      'precision_exercise': 'Precision Exercise',
      'low_impact': 'Low Impact',
      'cardio': 'Cardio',
      'strength': 'Strength Training',
      'flexibility': 'Flexibility',
      'yoga': 'Yoga',
      'walking': 'Walking',
      'running': 'Running',
      'swimming': 'Swimming',
      'cycling': 'Cycling'
    };
    
    // Format exercise types for display
    const formattedTypes = types
      .filter(type => type && type !== '_' && type.trim() !== '') // Filter out empty/invalid types
      .map(type => typeLabels[type] || type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))
      .join(', ');
    
    return {
      key: 'exercise',
      label: 'Exercise',
      iconSrc: '/images/sport.png',
      context: context,
      statusColor: 'bg-orange-400',
      statusText: 'Exercise completed',
      statusIcon: {
        src: '/images/sport.png',
        alt: 'Exercise Activity'
      },
      additionalInfo: formattedTypes || 'Exercise activity',
      tooltip: this.component.getExerciseTooltip(pillar),
      pillar: pillar
    };
  }

  createSexCard(pillar) {
    if (!pillar || (!pillar.today && !pillar.avoided)) return null;
    
    let context = 'Activity recorded';
    let statusColor = 'bg-pink-400';
    
    if (pillar.avoided) {
      context = 'Avoided';
      statusColor = 'bg-gray-400';
    } else if (pillar.satisfied) {
      context = 'Satisfied';
      statusColor = 'bg-green-400';
    }
    
    return {
      key: 'sex',
      label: 'Sexual Health',
      iconSrc: '/images/sex.png',
      context: context,
      statusColor: statusColor,
      statusText: context,
      statusIcon: {
        src: '/images/sex.png',
        alt: 'Sexual Health'
      },
      tooltip: this.component.getSexTooltip(pillar),
      pillar: pillar
    };
  }

  createNotesCard(pillar) {
    if (!pillar || !pillar.hasNote) return null;
    
    return {
      key: 'notes',
      label: 'Notes',
      iconSrc: '/images/grid_notes.png',
      context: 'Note recorded',
      statusColor: 'bg-gray-500',
      statusText: 'Note available',
      statusIcon: {
        src: '/images/grid_notes.png',
        alt: 'Notes'
      },
      tooltip: this.component.getNotesTooltip(pillar),
      pillar: pillar
    };
  }
}
