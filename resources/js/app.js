import Alpine from 'alpinejs';
import 'flowbite';

window.Alpine = Alpine;

Alpine.start();

const navGlass = document.querySelector('.nav-glass');

if (navGlass) {
    const syncNavSolid = () => {
        navGlass.classList.toggle('is-solid', window.scrollY > 12);
    };

    syncNavSolid();
    window.addEventListener('scroll', syncNavSolid, { passive: true });
}

document.addEventListener('pointermove', (event) => {
    const card = event.target instanceof Element ? event.target.closest('.glass-hover') : null;

    if (! card) {
        return;
    }

    const rect = card.getBoundingClientRect();
    card.style.setProperty('--glass-glow-x', `${event.clientX - rect.left}px`);
    card.style.setProperty('--glass-glow-y', `${event.clientY - rect.top}px`);
});
