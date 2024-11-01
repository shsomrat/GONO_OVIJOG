const toggleButton = document.getElementById('navbar-toggle');
const navbarLinks = document.getElementById('navbar-links');

toggleButton.addEventListener('click', function() {
  navbarLinks.classList.toggle('active');
  toggleButton.classList.toggle('active');
});


document.getElementById('complaintForm').addEventListener('submit', function(event) {
  const fileInput = document.getElementById('evidence');
  if (fileInput.files.length > 5) {
    alert('You can upload a maximum of 5 files.');
    event.preventDefault();
  }
});


function goToComplaintForm(department) {
  window.location.href = `complaint_form.php?department=${department}`;
}


function seeAllDepartments() {
  window.location.href = './departments.php';
}


// Load data from localStorage (passed from the previous page)
document.getElementById('name-display').innerText = `Complainant Name: ${localStorage.getItem('name')}`;
document.getElementById('address-display').innerText = `Complainant Address: ${localStorage.getItem('address')}`;
document.getElementById('phone-display').innerText = `Complainant Phone: ${localStorage.getItem('phone')}`;
document.getElementById('subject-display').innerText = `Complaint Subject: ${localStorage.getItem('subject')}`;
document.getElementById('details-display').innerText = `Complaint Details: ${localStorage.getItem('details')}`;

// Generate complaint ID and submission date/time (for demo purposes, you can generate your own ID)
const complaintId = Math.floor(Math.random() * 10000); // Example: random ID between 0 and 9999
const submissionDate = new Date();

// Set complaint ID and submission date/time in the HTML
document.getElementById('complaint-id').innerText = complaintId;
document.getElementById('submission-date').innerText = submissionDate.toLocaleDateString();
document.getElementById('submission-time').innerText = submissionDate.toLocaleTimeString();

// Function to download the PDF
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Complaint Receipt", 10, 10);
    doc.text(`Complaint ID: ${complaintId}`, 10, 20);
    doc.text(`Submission Date: ${submissionDate.toLocaleDateString()}`, 10, 30);
    doc.text(`Submission Time: ${submissionDate.toLocaleTimeString()}`, 10, 40);
    doc.text(`Complainant Name: ${localStorage.getItem('name')}`, 10, 50);
    doc.text(`Complainant Address: ${localStorage.getItem('address')}`, 10, 60);
    doc.text(`Complainant Phone: ${localStorage.getItem('phone')}`, 10, 70);
    doc.text(`Complaint Subject: ${localStorage.getItem('subject')}`, 10, 80);
    doc.text(`Complaint Details: ${localStorage.getItem('details')}`, 10, 90);

    doc.save('complaint-receipt.pdf');
}

// Function to download the complaint details as PDF
function downloadComplaintPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  // Set title font and color
  doc.setFont("Helvetica", "bold");
  doc.setFontSize(16);
  doc.setTextColor(0, 102, 204); // Dark blue color
  doc.text("Complaint Details", 10, 10);

  // Add a line for separation with space before and after
  const titleLineY = 12; // Line Y position
  doc.setLineWidth(0.5);
  doc.line(10, titleLineY, 200, titleLineY); // Line from (x1, y1) to (x2, y2)

  // Set default text color for details
  doc.setTextColor(0, 0, 0); // Black color

  // Check and retrieve complaint information with existence checks
  const complaintIdElement = document.getElementById('complaint-id');
  if (complaintIdElement) {
    doc.setFont("Helvetica", "normal");
    doc.setFontSize(12);
    doc.text(`Complaint ID: ${complaintIdElement.innerText}`, 10, 20);
  }

  const departmentElement = document.getElementById('complaint-department');
  if (departmentElement) {
    doc.text(`Complaint Department: ${departmentElement.innerText}`, 10, 30);
  }

  const overviewParagraphs = document.querySelectorAll('.complaint-overview p');
  const labels = ['Title', 'Filed By', 'Victim Name', 'Victim Email', 'Victim Phone', 'Date Submitted', 'Current Date', 'Days Since Submission', 'Status'];

  labels.forEach((label, index) => {
    if (overviewParagraphs[index]) {
      const text = overviewParagraphs[index].innerText.split(": ")[1] || "N/A";
      doc.text(`${label}: ${text}`, 10, 40 + (index * 10));
    }
  });

  // Add a separation line before the description
  const spaceBeforeDescriptionLine = 8; // Space before the line in mm
  const lineYPosition = 120 + spaceBeforeDescriptionLine; // Line Y position adjusted with space
  doc.line(10, lineYPosition, 200, lineYPosition);

  // Add description with a different font size and color
  const descriptionElement = document.querySelector('.complaint-description p');
  if (descriptionElement) {
    doc.setFontSize(14);
    doc.setTextColor(0, 102, 0); // Dark green color
    doc.text("Description:", 10, lineYPosition + 10); // Position below the line
    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0); // Reset text color to black
    doc.text(descriptionElement.innerText, 10, lineYPosition + 20);
  }

  // Add space before the status updates line
  const updatesStartY = lineYPosition + 40; // Space after the description and line
  doc.setFont("Helvetica", "bold");
  doc.setTextColor(0, 0, 102); // Dark blue for the heading
  doc.text("Status Updates:", 10, updatesStartY);
  doc.setFont("Helvetica", "normal");
  doc.setTextColor(0, 0, 0); // Reset text color to black

  // Add updates if available
  const updates = document.querySelectorAll('.complaint-updates .update');
  if (updates.length > 0) {
    updates.forEach((update, index) => {
      const heading = update.querySelector('h3') ? update.querySelector('h3').innerText : "No Heading";
      const description = update.querySelector('p') ? update.querySelector('p').innerText : "No Description";
      const status = update.querySelector('strong') ? update.querySelector('strong').innerText.split(": ")[1] : "No Status";

      // Update y position for each status update with spacing
      const updateY = updatesStartY + 10 + (index * 30);
      doc.text(`Update ${index + 1}: ${heading}`, 10, updateY);
      doc.text(`Description: ${description}`, 10, updateY + 10);
      doc.text(`Status: ${status}`, 10, updateY + 20);
    });

    // Add a line after the last update with spacing
    const lastUpdateY = updatesStartY + 10 + (updates.length * 30) + 10; // Adjust for spacing after the last update
    doc.setLineWidth(0.5);
    doc.line(10, lastUpdateY, 200, lastUpdateY);
  } else {
    doc.text("No updates available.", 10, updatesStartY + 10);
  }

  // Save the PDF
  doc.save('complaint-details.pdf');
}
