/**
 * Google Sheet receiver for the portfolio contact form.
 * 1) Create a Google Sheet → Extensions → Apps Script → paste this file.
 * 2) Change SECRET below (any long random text) and put the SAME text in
 *    Admin → Site settings → Contact & email → "Google Sheet secret token".
 * 3) Deploy → New deployment → type "Web app" → Execute as: Me → Who has access: Anyone → Deploy.
 * 4) Copy the Web app URL (ends with /exec) into Admin → Site settings → "Google Sheet webhook URL".
 * (If you edit this file later: Deploy → Manage deployments → ✏ → Version: New version → Deploy.)
 */
const SECRET = 'change-this-to-a-long-random-text';
const SHEET_NAME = 'Messages';

function doPost(e) {
  try {
    const d = JSON.parse(e.postData.contents);
    if (d.secret !== SECRET) return out({ ok: false, error: 'bad secret' });

    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sh = ss.getSheetByName(SHEET_NAME) || ss.insertSheet(SHEET_NAME);
    if (sh.getLastRow() === 0) {
      sh.appendRow(['ID', 'Date', 'Name', 'Email', 'Project type', 'Budget', 'Message', 'Status', 'IP']);
      sh.getRange(1, 1, 1, 9).setFontWeight('bold');
      sh.setFrozenRows(1);
    }
    // do not insert the same message twice (the admin "Sync" button can resend)
    if (sh.getLastRow() > 1) {
      const ids = sh.getRange(2, 1, sh.getLastRow() - 1, 1).getValues().flat();
      if (ids.indexOf(d.id) !== -1) return out({ ok: true, duplicate: true });
    }
    sh.appendRow([d.id, d.date, d.name, d.email, d.project_type, d.budget, d.message, d.status, d.ip]);
    return out({ ok: true });
  } catch (err) {
    return out({ ok: false, error: String(err) });
  }
}

function out(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj)).setMimeType(ContentService.MimeType.JSON);
}
