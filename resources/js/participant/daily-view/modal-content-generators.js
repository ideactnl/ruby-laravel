/**
 * Modal Content Generators Module
 * Generates detailed HTML content for each pillar type modal
 */

export class ModalContentGenerators {
  static generateBloodLossModal(pillar) {
    const amount = pillar?.amount ?? 0;
    const severity = pillar?.severity || 'none';
    const spotting = pillar?.flags?.spotting;
    
    let content = '<div class="space-y-4">';
    
    // Severity visualization
    content += '<div class="text-center">';
    content += '<h3 class="text-lg font-semibold mb-3">Blood Loss Severity</h3>';
    content += '<div class="flex justify-center items-center gap-2 mb-4">';
    
    // Add spotting icon first
    const spottingActive = spotting;
    const spottingClasses = spottingActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
    content += `<div class="${spottingClasses}"><img src="/images/spotting.png" alt="spotting" class="w-8 h-8 object-contain"></div>`;
    
    // Add regular severity level icons
    const severityLevels = ['very_light', 'light', 'moderate', 'heavy', 'very_heavy'];
    severityLevels.forEach((level, index) => {
      const isActive = severity === level && !spotting;
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/blood_loss_${index + 1}.png" alt="${level}" class="w-8 h-8 object-contain"></div>`;
    });
    
    content += '</div>';
    
    if (spotting) {
      content += '<div class="bg-orange-50 border border-orange-200 rounded-lg p-4">';
      content += '<div class="flex items-center gap-2">';
      content += '<img src="/images/spotting.png" alt="Spotting" class="w-6 h-6 object-contain">';
      content += '<span class="font-medium text-orange-800">Spotting Detected</span>';
      content += '</div>';
      content += '</div>';
    }
    
    content += `<p class="text-gray-600">Amount recorded: ${amount} ml</p>`;
    content += '</div>';
    content += '</div>';
    
    return content;
  }

  static generatePainModal(pillar) {
    const value = pillar?.value ?? 0;
    const regions = pillar?.regions || [];
    
    let content = '<div class="space-y-4">';
    
    // Pain level visualization
    content += '<div class="text-center">';
    content += '<h3 class="text-lg font-semibold mb-3">Pain Level</h3>';
    content += '<div class="flex justify-center items-center gap-1 mb-4">';
    
    // Calculate current pain icon using same logic as card
    let currentPainIcon = 1;
    if (value >= 9) currentPainIcon = 6;
    else if (value >= 7) currentPainIcon = 5;
    else if (value >= 5) currentPainIcon = 4;
    else if (value >= 3) currentPainIcon = 3;
    else if (value >= 2) currentPainIcon = 2;
    else currentPainIcon = 1;
    
    for (let i = 1; i <= 6; i++) {
      const isActive = i === currentPainIcon;
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/smile_${i}.png" alt="Pain level ${i}" class="w-8 h-8 object-contain"></div>`;
    }
    
    content += '</div>';
    
    // Affected regions
    if (regions.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Affected Areas:</h4>';
      content += '<div class="grid grid-cols-2 gap-2">';
      
      const regionLabels = {
        'umbilical': 'Umbilical', 'left_umbilical': 'Left Umbilical', 'right_umbilical': 'Right Umbilical',
        'bladder': 'Bladder', 'left_groin': 'Left Groin', 'right_groin': 'Right Groin',
        'left_leg': 'Left Leg', 'right_leg': 'Right Leg', 'upper_back': 'Upper Back',
        'back': 'Back', 'left_buttock': 'Left Buttock', 'right_buttock': 'Right Buttock',
        'left_back_leg': 'Left Back Leg', 'right_back_leg': 'Right Back Leg'
      };
      
      regions.forEach(region => {
        const label = regionLabels[region] || region;
        content += `<div class="text-sm text-red-700">• ${label}</div>`;
      });
      
      content += '</div>';
      content += '</div>';
    }
    
    content += '</div>';
    content += '</div>';
    
    return content;
  }

