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
  window.location.href = `complaint-form.html?department=${department}`;
}


function seeAllDepartments() {
  // Redirect to the page displaying all departments
  window.location.href = 'departments.html'; // Update with your actual URL
}
