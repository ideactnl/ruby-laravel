window.dailyView = function dailyView() {
  return {
    date: new Date().toISOString().slice(0,10),
    data: null,
    loading: false,
    items: [],
    videos: [],
    _symSwiper: null,
    _vidSwiper: null,
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
        return `Pain Level: ${value}/10 | Regions: ${regions.join(', ')}`;
      }
      return `Pain Level: ${value}/10`;
    },

    getImpactTooltip(pillar) {
      if (!pillar || !pillar.gradeYourDay) return 'No impact recorded';
      const grade = pillar.gradeYourDay;
      const limitations = pillar.limitations || [];
      
      if (limitations.length > 0) {
        return `Grade: ${grade}/10 | Limitations: ${limitations.join(', ')}`;
      }
      return `Grade Your Day: ${grade}/10`;
    },

    getEnergyTooltip(pillar) {
      if (!pillar || !pillar.energyLevel) return 'No energy level recorded';
      const energy = pillar.energyLevel;
      const symptoms = pillar.symptoms || [];
      
      if (symptoms.length > 0) {
        return `Energy: ${energy}/10 | Symptoms: ${symptoms.join(', ')}`;
      }
      return `Energy Level: ${energy}/10`;
    },

    getMoodTooltip(pillar) {
      if (!pillar) return 'No mood indicators recorded';
      const positives = pillar.positives || [];
      const negatives = pillar.negatives || [];
      
      if (positives.length === 0 && negatives.length === 0) return 'No mood indicators recorded';
      
      let tooltip = '';
      if (positives.length > 0) tooltip += `Positive: ${positives.join(', ')}`;
      if (negatives.length > 0) {
        if (tooltip) tooltip += ' | ';
        tooltip += `Negative: ${negatives.join(', ')}`;
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
      
      if (positives.length === 0 && negatives.length === 0) return 'No diet items recorded';
      
      let tooltip = '';
      if (positives.length > 0) tooltip += `Good: ${positives.join(', ')}`;
      if (negatives.length > 0) {
        if (tooltip) tooltip += ' | ';
        tooltip += `Poor: ${negatives.join(', ')}`;
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
          const items = [
            { 
              key:'blood_loss', 
              label:'bloodloss', 
              badge:'bg-red-600', 
              pillar: pillars.blood_loss,
              value: pillars.blood_loss?.amount ?? 0,
              tooltip: this.getBloodLossTooltip(pillars.blood_loss)
            },
            { 
              key:'pain', 
              label:'pain', 
              badge:'bg-[#5E0F0F]', 
              pillar: pillars.pain,
              value: pillars.pain?.value ?? 0,
              tooltip: this.getPainTooltip(pillars.pain)
            },
            { 
              key:'impact', 
              label:'impact', 
              badge:'bg-green-500', 
              pillar: pillars.impact,
              value: pillars.impact?.gradeYourDay ?? 0,
              tooltip: this.getImpactTooltip(pillars.impact)
            },
            { 
              key:'general_health', 
              label:'energy', 
              badge:'bg-yellow-500 text-black', 
              pillar: pillars.general_health,
              value: pillars.general_health?.energyLevel ?? 0,
              tooltip: this.getEnergyTooltip(pillars.general_health)
            },
            { 
              key:'mood', 
              label:'mood', 
              badge:'bg-yellow-400 text-black', 
              pillar: pillars.mood,
              value: (pillars.mood?.positives?.length ?? 0) + (pillars.mood?.negatives?.length ?? 0),
              tooltip: this.getMoodTooltip(pillars.mood)
            },
            { 
              key:'stool_urine', 
              label:'stool', 
              badge:'bg-sky-500', 
              pillar: pillars.stool_urine,
              value: (pillars.stool_urine?.urine?.blood ? 1 : 0) + (pillars.stool_urine?.stool?.blood ? 1 : 0),
              tooltip: this.getStoolTooltip(pillars.stool_urine)
            },
            { 
              key:'sleep', 
              label:'sleep', 
              badge:'bg-indigo-500', 
              pillar: pillars.sleep,
              value: pillars.sleep?.calculatedHours ?? 0,
              tooltip: this.getSleepTooltip(pillars.sleep)
            },
            { 
              key:'diet', 
              label:'diet', 
              badge:'bg-green-400', 
              pillar: pillars.diet,
              value: (pillars.diet?.positives?.length ?? 0) + (pillars.diet?.negatives?.length ?? 0),
              tooltip: this.getDietTooltip(pillars.diet)
            },
            { 
              key:'exercise', 
              label:'exercise', 
              badge:'bg-orange-400', 
              pillar: pillars.exercise,
              value: pillars.exercise?.any ? 1 : 0,
              tooltip: this.getExerciseTooltip(pillars.exercise)
            },
            { 
              key:'sex', 
              label:'sex', 
              badge:'bg-pink-500', 
              pillar: pillars.sex,
              value: pillars.sex?.today ? 1 : 0,
              tooltip: this.getSexTooltip(pillars.sex)
            },
            { 
              key:'notes', 
              label:'notes', 
              badge:'bg-gray-500', 
              pillar: pillars.notes,
              value: pillars.notes?.hasNote ? 1 : 0,
              tooltip: this.getNotesTooltip(pillars.notes)
            },
          ];
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
