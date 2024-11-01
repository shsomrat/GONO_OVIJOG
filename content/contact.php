<?php
include './includes/header.php';
?>
    <section class="contact-us">
        <div class="contact-container">
            <h1>Contact Us</h1>
            <p>If you have any questions or concerns, feel free to reach out to us using the form below or through our contact information.</p>

            <!-- Office Image -->
            <div class="office-image">
                <img src="./assets/img/office.png" alt="Our Office">
            </div>

            <div class="contact-info">
                <h2>Our Contact Information</h2>
                <p><strong>Address:</strong> 123 Main Street, Your City, Your Country</p>
                <p><strong>Phone:</strong> +1 234 567 890</p>
                <p><strong>Email:</strong> contact@yourorganization.org</p>
            </div>

            <!-- Contact Form -->
            <form action="#" method="POST" class="contact-form">
                <h2>Get in Touch</h2>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="4" required></textarea>

                <button type="submit">Send Message</button>
            </form>

            <!-- Map Section -->
            <div class="map">
                <h2>Find Us Here</h2>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345090804!2d144.9537353156397!3d-37.81720997975188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0c14c8ef%3A0x5045675218cee7ef!2sYour%20Organization!5e0!3m2!1sen!2sus!4v1624550122253!5m2!1sen!2sus"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>
<?php
include './includes/footer.php';
?>
