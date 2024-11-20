const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const app = express();
const port = 3000;

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Endpoint to register a user
app.post('/register', (req, res) => {
  const userData = req.body;
  const users = JSON.parse(fs.readFileSync('users.json', 'utf-8') || '[]');

  // Check if user ID already exists
  if (users.find(user => user.userID === userData.userID)) {
    return res.status(400).json({ message: 'User ID already exists' });
  }

  users.push(userData);
  fs.writeFileSync('users.json', JSON.stringify(users));
  res.status(201).json({ message: 'User registered successfully' });
});

// Endpoint to validate login
app.post('/login', (req, res) => {
  const { userID, userPassword } = req.body;
  const users = JSON.parse(fs.readFileSync('users.json', 'utf-8') || '[]');

  const user = users.find(user => user.userID === userID && user.userPassword === userPassword);
  if (user) {
    res.status(200).json({ message: 'Login successful' });
  } else {
    res.status(400).json({ message: 'Invalid user ID or password' });
  }
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}/`);
});
