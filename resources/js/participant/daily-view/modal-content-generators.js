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
    
    // Detailed information section
    content += '<div class="space-y-4">';
    
    if (spotting) {
      content += '<div class="bg-orange-50 border border-orange-200 rounded-lg p-4">';
      content += '<div class="flex items-center gap-2 mb-2">';
      content += '<img src="/images/spotting.png" alt="Spotting" class="w-6 h-6 object-contain">';
      content += '<span class="font-medium text-orange-800">Spotting Detected</span>';
      content += '</div>';
      content += '<p class="text-sm text-orange-700">Light bleeding between periods or at unexpected times. This may be normal but worth tracking for patterns.</p>';
      content += '</div>';
    } else if (severity && severity !== 'none') {
      content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-blue-800 mb-2">Blood Loss Details</h4>';
      
      const severityDescriptions = {
        'very_light': 'Very light flow - minimal bleeding, may only need panty liners',
        'light': 'Light flow - comfortable with regular pads or tampons',
        'moderate': 'Moderate flow - normal menstrual bleeding, may need to change protection every 3-4 hours',
        'heavy': 'Heavy flow - requires frequent changes of protection, may impact daily activities',
        'very_heavy': 'Very heavy flow - may require super absorbent products, could significantly impact daily life'
      };
      
      content += `<p class="text-sm text-blue-700 mb-2"><strong>Severity:</strong> ${severity.charAt(0).toUpperCase() + severity.slice(1).replace('_', ' ')}</p>`;
      content += `<p class="text-sm text-blue-700">${severityDescriptions[severity] || 'Blood loss recorded'}</p>`;
      content += '</div>';
    }
    
    // Amount and tracking info
    content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-gray-800 mb-2">Tracking Information</h4>';
    content += `<p class="text-sm text-gray-700 mb-1"><strong>Amount recorded:</strong> ${amount} ml</p>`;
    content += '<p class="text-xs text-gray-600">Tracking blood loss helps identify patterns and assess overall menstrual health. Share this data with your healthcare provider if you notice significant changes.</p>';
    content += '</div>';
    
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
    
    // Detailed pain information
    content += '<div class="space-y-4">';
    
    // Pain intensity description
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Pain Assessment</h4>';
    
    const painDescriptions = {
      0: 'No pain - feeling comfortable',
      1: 'Minimal pain - barely noticeable, does not interfere with activities',
      2: 'Mild pain - noticeable but tolerable, minimal interference with activities',
      3: 'Mild to moderate pain - some interference with activities',
      4: 'Moderate pain - interferes with concentration and activities',
      5: 'Moderate to severe pain - significantly interferes with activities',
      6: 'Severe pain - difficult to ignore, limits activities',
      7: 'Very severe pain - dominates senses, difficult to think clearly',
      8: 'Intense pain - physical activity severely limited',
      9: 'Excruciating pain - unable to engage in normal activities',
      10: 'Unbearable pain - bedridden, may require emergency care'
    };
    
    const painLevelText = value <= 1 ? 'Minimal' : value <= 3 ? 'Mild' : value <= 5 ? 'Moderate' : value <= 7 ? 'Severe' : 'Very Severe';
    
    content += `<p class="text-sm text-blue-700 mb-2"><strong>Pain Level:</strong> ${value}/10 (${painLevelText})</p>`;
    content += `<p class="text-sm text-blue-700">${painDescriptions[value] || painDescriptions[Math.min(10, Math.max(0, Math.round(value)))]}</p>`;
    content += '</div>';
    
    // Affected regions
    if (regions.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Affected Body Regions</h4>';
      content += '<div class="grid grid-cols-2 gap-2 mb-3">';
      
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
      content += '<p class="text-xs text-red-600">Pain in multiple regions may indicate referred pain or systemic conditions. Consider discussing patterns with your healthcare provider.</p>';
      content += '</div>';
    }
    
    // Management suggestions
    content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-gray-800 mb-2">Pain Management Notes</h4>';
    if (value >= 7) {
      content += '<p class="text-sm text-gray-700 mb-2"><strong>Severe Pain Alert:</strong> Consider contacting your healthcare provider if this level persists.</p>';
    } else if (value >= 4) {
      content += '<p class="text-sm text-gray-700 mb-2"><strong>Moderate Pain:</strong> May benefit from pain management strategies or medication.</p>';
    } else if (value >= 1) {
      content += '<p class="text-sm text-gray-700 mb-2"><strong>Mild Pain:</strong> Monitor for patterns and triggers.</p>';
    }
    content += '<p class="text-xs text-gray-600">Track pain patterns over time to identify triggers, effective treatments, and changes that may need medical attention.</p>';
    content += '</div>';
    
    content += '</div>';
    
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
    
    // Detailed mood analysis
    content += '<div class="space-y-4">';
    
    // Positive mood states
    if (positives.length > 0) {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">Positive Emotions Experienced</h4>';
      
      const positiveMoodDescriptions = {
        'calm': 'Feeling peaceful and relaxed, free from stress or anxiety',
        'happy': 'Experiencing joy, contentment, and positive emotions',
        'excited': 'Feeling energetic, enthusiastic, and looking forward to things'
      };
      
      positives.forEach(mood => {
        const description = positiveMoodDescriptions[mood];
        content += `<div class="mb-2">`;
        content += `<p class="text-sm text-green-700 font-medium">${mood.charAt(0).toUpperCase() + mood.slice(1)}</p>`;
        if (description) {
          content += `<p class="text-xs text-green-600">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-green-600 mt-2">Positive emotions contribute to overall wellbeing and can help manage challenging symptoms.</p>';
      content += '</div>';
    }
    
    // Negative mood states
    if (negatives.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-2">Challenging Emotions Experienced</h4>';
      
      const negativeMoodDescriptions = {
        'anxious': 'Feeling worried, nervous, or uneasy about uncertain outcomes',
        'stressed': 'Experiencing pressure, tension, or feeling overwhelmed',
        'ashamed': 'Feeling embarrassed, guilty, or disappointed in oneself',
        'angry': 'Feeling frustrated, irritated, or upset about situations',
        'irritable': 'Easily annoyed or made angry by minor things',
        'sad': 'Feeling down, unhappy, or experiencing low mood',
        'mood_swings': 'Experiencing rapid changes between different emotional states',
        'worthless': 'Feeling like you have no value or importance',
        'guilty': 'Feeling responsible for something wrong or feeling regret',
        'overwhelmed': 'Feeling like there is too much to handle or cope with',
        'hopeless': 'Feeling like things will not improve or get better',
        'depressed': 'Experiencing persistent low mood, sadness, or lack of interest'
      };
      
      negatives.forEach(mood => {
        const description = negativeMoodDescriptions[mood];
        content += `<div class="mb-2">`;
        content += `<p class="text-sm text-red-700 font-medium">${mood.charAt(0).toUpperCase() + mood.slice(1).replace('_', ' ')}</p>`;
        if (description) {
          content += `<p class="text-xs text-red-600">${description}</p>`;
        }
        content += `</div>`;
      });
      
      if (negatives.some(mood => ['depressed', 'hopeless', 'worthless'].includes(mood))) {
        content += '<p class="text-xs text-red-700 font-medium mt-3">⚠️ If these feelings persist, consider reaching out to a mental health professional or your healthcare provider.</p>';
      }
      
      content += '<p class="text-xs text-red-600 mt-2">Tracking challenging emotions helps identify patterns and triggers that may be related to your health condition.</p>';
      content += '</div>';
    }
    
    // Mood tracking insights
    content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-gray-800 mb-2">Mood Tracking Insights</h4>';
    content += `<p class="text-sm text-gray-700 mb-2"><strong>Today's Balance:</strong> ${positives.length} positive, ${negatives.length} challenging emotions</p>`;
    
    if (balance > 1) {
      content += '<p class="text-sm text-gray-700">Strong positive day! This emotional state may help with symptom management and overall wellbeing.</p>';
    } else if (balance === 1) {
      content += '<p class="text-sm text-gray-700">Mostly positive day with some challenges. This is a normal emotional balance.</p>';
    } else if (balance === 0) {
      content += '<p class="text-sm text-gray-700">Balanced emotional day. You experienced both positive and challenging emotions.</p>';
    } else if (balance >= -1) {
      content += '<p class="text-sm text-gray-700">Somewhat challenging day emotionally. Consider self-care strategies that have helped before.</p>';
    } else {
      content += '<p class="text-sm text-gray-700">Emotionally challenging day. Remember that difficult emotions are temporary and support is available.</p>';
    }
    
    content += '<p class="text-xs text-gray-600 mt-2">Mood patterns often correlate with physical symptoms. Share this information with your healthcare team to get comprehensive care.</p>';
    content += '</div>';
    
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
    content += `<p class="text-gray-600">Total items consumed: ${allDietItems.length}/12</p>`;
    content += '</div>';
    
    // Detailed diet analysis
    content += '<div class="space-y-4">';
    
    const dietLabels = {
      'vegetables': 'Vegetables', 'fruit': 'Fruit', 'potato_rice_bread': 'Carbohydrates',
      'dairy_products': 'Dairy Products', 'nuts_tofu_tempe': 'Protein Alternatives', 'egg': 'Eggs',
      'fish': 'Fish', 'meat': 'Meat', 'snacks': 'Snacks', 'soda': 'Soda',
      'water': 'Water', 'coffee': 'Coffee', 'alcohol': 'Alcohol'
    };
    
    const dietDescriptions = {
      'vegetables': 'Rich in vitamins, minerals, and fiber. Essential for digestive health and inflammation reduction.',
      'fruit': 'Natural source of vitamins, antioxidants, and fiber. Supports immune system and provides natural energy.',
      'potato_rice_bread': 'Complex carbohydrates provide sustained energy. Choose whole grains when possible.',
      'dairy_products': 'Good source of calcium and protein. May affect inflammation in some individuals.',
      'nuts_tofu_tempe': 'Plant-based proteins with healthy fats. Anti-inflammatory properties may help with symptoms.',
      'egg': 'Complete protein source with essential amino acids. Generally well-tolerated by most people.',
      'fish': 'Omega-3 fatty acids have anti-inflammatory properties. Excellent for overall health.',
      'meat': 'High-quality protein source. Red meat may increase inflammation in some people.',
      'snacks': 'Processed foods may contribute to inflammation. Choose healthier alternatives when possible.',
      'water': 'Essential for hydration and overall health. Helps with symptom management and medication effectiveness.',
      'coffee': 'Contains antioxidants but caffeine may affect some symptoms. Monitor individual response.',
      'alcohol': 'May interfere with medications and worsen symptoms. Consider limiting intake.'
    };
    
    if (positives.length > 0) {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-3">Beneficial Food Choices</h4>';
      
      positives.forEach(item => {
        const label = dietLabels[item] || item;
        const description = dietDescriptions[item];
        content += `<div class="mb-3">`;
        content += `<p class="text-sm text-green-700 font-medium">• ${label}</p>`;
        if (description) {
          content += `<p class="text-xs text-green-600 ml-3">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-green-600 mt-2">These foods support overall health and may help manage symptoms through their nutritional benefits.</p>';
      content += '</div>';
    }
    
    if (negatives.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-3">Foods to Monitor</h4>';
      
      negatives.forEach(item => {
        const label = dietLabels[item] || item;
        const description = dietDescriptions[item];
        content += `<div class="mb-3">`;
        content += `<p class="text-sm text-red-700 font-medium">• ${label}</p>`;
        if (description) {
          content += `<p class="text-xs text-red-600 ml-3">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-red-600 mt-2">These items may contribute to inflammation or worsen symptoms in some individuals. Consider moderation or alternatives.</p>';
      content += '</div>';
    }
    
    if (neutrals.length > 0) {
      content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-gray-800 mb-3">Neutral Food Choices</h4>';
      
      neutrals.forEach(item => {
        const label = dietLabels[item] || item;
        const description = dietDescriptions[item];
        content += `<div class="mb-3">`;
        content += `<p class="text-sm text-gray-700 font-medium">• ${label}</p>`;
        if (description) {
          content += `<p class="text-xs text-gray-600 ml-3">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-gray-600 mt-2">These foods have neutral effects on your symptoms but still contribute to overall nutrition.</p>';
      content += '</div>';
    }
    
    // Diet summary and insights
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Nutritional Analysis</h4>';
    
    const totalItems = allDietItems.length;
    const healthyRatio = positives.length / totalItems;
    
    if (healthyRatio >= 0.7) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Excellent nutrition day!</strong> You made predominantly healthy food choices.</p>';
    } else if (healthyRatio >= 0.5) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Good nutrition balance.</strong> You had a mix of healthy and less optimal choices.</p>';
    } else if (healthyRatio >= 0.3) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Room for improvement.</strong> Consider increasing healthy food choices tomorrow.</p>';
    } else {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Focus on nutrition.</strong> Try to include more anti-inflammatory foods in your diet.</p>';
    }
    
    content += `<p class="text-sm text-blue-700 mb-2"><strong>Today's breakdown:</strong> ${positives.length} beneficial, ${negatives.length} to monitor, ${neutrals.length} neutral foods</p>`;
    content += '<p class="text-xs text-blue-600">Diet plays a crucial role in managing symptoms. Anti-inflammatory foods may help reduce symptom severity, while processed foods might worsen them.</p>';
    content += '</div>';
    
    content += '</div>';
    
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
      
      // Add detailed descriptions for each limitation
      content += '<div class="mt-4 space-y-2">';
      content += '<h5 class="font-medium text-red-800 text-sm mb-2">Impact Details:</h5>';
      
      const limitationDescriptions = {
        'used_medication': 'Needed medication to manage symptoms - indicates active symptom management',
        'missed_work': 'Unable to attend work due to symptoms - significant impact on professional life',
        'missed_school': 'Could not attend educational activities - academic impact from symptoms',
        'could_not_sport': 'Physical activities were limited - symptoms affected exercise capacity',
        'missed_social_activities': 'Social interactions were avoided - symptoms impacted social wellbeing',
        'missed_leisure_activities': 'Recreational activities were skipped - reduced quality of life',
        'had_to_sit_more': 'Required more sitting/resting - symptoms affected mobility and energy',
        'had_to_lie_down': 'Needed to lie down more than usual - significant symptom burden',
        'had_to_stay_longer_in_bed': 'Extended bed rest required - severe symptom impact',
        'could_not_do_unpaid_work': 'Household/volunteer work affected - daily functioning impaired',
        'other': 'Additional limitations not captured above - unique symptom impacts'
      };
      
      limitations.forEach(limitation => {
        const description = limitationDescriptions[limitation];
        if (description) {
          content += `<p class="text-xs text-red-600">• ${description}</p>`;
        }
      });
      
      content += '<p class="text-xs text-red-600 mt-3 font-medium">Multiple limitations suggest significant symptom burden. Consider discussing management strategies with your healthcare provider.</p>';
      content += '</div>';
      content += '</div>';
    } else {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">No Limitations Reported</h4>';
      content += '<p class="text-sm text-green-700">Great! Your symptoms did not significantly interfere with your daily activities today.</p>';
      content += '<p class="text-xs text-green-600 mt-2">Days without limitations are important markers of successful symptom management.</p>';
      content += '</div>';
    }
    
    // Daily grade analysis
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Daily Assessment Analysis</h4>';
    
    const gradeDescriptions = {
      10: 'Perfect day - no symptoms interfered with daily activities',
      9: 'Excellent day - minimal symptom impact, felt very good',
      8: 'Very good day - slight symptoms but managed well',
      7: 'Good day - some symptoms but able to function normally',
      6: 'Decent day - moderate symptoms with some limitations',
      5: 'Average day - symptoms present but manageable',
      4: 'Challenging day - symptoms significantly impacted activities',
      3: 'Difficult day - symptoms made most activities challenging',
      2: 'Very difficult day - symptoms severely limited activities',
      1: 'Extremely difficult day - symptoms dominated the day',
      0: 'No assessment provided'
    };
    
    content += `<p class="text-sm text-blue-700 mb-2"><strong>Your Rating:</strong> ${grade}/10 (${gradeText})</p>`;
    content += `<p class="text-sm text-blue-700 mb-3">${gradeDescriptions[grade] || gradeDescriptions[Math.min(10, Math.max(0, Math.round(grade)))]}</p>`;
    
    // Impact level assessment
    const impactLevel = limitations.length;
    if (impactLevel === 0) {
      content += '<p class="text-sm text-blue-700"><strong>Impact Level:</strong> Minimal - Symptoms had little to no effect on daily functioning.</p>';
    } else if (impactLevel <= 2) {
      content += '<p class="text-sm text-blue-700"><strong>Impact Level:</strong> Mild - Some limitations but overall functioning maintained.</p>';
    } else if (impactLevel <= 4) {
      content += '<p class="text-sm text-blue-700"><strong>Impact Level:</strong> Moderate - Several areas of life affected by symptoms.</p>';
    } else if (impactLevel <= 6) {
      content += '<p class="text-sm text-blue-700"><strong>Impact Level:</strong> Significant - Multiple daily activities were limited.</p>';
    } else {
      content += '<p class="text-sm text-blue-700"><strong>Impact Level:</strong> Severe - Symptoms extensively affected daily functioning.</p>';
    }
    
    content += '</div>';
    
    // Tracking insights
    content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-gray-800 mb-2">Impact Tracking Insights</h4>';
    content += `<p class="text-sm text-gray-700 mb-2"><strong>Limitations count:</strong> ${impactLevel} out of 11 possible areas affected</p>`;
    content += '<p class="text-xs text-gray-600">Tracking daily impact helps identify patterns and assess treatment effectiveness. Share this data with your healthcare team for comprehensive care planning.</p>';
    content += '</div>';
    
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
    
    // Detailed energy analysis
    content += '<div class="space-y-4">';
    
    // Energy level description
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Energy Assessment</h4>';
    
    const energyDescriptions = {
      1: 'Very Low Energy - Feeling extremely tired, may need to rest frequently and avoid strenuous activities',
      2: 'Low Energy - Below normal energy levels, some activities may feel challenging or require more effort',
      3: 'Moderate Energy - Average energy levels, able to perform most daily activities with some limitations',
      4: 'Good Energy - Above average energy, feeling capable and able to engage in most activities comfortably',
      5: 'High Energy - Excellent energy levels, feeling vibrant and able to tackle demanding activities'
    };
    
    content += `<p class="text-sm text-blue-700 mb-2"><strong>Current Level:</strong> ${energyLabels[energy]} (${energy}/5)</p>`;
    content += `<p class="text-sm text-blue-700">${energyDescriptions[energy] || 'Energy level assessment not available'}</p>`;
    content += '</div>';
    
    // Symptoms analysis
    if (symptoms.length > 0) {
      content += '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-red-800 mb-3">Symptoms Experienced</h4>';
      
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
      
      const symptomDescriptions = {
        'fatigue': 'Persistent tiredness that doesn\'t improve with rest, often affecting daily functioning',
        'headache': 'Head pain that may range from mild discomfort to severe, debilitating pain',
        'nausea': 'Feeling of sickness with an urge to vomit, may affect appetite and daily activities',
        'nauseous': 'Feeling queasy or sick to the stomach, potentially affecting food intake',
        'dizziness': 'Feeling unsteady, lightheaded, or having a spinning sensation',
        'dizzy': 'Sensation of unsteadiness or feeling faint, may affect balance and coordination',
        'weakness': 'Reduced physical strength or energy, making normal activities more difficult',
        'joint_pain': 'Discomfort in joints that may limit movement and affect daily activities',
        'muscle_pain': 'Aching or soreness in muscles, potentially affecting movement and comfort',
        'fever': 'Elevated body temperature indicating possible infection or inflammation',
        'chills': 'Feeling cold and shivering, often accompanying fever or illness',
        'sweating': 'Excessive perspiration that may be related to hormonal changes or fever',
        'bloated': 'Feeling of fullness or swelling in the abdomen, may affect comfort and appetite',
        'painful_sensitive_breasts': 'Breast tenderness that may be hormonal or condition-related',
        'acne': 'Skin breakouts that may be related to hormonal changes or medications',
        'muscle_joint_pain': 'Combined muscle and joint discomfort affecting mobility and comfort',
        'headache_migraine': 'Severe headache that may include sensitivity to light, sound, or nausea'
      };
      
      symptoms.forEach(symptom => {
        const label = symptomLabels[symptom] || symptom.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const description = symptomDescriptions[symptom];
        
        content += `<div class="mb-3">`;
        content += `<p class="text-sm text-red-700 font-medium">• ${label}</p>`;
        if (description) {
          content += `<p class="text-xs text-red-600 ml-3">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-red-600 mt-3">Multiple symptoms may indicate increased disease activity or treatment side effects. Consider discussing symptom patterns with your healthcare provider.</p>';
      content += '</div>';
    } else {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">No Symptoms Today</h4>';
      content += '<p class="text-sm text-green-700">Excellent! No additional symptoms reported alongside your energy level.</p>';
      content += '<p class="text-xs text-green-600 mt-2">Symptom-free days are important markers of good health management and treatment effectiveness.</p>';
      content += '</div>';
    }
    
    // Energy-symptom correlation
    content += '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-yellow-800 mb-2">Energy & Symptom Correlation</h4>';
    
    if (energy <= 2 && symptoms.length > 0) {
      content += '<p class="text-sm text-yellow-700 mb-2"><strong>Low Energy with Symptoms:</strong> Your low energy level combined with symptoms suggests significant health impact today.</p>';
    } else if (energy >= 4 && symptoms.length > 0) {
      content += '<p class="text-sm text-yellow-700 mb-2"><strong>Good Energy Despite Symptoms:</strong> Maintaining good energy while experiencing symptoms shows resilience and effective management.</p>';
    } else if (energy >= 4 && symptoms.length === 0) {
      content += '<p class="text-sm text-yellow-700 mb-2"><strong>Optimal Day:</strong> High energy with no symptoms indicates excellent health status today.</p>';
    } else if (energy <= 2 && symptoms.length === 0) {
      content += '<p class="text-sm text-yellow-700 mb-2"><strong>Low Energy Only:</strong> Low energy without other symptoms may indicate fatigue, sleep issues, or need for rest.</p>';
    } else {
      content += '<p class="text-sm text-yellow-700 mb-2"><strong>Moderate Status:</strong> Your energy and symptom levels suggest a manageable health day.</p>';
    }
    
    content += '<p class="text-xs text-yellow-600">Energy levels often correlate with symptom burden. Tracking both helps identify patterns and treatment effectiveness.</p>';
    content += '</div>';
    
    // Health insights
    content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-gray-800 mb-2">General Health Insights</h4>';
    content += `<p class="text-sm text-gray-700 mb-2"><strong>Today\'s Summary:</strong> ${energyLabels[energy]} energy with ${symptoms.length} symptom(s) reported</p>`;
    
    if (energy >= 4) {
      content += '<p class="text-xs text-gray-600">Good energy levels support better symptom management, improved mood, and enhanced quality of life. Maintain healthy habits that contribute to sustained energy.</p>';
    } else if (energy <= 2) {
      content += '<p class="text-xs text-gray-600">Low energy can significantly impact daily functioning and symptom management. Consider discussing energy-boosting strategies with your healthcare provider.</p>';
    } else {
      content += '<p class="text-xs text-gray-600">Moderate energy levels are manageable but may benefit from lifestyle adjustments or treatment optimization to improve overall wellbeing.</p>';
    }
    
    content += '</div>';
    
    content += '</div>';
    
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
      content += '<h4 class="font-medium text-red-800 mb-2">Blood Detection Alert</h4>';
      if (hasUrineBlood) {
        content += '<div class="mb-2">';
        content += '<p class="text-sm text-red-700 font-medium">• Blood in urine</p>';
        content += '<p class="text-xs text-red-600 ml-3">Blood in urine may indicate urinary tract issues, kidney problems, or other medical conditions requiring attention.</p>';
        content += '</div>';
      }
      if (hasStoolBlood) {
        content += '<div class="mb-2">';
        content += '<p class="text-sm text-red-700 font-medium">• Blood in stool</p>';
        content += '<p class="text-xs text-red-600 ml-3">Blood in stool can indicate digestive tract issues, hemorrhoids, or other gastrointestinal conditions.</p>';
        content += '</div>';
      }
      content += '<p class="text-xs text-red-700 font-medium mt-3">⚠️ Blood detection warrants medical evaluation. Contact your healthcare provider to discuss these findings.</p>';
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
      
      const consistencyDescriptions = {
        'hard': 'Hard, difficult to pass stools may indicate constipation or dehydration. Consider increasing fiber and water intake.',
        'normal': 'Normal, well-formed stools indicate healthy digestive function and good hydration.',
        'soft': 'Soft stools are generally normal but may indicate dietary changes or mild digestive sensitivity.',
        'watery': 'Watery stools suggest diarrhea, which may be caused by infection, medication, or dietary factors.',
        'something_else': 'Unusual stool characteristics may warrant further observation or medical consultation.',
        'no_stool': 'No bowel movement today. Monitor for patterns of constipation or changes in bowel habits.'
      };
      
      const label = consistencyLabels[consistency] || consistency;
      const description = consistencyDescriptions[consistency];
      
      content += `<p class="text-sm text-blue-700 font-medium mb-2">${label} Stool</p>`;
      if (description) {
        content += `<p class="text-xs text-blue-600">${description}</p>`;
      }
      content += '</div>';
    }
    
    // Health insights
    content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-gray-800 mb-2">Digestive Health Tracking</h4>';
    content += '<p class="text-sm text-gray-700 mb-2">Monitoring bowel movements helps identify patterns related to your condition, medications, and dietary choices.</p>';
    
    if (hasUrineBlood || hasStoolBlood) {
      content += '<p class="text-xs text-gray-600"><strong>Important:</strong> Blood detection requires medical attention for proper evaluation and treatment.</p>';
    } else if (consistency === 'normal') {
      content += '<p class="text-xs text-gray-600"><strong>Good news:</strong> Normal stool consistency indicates healthy digestive function.</p>';
    } else if (consistency === 'hard' || consistency === 'no_stool') {
      content += '<p class="text-xs text-gray-600"><strong>Tip:</strong> Constipation may be managed with increased fiber, water, and physical activity.</p>';
    } else if (consistency === 'watery') {
      content += '<p class="text-xs text-gray-600"><strong>Monitor:</strong> Persistent diarrhea may require dietary adjustments or medical evaluation.</p>';
    }
    
    content += '</div>';
    
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
      content += '<h4 class="font-medium text-red-800 mb-3">Sleep Challenges</h4>';
      
      const issueDescriptions = {
        'Trouble falling asleep': 'Difficulty initiating sleep may be caused by stress, pain, caffeine, or irregular sleep schedules.',
        'Woke up during night': 'Sleep fragmentation can reduce sleep quality and may be related to pain, anxiety, or environmental factors.',
        'Not well rested': 'Feeling unrefreshed despite adequate sleep time may indicate poor sleep quality or underlying sleep disorders.'
      };
      
      issues.forEach(issue => {
        content += `<div class="mb-2">`;
        content += `<p class="text-sm text-red-700 font-medium">• ${issue}</p>`;
        const description = issueDescriptions[issue];
        if (description) {
          content += `<p class="text-xs text-red-600 ml-3">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-red-600 mt-3">Poor sleep can worsen symptoms and affect overall health. Consider discussing sleep hygiene strategies with your healthcare provider.</p>';
      content += '</div>';
    } else {
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">Good Sleep Quality</h4>';
      content += '<p class="text-sm text-green-700">No sleep issues reported - excellent for symptom management and overall health.</p>';
      content += '<p class="text-xs text-green-600 mt-2">Quality sleep supports immune function, pain management, and emotional wellbeing.</p>';
      content += '</div>';
    }
    
    // Sleep analysis
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Sleep Analysis</h4>';
    
    if (hours >= 7 && hours <= 9) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Optimal Duration:</strong> Your sleep duration falls within the recommended 7-9 hours for adults.</p>';
    } else if (hours < 6) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Short Sleep:</strong> Less than 6 hours may negatively impact symptom management and recovery.</p>';
    } else if (hours > 10) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Extended Sleep:</strong> More than 10 hours might indicate increased symptom burden or recovery needs.</p>';
    } else {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Borderline Duration:</strong> Consider aiming for 7-9 hours for optimal health benefits.</p>';
    }
    
    content += `<p class="text-sm text-blue-700 mb-2"><strong>Sleep Quality Score:</strong> ${quality} based on duration and reported issues</p>`;
    content += '<p class="text-xs text-blue-600">Sleep patterns can significantly impact symptom severity, mood, and treatment effectiveness. Consistent, quality sleep is crucial for managing chronic conditions.</p>';
    content += '</div>';
    
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
        
        const impactDescriptions = {
          'high_impact': 'High-impact exercises like running or jumping. Great for cardiovascular health and bone density, but may increase joint stress.',
          'low_impact': 'Low-impact activities like walking or swimming. Gentle on joints while providing cardiovascular and strength benefits.',
          'precision_exercise': 'Precision exercises requiring focus and control. Excellent for coordination, balance, and targeted muscle strengthening.'
        };
        
        impacts.forEach(impact => {
          const label = impactLabels[impact] || impact;
          const description = impactDescriptions[impact];
          content += `<div class="mb-2">`;
          content += `<p class="text-sm text-blue-700 font-medium">• ${label}</p>`;
          if (description) {
            content += `<p class="text-xs text-blue-600 ml-3">${description}</p>`;
          }
          content += `</div>`;
        });
        
        content += '</div>';
      }
      
      // Exercise benefits
      content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-green-800 mb-2">Exercise Benefits</h4>';
      
      const totalDuration = levels.length;
      if (levels.includes('greater_sixty')) {
        content += '<p class="text-sm text-green-700 mb-2"><strong>Extended Activity:</strong> Over 60 minutes of exercise provides excellent cardiovascular and mental health benefits.</p>';
      } else if (levels.includes('thirty_to_sixty')) {
        content += '<p class="text-sm text-green-700 mb-2"><strong>Moderate Activity:</strong> 30-60 minutes meets recommended daily exercise guidelines for health benefits.</p>';
      } else if (levels.includes('less_thirty')) {
        content += '<p class="text-sm text-green-700 mb-2"><strong>Light Activity:</strong> Even short exercise sessions provide health benefits and can help manage symptoms.</p>';
      }
      
      content += '<p class="text-xs text-green-600">Regular exercise can help reduce inflammation, improve mood, enhance sleep quality, and may help manage chronic condition symptoms.</p>';
      content += '</div>';
    } else {
      content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-gray-800 mb-2">No Exercise Today</h4>';
      content += '<p class="text-sm text-gray-700 mb-2">Rest days are important for recovery, especially when managing symptoms.</p>';
      content += '<p class="text-xs text-gray-600">Consider gentle activities like stretching or short walks when feeling up to it. Always listen to your body and consult your healthcare provider about appropriate exercise levels.</p>';
      content += '</div>';
    }
    
    // Exercise tracking insights
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Activity Tracking Insights</h4>';
    
    if (hasExercise) {
      content += `<p class="text-sm text-blue-700 mb-2"><strong>Activity Summary:</strong> ${levels.length} duration level(s), ${impacts.length} exercise type(s)</p>`;
      content += '<p class="text-xs text-blue-600">Tracking exercise helps identify which activities you tolerate well and which might trigger symptoms. This information is valuable for creating a personalized exercise plan.</p>';
    } else {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Rest Day:</strong> No exercise recorded today</p>';
      content += '<p class="text-xs text-blue-600">Both active days and rest days are important data points for understanding your activity tolerance and symptom patterns.</p>';
    }
    
    content += '</div>';
    
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
      content += '<h4 class="font-medium text-red-800 mb-3">Health Concerns</h4>';
      
      const issueDescriptions = {
        'pain': 'Pain during sexual activity may indicate underlying conditions that warrant medical evaluation.',
        'discomfort': 'Discomfort can affect intimacy and may be related to your health condition or treatments.',
        'bleeding': 'Unusual bleeding during or after sexual activity should be discussed with your healthcare provider.',
        'dryness': 'Vaginal dryness can be caused by hormonal changes, medications, or medical conditions.',
        'fatigue': 'Fatigue affecting sexual activity may be related to your overall health condition.'
      };
      
      issues.forEach(issue => {
        const description = issueDescriptions[issue];
        content += `<div class="mb-2">`;
        content += `<p class="text-sm text-red-700 font-medium">• ${issue.charAt(0).toUpperCase() + issue.slice(1)}</p>`;
        if (description) {
          content += `<p class="text-xs text-red-600 ml-3">${description}</p>`;
        }
        content += `</div>`;
      });
      
      content += '<p class="text-xs text-red-600 mt-3">Sexual health concerns can significantly impact quality of life. Consider discussing these issues with your healthcare provider for appropriate support and treatment options.</p>';
      content += '</div>';
    }
    
    // Sexual health insights
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Sexual Health & Wellbeing</h4>';
    
    if (today && satisfied) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Positive Experience:</strong> Satisfying sexual activity contributes to overall wellbeing and relationship health.</p>';
    } else if (today && !satisfied) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Activity Noted:</strong> Sexual activity occurred but satisfaction may have been affected by symptoms or other factors.</p>';
    } else if (avoided) {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>Activity Avoided:</strong> Avoidance may be related to symptoms, pain, or other health concerns.</p>';
    } else {
      content += '<p class="text-sm text-blue-700 mb-2"><strong>No Activity:</strong> Sexual activity patterns can be affected by health conditions, treatments, and overall wellbeing.</p>';
    }
    
    content += '<p class="text-xs text-blue-600">Sexual health is an important component of overall health and quality of life. Tracking patterns helps identify how your condition affects intimacy and can guide discussions with healthcare providers.</p>';
    content += '</div>';
    
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
      content += '<h4 class="font-medium text-blue-800 mb-3">Your Personal Note</h4>';
      content += `<div class="bg-white rounded-lg p-3 border border-blue-200">`;
      content += `<p class="text-gray-800 whitespace-pre-wrap leading-relaxed">${text}</p>`;
      content += `</div>`;
      
      // Note analysis
      const wordCount = text.trim().split(/\s+/).length;
      const charCount = text.length;
      
      content += '<div class="mt-3 pt-3 border-t border-blue-200">';
      content += '<h5 class="font-medium text-blue-800 text-sm mb-2">Note Details:</h5>';
      content += `<p class="text-xs text-blue-600 mb-1"><strong>Length:</strong> ${wordCount} words, ${charCount} characters</p>`;
      
      // Analyze note content for key themes
      const lowerText = text.toLowerCase();
      const themes = [];
      
      if (lowerText.includes('pain') || lowerText.includes('hurt') || lowerText.includes('ache')) {
        themes.push('Pain mentioned');
      }
      if (lowerText.includes('tired') || lowerText.includes('fatigue') || lowerText.includes('exhausted')) {
        themes.push('Fatigue noted');
      }
      if (lowerText.includes('mood') || lowerText.includes('sad') || lowerText.includes('happy') || lowerText.includes('anxious')) {
        themes.push('Mood discussed');
      }
      if (lowerText.includes('medication') || lowerText.includes('medicine') || lowerText.includes('treatment')) {
        themes.push('Treatment mentioned');
      }
      if (lowerText.includes('sleep') || lowerText.includes('rest')) {
        themes.push('Sleep referenced');
      }
      
      if (themes.length > 0) {
        content += `<p class="text-xs text-blue-600"><strong>Key themes:</strong> ${themes.join(', ')}</p>`;
      }
      
      content += '</div>';
      content += '</div>';
    } else {
      content += '<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
      content += '<h4 class="font-medium text-gray-800 mb-2">No Notes Today</h4>';
      content += '<p class="text-sm text-gray-600 text-center mb-3">No personal notes were recorded for this day.</p>';
      content += '<div class="bg-white rounded-lg p-3 border border-gray-200">';
      content += '<p class="text-xs text-gray-500 italic">Personal notes can help you track patterns, record important observations, or note how treatments are working.</p>';
      content += '</div>';
      content += '</div>';
    }
    
    // Notes insights
    content += '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-green-800 mb-2">The Value of Personal Notes</h4>';
    
    if (hasNote && text) {
      content += '<p class="text-sm text-green-700 mb-2"><strong>Great job!</strong> Recording personal notes helps create a comprehensive health picture.</p>';
      content += '<p class="text-xs text-green-600">Your notes provide valuable context that numbers alone cannot capture. They help healthcare providers understand your daily experience and can reveal important patterns over time.</p>';
    } else {
      content += '<p class="text-sm text-green-700 mb-2"><strong>Consider adding notes:</strong> Personal observations can be incredibly valuable for health tracking.</p>';
      content += '<p class="text-xs text-green-600">Notes can include how you felt, what helped or worsened symptoms, medication effects, or any other observations. This qualitative data complements your quantitative health metrics.</p>';
    }
    
    content += '</div>';
    
    // Usage tips
    content += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
    content += '<h4 class="font-medium text-blue-800 mb-2">Note-Taking Tips</h4>';
    content += '<div class="space-y-1 text-xs text-blue-600">';
    content += '<p>• <strong>Be specific:</strong> Note times, triggers, and severity</p>';
    content += '<p>• <strong>Track treatments:</strong> Record medication effects and side effects</p>';
    content += '<p>• <strong>Note patterns:</strong> Weather, stress, diet, or activity correlations</p>';
    content += '<p>• <strong>Include emotions:</strong> How symptoms affect your mood and daily life</p>';
    content += '<p>• <strong>Be honest:</strong> Accurate notes lead to better care</p>';
    content += '</div>';
    content += '</div>';
    
    content += '</div>';
    
    return content;
  }
}
