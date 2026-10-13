// Backs <x-countdown>: counts down to a moment on the browser's clock and
// formats what is left as a rounded-up, localized duration in its largest
// unit ("42 seconds", "2 minutes", "1 hour").
document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', ({ until, locale }) => ({
        remaining: 0,

        init() {
            this.tick();

            const timer = setInterval(() => {
                this.tick();

                if (this.remaining === 0) {
                    clearInterval(timer);
                }
            }, 1000);
        },

        tick() {
            this.remaining = Math.max(0, Math.ceil(until - Date.now() / 1000));
        },

        countdown() {
            const [unit, count] =
                this.remaining < 60
                    ? ['second', this.remaining]
                    : this.remaining < 3600
                      ? ['minute', Math.ceil(this.remaining / 60)]
                      : ['hour', Math.ceil(this.remaining / 3600)];

            return new Intl.NumberFormat(locale, { style: 'unit', unit, unitDisplay: 'long' }).format(count);
        },
    }));
});
