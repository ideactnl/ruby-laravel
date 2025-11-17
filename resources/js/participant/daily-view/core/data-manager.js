/**
 * Data Manager Module
 * Handles API data fetching and processing
 */

import { CardGenerators } from '../cards/card-generators.js';

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
    
    this.component.items = [];
    this.component.videos = [];
    this.component.data = null;
    
    try {
      const url = new URL('/api/v1/participant/daily', window.location.origin);
      url.searchParams.set('date', this.component.date);
      url.searchParams.set('_t', Date.now());
      
      
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        cache: 'no-cache'
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
      
      if (response_data && response_data.success !== false) {
        this.component.data = response_data.data || response_data;
        await this.processData(this.component.data);
      } else {
        this.component.data = { pillars: {} };
        this.component.items = [];
        this.component.videos = [];
        this.component.$nextTick(() => {
          if (this.component._swiperManager) {
            this.component._swiperManager.initVideosSwiper();
          }
        });
      }
      
    } catch (error) {
      console.error('Failed to fetch daily data:', error);
      this.component.data = { pillars: {} };
      this.component.items = [];
      this.component.videos = [];
      this.component.$nextTick(() => {
        if (this.component._swiperManager) {
          this.component._swiperManager.initVideosSwiper();
        }
      });
    } finally {
      this.component.loading = false;
      
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
  async processData(data) {
    this.component.items = [];
    this.component.videos = [];
    
    if (!data || !data.pillars || typeof data.pillars !== 'object') {
      this.component.$nextTick(() => {
        if (this.component._swiperManager) {
          this.component._swiperManager.initVideosSwiper();
        }
      });
      return;
    }

    const hasAnyPillarData = Object.values(data.pillars).some(pillar => {
      return pillar && typeof pillar === 'object' && Object.keys(pillar).length > 0;
    });

    if (!hasAnyPillarData) {
      this.component.$nextTick(() => {
        if (this.component._swiperManager) {
          this.component._swiperManager.initVideosSwiper();
        }
      });
      return;
    }

    const items = this.cardGenerators.generateCards(data.pillars);
    this.component.items = items || [];
    
    await this.fetchConditionalVideos();
    
    this.component.$nextTick(() => {
      if (this.component._swiperManager) {
        if (this.component.items.length > 0) {
          this.component._swiperManager.initSymptomsSwiper();
        }
        this.component._swiperManager.initVideosSwiper();
      }
    });
  }

  /**
   * Fetch conditional videos from API based on participant's data for the selected date
   */
  async fetchConditionalVideos() {
    try {
      const url = new URL('/api/v1/participant/videos/daily-view', window.location.origin);
      url.searchParams.set('date', this.component.date);
      url.searchParams.set('_t', Date.now());
      
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        credentials: 'include',
        cache: 'no-cache'
      });
      
      if (!response.ok) {
        if (response.status === 401) {
          console.warn('User not authenticated for video data');
          this.component.videos = [];
          return;
        }
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      
      const videoData = await response.json();
      
      if (videoData && videoData.videos && Array.isArray(videoData.videos)) {
        this.component.videos = videoData.videos.map(video => ({
          type: video.video_type || 'youtube',
          id: video.video_id,
          title: video.title,
          subtitle: video.subtitle,
          src: video.video_url
        }));
        
        console.log(`Loaded ${this.component.videos.length} conditional videos for ${this.component.date}`);
      } else {
        this.component.videos = [];
      }
      
    } catch (error) {
      console.error('Failed to fetch conditional videos:', error);
      this.component.videos = [];
    }
  }

}
