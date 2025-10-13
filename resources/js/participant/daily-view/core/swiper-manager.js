/**
 * Swiper Manager Module
 * Handles Swiper.js initialization and management for symptoms and videos
 */

export class SwiperManager {
    constructor(component) {
        this.component = component;
        this.hapticCooldown = false;
        this.hapticInterval = null;
    }

    triggerHaptic(type = "light") {
        if (window.innerWidth > 768 || !("vibrate" in navigator)) return;

        const patterns = {
            light: 8,
            medium: 20,
            strong: [30, 20, 30],
        };

        if (this.hapticCooldown) return;
        this.hapticCooldown = true;
        navigator.vibrate(patterns[type] || patterns.light);
        setTimeout(() => (this.hapticCooldown = false), 60);
    }

    attachHapticFeedback(swiper) {
        if (!swiper) return;

        swiper.on("slideChange", () => this.triggerHaptic("medium"));
        swiper.on("reachEnd", () => this.triggerHaptic("strong"));
        swiper.on("reachBeginning", () => this.triggerHaptic("strong"));
    }

    /**
     * Continuous smooth haptic feedback during fast swipes
     */
    attachDynamicSpeed(swiper) {
        if (!swiper) return;

        let startX = 0;
        let lastX = 0;
        let startTime = 0;
        let lastTime = 0;
        let moveVelocity = 0;

        const getX = (e) =>
            e.touches?.[0]?.clientX ?? e.clientX ?? e.pageX ?? 0;

        swiper.on("touchStart", (e) => {
            startX = getX(e);
            lastX = startX;
            startTime = lastTime = performance.now();
            moveVelocity = 0;

            if (this.hapticInterval) clearInterval(this.hapticInterval);
            this.hapticInterval = setInterval(() => {
                if (Math.abs(moveVelocity) > 0.2) this.triggerHaptic("light");
            }, 120);
        });

        swiper.on("touchMove", (e) => {
            const now = performance.now();
            const x = getX(e);
            moveVelocity = (x - lastX) / (now - lastTime);
            lastX = x;
            lastTime = now;
        });

        swiper.on("touchEnd", () => {
            if (this.hapticInterval) {
                clearInterval(this.hapticInterval);
                this.hapticInterval = null;
            }

            const dx = lastX - startX;
            const dt = lastTime - startTime;
            if (dt <= 0) return;

            const velocity = dx / dt;
            const absVel = Math.abs(velocity);
            const momentum = Math.min(Math.max(absVel * 2000, 300), 1000);
            const direction = velocity > 0 ? "prev" : "next";

            swiper.setTransition(momentum);

            if (absVel > 1.5) this.triggerHaptic("strong");
            else if (absVel > 0.8) this.triggerHaptic("medium");
            else this.triggerHaptic("light");

            if (absVel > 0.2) {
                if (direction === "next") swiper.slideNext(momentum);
                else swiper.slidePrev(momentum);
            } else {
                swiper.slideToClosest(momentum);
            }
        });
    }

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

    initSymptomsSwiper() {
        if (!window.Swiper || !this.component.$refs?.symSwiper) return;

        this.component._symSwiper?.destroy(true, true);
        this.component._symSwiper = new window.Swiper(
            this.component.$refs.symSwiper,
            this.getCommonSwiperConfig()
        );

        this.attachHapticFeedback(this.component._symSwiper);
        this.attachDynamicSpeed(this.component._symSwiper);
    }

    initVideosSwiper() {
        if (!window.Swiper || !this.component.$refs?.vidSwiper) return;

        this.component._vidSwiper?.destroy(true, true);
        this.component._vidSwiper = new window.Swiper(
            this.component.$refs.vidSwiper,
            this.getCommonSwiperConfig()
        );

        this.attachHapticFeedback(this.component._vidSwiper);
        this.attachDynamicSpeed(this.component._vidSwiper);
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
        if (this.hapticInterval) clearInterval(this.hapticInterval);
    }

    reinitializeSwipers() {
        this.destroySwipers();
        setTimeout(() => {
            this.initSymptomsSwiper();
            this.initVideosSwiper();
        }, 50);
    }
}
