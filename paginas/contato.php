<main class="contact-page">
    <section class="contact-hero">
        <h1>Visit Our Refuge</h1>
        <p>Whether you want to talk about our bean selection, reserve a spot for an event, or simply stop by for a chat, we would love to hear from you.</p>
    </section>

    <section class="contact-shell">
        <div class="contact-layout">
            <div class="contact-form-card">
                <h2>Send a message</h2>

                <form class="contact-form" id="contactForm" action="#" method="POST">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" placeholder="Your name" required>
                        </div>

                        <div class="form-field">
                            <label for="email">E-mail</label>
                            <input id="email" name="email" type="email" placeholder="you@email.com" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject">
                            <option>Questions about coffee</option>
                            <option>Events and reservations</option>
                            <option>Wholesale</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="How can we help?" required></textarea>
                    </div>

                    <button type="submit" class="primary-btn">Send message</button>
                </form>

                <script>
                    document.getElementById('contactForm').addEventListener('submit', function(event) {
                        event.preventDefault();

                        alert('Thank you for your message! We will get back to you shortly.');

                        this.reset();
                    });
                </script>
            </div>

            <aside class="contact-sidebar">
                <div class="contact-card">
                    <h3>Contact information</h3>
                    <ul class="contact-list">
                        <li>
                            <span class="label">Address</span>
                            <p>123 Coffee Street, Bean City, CO 12345</p>
                        </li>
                        <li>
                            <span class="label">Phone</span>
                            <p>(123) 456-7890</p>
                        </li>
                        <li>
                            <span class="label">E-mail</span>
                            <p><a href="mailto:info@refugecoffee.com">info@refugecoffee.com</a></p>
                        </li>
                    </ul>
                </div>

                <div class="contact-card">
                    <h3>Hours of operation</h3>
                    <ul class="hours-list">
                        <li><span>Monday - Friday</span><strong>7:00 AM - 8:00 PM</strong></li>
                        <li><span>Saturday</span><strong>8:00 AM - 6:00 PM</strong></li>
                        <li><span>Sunday</span><strong>8:00 AM - 4:00 PM</strong></li>
                    </ul>
                </div>
            </aside>
        </div>

        <div class="map-card">
            <h2>Find us on the map</h2>
            <iframe
                src="https://www.google.com/maps?q=123%20Coffee%20Street%2C%20Bean%20City%2C%20CO%2012345&output=embed"
                title="Dark Cafeteria location"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen>
            </iframe>
        </div>
    </section>
</main>