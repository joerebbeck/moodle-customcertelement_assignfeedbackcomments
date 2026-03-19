# Assignment Feedback Comments — Custom Certificate Element

A Moodle [mod_customcert](https://moodle.org/plugins/mod_customcert) element plugin that renders a student's assignment **feedback comment** text directly onto a generated PDF certificate.

---

## 🌟 Features

*   **Rich Formatting preservation**: Preserves safe inline HTML tags (like bolding, italics, paragraphs, and list layouts) when rendering onto PDFs instead of bulk stripping.
*   **Automatic Truncation Handling**: Includes a configurable character limit setting with automatic "Read more online" URL appended if threshold limits are exceeded.
*   **Performance Cache Integrates**: Uses indexed fast-queries with local cache storage pipelines built natively into database configurations to boost layout loads.
*   **Compatibility V2 ready**: Wired correctly out-of-the-box with conditionals supporting upcoming core `Element System v2` layout specifications node.

---

## 📋 Requirements

| Dependency | Version |
|---|---|
| Moodle | 4.1+ |
| mod_customcert | 4.1+ (supports Element System v2 for 5.2+) |
| mod_assign | Must be installed and enabled |

---

## ⚙️ Configuration & Usage

Once installed to your moodle build, the element will appear as **"Assignment Feedback Comments"** inside the template bundle items menu.

Upon adding the element to a cert template:
1.  **Assignment ID**: Choose the target assignment mapping from the dropdown directory.
2.  **Character Limit**: *(Optional)* Enter the maximum allowed characters. Type `0` to display full feedback length boundaries.

## ⚠️ Limitations & Fallbacks

This element reads **only** from the *Feedback comments* sub-plugin (`assignfeedback_comments`).

| Situation | Text Shown on Certificate |
|---|---|
| Assignment deleted / uninstalled | _Feedback not available_ |
| Grader has not graded user yet | _Feedback not available_ |
| Graded but with NO comment filled | _No feedback provided_ |
| HTML tags exceed threshold | Safely closes open tags during shorten pass |

---

## 🚀 Installation

### Method 1: Via Zip Upload (Recommended)
1. Log in as an admin to your Moodle site.
2. Go to **Site administration › Plugins › Install plugins**.
3. Attach and upload the `assignfeedbackcomments.zip` file.
4. Run standard DB checks to trigger deployment.

### Method 2: Manual (Developer workflow)
1. Extract or clone this folder into your Moodle build at:
   `<moodleroot>/mod/customcert/element/assignfeedbackcomments/`
2. Go to **Site administration › Notifications** to kick off script installs.

---

## 📄 License

GNU GPL v3 or later — see [LICENSE](LICENSE)
