// script.js
function saveUserInfo() {
  const userID = document.getElementById('userID').value;
  const userPassword = document.getElementById('userPassword').value;
  if (userID && userPassword) {
    localStorage.setItem('userID', userID);
    localStorage.setItem('userPassword', userPassword);
    alert('User information saved successfully!');
  } else {
    alert('Please fill in both fields.');
  }
}

function retrieveUserInfo() {
  const savedUserID = localStorage.getItem('userID');
  const savedUserPassword = localStorage.getItem('userPassword');
  if (savedUserID && savedUserPassword) {
    document.getElementById('userID').value = savedUserID;
    document.getElementById('userPassword').value = savedUserPassword;
    alert('User information retrieved successfully!');
  } else {
    alert('No user information found.');
  }
}