  static generateMoodModal(pillar) {
    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    
    let content = '<div class="space-y-4">';
    
    // Mood states visualization - show all 12 mood icons
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Mood States</h3>';
    
    // All mood states with their icons
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
    
    // Display all 12 mood icons in a grid
    content += '<div class="grid grid-cols-6 gap-2 mb-4 justify-items-center">';
    const moodKeys = ['calm', 'happy', 'excited', 'anxious', 'ashamed', 'angry', 'sad', 'mood_swings', 'worthless', 'overwhelmed', 'hopeless', 'depressed'];
    
    moodKeys.forEach((moodKey, index) => {
      const isActive = allMoods.includes(moodKey) || 
                      (moodKey === 'anxious' && (allMoods.includes('anxious') || allMoods.includes('stressed'))) ||
                      (moodKey === 'angry' && (allMoods.includes('angry') || allMoods.includes('irritable'))) ||
                      (moodKey === 'worthless' && (allMoods.includes('worthless') || allMoods.includes('guilty')));
      
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      const iconFile = moodIconMap[moodKey];
      content += `<div class="${classes}"><img src="/images/${iconFile}" alt="${moodKey}" class="w-8 h-8 object-contain"></div>`;
    });
    content += '</div>';
    
    // Overall mood summary
    const balance = positives.length - negatives.length;
    if (balance > 0) {
      content += '<p class="text-green-600 font-medium">Overall: Positive Day</p>';
    } else if (balance < 0) {
      content += '<p class="text-red-600 font-medium">Overall: Challenging Day</p>';
    } else {
      content += '<p class="text-yellow-600 font-medium">Overall: Balanced Day</p>';
    }
    
    content += '</div>';
    
    // Mood states
    const moodLabels = {
      'calm': 'Calm', 'happy': 'Happy', 'excited': 'Excited', 'hopes': 'Hopeful',
      'anxious_stressed': 'Anxious/Stressed', 'ashamed': 'Ashamed', 'angry_irritable': 'Angry/Irritable',
      'sad': 'Sad', 'mood_swings': 'Mood Swings', 'worthless_guilty': 'Worthless/Guilty',
      'overwhelmed': 'Overwhelmed', 'hopeless': 'Hopeless', 'depressed_sad_down': 'Depressed/Sad/Down'
    };
    
    if (positives.length > 0) {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">Positive States:</h4>';
      content += '<div class="space-y-1">';
      positives.forEach(mood => {
        const label = moodLabels[mood] || mood;
        content += `<div class="text-sm text-green-700">• ${label}</div>`;
      });
      content += '</div>';
      content += '</div>';
    }
    
    if (negatives.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Negative States:</h4>';
      content += '<div class="space-y-1">';
      negatives.forEach(mood => {
        const label = moodLabels[mood] || mood;
        content += `<div class="text-sm text-red-700">• ${label}</div>`;
      });
      content += '</div>';
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateDietModal(pillar) {
    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    const neutrals = pillar?.neutrals || [];
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Diet Items</h3>';
    
    // Show all 12 diet item icons
    content += '<div class="grid grid-cols-6 gap-2 mb-4 justify-items-center">';
    
    const allDietItems = [...positives, ...negatives, ...neutrals];
    const dietItems = [
      { key: 'vegetables', icon: 'diet_1.png', label: 'Vegetables' },
      { key: 'fruit', icon: 'diet_2.png', label: 'Fruit' },
      { key: 'potato_rice_bread', icon: 'diet_3.png', label: 'Carbohydrates' },
      { key: 'dairy_products', icon: 'diet_4.png', label: 'Dairy' },
      { key: 'nuts_tofu_tempe', icon: 'diet_5.png', label: 'Protein Alt.' },
      { key: 'egg', icon: 'diet_6.png', label: 'Eggs' },
      { key: 'fish', icon: 'diet_7.png', label: 'Fish' },
      { key: 'meat', icon: 'diet_8.png', label: 'Meat' },
      { key: 'snacks', icon: 'diet_9.png', label: 'Snacks' },
      { key: 'water', icon: 'diet_10.png', label: 'Water' },
      { key: 'coffee', icon: 'diet_11.png', label: 'Coffee' },
      { key: 'alcohol', icon: 'diet_12.png', label: 'Alcohol' }
    ];
    
    dietItems.forEach(item => {
      const isActive = allDietItems.includes(item.key);
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/${item.icon}" alt="${item.label}" class="w-8 h-8 object-contain"></div>`;
    });
    
    content += '</div>';
    content += `<p class="text-gray-600">Total items consumed: ${allDietItems.length}</p>`;
    content += '</div>';
    
    const dietLabels = {
      'vegetables': 'Vegetables', 'fruit': 'Fruit', 'potato_rice_bread': 'Carbohydrates',
      'dairy': 'Dairy Products', 'nuts_tofu_tempe': 'Protein Alternatives', 'eggs': 'Eggs',
      'fish': 'Fish', 'meat': 'Meat', 'snacks': 'Snacks', 'soda': 'Soda',
      'water': 'Water', 'coffee': 'Coffee', 'alcohol': 'Alcohol'
    };
    
    if (positives.length > 0) {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">Healthy Choices:</h4>';
      content += '<div class="grid grid-cols-2 gap-1">';
      positives.forEach(item => {
        const label = dietLabels[item] || item;
        content += `<div class="text-sm text-green-700">• ${label}</div>`;
      });
      content += '</div>';
      content += '</div>';
    }
    
    if (negatives.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Poor Choices:</h4>';
      content += '<div class="grid grid-cols-2 gap-1">';
      negatives.forEach(item => {
        const label = dietLabels[item] || item;
        content += `<div class="text-sm text-red-700">• ${label}</div>`;
      });
      content += '</div>';
      content += '</div>';
    }
    
    if (neutrals.length > 0) {
      content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-gray-800 mb-2">Neutral Choices:</h4>';
      content += '<div class="grid grid-cols-2 gap-1">';
      neutrals.forEach(item => {
        const label = dietLabels[item] || item;
        content += `<div class="text-sm text-gray-700">• ${label}</div>`;
      });
      content += '</div>';
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateImpactModal(pillar) {
    const grade = pillar?.gradeYourDay ?? 0;
    const limitations = pillar?.limitations || [];
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Daily Impact & Limitations</h3>';
    
    // Show all 11 impact limitation icons
    content += '<div class="grid grid-cols-6 gap-2 mb-4 justify-items-center">';
    
    const impactTypes = [
      { key: 'used_medication', icon: 'impact_1.png', label: 'Used Medication' },
      { key: 'missed_work', icon: 'impact_2.png', label: 'Missed Work' },
      { key: 'missed_school', icon: 'impact_3.png', label: 'Missed School' },
      { key: 'could_not_sport', icon: 'impact_4.png', label: 'Could Not Exercise' },
      { key: 'missed_social_activities', icon: 'impact_5.png', label: 'Missed Social' },
      { key: 'missed_leisure_activities', icon: 'impact_6.png', label: 'Missed Leisure' },
      { key: 'had_to_sit_more', icon: 'impact_7.png', label: 'Had to Sit More' },
      { key: 'had_to_lie_down', icon: 'impact_8.png', label: 'Had to Lie Down' },
      { key: 'had_to_stay_longer_in_bed', icon: 'impact_9.png', label: 'Stayed in Bed' },
      { key: 'could_not_do_unpaid_work', icon: 'impact_10.png', label: 'No Unpaid Work' },
      { key: 'other', icon: 'impact_11.png', label: 'Other Limitations' }
    ];
    
    impactTypes.forEach(impact => {
      const isActive = limitations.includes(impact.key);
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/${impact.icon}" alt="${impact.label}" class="w-8 h-8 object-contain"></div>`;
    });
    
    content += '</div>';
    
    // Day grade summary
    let gradeText = 'Difficult Day';
    let gradeColor = 'text-red-600';
    if (grade >= 8) {
      gradeText = 'Great Day';
      gradeColor = 'text-green-600';
    } else if (grade >= 6) {
      gradeText = 'Good Day';
      gradeColor = 'text-yellow-600';
    } else if (grade >= 4) {
      gradeText = 'Challenging Day';
      gradeColor = 'text-orange-600';
    }
    
    content += `<p class="text-lg font-medium mb-2 ${gradeColor}">${gradeText}</p>`;
    if (grade > 0) {
      content += `<p class="text-gray-600">Daily Grade: ${grade}/10</p>`;
    }
    content += '</div>';
    
    // Limitations
    if (limitations.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Daily Limitations:</h4>';
      content += '<div class="grid grid-cols-1 gap-2">';
      
      const limitationLabels = {
        'used_medication': 'Used Medication', 'missed_work': 'Missed Work', 'missed_school': 'Missed School',
        'could_not_sport': 'Could Not Sport', 'missed_social_activities': 'Missed Social Activities',
        'missed_leisure_activities': 'Missed Leisure Activities', 'had_to_sit_more': 'Had to Sit More',
        'had_to_lie_down': 'Had to Lie Down', 'had_to_stay_longer_in_bed': 'Had to Stay Longer in Bed',
        'could_not_do_unpaid_work': 'Could Not Do Unpaid Work', 'other': 'Other Impact'
      };
      
      limitations.forEach(limitation => {
        const label = limitationLabels[limitation] || limitation;
        const iconIndex = Object.keys(limitationLabels).indexOf(limitation) + 1;
        content += `<div class="flex items-center gap-2 text-sm text-red-700">`;
        content += `<img src="/images/impact_${iconIndex}.png" alt="${label}" class="w-4 h-4 object-contain">`;
        content += `<span>${label}</span>`;
        content += `</div>`;
      });
      
      content += '</div>';
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateEnergyModal(pillar) {
    const energy = pillar?.energyLevel ?? 0;
    const symptoms = pillar?.symptoms || [];
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Energy Level</h3>';
    
    // Energy level visualization - show all 5 levels with current one highlighted
    content += '<div class="flex justify-center items-center gap-2 mb-4">';
    for (let i = 1; i <= 5; i++) {
      const isActive = energy === i;
      const classes = isActive ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      const iconName = i === 1 ? 'sleep.png' : `general_health_${i - 1}.png`;
      content += `<div class="${classes}"><img src="/images/${iconName}" alt="Energy level ${i}" class="w-8 h-8 object-contain"></div>`;
    }
    content += '</div>';
    
    const energyLabels = ['', 'Very Low', 'Low', 'Moderate', 'Good', 'High'];
    content += `<p class="text-lg font-medium mb-2">${energyLabels[energy]} Energy</p>`;
    content += `<p class="text-gray-600">Level: ${energy}/5</p>`;
    content += '</div>';
    
    // Symptoms
    if (symptoms.length > 0) {
      content += '<div class="bg-orange-50 border border-orange-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-orange-800 mb-2">Symptoms:</h4>';
      content += '<div class="grid grid-cols-2 gap-2">';
      
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
      
      symptoms.forEach(symptom => {
        const label = symptomLabels[symptom] || symptom;
        content += `<div class="text-sm text-orange-700">• ${label}</div>`;
      });
      
      content += '</div>';
      content += '</div>';
    } else {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<p class="text-green-700 text-center">No symptoms reported</p>';
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateStoolModal(pillar) {
    const hasUrineBlood = pillar?.urine?.blood ?? false;
    const hasStoolBlood = pillar?.stool?.blood ?? false;
    const consistency = pillar?.stool?.consistency;
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Stool & Urine Conditions</h3>';
    
    // Show all 7 stool/urine condition icons
    content += '<div class="flex justify-center items-center gap-2 mb-4 flex-wrap">';
    
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
      const classes = condition.active ? 'opacity-100 bg-blue-100 border-2 border-blue-500 rounded-full p-1' : 'opacity-30';
      content += `<div class="${classes}"><img src="/images/${condition.icon}" alt="${condition.label}" class="w-8 h-8 object-contain"></div>`;
    });
    
    content += '</div>';
    
    // Status summary
    if (hasUrineBlood || hasStoolBlood) {
      let bloodStatus = 'Status: Blood Detected';
      if (hasUrineBlood && hasStoolBlood) {
        bloodStatus = 'Status: Blood in Urine & Stool';
      } else if (hasUrineBlood) {
        bloodStatus = 'Status: Blood in Urine';
      } else {
        bloodStatus = 'Status: Blood in Stool';
      }
      content += `<p class="text-red-600 font-medium">${bloodStatus}</p>`;
    } else if (consistency) {
      const statusColors = {
        'hard': 'text-orange-600',
        'normal': 'text-green-600',
        'soft': 'text-yellow-600',
        'watery': 'text-blue-600',
        'something_else': 'text-purple-600',
        'no_stool': 'text-gray-600'
      };
      const colorClass = statusColors[consistency] || 'text-gray-600';
      content += `<p class="${colorClass} font-medium">Status: ${consistency.charAt(0).toUpperCase() + consistency.slice(1).replace('_', ' ')}</p>`;
    }
    
    content += '</div>';
    
    // Blood detection details
    if (hasUrineBlood || hasStoolBlood) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Blood Detection:</h4>';
      if (hasUrineBlood) content += '<div class="text-sm text-red-700">• Blood in urine</div>';
      if (hasStoolBlood) content += '<div class="text-sm text-red-700">• Blood in stool</div>';
      content += '</div>';
    }
    
    // Consistency details
    if (consistency) {
      content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-blue-800 mb-2">Stool Consistency:</h4>';
      
      const consistencyLabels = {
        'hard': 'Hard', 'normal': 'Normal', 'soft': 'Soft',
        'watery': 'Watery', 'something_else': 'Something Else', 'no_stool': 'No Stool'
      };
      
      const label = consistencyLabels[consistency] || consistency;
      content += `<div class="text-sm text-blue-700">${label}</div>`;
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateSleepModal(pillar) {
    const hours = pillar?.calculatedHours ?? 0;
    const fellAsleep = pillar?.fellAsleepTime;
    const wokeUp = pillar?.wokeUpTime;
    const troubleAsleep = pillar?.troubleAsleep ?? false;
    const wakeUpDuringNight = pillar?.wakeUpDuringNight ?? false;
    const tiredRested = pillar?.tiredRested ?? false;
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Sleep Quality</h3>';
    content += `<img src="/images/sleep.png" alt="Sleep" class="w-16 h-16 object-contain mx-auto mb-2">`;
    
    let quality = 'Good Sleep';
    let qualityColor = 'text-green-600';
    if (hours < 6 || hours > 10 || troubleAsleep || wakeUpDuringNight || !tiredRested) {
      quality = 'Poor Sleep';
      qualityColor = 'text-red-600';
    } else if (hours < 7 || hours > 9) {
      quality = 'Okay Sleep';
      qualityColor = 'text-yellow-600';
    }
    
    content += `<p class="${qualityColor} font-medium mb-2">${quality}</p>`;
    content += `<p class="text-gray-600">${hours.toFixed(1)} hours</p>`;
    content += '</div>';
    
    // Sleep schedule
    if (fellAsleep && wokeUp) {
      content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-blue-800 mb-2">Sleep Schedule:</h4>';
      content += `<div class="text-sm text-blue-700">Fell asleep: ${fellAsleep}</div>`;
      content += `<div class="text-sm text-blue-700">Woke up: ${wokeUp}</div>`;
      content += '</div>';
    }
    
    // Sleep issues
    const issues = [];
    if (troubleAsleep) issues.push('Trouble falling asleep');
    if (wakeUpDuringNight) issues.push('Woke up during night');
    if (!tiredRested) issues.push('Not well rested');
    
    if (issues.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Sleep Issues:</h4>';
      issues.forEach(issue => {
        content += `<div class="text-sm text-red-700">• ${issue}</div>`;
      });
      content += '</div>';
    } else {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<p class="text-green-700 text-center">No sleep issues reported</p>';
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateExerciseModal(pillar) {
    const hasExercise = pillar?.any ?? false;
    const levels = pillar?.levels || [];
    const impacts = pillar?.impacts || [];
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Exercise Activity</h3>';
    content += `<img src="/images/sport.png" alt="Exercise" class="w-16 h-16 object-contain mx-auto mb-2">`;
    
    if (hasExercise) {
      content += '<p class="text-green-600 font-medium">Exercise Completed</p>';
    } else {
      content += '<p class="text-gray-600 font-medium">No Exercise</p>';
    }
    
    content += '</div>';
    
    if (hasExercise) {
      // Duration
      if (levels.length > 0) {
        content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
        content += '<h4 class="font-medium text-green-800 mb-2">Duration:</h4>';
        
        const durationLabels = {
          'less_thirty': 'Less than 30 minutes',
          'thirty_to_sixty': '30-60 minutes',
          'greater_sixty': 'More than 60 minutes'
        };
        
        levels.forEach(level => {
          const label = durationLabels[level] || level;
          content += `<div class="text-sm text-green-700">• ${label}</div>`;
        });
        
        content += '</div>';
      }
      
      // Impact type
      if (impacts.length > 0) {
        content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
        content += '<h4 class="font-medium text-blue-800 mb-2">Exercise Type:</h4>';
        
        const impactLabels = {
          'high_impact': 'High Impact',
          'low_impact': 'Low Impact',
          'precision_exercise': 'Precision Exercise'
        };
        
        impacts.forEach(impact => {
          const label = impactLabels[impact] || impact;
          content += `<div class="text-sm text-blue-700">• ${label}</div>`;
        });
        
        content += '</div>';
      }
    }
    
    content += '</div>';
    
    return content;
  }

  static generateSexModal(pillar) {
    const today = pillar?.today ?? false;
    const avoided = pillar?.avoided ?? false;
    const satisfied = pillar?.satisfied ?? false;
    const issues = pillar?.issues || [];
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Sexual Health</h3>';
    content += `<img src="/images/sex.png" alt="Sexual Health" class="w-16 h-16 object-contain mx-auto mb-2">`;
    
    if (today) {
      if (satisfied) {
        content += '<p class="text-green-600 font-medium">Satisfying Experience</p>';
      } else {
        content += '<p class="text-yellow-600 font-medium">Activity Recorded</p>';
      }
    } else if (avoided) {
      content += '<p class="text-orange-600 font-medium">Activity Avoided</p>';
    } else {
      content += '<p class="text-gray-600 font-medium">No Activity</p>';
    }
    
    content += '</div>';
    
    if (today) {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">Activity Details:</h4>';
      content += '<div class="text-sm text-green-700">Sexual activity recorded for today</div>';
      if (satisfied) {
        content += '<div class="text-sm text-green-700">• Emotionally/physically satisfied</div>';
      }
      content += '</div>';
    }
    
    if (avoided) {
      content += '<div class="bg-orange-50 border border-orange-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-orange-800 mb-2">Avoidance:</h4>';
      content += '<div class="text-sm text-orange-700">Sexual activity was avoided today</div>';
      content += '</div>';
    }
    
    if (issues.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Issues Reported:</h4>';
      issues.forEach(issue => {
        content += `<div class="text-sm text-red-700">• ${issue}</div>`;
      });
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }

  static generateNotesModal(pillar) {
    const text = pillar?.text || '';
    const hasNote = pillar?.hasNote ?? false;
    
    let content = '<div class="space-y-4">';
    
    content += '<div class="text-center mb-6">';
    content += '<h3 class="text-lg font-semibold mb-3">Personal Notes</h3>';
    content += `<img src="/images/grid_notes.png" alt="Notes" class="w-16 h-16 object-contain mx-auto mb-2">`;
    content += '</div>';
    
    if (hasNote && text) {
      content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-blue-800 mb-2">Your Note:</h4>';
      content += `<p class="text-blue-700 whitespace-pre-wrap">${text}</p>`;
      content += '</div>';
    } else {
      content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
      content += '<p class="text-gray-600 text-center">No notes recorded for this day</p>';
      content += '</div>';
    }
    
    content += '</div>';
    
    return content;
  }
}
