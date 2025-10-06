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
    
    // Module instances
    _dateManager: null,
    _dataManager: null,
    _modalManager: null,
    _swiperManager: null,
    
    // Computed properties
    get heading() {
      return this._dateManager ? this._dateManager.getHeading(this.date) : this.date;
    },
    
    get shortDate() {
      return this._dateManager ? this._dateManager.getShortDate(this.date) : this.date;
    },
    
    // Initialization
    init() {
      // Initialize managers
      this._dateManager = new DateManager(this);
      this._dataManager = new DataManager(this);
      this._modalManager = new ModalManager(this);
      this._swiperManager = new SwiperManager(this);
      
      // Setup date from URL
      const urlDate = this._dateManager.getDateFromURL();
      if (urlDate) this.date = urlDate;
      
      // Initialize date picker
      this._dateManager.initDatePicker();
      
      // Fetch initial data
      this.fetchData();
    },
    
    // Date navigation methods
    openDate() {
      this._dateManager.openDate();
    },
    
    prevDay() {
      this._dateManager.prevDay();
    },
    
    nextDay() {
      this._dateManager.nextDay();
    },
    
    // Data methods
    fetchData() {
      return this._dataManager.fetchData();
    },

    // Tooltip methods - delegate to TooltipGenerators
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

    // Modal methods - delegate to ModalManager
    openDomainModal(item) {
      this._modalManager.openDomainModal(item);
    },

    closeModal() {
      this._modalManager.closeModal();
    },

    generateModalContent(item) {
      return this._modalManager.generateModalContent(item);
    }
  };
};
