<?php
// Include database configuration
include '../admin/config.php';
include './includes/header.php';
?>

<section class="hero-section">
    <div class="hero container">
        <div class="hero-text">
            <h1>Empowering Citizens for a Transparent Tomorrow</h1>
            <h2>Streamline Your Voice – Track, Resolve, and Transform Public Grievances in Real-Time</h2>
            <p>A dynamic platform for streamlined public grievance reporting, empowering citizens to voice their concerns easily. This system provides real-time tracking of complaints, ensuring timely resolutions and fostering transparency. By enhancing accountability and communication between citizens and authorities, it promotes a more responsive and effective approach to addressing public issues.</p>
            <a href="departments.php" class="cta-btn">Submit Your Complaint <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <img src="./assets/img/hero-g.webp" alt="Justice Image">
    </div>
</section>

<section class="process-section">
    <div class="container">
        <h2 class="process-heading">How We Handle Your Complaint</h2>
        <div class="process-wrapper">
            <div class="process-step">
                <div class="step-number">1</div>
                <h3>Submit Complaint</h3>
                <p>Fill out our online form with details about your issue and submit it.</p>
            </div>
            <div class="process-step">
                <div class="step-number">2</div>
                <h3>Verification & Review</h3>
                <p>Our team verifies the complaint and reviews the provided information.</p>
            </div>
            <div class="process-step">
                <div class="step-number">3</div>
                <h3>Investigation</h3>
                <p>We conduct a thorough investigation, gathering more details as necessary.</p>
            </div>
            <div class="process-step">
                <div class="step-number">4</div>
                <h3>Action Taken</h3>
                <p>We take the appropriate action, either mediating or escalating the matter.</p>
            </div>
            <div class="process-step">
                <div class="step-number">5</div>
                <h3>Result & Publication</h3>
                <p>The final outcome is communicated to you and may be published on the site.</p>
            </div>
        </div>
    </div>
</section>

<section class="select-department">
    <h2>Select a Department</h2>
    <div class="departments container">
        <?php
        // Fetch 3 departments from the database
        $sql_departments = "SELECT id, title, description, thumbnail FROM departments LIMIT 3";
        $result_departments = $conn->query($sql_departments);

        if ($result_departments->num_rows > 0):
            while ($department = $result_departments->fetch_assoc()):
        ?>
            <div class="department-card" onclick="goToComplaintForm('<?php echo htmlspecialchars($department['id']); ?>')">
                <img src="../admin/<?php echo htmlspecialchars($department['thumbnail']); ?>" alt="<?php echo htmlspecialchars($department['title']); ?>">
                <div class="info">
                    <h3><?php echo htmlspecialchars($department['title']); ?></h3>
                    <p><?php echo htmlspecialchars($department['description']); ?></p>
                </div>
            </div>
        <?php endwhile; else: ?>
            <p>No departments available.</p>
        <?php endif; ?>
    </div>
    <button class="see-all-departments" onclick="seeAllDepartments()">See All Departments</button>
</section>


</script>

    <section class="testimonial-section">
      <div class="container">
        <h2 class="testimonial-heading">What People Are Saying</h2>
        <div class="testimonial-carousel">
          <!-- Testimonial 1 -->
          <div class="testimonial-item">
            <div class="testimonial-content">
              <p>"This organization helped me resolve my complaint against local officials quickly and effectively. I'm very grateful for their support!"</p>
            </div>
            <div class="testimonial-author">
              <img src="./assets/img/person/p-3.jpg" alt="Author 1" class="author-img">
              <div class="author-details">
                <h4>John Doe</h4>
                <span>Beneficiary</span>
              </div>
            </div>
          </div>
          <!-- Testimonial 2 -->
          <div class="testimonial-item">
            <div class="testimonial-content">
              <p>"They handled my case professionally and kept me informed throughout the entire process. Highly recommended!"</p>
            </div>
            <div class="testimonial-author">
              <img src="./assets/img/person/p-2.jpg" alt="Author 2" class="author-img">
              <div class="author-details">
                <h4>Jane Smith</h4>
                <span>Complainant</span>
              </div>
            </div>
          </div>
          <!-- Testimonial 3 -->
          <div class="testimonial-item">
            <div class="testimonial-content">
              <p>"Thanks to this amazing organization, I was able to get justice for my issue. I truly appreciate their dedication!"</p>
            </div>
            <div class="testimonial-author">
              <img src="./assets/img/person/p-1.jpg" alt="Author 3" class="author-img">
              <div class="author-details">
                <h4>Emily Johnson</h4>
                <span>Case Resolved</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="supported-brands" class="py-5">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-4 col-sm-6 mb-4">
            <img src="./assets/img/brands/b-2 (1).png" alt="Brand 1" class="img-fluid" />
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <img src="./assets/img/brands/b-2 (3).png" alt="Brand 2" class="img-fluid" />
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <img src="./assets/img/brands/b-2 (2).png" alt="Brand 3" class="img-fluid" />
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <img src="./assets/img/brands/b-2 (4).png" alt="Brand 4" class="img-fluid" />
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <img src="./assets/img/brands/b-2 (5).png" alt="Brand 5" class="img-fluid" />
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <img src="./assets/img/brands/b-2(6).png" alt="Brand 6" class="img-fluid" />
          </div>
        </div>
      </div>
    </section>
    <section class="case-study-section">
      <div class="container">
        <h2 class="case-study-heading">Case Studies</h2>
        <div class="case-studies">
          <div class="case-study">
            <img src="./assets/img/case_study/cs-2.jpg" alt="Case Study 1">
            <div class="case-content">
              <h3>Case Study: Successful Resolution of Local Complaint</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vehicula erat sit...</p>
              <a href="#" class="read-more">Read More <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
          </div>
          <div class="case-study">
            <img src="./assets/img/case_study/cs-1.jpg" alt="Case Study 2">
            <div class="case-content">
              <h3>Case Study: Protecting Vulnerable People</h3>
              <p>Donec sit amet accumsan tortor. Vestibulum vel velit ante. Integer ac ipsum vitae...</p>
              <a href="#" class="read-more">Read More<i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
          </div>
          <div class="case-study">
            <img src="./assets/img/case_study/cs-3.jpg" alt="Case Study 3">
            <div class="case-content">
              <h3>Case Study: Holding Officials Accountable</h3>
              <p>Phasellus dignissim magna sit amet dui placerat, ac elementum eros vestibulum...</p>
              <a href="#" class="read-more">Read More<i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

<?php
include './includes/footer.php';
?>