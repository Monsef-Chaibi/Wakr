<?php

use App\Models\Bird;
use App\Models\FlockRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public bool $showForm = false;
    public ?int $editingRecordId = null;
    public string $activity = '';
    public ?string $bird_id = null;
    public string $type = 'flock';
    public string $occurred_at = '';
    public ?string $notes = null;

    /** @return Collection<int, FlockRecord> */
    public function getRecordsProperty(): Collection
    {
        return auth()->user()->flockRecords()->with('bird:id,name')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('activity', 'like', '%'.$this->search.'%')
                        ->orWhere('notes', 'like', '%'.$this->search.'%');
                });
            })
            ->latest('occurred_at')->get();
    }

    /** @return Collection<int, Bird> */
    public function getBirdsProperty(): Collection
    {
        return auth()->user()->birds()->orderBy('name')->get(['id', 'name']);
    }

    public function openCreateForm(): void { $this->resetForm(); $this->showForm = true; }

    public function openEditForm(int $recordId): void
    {
        $record = auth()->user()->flockRecords()->findOrFail($recordId);
        $this->editingRecordId = $record->id;
        $this->activity = $record->activity;
        $this->bird_id = $record->bird_id ? (string) $record->bird_id : null;
        $this->type = $record->type;
        $this->occurred_at = $record->occurred_at->format('Y-m-d');
        $this->notes = $record->notes;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'activity' => ['required', 'string', 'max:255'],
            'bird_id' => ['nullable', 'integer', Rule::exists('birds', 'id')->where('user_id', auth()->id())],
            'type' => ['required', Rule::in(['health', 'breeding', 'flock'])],
            'occurred_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $record = $this->editingRecordId ? auth()->user()->flockRecords()->findOrFail($this->editingRecordId) : auth()->user()->flockRecords()->make();
        $record->fill($validated);
        $record->user_id = auth()->id();
        $record->save();
        $this->closeForm();
        $this->dispatch('record-saved');
    }

    public function deleteRecord(int $recordId): void
    {
        auth()->user()->flockRecords()->findOrFail($recordId)->delete();
        $this->dispatch('record-deleted');
    }

    public function closeForm(): void { $this->showForm = false; $this->resetForm(); }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->reset(['editingRecordId', 'activity', 'bird_id', 'notes']);
        $this->type = 'flock';
        $this->occurred_at = now()->format('Y-m-d');
    }
};
?>

<div>
    <section class="birds-stats" aria-label="{{ __('records_title') }}">
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-green">&#9670;</span><div><span>{{ __('records_total') }}</span><strong>{{ auth()->user()->flockRecords()->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-blue">&#10003;</span><div><span>{{ __('records_health') }}</span><strong>{{ auth()->user()->flockRecords()->where('type', 'health')->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-coral">&#9672;</span><div><span>{{ __('records_breeding') }}</span><strong>{{ auth()->user()->flockRecords()->where('type', 'breeding')->count() }}</strong></div></article>
    </section>
    <section class="birds-panel panel">
        <div class="birds-toolbar"><label class="birds-search"><span aria-hidden="true">&#9906;</span><input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('records_search') }}" aria-label="{{ __('records_search') }}"></label></div>
        <div class="birds-table-wrap"><table class="birds-table"><thead><tr><th>{{ __('records_table_activity') }}</th><th>{{ __('records_table_subject') }}</th><th>{{ __('records_table_type') }}</th><th>{{ __('records_table_date') }}</th><th>{{ __('records_table_notes') }}</th><th>Actions</th></tr></thead><tbody>
            @forelse ($this->records as $record)
                <tr wire:key="record-{{ $record->id }}"><td><strong>{{ $record->activity }}</strong></td><td>{{ $record->bird?->name ?: 'Whole flock' }}</td><td><span class="bird-status status-{{ $record->type === 'health' ? 'resting' : ($record->type === 'breeding' ? 'pair' : 'active') }}">{{ ucfirst($record->type) }}</span></td><td>{{ $record->occurred_at->format('M d, Y') }}</td><td>{{ $record->notes ?: '—' }}</td><td><div class="flex gap-1"><button type="button" wire:click="openEditForm({{ $record->id }})" class="text-slate-500">✎</button><button type="button" wire:click="deleteRecord({{ $record->id }})" wire:confirm="Delete this record?" class="text-red-500">⌫</button></div></td></tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-slate-500">No records found. Add your first flock record.</td></tr>
            @endforelse
        </tbody></table></div>
    </section>
    @if ($showForm)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/50 p-4" wire:click.self="closeForm"><section role="dialog" aria-modal="true" class="w-full max-w-lg rounded-lg bg-white p-5 shadow-xl"><div class="flex justify-between border-b border-slate-200 pb-4"><h2 class="text-xl font-semibold">{{ $editingRecordId ? 'Edit record' : 'Add record' }}</h2><button type="button" wire:click="closeForm" class="text-2xl">&times;</button></div><form wire:submit="save" class="mt-5 grid gap-4"><label>Activity<input type="text" wire:model="activity" class="w-full rounded-md border p-2" placeholder="e.g. Health check completed"></label><label>Bird<select wire:model="bird_id" class="w-full rounded-md border p-2"><option value="">Whole flock</option>@foreach ($this->birds as $bird)<option value="{{ $bird->id }}">{{ $bird->name }}</option>@endforeach</select></label><label>Type<select wire:model="type" class="w-full rounded-md border p-2"><option value="flock">Flock</option><option value="health">Health</option><option value="breeding">Breeding</option></select></label><label>Date<input type="date" wire:model="occurred_at" class="w-full rounded-md border p-2"></label><label>Notes<textarea wire:model="notes" rows="3" class="w-full rounded-md border p-2"></textarea></label><div class="flex justify-end gap-3 border-t pt-4"><button type="button" wire:click="closeForm" class="rounded-md border px-4 py-2">Cancel</button><button type="submit" class="dashboard-primary-button">Save record</button></div></form></section></div>
    @endif
</div>
