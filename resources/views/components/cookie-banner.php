<div id="cookie-consent-banner" class="cookie-banner">
    <div class="cookie-content">
        <div class="cookie-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/>
                <path d="M8.5 8.5v.01"/>
                <path d="M16 15.5v.01"/>
                <path d="M12 12v.01"/>
                <path d="M11 17v.01"/>
                <path d="M7 14v.01"/>
            </svg>
        </div>
        <div class="cookie-text">
            <h4>We value your privacy</h4>
            <p>
                We use essential cookies to make our site work. We'd also like to use optional cookies to improve your experience. 
                You can read more in our <a href="/privacy">Privacy Policy</a>.
            </p>
        </div>
        <div class="cookie-actions">
            <button id="btn-decline-cookies" class="btn-decline">Essential Only</button>
            <button id="btn-accept-cookies" class="btn-accept">Accept All</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const banner = document.getElementById('cookie-consent-banner');
    const acceptBtn = document.getElementById('btn-accept-cookies');
    const declineBtn = document.getElementById('btn-decline-cookies');

    if (!banner || !acceptBtn || !declineBtn) return;

    // Check if consent has already been given
    if (!getCookie('cookie_consent')) {
        // Show the banner with a smooth animation delay
        setTimeout(() => {
            banner.classList.add('show');
        }, 800);
    }

    acceptBtn.addEventListener('click', () => {
        setCookie('cookie_consent', 'accepted', 365);
        hideBanner();
        // Insert analytics initialization here if applicable in the future
    });

    declineBtn.addEventListener('click', () => {
        setCookie('cookie_consent', 'essential_only', 365);
        hideBanner();
    });

    function hideBanner() {
        banner.classList.remove('show');
        setTimeout(() => {
            banner.style.display = 'none';
        }, 500); // Matches the CSS transition duration
    }

    function getCookieDomain() {
        const host = window.location.hostname;
        const parts = host.split('.');
        // Check if it's not localhost and not an IP address
        if (parts.length >= 2 && host !== 'localhost' && !/^\d{1,3}(\.\d{1,3}){3}$/.test(host)) {
            return '; domain=.' + parts.slice(-2).join('.');
        }
        return '';
    }

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        const secure = window.location.protocol === 'https:' ? '; Secure' : '';
        const domain = getCookieDomain();
        document.cookie = name + "=" + (value || "") + expires + "; path=/" + domain + "; SameSite=Lax" + secure;
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i=0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});
</script>
