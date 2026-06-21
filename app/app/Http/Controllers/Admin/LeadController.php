<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')))
            ->latest()->paginate(20)->withQueryString();
        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead): View { return view('admin.leads.show', ['lead' => $lead->load('notes.user')]); }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(Lead::STATUSES)], 'follow_up_at' => ['nullable', 'date']]);
        $lead->update($data); ActivityLogger::log('lead.updated', $lead, $data);
        return back()->with('status', 'تم تحديث الطلب.');
    }

    public function note(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:3000']]);
        $lead->notes()->create(['user_id' => auth()->id(), 'body' => $data['body']]);
        ActivityLogger::log('lead.note_added', $lead);
        return back()->with('status', 'تمت إضافة الملاحظة.');
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Name', 'Email', 'Service', 'Status', 'Consultation date', 'Created']);
            Lead::orderBy('id')->chunk(200, fn ($items) => $items->each(fn ($lead) => fputcsv($out, [$lead->id, $lead->name, $lead->email, $lead->service, $lead->status, $lead->consultation_date?->format('Y-m-d'), $lead->created_at])));
            fclose($out);
        }, 'leads-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
