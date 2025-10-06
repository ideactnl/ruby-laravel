/**
 * Data Manager Module
 * Handles API data fetching and processing
 */

import { CardGenerators } from './card-generators.js';

export class DataManager {
  constructor(component) {
    this.component = component;
    this.cardGenerators = new CardGenerators(component);
  }

  /**
   * Fetch daily data from API
   */
  async fetchData() {
    if (this.component.loading) return;
    
    this.component.loading = true;
    
    // Clear existing data immediately to prevent showing stale data
    this.component.items = [];
    this.component.videos = [];
    this.component.data = null;
    
    try {
      const url = new URL('/api/v1/participant/daily', window.location.origin);
      url.searchParams.set('date', this.component.date);
      url.searchParams.set('_t', Date.now()); // Cache busting
      
      
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        cache: 'no-cache' // Prevent caching
      });
      
      if (!response.ok) {
        if (response.status === 401) {
          console.warn('User not authenticated for daily data');
          this.component.data = null;
          this.component.items = [];
          this.component.videos = [];
          return;
        }
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      
      const response_data = await response.json();
      
      // Ensure we have the expected data structure
      if (response_data && response_data.success !== false) {
        this.component.data = response_data.data || response_data;
        this.processData(this.component.data);
      } else {
        // API returned success: false or no data - set minimal data structure
        this.component.data = { pillars: {} }; // Set minimal data so template shows
        this.component.items = [];
        // Still show videos even when API returns no data
        this.component.videos = [
          { type:'youtube', id:'dQw4w9WgXcQ', title: 'Understanding PBAC Scoring' },
          { type:'youtube', id:'l482T0yNkeo', title: 'Managing Pain: Tips and Techniques' },
          { type:'youtube', id:'ysz5S6PUM-U', title: 'Sleep Hygiene Basics' },
          { type:'youtube', id:'CevxZvSJLk8', title: 'Diet and Energy Levels' },
          { type:'youtube', id:'hTWKbfoikeg', title: 'General Wellbeing Guidance' },
        ];
        // Initialize video swiper even when no data
        this.component.$nextTick(() => {
          if (this.component._swiperManager) {
            this.component._swiperManager.initVideosSwiper();
          }
        });
      }
      
    } catch (error) {
      console.error('Failed to fetch daily data:', error);
      this.component.data = { pillars: {} }; // Set minimal data so template shows
      this.component.items = [];
      // Still show videos even when API fails
      this.component.videos = [
        { type:'youtube', id:'dQw4w9WgXcQ', title: 'Understanding PBAC Scoring' },
        { type:'youtube', id:'l482T0yNkeo', title: 'Managing Pain: Tips and Techniques' },
        { type:'youtube', id:'ysz5S6PUM-U', title: 'Sleep Hygiene Basics' },
        { type:'youtube', id:'CevxZvSJLk8', title: 'Diet and Energy Levels' },
        { type:'youtube', id:'hTWKbfoikeg', title: 'General Wellbeing Guidance' },
      ];
      // Initialize video swiper even when API fails
      this.component.$nextTick(() => {
        if (this.component._swiperManager) {
          this.component._swiperManager.initVideosSwiper();
        }
      });
    } finally {
      this.component.loading = false;
      
      // Always ensure video swiper is initialized after loading completes
      this.component.$nextTick(() => {
        setTimeout(() => {
          if (this.component._swiperManager && this.component.$refs?.vidSwiper) {
            this.component._swiperManager.initVideosSwiper();
          }
        }, 150);
      });
    }
  }

  /**
   * Process fetched data into display format
   */
  processData(data) {
    // Always reset items first
    this.component.items = [];
    
    // Always show videos regardless of any data
    this.component.videos = [
      { type:'youtube', id:'dQw4w9WgXcQ', title: 'Understanding PBAC Scoring' },
      { type:'youtube', id:'l482T0yNkeo', title: 'Managing Pain: Tips and Techniques' },
      { type:'youtube', id:'ysz5S6PUM-U', title: 'Sleep Hygiene Basics' },
      { type:'youtube', id:'CevxZvSJLk8', title: 'Diet and Energy Levels' },
      { type:'youtube', id:'hTWKbfoikeg', title: 'General Wellbeing Guidance' },
    ];
    
    // Check if we have valid data structure for cards
    if (!data || !data.pillars || typeof data.pillars !== 'object') {
      // Initialize video swiper even when no pillar data
      this.component.$nextTick(() => {
        if (this.component._swiperManager) {
          this.component._swiperManager.initVideosSwiper();
        }
      });
      return;
    }

    // Check if pillars object has any meaningful data
    const hasAnyPillarData = Object.values(data.pillars).some(pillar => {
      return pillar && typeof pillar === 'object' && Object.keys(pillar).length > 0;
    });

    if (!hasAnyPillarData) {
      // Initialize video swiper even when no meaningful pillar data
      this.component.$nextTick(() => {
        if (this.component._swiperManager) {
          this.component._swiperManager.initVideosSwiper();
        }
      });
      return;
    }

    // Process pillar data into card items
    const items = this.cardGenerators.generateCards(data.pillars);
    this.component.items = items || [];
    
    // Initialize swipers after data is loaded
    this.component.$nextTick(() => {
      if (this.component._swiperManager) {
        // Only initialize symptoms swiper if there are items
        if (this.component.items.length > 0) {
          this.component._swiperManager.initSymptomsSwiper();
        }
        // Always initialize videos swiper since videos are always available
        this.component._swiperManager.initVideosSwiper();
      }
    });
  }

}
