import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function() {
    const periodicityTypeRadios = document.querySelectorAll('input[name="task[periodicity]"]');
    const weeklyDaysSelector = document.querySelector('.weekly-days-selector');

    function toggleWeeklyDaysVisibility() {
        const isWeekly = document.querySelector('input[name="task[periodicity]"]:checked').value === 'weekly';
        weeklyDaysSelector.style.display = isWeekly ? 'block' : 'none';
    }

    toggleWeeklyDaysVisibility();

    periodicityTypeRadios.forEach(function(radio) {
        radio.addEventListener('change', toggleWeeklyDaysVisibility);
    });
});