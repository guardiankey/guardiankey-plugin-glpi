# GuardianKey AuthSecurity for GLPI

This plugin integrates [GuardianKey AuthSecurity](https://guardiankey.io) into GLPI, protecting the login process by analyzing risk using AI, geolocation, behavioral authentication, and threat intelligence.

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
- An account on [GuardianKey Platform](https://gdn.guardiankey.io)
- Required configuration parameters:
  - `orgid`
  - `authgroupid`
  - `key`
  - `iv`

---

## 📦 Installation

### Requirements

- Tested with GLPI version 10.0.20

### If you are **not** using Docker

1. **Clone or download this repository**:

    ```bash
    git clone https://github.com/guardiankey/guardiankey-plugin-glpi.git
    ```

2. **Rename and Compress the plugin folder** Rename de folder `guardiankey-plugin-glpi/` to `guardiankeyauth` and compress it as a ZIP file.

3. **In GLPI**:

    - Go to **Setup > Plugins**
    - Click **Install a new plugin**
    - Upload the ZIP file
    - Click **Install**, then **Enable**

---

### If you are using **GLPI with Docker**

The GLPI Docker environment does **not** support uploading plugins via ZIP file.  
Instead, you must copy the plugin files directly into the container or the appropriate volume:

1. **Clone this repository** on your host machine:

    ```bash
    git clone https://github.com/guardiankey/guardiankey-plugin-glpi.git
    ```

2. **Rename and Copy the plugin folder** Rename de folder `guardiankey-plugin-glpi/` to `guardiankeyauth` and 
   copy it into the GLPI plugins directory. This is usually at `/var/www/html/glpi/plugins/` inside the container.

    - If using Docker volumes, copy to the mapped `plugins` directory on your host.
    - If you have shell access to the container, you can use:

        ```bash
        docker cp guardiankey-plugin-glpi your_glpi_container:/var/www/html/plugins/guardiankeyauth
        ```

3. **Restart the GLPI container** (if needed).

4. **In GLPI**:

    - Go to **Setup > Plugins**
    - Find **GuardianKey AuthSecurity** in the list
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

## 🛠 Development Notes

- Main logic is in `guardiankeyauth.class.php`
- Configuration is stored in `glpi_plugin_guardiankeyauth_configs`
- GuardianKey API client class is in `guardiankey.class.php` (can be customized)

---

## 🙋 Support

This plugin was developed by GuardianKey Cybersecurity.  
For support or questions, contact:

- Email: contact@guardiankey.io
- Website: [https://guardiankey.io](https://guardiankey.io)
- Documentation: https://guardiankey.io/docs

