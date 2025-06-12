// scripts.js

function fetchIpAddress() {
    const clientIpElement = document.getElementById('clientIpAddress');
    if (!clientIpElement) {
        // Element tidak ditemukan di halaman ini, tidak perlu melanjutkan.
        return;
    }

    fetch('https://api.ipify.org?format=json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            clientIpElement.textContent = data.ip;
        })
        .catch(error => {
            clientIpElement.textContent = 'Unavailable';
            console.error('Error fetching IP:', error);
        });
}

function displayDateTime() {
    const dateTimeElement = document.getElementById('datetime');
    if (!dateTimeElement) {
        // Element tidak ditemukan di halaman ini, tidak perlu melanjutkan.
        return;
    }

    const now = new Date();
    const preferredLang = localStorage.getItem('preferredLanguage') || 'en';
    let localeToUse = preferredLang === 'id' ? 'id-ID' : 'en-US';

    const options = { dateStyle: 'full', timeStyle: 'long' };
    const formattedDateTime = now.toLocaleString(localeToUse, options);
    dateTimeElement.textContent = formattedDateTime;
}

function updateFooterYear() {
    const yearElement = document.getElementById('year');
    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchIpAddress();
    displayDateTime();
    updateFooterYear();

    if (document.getElementById('datetime')) {
        setInterval(displayDateTime, 1000);
    }
});