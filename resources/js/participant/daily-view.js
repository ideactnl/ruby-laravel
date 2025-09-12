/* Daily View page logic (Alpine component + Swiper setup) */

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
      const el = this.$refs?.datePick;
      try{
        if (el && typeof el.showPicker === 'function'){
          el.showPicker();
        } else if (el) {
          el.click();
        }
      }catch(e){ if (el) el.click(); }
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
      this.fetchData();
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
          const items = [
            { key:'pbac', label:'Blood Loss', badge:'bg-red-600', value: this.data.pbac_score_per_day },
            { key:'pain', label:'Pain', badge:'bg-[#5E0F0F]', value: this.data.pain_score_per_day },
            { key:'general', label:'General Health', badge:'bg-green-500', value: this.data.quality_of_life },
            { key:'mood', label:'Mood', badge:'bg-yellow-400 text-black', value: this.data.influence_factor },
            { key:'sleep', label:'Sleep (hrs)', badge:'bg-slate-500', value: this.data.sleep_hours },
            { key:'exercise', label:'Exercise', badge:'bg-orange-400', value: this.data.exercise },
            { key:'stool', label:'Stool/Urine', badge:'bg-sky-500', value: (this.data.complaints_with_defecation||0)+(this.data.complaints_with_urinating||0) },
            { key:'spotting', label:'Spotting', badge:'bg-red-500', value: this.data.spotting_yes_no },
            { key:'meds', label:'Pain medication', badge:'bg-amber-500', value: this.data.pain_medication },
            { key:'sleepq', label:'Quality of sleep', badge:'bg-indigo-500', value: this.data.quality_of_sleep },
            { key:'energy', label:'Energy level', badge:'bg-yellow-500 text-black', value: this.data.energy_level },
          ].map(it=>({ ...it, display: (it.key==='sleep' && it.value!=null) ? `${it.value}` : (it.value ?? 0) }));
          this.items = items;
        }

        // Always show videos
        this.videos = [
          { type:'youtube', id:'dQw4w9WgXcQ' },
          { type:'youtube', id:'l482T0yNkeo' },
          { type:'youtube', id:'ysz5S6PUM-U' },
          { type:'youtube', id:'CevxZvSJLk8' },
          { type:'youtube', id:'hTWKbfoikeg' },
        ];

        this.$nextTick(()=>{
          if (this.items.length && this.$refs?.symSwiper){
            if (this._symSwiper) { this._symSwiper.destroy(true,true); }
            this._symSwiper = new Swiper(this.$refs.symSwiper, {
              slidesPerView: 'auto',
              spaceBetween: 28,
              grabCursor: true,
              freeMode: true,
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
