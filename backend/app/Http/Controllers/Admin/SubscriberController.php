<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function index()
    {
        return view('admin.subscribers', ['items' => Subscriber::latest()->paginate(20)]);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('ok', 'Subscriber removed.');
    }

    public function export()
    {
        return response()->streamDownload(function () {
            $o = fopen('php://output', 'w');
            fputcsv($o, ['Email', 'Subscribed at']);
            Subscriber::orderBy('id')->each(fn ($s) => fputcsv($o, [$s->email, $s->created_at->format('Y-m-d H:i')]));
            fclose($o);
        }, 'subscribers-' . date('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
