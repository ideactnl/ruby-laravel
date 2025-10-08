/**
 * Swiper Manager Module
 * Handles Swiper.js initialization and management for symptoms and videos
 */

export class SwiperManager {
  constructor(component) {
    this.component = component;
  }

  /**
   * Initialize symptoms swiper
   */
  initSymptomsSwiper() {
    if (!window.Swiper || !this.component.$refs?.symSwiper) return;

    try {
      // Destroy existing swiper if it exists
      if (this.component._symSwiper) {
        this.component._symSwiper.destroy(true, true);
      }
      
      this.component._symSwiper = new window.Swiper(this.component.$refs.symSwiper, {
        slidesPerView: 1.3,
        spaceBetween: 16,
        grabCursor: true,
        freeMode: true,
        allowTouchMove: true,
        simulateTouch: true,
        preventClicks: false,
        preventClicksPropagation: false,
        breakpoints: {
          768: {
            slidesPerView: 'auto',
            spaceBetween: 28,
          }
        }
      });
    } catch (error) {
      console.warn('Failed to initialize symptoms swiper:', error);
    }
  }

  /**
   * Initialize videos swiper
   */
  initVideosSwiper() {
    if (!window.Swiper || !this.component.$refs?.vidSwiper) return;

    try {
      // Destroy existing swiper if it exists
      if (this.component._vidSwiper) {
        this.component._vidSwiper.destroy(true, true);
      }
      
      this.component._vidSwiper = new window.Swiper(this.component.$refs.vidSwiper, {
        slidesPerView: 1.3,
        spaceBetween: 16,
        grabCursor: true,
        freeMode: true,
        centeredSlides: false,
        loop: false,
        preventClicks: false,
        preventClicksPropagation: false,
        breakpoints: {
          768: {
            slidesPerView: 'auto',
            spaceBetween: 28,
          }
        }
      });
    } catch (error) {
      console.warn('Failed to initialize videos swiper:', error);
    }
  }

  /**
   * Update swipers when data changes
   */
  updateSwipers() {
    // Delay to ensure DOM is updated
    setTimeout(() => {
      if (this.component._symSwiper) {
        try {
          this.component._symSwiper.update();
        } catch (error) {
          console.warn('Failed to update symptoms swiper:', error);
        }
      }

      if (this.component._vidSwiper) {
        try {
          this.component._vidSwiper.update();
        } catch (error) {
          console.warn('Failed to update videos swiper:', error);
        }
      }
    }, 100);
  }

  /**
   * Destroy swipers on cleanup
   */
  destroySwipers() {
    if (this.component._symSwiper) {
      try {
        this.component._symSwiper.destroy(true, true);
        this.component._symSwiper = null;
      } catch (error) {
        console.warn('Failed to destroy symptoms swiper:', error);
      }
    }

    if (this.component._vidSwiper) {
      try {
        this.component._vidSwiper.destroy(true, true);
        this.component._vidSwiper = null;
      } catch (error) {
        console.warn('Failed to destroy videos swiper:', error);
      }
    }
  }

  /**
   * Reinitialize swipers
   */
  reinitializeSwipers() {
    this.destroySwipers();
    
    // Delay to ensure cleanup is complete
    setTimeout(() => {
      this.initSymptomsSwiper();
      this.initVideosSwiper();
    }, 50);
  }
}
