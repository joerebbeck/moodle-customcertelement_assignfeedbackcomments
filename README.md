# Assignment Feedback Comments — Custom Certificate Element

A [mod_customcert](https://moodle.org/plugins/mod_customcert) element plugin that renders
a student's assignment **feedback comment** text directly onto a generated PDF certificate.

## Requirements

| Dependency | Version |
|---|---|
| Moodle | 4.1 + |
| mod_customcert | 4.1 + (Element System v2 supported from 5.2+) |
| mod_assign | Must be installed and enabled |

## What it does

When a certificate is generated the element looks up the grader's feedback comment for
the configured assignment and renders it as formatted text in the certificate PDF.

The text is processed through Moodle's `format_text()` (filter pipeline, pluginfile
URL resolution) and then sanitised for TCPDF compatibility before rendering.

## ⚠️ Feedback sub-plugin limitation

Moodle's assignment activity uses a **sub-plugin architecture** for feedback types.
This element reads **only** from the _Feedback comments_ sub-plugin
(`assignfeedback_comments`).

| Feedback sub-plugin | Supported? |
|---|---|
| Feedback comments (`assignfeedback_comments`) | ✅ Yes |
| Annotate PDF (`assignfeedback_editpdf`) | ❌ No |
| File feedback (`assignfeedback_file`) | ❌ No |
| Offline grading worksheet (`assignfeedback_offline`) | ❌ No |

If a grader uses _Annotate PDF_ or _File feedback_ **without** also writing a feedback
comment, the certificate will display _"No feedback provided"_.

**Before issuing certificates**, verify that _Feedback comments_ is enabled on the
assignment: **Assignment › Edit settings › Feedback types**.

## Placeholder strings

| Situation | Text shown on certificate |
|---|---|
| mod_assign uninstalled, or assignment deleted | _Feedback not available_ |
| Student has not been graded yet | _Feedback not available_ |
| Student graded but no comment written | _No feedback provided_ |
| Feedback comment present | Formatted comment text |

## Installation

### Method 1: Via Zip Upload (Recommended)
1. Log in to your Moodle site as an administrator.
2. Go to **Site administration › Plugins › Install plugins**.
3. Upload the `.zip` file for this plugin.
4. Follow the prompts to trigger the upgrade set.

### Method 2: Manual (Developer Workflow)
1. Copy or clone this folder into your Moodle installation's customcert element directory.
2. Visit **Site administration › Notifications** to trigger the installation script.

Once installed, the item will appear as **"Assignment Feedback Comments"** in the certificate template editor's element menu.

## License

GNU GPL v3 or later — see [LICENSE](LICENSE)
