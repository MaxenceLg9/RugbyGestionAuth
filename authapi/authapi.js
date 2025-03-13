// Example JavaScript code to refresh the token periodically

const refreshToken = () => {
    const token = localStorage.getItem('jwtToken'); // Assuming the token is stored in localStorage
    if (token) {
        fetch('http://localhost/RugbyGestionAuth/authapi.php?token=' + token, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status_code === 200) {
                localStorage.setItem('jwtToken', data.data); // Update the token in localStorage
            } else {
                console.error('Token refresh failed:', data.status_message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
};

// Call refreshToken every 30 minutes (1800000 milliseconds)
setInterval(refreshToken, 1800000);