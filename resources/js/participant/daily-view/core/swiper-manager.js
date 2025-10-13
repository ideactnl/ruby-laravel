/**
 * Swiper Manager Module
 * Handles Swiper.js initialization and management for symptoms and videos
 */

export class SwiperManager {
    constructor(component) {
        this.component = component;
        this.lastTouchTime = 0;
        this.lastTouchX = 0;
        this.velocity = 0;
    }

    triggerHaptic(type = "light") {
        if (window.innerWidth <= 768 && "vibrate" in navigator) {
            const patterns = {
                light: 10,
                medium: 25,
                strong: [30, 20, 30],
                continuous: [15, 15, 15, 15, 15],
            };
            navigator.vibrate(patterns[type] || patterns.light);
        }
    }

    attachHapticFeedback(swiper) {
        if (!swiper) return;

        swiper.on("slideChange", () => this.triggerHaptic("medium"));
        swiper.on(
            "sliderMove",
            () => swiper.isTouched && this.triggerHaptic("light")
        );
        swiper.on("reachEnd", () => this.triggerHaptic("strong"));
        swiper.on("reachBeginning", () => this.triggerHaptic("strong"));
    }

    /**
     * Attach dynamic speed control (based on swipe acceleration)
     */
    attachDynamicSpeed(swiper) {
        if (!swiper) return;

        let startX = 0;
        let startTime = 0;
        let lastX = 0;
        let lastTime = 0;

        const getX = (e) =>
            e.touches?.[0]?.clientX ?? e.clientX ?? e.pageX ?? 0;

        swiper.on("touchStart", (e) => {
            startX = getX(e);
            lastX = startX;
            startTime = performance.now();
            lastTime = startTime;
        });

        swiper.on("touchMove", (e) => {
            const now = performance.now();
            const x = getX(e);
            lastX = x;
            lastTime = now;
        });

        swiper.on("touchEnd", () => {
            const dx = lastX - startX;
            const dt = lastTime - startTime;
            if (dt <= 0) return;

            const velocity = dx / dt;

            const absVel = Math.abs(velocity);
            const momentum = Math.min(Math.max(absVel * 2000, 300), 1200);
            const direction = velocity > 0 ? "prev" : "next";

            swiper.setTransition(momentum);

            if (absVel > 1.2) this.triggerHaptic("strong");
            else if (absVel > 0.6) this.triggerHaptic("medium");
            else this.triggerHaptic("light");

            if (absVel > 0.2) {
                if (direction === "next") swiper.slideNext(momentum);
                else swiper.slidePrev(momentum);
            } else {
                swiper.slideToClosest(momentum);
            }
        });
    }

    /**
     * Common swiper config
     */
    getCommonSwiperConfig() {
        return {
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
                    slidesPerView: "auto",
                    spaceBetween: 28,
                },
            },
        };
    }

    /**
     * Initialize symptoms swiper
     */
    initSymptomsSwiper() {
        if (!window.Swiper || !this.component.$refs?.symSwiper) return;

        try {
            if (this.component._symSwiper)
                this.component._symSwiper.destroy(true, true);

            this.component._symSwiper = new window.Swiper(
                this.component.$refs.symSwiper,
                this.getCommonSwiperConfig()
            );

            this.attachHapticFeedback(this.component._symSwiper);
            this.attachDynamicSpeed(this.component._symSwiper);
        } catch (error) {
            console.warn("Failed to initialize symptoms swiper:", error);
        }
    }

    /**
     * Initialize videos swiper
     */
    initVideosSwiper() {
        if (!window.Swiper || !this.component.$refs?.vidSwiper) return;

        try {
            if (this.component._vidSwiper)
                this.component._vidSwiper.destroy(true, true);

            this.component._vidSwiper = new window.Swiper(
                this.component.$refs.vidSwiper,
                this.getCommonSwiperConfig()
            );

            this.attachHapticFeedback(this.component._vidSwiper);
            this.attachDynamicSpeed(this.component._vidSwiper);
        } catch (error) {
            console.warn("Failed to initialize videos swiper:", error);
        }
    }

    updateSwipers() {
        setTimeout(() => {
            this.component._symSwiper?.update();
            this.component._vidSwiper?.update();
        }, 100);
    }

    destroySwipers() {
        this.component._symSwiper?.destroy(true, true);
        this.component._vidSwiper?.destroy(true, true);
        this.component._symSwiper = null;
        this.component._vidSwiper = null;
    }

    reinitializeSwipers() {
        this.destroySwipers();
        setTimeout(() => {
            this.initSymptomsSwiper();
            this.initVideosSwiper();
        }, 50);
    }
}
