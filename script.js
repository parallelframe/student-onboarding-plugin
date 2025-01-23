document.getElementById('onboarding-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const studentName = document.getElementById('student_name').value;
    const email = document.getElementById('email').value;
    const course = document.getElementById('course').value;

    const requestData = {
        student_name: studentName,
        email: email,
        course: course
    };

    const responseMessage = document.getElementById('response-message');
    responseMessage.textContent = "Processing...";

    try {
        const response = await fetch('http://yourwordpresssite.com/wp-json/plugin-name/v1/students', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa('your_username:your_password')
            },
            body: JSON.stringify(requestData)
        });

        const responseData = await response.json();

        if (response.ok) {
            responseMessage.textContent = responseData.message;
            responseMessage.style.color = 'green';
        } else {
            responseMessage.textContent = responseData.message || 'Something went wrong';
            responseMessage.style.color = 'red';
        }
    } catch (error) {
        responseMessage.textContent = 'Failed to connect to the server';
        responseMessage.style.color = 'red';
    }
});
