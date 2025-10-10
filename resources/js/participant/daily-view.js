/**
 * Daily View Main Module
 * Orchestrates all daily view functionality using modular components
 */

import { 
  DateManager, 
  DataManager, 
  TooltipGenerators, 
  ModalManager, 
  SwiperManager 
} from './daily-view/index.js';

window.dailyView = function dailyView() {
  return {
    date: new Date().toISOString().slice(0, 10),
    data: null,
    loading: false,
    items: [],
    videos: [],
    _symSwiper: null,
    _vidSwiper: null,
    showModal: false,
    modalData: null,
    modalContent: '',
    
    _dateManager: null,
    _dataManager: null,
    _modalManager: null,
    _swiperManager: null,
    
    get heading() {
      return this._dateManager ? this._dateManager.getHeading(this.date) : this.date;
    },
    
    get shortDate() {
      return this._dateManager ? this._dateManager.getShortDate(this.date) : this.date;
    },
    
    init() {
      this._dateManager = new DateManager(this);
      this._dataManager = new DataManager(this);
      this._modalManager = new ModalManager(this);
      this._swiperManager = new SwiperManager(this);
      
      const urlDate = this._dateManager.getDateFromURL();
      if (urlDate) this.date = urlDate;
      
      this._dateManager.initDatePicker();
      
      this.fetchData();
      
      this.$nextTick(() => {
        setTimeout(() => {
          if (this._swiperManager && this.$refs?.vidSwiper) {
            this._swiperManager.initVideosSwiper();
          }
        }, 100);
      });
    },
    
    openDate() {
      if (window.innerWidth <= 768 && 'vibrate' in navigator) {
        try {
          navigator.vibrate(15);
        } catch (e) {}
      }
      this._dateManager.openDate();
    },
    
    prevDay() {
      if (window.innerWidth <= 768 && 'vibrate' in navigator) {
        try {
          navigator.vibrate(25);
        } catch (e) {}
      }
      this._dateManager.prevDay();
    },
    
    nextDay() {
      if (window.innerWidth <= 768 && 'vibrate' in navigator) {
        try {
          navigator.vibrate(25);
        } catch (e) {}
      }
      this._dateManager.nextDay();
    },
    
    fetchData() {
      return this._dataManager.fetchData();
    },

    getBloodLossTooltip(pillar) {
      return TooltipGenerators.getBloodLossTooltip(pillar);
    },

    getPainTooltip(pillar) {
      return TooltipGenerators.getPainTooltip(pillar);
    },

    getImpactTooltip(pillar) {
      return TooltipGenerators.getImpactTooltip(pillar);
    },

    getEnergyTooltip(pillar) {
      return TooltipGenerators.getEnergyTooltip(pillar);
    },

    getMoodTooltip(pillar) {
      return TooltipGenerators.getMoodTooltip(pillar);
    },

    getStoolTooltip(pillar) {
      return TooltipGenerators.getStoolTooltip(pillar);
    },

    getSleepTooltip(pillar) {
      return TooltipGenerators.getSleepTooltip(pillar);
    },

    getDietTooltip(pillar) {
      return TooltipGenerators.getDietTooltip(pillar);
    },

    getExerciseTooltip(pillar) {
      return TooltipGenerators.getExerciseTooltip(pillar);
    },

    getSexTooltip(pillar) {
      return TooltipGenerators.getSexTooltip(pillar);
    },

    getNotesTooltip(pillar) {
      return TooltipGenerators.getNotesTooltip(pillar);
    },

    openDomainModal(item) {
      if (window.innerWidth <= 768 && 'vibrate' in navigator) {
        try {
          navigator.vibrate(15);
        } catch (e) {}
      }
      this._modalManager.openDomainModal(item);
    },

    closeModal() {
      if (window.innerWidth <= 768 && 'vibrate' in navigator) {
        try {
          navigator.vibrate(10);
        } catch (e) {}
      }
      this._modalManager.closeModal();
    },

    generateModalContent(item) {
      return this._modalManager.generateModalContent(item);
    }
  };
};
