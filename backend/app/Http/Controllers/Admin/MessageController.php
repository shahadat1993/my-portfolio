<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\GoogleSheet;
use App\Support\XlsxWriter;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $r)
    {
        $q = ContactMessage::query();
        if ($s = trim((string) $r->query('q'))) $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('message', 'like', "%$s%"));
        if ($st = $r->query('status')) $q->where('status', $st);
        return view('admin.messages.index', ['items' => $q->latest()->paginate(15)->withQueryString(), 'q' => $s, 'status' => $st,
            'counts' => ['all' => ContactMessage::count(), 'new' => ContactMessage::where('status', 'new')->count(), 'read' => ContactMessage::where('status', 'read')->count(), 'replied' => ContactMessage::where('status', 'replied')->count()],
            'sheet' => GoogleSheet::enabled()]);
    }

    public function show(ContactMessage $message)
    {
        if ($message->status === 'new') $message->update(['status' => 'read']);
        return view('admin.messages.show', ['m' => $message]);
    }

    public function update(Request $r, ContactMessage $message)
    {
        $message->update($r->validate(['status' => 'required|in:new,read,replied', 'note' => 'nullable|string|max:2000']));
        return back()->with('ok', 'Message updated.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('ok', 'Message deleted.');
    }

    /** Download all messages as .xlsx (Excel) or .csv */
    public function export(Request $r)
    {
        $head = ['ID', 'Date', 'Name', 'Email', 'Project type', 'Budget', 'Message', 'Status', 'Email sent', 'In Google Sheet', 'Note'];
        $rows = ContactMessage::orderBy('id')->get()->map(fn ($m) => [
            $m->id, $m->created_at->format('Y-m-d H:i'), $m->name, $m->email, (string) $m->project_type, (string) $m->budget, $m->message,
            $m->status, $m->mail_sent ? 'yes' : 'no', $m->sheet_synced ? 'yes' : 'no', (string) $m->note,
        ])->all();
        $name = 'contact-messages-' . date('Y-m-d');

        if ($r->query('format') === 'csv' || !class_exists(\ZipArchive::class)) {
            return response()->streamDownload(function () use ($head, $rows) {
                $o = fopen('php://output', 'w');
                fwrite($o, "\xEF\xBB\xBF");
                fputcsv($o, $head);
                foreach ($rows as $row) fputcsv($o, $row);
                fclose($o);
            }, $name . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        $tmp = XlsxWriter::make($head, $rows, 'Messages');
        return response()->download($tmp, $name . '.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend(true);
    }

    /** Push every message that is not yet in the Google Sheet. */
    public function sync()
    {
        if (!GoogleSheet::enabled()) return back()->with('err', 'Set the Google Sheet webhook URL in Site settings → Contact & email first.');
        $ok = $fail = 0;
        foreach (ContactMessage::where('sheet_synced', false)->orderBy('id')->get() as $m) {
            if (GoogleSheet::push($m)) { $m->update(['sheet_synced' => true]); $ok++; } else $fail++;
        }
        return back()->with($fail ? 'err' : 'ok', "Sheet sync finished: $ok sent, $fail failed.");
    }
}
