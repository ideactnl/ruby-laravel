window.dailyView = function dailyView() {
  return {
    date: new Date().toISOString().slice(0,10),
    data: null,
    loading: false,
    items: [],
    videos: [],
    _symSwiper: null,
    _vidSwiper: null,
    showModal: false,
    modalData: null,
    modalContent: '',
    get heading(){
      try{
        const d = new Date(this.date);
        return d.toLocaleDateString(undefined,{ day:'2-digit', month:'long', year:'numeric', weekday:'long' });
      }catch(e){ return this.date; }
    },
    openDate(){
      if (this._fp && typeof this._fp.open === 'function'){
        try { this._fp.setDate(this.date, false); this._fp.open(); return; } catch(_){}
      }
      const el = this.$refs?.datePick;
      try{
        if (el && typeof el.showPicker === 'function'){
          el.showPicker();
        } else if (el) {
          el.click();
        }
      }catch(e){ if (el) el.click(); }
    },
    prevDay(){
      try{
        const d = new Date(this.date);
        d.setDate(d.getDate() - 1);
        this.date = d.toISOString().slice(0,10);
        this.fetchData();
      }catch(e){}
    },
    nextDay(){
      try{
        const d = new Date(this.date);
        d.setDate(d.getDate() + 1);
        this.date = d.toISOString().slice(0,10);
        this.fetchData();
      }catch(e){}
    },
    get shortDate(){
      try{
        const d = new Date(this.date);
        const dd = String(d.getDate()).padStart(2,'0');
        const mm = String(d.getMonth()+1).padStart(2,'0');
        const yyyy = d.getFullYear();
        return `${dd}/${mm}/${yyyy}`;
      }catch(e){ return this.date; }
    },
    init(){
      const urlParams = new URLSearchParams(window.location.search);
      const d = urlParams.get('date');
      if (d) this.date = d;
      try {
        if (window.flatpickr && this.$refs?.datePick){
          this._fp = window.flatpickr(this.$refs.datePick, {
            dateFormat: 'Y-m-d',
            defaultDate: this.date,
            allowInput: false,
            clickOpens: false,
            wrap: false,
            onChange: (sel) => {
              if (sel && sel[0]){
                const iso = sel[0].toISOString().slice(0,10);
                if (iso !== this.date){ this.date = iso; this.fetchData(); }
              }
            },
          });
        }
      } catch(_) {}
      this.fetchData();
    },

    getBloodLossTooltip(pillar) {
      if (!pillar || !pillar.amount) return 'No blood loss recorded';
      const amount = pillar.amount;
      const severity = pillar.severity || 'light';
      const spotting = pillar.flags?.spotting;
      
      if (spotting) return `Amount: ${amount} (spotting detected)`;
      return `Amount: ${amount} | Severity: ${severity}`;
    },

    getPainTooltip(pillar) {
      if (!pillar || !pillar.value) return 'No pain recorded';
      const value = pillar.value;
      const regions = pillar.regions || [];
      
      if (regions.length > 0) {
        // Convert raw region names to friendly text
        const regionLabels = {
          'umbilical': 'Umbilical', 'left_umbilical': 'Left Umbilical', 'right_umbilical': 'Right Umbilical',
          'bladder': 'Bladder', 'left_groin': 'Left Groin', 'right_groin': 'Right Groin',
          'left_leg': 'Left Leg', 'right_leg': 'Right Leg', 'upper_back': 'Upper Back',
          'back': 'Back', 'left_buttock': 'Left Buttock', 'right_buttock': 'Right Buttock',
          'left_back_leg': 'Left Back Leg', 'right_back_leg': 'Right Back Leg'
        };
        const friendlyRegions = regions.map(region => regionLabels[region] || region);
        return `Pain Level: ${value}/10 | Regions: ${friendlyRegions.join(', ')}`;
      }
      return `Pain Level: ${value}/10`;
    },

    getImpactTooltip(pillar) {
      if (!pillar || !pillar.gradeYourDay) return 'No impact recorded';
      const grade = pillar.gradeYourDay;
      const limitations = pillar.limitations || [];
      
      if (limitations.length > 0) {
        // Convert raw limitation names to friendly text
        const limitationLabels = {
          'used_medication': 'Used Medication', 'missed_work': 'Missed Work', 'missed_school': 'Missed School',
          'could_not_sport': 'Could Not Sport', 'missed_social_activities': 'Missed Social Activities',
          'missed_leisure_activities': 'Missed Leisure Activities', 'had_to_sit_more': 'Had to Sit More',
          'had_to_lie_down': 'Had to Lie Down', 'had_to_stay_longer_in_bed': 'Had to Stay Longer in Bed',
          'could_not_do_unpaid_work': 'Could Not Do Unpaid Work', 'other': 'Other Impact'
        };
        const friendlyLimitations = limitations.map(limitation => limitationLabels[limitation] || limitation);
        return `Grade: ${grade}/10 | Limitations: ${friendlyLimitations.join(', ')}`;
      }
      return `Grade Your Day: ${grade}/10`;
    },

    getEnergyTooltip(pillar) {
      if (!pillar || !pillar.energyLevel) return 'No energy level recorded';
      const energy = pillar.energyLevel;
      const symptoms = pillar.symptoms || [];
      
      if (symptoms.length > 0) {
        // Convert raw symptom names to friendly text
        const symptomLabels = {
          'dizzy': 'Dizzy', 'nauseous': 'Nauseous', 'headache_migraine': 'Headache/Migraine',
          'bloated': 'Bloated', 'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
          'acne': 'Acne', 'muscle_joint_pain': 'Muscle/Joint Pain'
        };
        const friendlySymptoms = symptoms.map(symptom => symptomLabels[symptom] || symptom);
        return `Energy: ${energy}/5 | Symptoms: ${friendlySymptoms.join(', ')}`;
      }
      return `Energy Level: ${energy}/5`;
    },

    getMoodTooltip(pillar) {
      if (!pillar) return 'No mood indicators recorded';
      const positives = pillar.positives || [];
      const negatives = pillar.negatives || [];
      
      if (positives.length === 0 && negatives.length === 0) return 'No mood indicators recorded';
      
      // Convert raw mood state names to friendly text
      const moodLabels = {
        'calm': 'Calm', 'happy': 'Happy', 'excited': 'Excited', 'hopes': 'Hopeful',
        'anxious_stressed': 'Anxious/Stressed', 'ashamed': 'Ashamed', 'angry_irritable': 'Angry/Irritable',
        'sad': 'Sad', 'mood_swings': 'Mood Swings', 'worthless_guilty': 'Worthless/Guilty',
        'overwhelmed': 'Overwhelmed', 'hopeless': 'Hopeless', 'depressed_sad_down': 'Depressed/Sad/Down'
      };
      
      let tooltip = '';
      if (positives.length > 0) {
        const friendlyPositives = positives.map(mood => moodLabels[mood] || mood);
        tooltip += `Positive: ${friendlyPositives.join(', ')}`;
      }
      if (negatives.length > 0) {
        if (tooltip) tooltip += ' | ';
        const friendlyNegatives = negatives.map(mood => moodLabels[mood] || mood);
        tooltip += `Negative: ${friendlyNegatives.join(', ')}`;
      }
      return tooltip;
    },

    getStoolTooltip(pillar) {
      if (!pillar) return 'No stool/urine issues recorded';
      const issues = [];
      if (pillar.urine?.blood) issues.push('blood in urine');
      if (pillar.stool?.blood) issues.push('blood in stool');
      
      if (issues.length === 0) return 'No stool/urine issues recorded';
      return `Issues: ${issues.join(' and ')}`;
    },

    getSleepTooltip(pillar) {
      if (!pillar || !pillar.calculatedHours) return 'No sleep data recorded';
      const hours = pillar.calculatedHours;
      const issues = [];
      
      if (pillar.troubleAsleep) issues.push('trouble falling asleep');
      if (pillar.wakeUpDuringNight) issues.push('woke up during night');
      if (!pillar.tiredRested) issues.push('not well rested');
      
      let tooltip = `Sleep: ${hours} hours`;
      if (issues.length > 0) {
        tooltip += ` | Issues: ${issues.join(', ')}`;
      }
      return tooltip;
    },

    getDietTooltip(pillar) {
      if (!pillar) return 'No diet items recorded';
      const positives = pillar.positives || [];
      const negatives = pillar.negatives || [];
      const neutrals = pillar.neutrals || [];
      
      if (positives.length === 0 && negatives.length === 0 && neutrals.length === 0) return 'No diet items recorded';
      
      // Convert raw diet item names to friendly text
      const dietLabels = {
        'vegetables': 'Vegetables', 'fruit': 'Fruit', 'potato_rice_bread': 'Carbohydrates',
        'dairy': 'Dairy Products', 'nuts_tofu_tempe': 'Protein Alternatives', 'eggs': 'Eggs',
        'fish': 'Fish', 'meat': 'Meat', 'snacks': 'Snacks', 'soda': 'Soda',
        'water': 'Water', 'coffee': 'Coffee', 'alcohol': 'Alcohol'
      };
      
      let tooltip = '';
      if (positives.length > 0) {
        const friendlyPositives = positives.map(item => dietLabels[item] || item);
        tooltip += `Good: ${friendlyPositives.join(', ')}`;
      }
      if (negatives.length > 0) {
        if (tooltip) tooltip += ' | ';
        const friendlyNegatives = negatives.map(item => dietLabels[item] || item);
        tooltip += `Poor: ${friendlyNegatives.join(', ')}`;
      }
      if (neutrals.length > 0) {
        if (tooltip) tooltip += ' | ';
        const friendlyNeutrals = neutrals.map(item => dietLabels[item] || item);
        tooltip += `Neutral: ${friendlyNeutrals.join(', ')}`;
      }
      return tooltip;
    },

    getExerciseTooltip(pillar) {
      if (!pillar || !pillar.any) return 'No exercise recorded';
      const levels = pillar.levels || [];
      const impacts = pillar.impacts || [];
      
      let timeRange = 'Exercise completed';
      if (levels.includes('greater_sixty')) timeRange = 'Duration: >60 minutes';
      else if (levels.includes('thirty_to_sixty')) timeRange = 'Duration: 30-60 minutes';
      else if (levels.includes('less_thirty')) timeRange = 'Duration: <30 minutes';
      
      if (impacts.length > 0) {
        return `${timeRange} | Impact: ${impacts.join(', ')}`;
      }
      return timeRange;
    },

    getSexTooltip(pillar) {
      if (!pillar || !pillar.today) return 'No sexual activity recorded';
      const avoided = pillar.avoided;
      const issues = pillar.issues || [];
      const satisfied = pillar.satisfied;
      
      if (avoided) return 'Sexual activity avoided';
      if (issues.length > 0) return `Sexual activity with issues: ${issues.join(', ')}`;
      if (satisfied) return 'Sexual activity - satisfied';
      return 'Sexual activity recorded';
    },

    getNotesTooltip(pillar) {
      if (!pillar || !pillar.hasNote) return 'No notes recorded';
      const text = pillar.text || 'Note recorded';
      return `Note: ${text}`;
    },

    openDomainModal(item) {
      this.modalData = item;
      this.modalContent = this.generateModalContent(item);
      this.showModal = true;
      // Prevent body scroll
      document.body.style.overflow = 'hidden';
    },

    closeModal() {
      this.showModal = false;
      this.modalData = null;
      this.modalContent = '';
      // Restore body scroll
      document.body.style.overflow = '';
    },

    generateModalContent(item) {
      const pillar = item.pillar;
      
      switch(item.key) {
        case 'blood_loss':
          return this.generateBloodLossModal(pillar);
        case 'pain':
          return this.generatePainModal(pillar);
        case 'impact':
          return this.generateImpactModal(pillar);
        case 'general_health':
          return this.generateEnergyModal(pillar);
        case 'mood':
          return this.generateMoodModal(pillar);
        case 'stool_urine':
          return this.generateStoolModal(pillar);
        case 'sleep':
          return this.generateSleepModal(pillar);
        case 'diet':
          return this.generateDietModal(pillar);
        case 'exercise':
          return this.generateExerciseModal(pillar);
        case 'sex':
          return this.generateSexModal(pillar);
        case 'notes':
          return this.generateNotesModal(pillar);
        default:
          return '<p>No detailed information available.</p>';
      }
    },

    generateBloodLossModal(pillar) {
      const amount = pillar?.amount ?? 0;
      const severity = pillar?.severity || 'none';
      const spotting = pillar?.flags?.spotting;
      
      let content = '<div class="space-y-4">';
      
      // Severity visualization
      content += '<div class="text-center">';
      content += '<h3 class="text-lg font-semibold mb-3">Blood Loss Severity</h3>';
      content += '<div class="flex justify-center items-center gap-2 mb-4">';
      
      const severityLevels = ['very_light', 'light', 'moderate', 'heavy', 'very_heavy'];
      severityLevels.forEach((level, index) => {
        const isActive = severity === level;
        const opacity = isActive ? 'opacity-100' : 'opacity-30';
        content += `<img src="/images/blood_loss_${index + 1}.png" alt="${level}" class="w-8 h-8 object-contain ${opacity}">`;
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
    },

    generatePainModal(pillar) {
      const value = pillar?.value ?? 0;
      const regions = pillar?.regions || [];
      
      let content = '<div class="space-y-4">';
      
      // Pain level visualization
      content += '<div class="text-center">';
      content += '<h3 class="text-lg font-semibold mb-3">Pain Level</h3>';
      content += '<div class="flex justify-center items-center gap-1 mb-4">';
      
      for (let i = 1; i <= 6; i++) {
        const isActive = Math.ceil(value / 2) + 1 >= i;
        const opacity = isActive ? 'opacity-100' : 'opacity-30';
        content += `<img src="/images/smile_${i}.png" alt="Pain level ${i}" class="w-8 h-8 object-contain ${opacity}">`;
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
    },

    generateMoodModal(pillar) {
      const positives = pillar?.positives || [];
      const negatives = pillar?.negatives || [];
      
      let content = '<div class="space-y-4">';
      
      // Mood balance visualization
      content += '<div class="text-center mb-6">';
      content += '<h3 class="text-lg font-semibold mb-3">Mood Overview</h3>';
      
      const balance = positives.length - negatives.length;
      let moodIcon = 'mood_1.png'; // calm
      if (balance > 1) moodIcon = 'mood_2.png'; // happy
      else if (balance < -1) moodIcon = 'mood_7.png'; // sad
      
      content += `<img src="/images/${moodIcon}" alt="Overall mood" class="w-16 h-16 object-contain mx-auto mb-2">`;
      
      if (balance > 0) {
        content += '<p class="text-green-600 font-medium">Positive Day</p>';
      } else if (balance < 0) {
        content += '<p class="text-red-600 font-medium">Challenging Day</p>';
      } else {
        content += '<p class="text-yellow-600 font-medium">Balanced Day</p>';
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
    },

    generateDietModal(pillar) {
      const positives = pillar?.positives || [];
      const negatives = pillar?.negatives || [];
      const neutrals = pillar?.neutrals || [];
      
      let content = '<div class="space-y-4">';
      
      content += '<div class="text-center mb-6">';
      content += '<h3 class="text-lg font-semibold mb-3">Diet Overview</h3>';
      content += `<img src="/images/diet.png" alt="Diet" class="w-16 h-16 object-contain mx-auto">`;
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
        content += '<h4 class="font-medium text-red-800 mb-2">Less Healthy Choices:</h4>';
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
    },

    generateImpactModal(pillar) {
      const grade = pillar?.gradeYourDay ?? 0;
      const limitations = pillar?.limitations || [];
      
      let content = '<div class="space-y-4">';
      
      content += '<div class="text-center mb-6">';
      content += '<h3 class="text-lg font-semibold mb-3">Daily Impact</h3>';
      
      // Grade visualization
      content += '<div class="flex justify-center items-center gap-1 mb-4">';
      for (let i = 1; i <= 10; i++) {
        const isActive = grade >= i;
        const opacity = isActive ? 'opacity-100' : 'opacity-30';
        let color = 'bg-red-500';
        if (i >= 8) color = 'bg-green-500';
        else if (i >= 6) color = 'bg-yellow-500';
        
        content += `<div class="w-3 h-6 ${color} ${opacity} rounded-sm"></div>`;
      }
      content += '</div>';
      
      let gradeText = 'Difficult Day';
      if (grade >= 8) gradeText = 'Good Day';
      else if (grade >= 6) gradeText = 'Okay Day';
      
      content += `<p class="text-lg font-medium mb-2">${gradeText}</p>`;
      content += `<p class="text-gray-600">Grade: ${grade}/10</p>`;
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
    },

    generateEnergyModal(pillar) {
      const energy = pillar?.energyLevel ?? 0;
      const symptoms = pillar?.symptoms || [];
      
      let content = '<div class="space-y-4">';
      
      content += '<div class="text-center mb-6">';
      content += '<h3 class="text-lg font-semibold mb-3">Energy Level</h3>';
      
      // Energy level visualization
      content += '<div class="flex justify-center items-center gap-2 mb-4">';
      for (let i = 1; i <= 5; i++) {
        const isActive = energy >= i;
        const opacity = isActive ? 'opacity-100' : 'opacity-30';
        const iconName = i === 1 ? 'sleep.png' : `general_health_${i - 1}.png`;
        content += `<img src="/images/${iconName}" alt="Energy level ${i}" class="w-8 h-8 object-contain ${opacity}">`;
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
          'dizzy': 'Dizzy', 'nauseous': 'Nauseous', 'headache_migraine': 'Headache/Migraine',
          'bloated': 'Bloated', 'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
          'acne': 'Acne', 'muscle_joint_pain': 'Muscle/Joint Pain'
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
    },

    generateStoolModal(pillar) {
      const hasUrineBlood = pillar?.urine?.blood ?? false;
      const hasStoolBlood = pillar?.stool?.blood ?? false;
      const consistency = pillar?.stool?.consistency;
      
      let content = '<div class="space-y-4">';
      
      content += '<div class="text-center mb-6">';
      content += '<h3 class="text-lg font-semibold mb-3">Stool & Urine</h3>';
      
      if (hasUrineBlood || hasStoolBlood) {
        content += `<img src="/images/urine_stool.png" alt="Blood detected" class="w-16 h-16 object-contain mx-auto mb-2">`;
        content += '<p class="text-red-600 font-medium">Blood Detected</p>';
      } else if (consistency) {
        const consistencyIcons = {
          'hard': 'urine_stool_1.png',
          'normal': 'urine_stool_2.png',
          'soft': 'urine_stool_3.png',
          'watery': 'urine_stool_4.png',
          'something_else': 'urine_stool_5.png',
          'no_stool': 'urine_stool_6.png'
        };
        const icon = consistencyIcons[consistency] || 'urine_stool_2.png';
        content += `<img src="/images/${icon}" alt="${consistency}" class="w-16 h-16 object-contain mx-auto mb-2">`;
        content += `<p class="text-green-600 font-medium">${consistency.charAt(0).toUpperCase() + consistency.slice(1)}</p>`;
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
    },

    generateSleepModal(pillar) {
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
    },

    generateExerciseModal(pillar) {
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
            'precision': 'Precision Exercise'
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
    },

    generateSexModal(pillar) {
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
    },

    generateNotesModal(pillar) {
      const text = pillar?.text || '';
      return `<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="font-medium text-blue-800 mb-2">Your Note:</h4>
        <p class="text-blue-700">${text}</p>
      </div>`;
    },

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
      severityLevels.forEach((level, index) => {
        severityIcons.push({
          src: `/images/blood_loss_${index + 1}.png`,
          alt: level,
          active: severity === level
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
        additionalInfo: spotting ? 'Spotting detected' : '',
        tooltip: this.getBloodLossTooltip(pillar),
        pillar: pillar
      };
    },

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
        } else if (value >= 3) {
          statusColor = 'bg-yellow-500';
          statusText = 'Mild pain';
          context = 'Mild';
          progressColor = 'bg-yellow-400';
        } else {
          statusColor = 'bg-green-400';
          statusText = 'Light pain';
          context = 'Light';
          progressColor = 'bg-green-400';
        }
      }
      
      // Create pain level icons array
      const painIcons = [];
      for (let i = 1; i <= 6; i++) {
        painIcons.push({
          src: `/images/smile_${i}.png`,
          alt: `Pain level ${i}`,
          active: Math.ceil(value / 2) + 1 >= i
        });
      }

      return {
        key: 'pain',
        label: 'Pain',
        iconSrc: `/images/smile_${Math.min(Math.ceil(value / 2) + 1, 6)}.png`,
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        severityIcons: painIcons,
        additionalInfo: regions.length > 0 ? `${regions.length} affected area(s)` : '',
        tooltip: this.getPainTooltip(pillar),
        pillar: pillar
      };
    },

    createImpactCard(pillar) {
      const grade = pillar?.gradeYourDay ?? 0;
      const limitations = pillar?.limitations || [];
      
      // Hide card if no impact data
      if (!pillar || grade === 0) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No impact';
      let context = '';
      let progressColor = 'bg-gray-400';
      
      if (grade > 0) {
        if (grade >= 8) {
          statusColor = 'bg-green-500';
          statusText = 'Good day';
          context = 'Good Day';
          progressColor = 'bg-green-400';
        } else if (grade >= 6) {
          statusColor = 'bg-yellow-500';
          statusText = 'Okay day';
          context = 'Okay Day';
          progressColor = 'bg-yellow-400';
        } else {
          statusColor = 'bg-red-500';
          statusText = 'Difficult day';
          context = 'Difficult Day';
          progressColor = 'bg-red-400';
        }
      }
      
      return {
        key: 'impact',
        label: 'Impact',
        iconSrc: limitations.length > 0 ? '/images/impact_1.png' : '/images/impact.png',
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: limitations.length > 0 ? '/images/impact_1.png' : '/images/impact.png',
          alt: 'Daily Impact'
        },
        additionalInfo: limitations.length > 0 ? `${limitations.length} limitation(s)` : '',
        tooltip: this.getImpactTooltip(pillar),
        pillar: pillar
      };
    },

    createEnergyCard(pillar) {
      const energy = pillar?.energyLevel ?? 0;
      const symptoms = pillar?.symptoms || [];
      
      // Hide card if no energy data
      if (!pillar || energy === 0) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No data';
      let context = '';
      let progressColor = 'bg-gray-400';
      
      if (energy > 0) {
        if (energy >= 4) {
          statusColor = 'bg-green-500';
          statusText = 'High energy';
          context = 'High Energy';
          progressColor = 'bg-green-400';
        } else if (energy >= 3) {
          statusColor = 'bg-yellow-500';
          statusText = 'Moderate energy';
          context = 'Moderate';
          progressColor = 'bg-yellow-400';
        } else {
          statusColor = 'bg-red-500';
          statusText = 'Low energy';
          context = 'Low Energy';
          progressColor = 'bg-red-400';
        }
      }
      
      // Create energy level icons array
      const energyIcons = [];
      for (let i = 1; i <= 5; i++) {
        const iconName = i === 1 ? 'sleep.png' : `general_health_${i - 1}.png`;
        energyIcons.push({
          src: `/images/${iconName}`,
          alt: `Energy level ${i}`,
          active: energy >= i
        });
      }

      return {
        key: 'general_health',
        label: 'Energy',
        iconSrc: energy === 1 ? '/images/sleep.png' : `/images/general_health_${Math.min(energy - 1, 4)}.png`,
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        severityIcons: energyIcons,
        additionalInfo: symptoms.length > 0 ? `${symptoms.length} symptom(s)` : 'No symptoms',
        tooltip: this.getEnergyTooltip(pillar),
        pillar: pillar
      };
    },

    createMoodCard(pillar) {
      const positives = pillar?.positives || [];
      const negatives = pillar?.negatives || [];
      const balance = positives.length - negatives.length;
      
      if (!pillar || (positives.length === 0 && negatives.length === 0)) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No data';
      let context = '';
      
      if (positives.length > 0 || negatives.length > 0) {
        if (balance > 1) {
          statusColor = 'bg-green-500';
          statusText = 'Positive mood';
          context = 'Positive';
        } else if (balance >= 0) {
          statusColor = 'bg-yellow-500';
          statusText = 'Balanced mood';
          context = 'Balanced';
        } else {
          statusColor = 'bg-red-500';
          statusText = 'Challenging mood';
          context = 'Challenging';
        }
      }
      
      // Determine primary mood icon
      let moodIcon = 'mood_1.png'; // calm
      if (balance > 1) moodIcon = 'mood_2.png'; // happy
      else if (balance < -1) moodIcon = 'mood_7.png'; // sad

      return {
        key: 'mood',
        label: 'Mood',
        iconSrc: `/images/${moodIcon}`,
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: `/images/${moodIcon}`,
          alt: 'Mood State'
        },
        additionalInfo: `+${positives.length} / -${negatives.length}`,
        tooltip: this.getMoodTooltip(pillar),
        pillar: pillar
      };
    },

    createStoolCard(pillar) {
      const hasBlood = pillar?.urine?.blood || pillar?.stool?.blood;
      const consistency = pillar?.stool?.consistency;
      
      // Hide card if no stool/urine data
      if (!pillar || (!hasBlood && !consistency)) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No data';
      let context = '';
      
      if (pillar) {
        if (hasBlood) {
          statusColor = 'bg-red-500';
          statusText = 'Blood detected';
          context = 'Blood Detected';
        } else if (consistency) {
          statusColor = 'bg-green-400';
          statusText = 'Normal';
          context = consistency.charAt(0).toUpperCase() + consistency.slice(1);
        }
      }
      
      return {
        key: 'stool_urine',
        label: 'Stool/Urine',
        iconSrc: hasBlood ? '/images/urine_stool.png' : '/images/urine_stool_2.png',
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: hasBlood ? '/images/urine_stool.png' : '/images/urine_stool_2.png',
          alt: 'Stool/Urine Status'
        },
        additionalInfo: '',
        tooltip: this.getStoolTooltip(pillar),
        pillar: pillar
      };
    },

    createSleepCard(pillar) {
      const hours = pillar?.calculatedHours ?? 0;
      const hasIssues = pillar?.troubleAsleep || pillar?.wakeUpDuringNight || !pillar?.tiredRested;
      
      // Hide card if no sleep data
      if (!pillar || hours === 0) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No data';
      let context = '';
      
      if (hours > 0) {
        if (hours >= 7 && hours <= 9 && !hasIssues) {
          statusColor = 'bg-green-500';
          statusText = 'Good sleep';
          context = 'Good Sleep';
        } else if (hours >= 6 && hours <= 10) {
          statusColor = 'bg-yellow-500';
          statusText = 'Okay sleep';
          context = 'Okay Sleep';
        } else {
          statusColor = 'bg-red-500';
          statusText = 'Poor sleep';
          context = 'Poor Sleep';
        }
      }
      
      return {
        key: 'sleep',
        label: 'Sleep',
        iconSrc: '/images/sleep.png',
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: '/images/sleep.png',
          alt: 'Sleep Quality'
        },
        additionalInfo: hasIssues ? 'Sleep issues reported' : 'No issues',
        tooltip: this.getSleepTooltip(pillar),
        pillar: pillar
      };
    },

    createDietCard(pillar) {
      const positives = pillar?.positives || [];
      const negatives = pillar?.negatives || [];
      const neutrals = pillar?.neutrals || [];
      const total = positives.length + negatives.length + neutrals.length;
      
      // Hide card if no diet data
      if (!pillar || total === 0) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No data';
      let context = '';
      
      if (total > 0) {
        if (negatives.length === 0 && positives.length > 0) {
          statusColor = 'bg-green-500';
          statusText = 'Healthy diet';
          context = 'Healthy';
        } else if (negatives.length <= positives.length) {
          statusColor = 'bg-yellow-500';
          statusText = 'Mixed diet';
          context = 'Mixed';
        } else {
          statusColor = 'bg-red-500';
          statusText = 'Concerning diet';
          context = 'Concerning';
        }
      }
      
      return {
        key: 'diet',
        label: 'Diet',
        iconSrc: '/images/diet.png',
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: '/images/diet.png',
          alt: 'Diet Quality'
        },
        additionalInfo: `Good: ${positives.length}, Poor: ${negatives.length}`,
        tooltip: this.getDietTooltip(pillar),
        pillar: pillar
      };
    },

    createExerciseCard(pillar) {
      const hasExercise = pillar?.any ?? false;
      const levels = pillar?.levels || [];
      
      // Hide card if no exercise data
      if (!pillar || !hasExercise) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No exercise';
      let context = '';
      let duration = '';
      
      if (hasExercise) {
        statusColor = 'bg-green-500';
        statusText = 'Exercise completed';
        context = 'Completed';
        
        if (levels.includes('greater_sixty')) duration = '>60 min';
        else if (levels.includes('thirty_to_sixty')) duration = '30-60 min';
        else if (levels.includes('less_thirty')) duration = '<30 min';
      }
      
      return {
        key: 'exercise',
        label: 'Exercise',
        iconSrc: '/images/sport.png',
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: '/images/sport.png',
          alt: 'Exercise Activity'
        },
        additionalInfo: duration,
        tooltip: this.getExerciseTooltip(pillar),
        pillar: pillar
      };
    },

    createSexCard(pillar) {
      const today = pillar?.today ?? false;
      const avoided = pillar?.avoided ?? false;
      const satisfied = pillar?.satisfied ?? false;
      
      // Hide card if no sexual health data
      if (!pillar || (!today && !avoided)) {
        return null;
      }
      
      let statusColor = 'bg-gray-300';
      let statusText = 'No activity';
      let context = '';
      
      if (today) {
        if (satisfied) {
          statusColor = 'bg-green-500';
          statusText = 'Satisfying';
          context = 'Satisfying';
        } else {
          statusColor = 'bg-yellow-500';
          statusText = 'Activity recorded';
          context = 'Recorded';
        }
      } else if (avoided) {
        statusColor = 'bg-orange-500';
        statusText = 'Avoided';
        context = 'Avoided';
      }
      
      return {
        key: 'sex',
        label: 'Sexual Health',
        iconSrc: '/images/sex.png',
        context: context,
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: '/images/sex.png',
          alt: 'Sexual Health'
        },
        additionalInfo: '',
        tooltip: this.getSexTooltip(pillar),
        pillar: pillar
      };
    },

    createNotesCard(pillar) {
      const hasNote = pillar?.hasNote ?? false;
      const text = pillar?.text || '';
      
      // Hide card if no notes
      if (!pillar || !hasNote) {
        return null;
      }
      
      let statusColor = hasNote ? 'bg-blue-500' : 'bg-gray-300';
      let statusText = hasNote ? 'Note recorded' : 'No notes';
      
      return {
        key: 'notes',
        label: 'Notes',
        iconSrc: '/images/grid_notes.png',
        context: hasNote ? 'Recorded' : '',
        statusColor: statusColor,
        statusText: statusText,
        statusIcon: {
          src: '/images/grid_notes.png',
          alt: 'Notes'
        },
        additionalInfo: hasNote && text ? text.substring(0, 30) + '...' : '',
        tooltip: this.getNotesTooltip(pillar),
        pillar: pillar
      };
    },

    async fetchData(){
      this.loading = true;
      try{
        const url = new URL('/api/v1/participant/daily', window.location.origin);
        url.searchParams.set('date', this.date);
        const res = await fetch(url);
        const json = await res.json();
        this.data = json.data;
        if (this.data){
          const pillars = this.data.pillars || {};
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
          const items = allCards.filter(card => card !== null);
          this.items = items;
        }

        this.videos = [
          { type:'youtube', id:'dQw4w9WgXcQ', title: 'Understanding PBAC Scoring' },
          { type:'youtube', id:'l482T0yNkeo', title: 'Managing Pain: Tips and Techniques' },
          { type:'youtube', id:'ysz5S6PUM-U', title: 'Sleep Hygiene Basics' },
          { type:'youtube', id:'CevxZvSJLk8', title: 'Diet and Energy Levels' },
          { type:'youtube', id:'hTWKbfoikeg', title: 'General Wellbeing Guidance' },
        ];

        this.$nextTick(()=>{
          if (this.items.length && this.$refs?.symSwiper){
            if (this._symSwiper) { this._symSwiper.destroy(true,true); }
            this._symSwiper = new Swiper(this.$refs.symSwiper, {
              slidesPerView: 'auto',
              spaceBetween: 28,
              grabCursor: true, 
              freeMode: true,
              allowTouchMove: true,
              simulateTouch: true, 
              preventClicks: false, 
              preventClicksPropagation: false,
            });
          }

          if (this.$refs?.vidSwiper){
            if (this._vidSwiper) { this._vidSwiper.destroy(true,true); }
            this._vidSwiper = new Swiper(this.$refs.vidSwiper, {
              slidesPerView: 'auto',
              spaceBetween: 28,
              grabCursor: true,
              freeMode: true,
            });
          }
        });
      }catch(e){ this.data = null; }
      this.loading = false;
    }
  }
};
