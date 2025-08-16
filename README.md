# GuardianKey AuthSecurity for GLPI

This plugin integrates [GuardianKey AuthSecurity](https://guardiankey.io) into GLPI, protecting the login process by analyzing risk using AI, geolocation, behavioral biometrics, and other signals.

---

## 🔐 Features

- Sends login events to GuardianKey API
- Receives risk score and recommended action:
  - `ACCEPT`: login proceeds normally
  - `NOTIFY` / `HARD-NOTIFY`: warning shown to the user
  - `BLOCK`: login is automatically denied
- Real-time protection against anomalous behavior

---

## ✅ Requirements

- GLPI version **9.4+** (tested up to 10.x)
- An account on [GuardianKey Platform](https://guardiankey.io)
- Required configuration parameters:
  - `orgid`
  - `authgroupid`
  - `key`
  - `iv`

---

## 📦 Installation

1. **Clone or download this repository**:

    ```bash
    git clone https://github.com/youruser/guardiankeyauth-glpi.git
    ```
2. **Compress the plugin folder** (`guardiankeyauth/`) as a ZIP file.

3. **In GLPI**:

    - Go to **Setup > Plugins**
    - Click **Install a new plugin**
    - Upload the ZIP file
    - Click **Install**, then **Enable**

---

## ⚙️ Configuration

After enabling the plugin, go to:

- **Setup > Plugins > GuardianKey AuthSecurity**

Fill in the following fields:

- **Organization ID**
- **AuthGroup ID**
- **Key**
- **IV**

Click **Save**.

> **❗ Note:** The plugin will not function without valid configuration.

---

## 🔄 How it works

During each login attempt:

- The plugin collects:
  - Username
  - Client IP address
  - User-Agent (browser)
- Sends the data to GuardianKey's `/checkaccess` API
- Based on the response:
  - Allows login
  - Allows with warning
  - Blocks login

---

## 📤 Example of API response

```json
{
  "response": "BLOCK",
  "risk": 87,
  "details": "Login from unknown source with unusual behavior pattern."
}
```

---

## 🛠 Development Notes

- Main logic is in `guardiankeyauth.class.php`
- Configuration is stored in `glpi_plugin_guardiankeyauth_configs`
- GuardianKey API client class is in `guardiankey.class.php` (can be customized)

---

## 🙋 Support

This plugin was developed by [Your Name / Company].  
For support or questions, contact:

- Email: contact@guardiankey.io
- Website: [https://guardiankey.io](https://guardiankey.io)
