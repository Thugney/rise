<?php if (get_setting("enable_footer")) { ?>
    <!-- Enhanced Footer Structure -->
    <footer style="background-color: #1c2026; color: white; padding: 20px 0; text-align: left;">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Eriteach</h5>
                    <p>Eriteach er et ledende markedsføringsbyrå i Norge som spesialiserer seg på SEO, sosiale medier og e-handelsløsninger.</p>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/eriteach" target="_blank"><i class="fa fa-facebook"></i></a>
                        <a href="https://www.instagram.com/eriteach_official/" target="_blank"><i class="fa fa-instagram"></i></a>
                        <a href="https://www.x.com/eriteach" target="_blank"><i class="fa fa-twitter"></i></a>
                        <a href="https://www.youtube.com/@Helentube" target="_blank"><i class="fa fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Produkter</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="https://learn.eriteach.com/" style="color: white;">Learn</a></li>
                        <li><a href="https://status.eriteach.com/" style="color: white;">Status</a></li>
                        <li><a href="https://shop.eriteach.com/" style="color: white;">Shop</a></li>
                        <li><a href="https://eriteach.com/crm/" style="color: white;">CRM</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Bedrift</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="https://eriteach.com/about" style="color: white;">Om oss</a></li>
                        <li><a href="https://eriteach.com/contact" style="color: white;">Kontakt</a></li>
                        <li><a href="https://eriteach.com/blog" style="color: white;">Blogg</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Support</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="https://eriteach.com/help" style="color: white;">Hjelp</a></li>
                        <li><a href="https://eriteach.com/privacy" style="color: white;">Personvern</a></li>
                        <li><a href="https://eriteach.com/terms" style="color: white;">Vilkår</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-3">
                © 2025 Eriteach. All rights reserved.
            </div>
        </div>
    </footer>
<?php } ?>

<!-- Enhanced Cookie Consent Banner -->
<div id="cookie-consent-banner" style="display: none; position: fixed; bottom: 0; left: 0; width: 100%; background-color: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #dee2e6; z-index: 1000;">
    <p>We use cookies to improve your experience. Choose your preferences:</p>
    <label><input type="checkbox" checked disabled> Essential (Always on)</label>
    <label><input type="checkbox" id="analytics-consent"> Analytics</label>
    <label><input type="checkbox" id="ads-consent"> Advertising</label>
    <button onclick="saveConsent()" style="margin-left: 10px; background-color: #007bff; color: white; border: none; padding: 5px 10px; cursor: pointer;">Save</button>
    <button onclick="acceptAll()" style="margin-left: 10px; background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer;">Accept All</button>
    <button onclick="rejectAll()" style="margin-left: 10px; background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer;">Reject All</button>
</div>

<script>
function saveConsent() {
    if (document.getElementById('analytics-consent').checked) grantAnalyticsConsent();
    if (document.getElementById('ads-consent').checked) grantAdConsent();
    localStorage.setItem('cookieConsent', 'custom');
    document.getElementById('cookie-consent-banner').style.display = 'none';
}

function acceptAll() {
    grantAnalyticsConsent();
    grantAdConsent();
    localStorage.setItem('cookieConsent', 'accepted');
    document.getElementById('cookie-consent-banner').style.display = 'none';
}

function rejectAll() {
    localStorage.setItem('cookieConsent', 'rejected');
    document.getElementById('cookie-consent-banner').style.display = 'none';
}

if (!localStorage.getItem('cookieConsent')) {
    document.getElementById('cookie-consent-banner').style.display = 'block';
}
</script>