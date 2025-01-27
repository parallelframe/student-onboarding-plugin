# student-onboarding-plugin

## Description
The **Student Onboarding Plugin** is a custom WordPress plugin that provides a secure REST API endpoint to onboard students by handling user creation and sending welcome emails.

---

## Installation Instructions

1. **Download or Clone the Repository:**
   ```bash
   git clone https://github.com/parallelframe/student-onboarding-plugin.git
   ```

2. **Copy Plugin to WordPress Plugins Directory:**
   ```bash
   cp -r student-onboarding-plugin /wp-content/plugins/
   ```

3. **Activate the Plugin:**
   - Log into your WordPress dashboard
   - Navigate to `Plugins > Installed Plugins`
   - Find `Student Onboarding Plugin` and click `Activate`

4. **Set Authentication Credentials:**
   Add the following lines to your `wp-config.php` file:
   ```php
   define('API_USERNAME', 'your_username');
   define('API_PASSWORD', 'your_password');
   ```

---

## Configuration Requirements

To customize the plugin's behavior, you can use the following WordPress actions and filters:

**Actions:**
- `plugin_name_pre_validate_student` – Triggered before data validation.
- `plugin_name_pre_create_student` – Triggered before user creation.
- `plugin_name_post_create_student` – Triggered after user creation.
- `plugin_name_pre_send_welcome_email` – Triggered before sending welcome email.
- `plugin_name_post_send_welcome_email` – Triggered after sending welcome email.

**Filters:**
- `plugin_name_student_validation_rules` – Modify validation rules.
- `plugin_name_pre_user_data` – Modify user data before creation.
- `plugin_name_welcome_email_content` – Customize email content.

---

## API Documentation

### Endpoint
**URL:** `/wp-json/Student_Onboarding_Plugin/v1/students`

**Method:** `POST`

**Headers:**
```
Authorization: Basic base64(username:password)
Content-Type: application/json
```

**Request Payload:**
```json
{
    "student_name": "John Doe",
    "email": "john.doe@example.com",
    "course": "Accounting 101"
}
```

**Success Response (201):**
```json
{
    "success": true,
    "user_id": 123,
    "message": "Student successfully registered",
    "email_status": "sent"
}
```

**Error Response (400/401/500):**
```json
{
    "success": false,
    "error_code": "error_code_string",
    "message": "Detailed error message"
}
```

---

## Example Usage

You can test the API using tools like **Postman** or via a simple curl request:

```bash
curl -X POST http://yourwordpresssite.com/wp-json/Student-Onboarding-Plugin/v1/students \
     -H "Authorization: Basic base64(username:password)" \
     -H "Content-Type: application/json" \
     -d '{"student_name": "Jane Doe", "email": "jane.doe@example.com", "course": "Business 101"}'
```

---

## Testing Instructions

1. Install and activate the plugin.
2. Use Postman or the provided frontend form to send student data.
3. Check the WordPress users list to confirm new registrations.
4. Check the email logs for welcome messages.

---

## Logging
All plugin-related logs can be found in `wp-content/debug.log` if `WP_DEBUG_LOG` is enabled in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```
