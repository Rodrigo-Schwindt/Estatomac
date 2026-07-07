<?php

namespace App\Http\Controllers\Admin\Newsletter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Mail\NewsletterMail;
use Illuminate\Support\Facades\Mail;

class NewsletterCrud extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('searchTerm');

        $subscribers = Subscriber::query()
            ->when($searchTerm, function ($q) use ($searchTerm) {
                $q->where('email', 'like', "%$searchTerm%")
                  ->orWhere('id', $searchTerm);
            })
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->withQueryString();

        $activeSubscribersCount = Subscriber::where('active', true)->count();

        return view('livewire.admin.newsletter.newsletter-crud', [
            'subscribers' => $subscribers,
            'activeSubscribersCount' => $activeSubscribersCount,
            'searchTerm' => $searchTerm,
        ]);
    }

    public function toggleActive($id)
    {
        $sub = Subscriber::find($id);

        if ($sub) {
            $sub->active = !$sub->active;
            $sub->save();
        }

        return redirect()->back()->with('toast', [
            'message' => 'Estado actualizado',
            'type' => 'success'
        ]);
    }

    public function deleteSubscriber($id)
    {
        Subscriber::find($id)?->delete();

        return redirect()->back()->with('toast', [
            'message' => 'Suscriptor eliminado',
            'type' => 'success'
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:5',
            'attachments.*' => 'mimes:pdf|max:10240',
        ]);

        $subject = $validated['subject'];
        $message = $validated['message'];

        $prepared = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('newsletter_temp', 'local');
                $prepared[] = [
                    'path' => storage_path('app/' . $path),
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $emails = Subscriber::where('active', true)->pluck('email');
        $sent = 0;

        foreach ($emails as $email) {
            Mail::to($email)->send(new NewsletterMail($subject, $message, $prepared));
            $sent++;
        }

        foreach ($prepared as $a) {
            if (file_exists($a['path'])) unlink($a['path']);
        }

        return redirect()->route('admin.newsletter')->with('toast', [
            'message' => "Newsletter enviado a $sent suscriptores",
            'type' => 'success'
        ]);
    }
}
