<?php

use App\Models\Cage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public bool $showForm = false;

    public ?int $viewingCageId = null;

    public ?int $editingCageId = null;

    public string $status = 'active';

    /** @var array<int, string> */
    public array $selectedBirdIds = [];

    public string $birdSearch = '';

    /** @return Collection<int, Cage> */
    public function getCagesProperty(): Collection
    {
        return auth()->user()->cages()
            ->withCount('birds')
            ->with('birds:id,name,cage_id,user_id')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    if (is_numeric($this->search)) {
                        $query->orWhereKey((int) $this->search);
                    }
                });
            })
            ->latest('id')
            ->get();
    }

    public function getAvailableBirdsProperty(): Collection
    {
        return auth()->user()->birds()
            ->when($this->birdSearch !== '' || $this->selectedBirdIds !== [], function ($query): void {
                $search = '%'.$this->birdSearch.'%';
                $query->where(function ($query) use ($search): void {
                    if ($this->birdSearch !== '') {
                        $query->where('name', 'like', $search)
                            ->orWhere('band_number', 'like', $search);
                        if ($this->selectedBirdIds !== []) {
                            $query->orWhereIn('id', $this->selectedBirdIds);
                        }
                    } elseif ($this->selectedBirdIds !== []) {
                        $query->whereIn('id', $this->selectedBirdIds);
                    }
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'cage_id', 'band_number']);
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function showCage(int $cageId): void
    {
        $this->findUserCage($cageId);
        $this->viewingCageId = $cageId;
    }

    public function closeCageDetails(): void
    {
        $this->viewingCageId = null;
    }

    public function getViewingCageProperty(): ?Cage
    {
        return $this->viewingCageId
            ? auth()->user()->cages()->with('birds:id,name,cage_id')->findOrFail($this->viewingCageId)
            : null;
    }

    public function openEditForm(int $cageId): void
    {
        $cage = $this->findUserCage($cageId);

        $this->editingCageId = $cage->id;
        $this->status = $cage->status;
        $this->selectedBirdIds = $cage->birds->pluck('id')->map(fn (int $id): string => (string) $id)->all();
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $isEditing = $this->editingCageId !== null;

        $validated = $this->validate([
            'status' => ['required', Rule::in(['active', 'available', 'maintenance'])],
            'selectedBirdIds' => ['array'],
            'selectedBirdIds.*' => ['integer', Rule::exists('birds', 'id')->where('user_id', auth()->id())],
        ]);

        $cage = $isEditing
            ? $this->findUserCage($this->editingCageId)
            : auth()->user()->cages()->create([
                'status' => $validated['status'],
            ]);

        if ($isEditing) {
            $cage->update([
                'status' => $validated['status'],
            ]);
        }

        auth()->user()->birds()
            ->where('cage_id', $cage->id)
            ->update(['cage_id' => null]);

        auth()->user()->birds()
            ->whereIn('id', $validated['selectedBirdIds'])
            ->update(['cage_id' => $cage->id]);

        $this->closeForm();
        $this->dispatch($isEditing ? 'cage-updated' : 'cage-created');
    }

    public function deleteCage(int $cageId): void
    {
        $cage = $this->findUserCage($cageId);
        $cage->birds()->update(['cage_id' => null]);
        $cage->delete();
        if ($this->viewingCageId === $cageId) {
            $this->closeCageDetails();
        }
        $this->dispatch('cage-deleted');
    }

    private function findUserCage(int $cageId): Cage
    {
        return auth()->user()->cages()->findOrFail($cageId);
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->reset(['editingCageId', 'selectedBirdIds', 'birdSearch']);
        $this->status = 'active';

    }
};
?>

<div>
    <section class="birds-stats" aria-label="{{ __('cages_title') }}">
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-green">&#9670;</span><div><span>{{ __('cages_total') }}</span><strong>{{ auth()->user()->cages()->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-blue">&#10003;</span><div><span>{{ __('cages_occupied') }}</span><strong>{{ auth()->user()->cages()->has('birds')->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-coral">&#9672;</span><div><span>{{ __('cages_available') }}</span><strong>{{ auth()->user()->cages()->doesntHave('birds')->count() }}</strong></div></article>
    </section>

    <section class="birds-panel panel">
        <div class="birds-toolbar">
            <label class="birds-search">
                <span aria-hidden="true">&#9906;</span>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('cages_search') }}" aria-label="{{ __('cages_search') }}">
            </label>
        </div>
        <div class="birds-table-wrap">
            <table class="birds-table">
                <thead><tr><th>{{ __('cages_table_cage') }}</th><th>Birds</th><th>{{ __('cages_table_occupancy') }}</th><th>{{ __('cages_table_status') }}</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($this->cages as $cage)
                        <tr wire:key="cage-{{ $cage->id }}">
                            <td><div class="bird-identity"><span class="bird-avatar bird-avatar-green">CG</span><span><strong>Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }}</strong><small>Unique ID: {{ $cage->id }}</small></span></div></td>
                            <td>
                                @if ($cage->birds->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($cage->birds as $bird)
                                            <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-800">{{ $bird->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">No birds assigned</span>
                                @endif
                            </td>
                            <td>{{ $cage->birds_count }} birds</td>
                            <td><span class="bird-status status-{{ $cage->status === 'maintenance' ? 'sick' : ($cage->birds_count ? 'active' : 'resting') }}">{{ ucfirst($cage->status) }}</span></td>
                            <td><div class="flex items-center justify-end gap-1">
                                <button type="button" wire:click="showCage({{ $cage->id }})" title="View cage details" aria-label="View Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }} details" class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                    <svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button type="button" wire:click="openEditForm({{ $cage->id }})" title="Edit cage" aria-label="Edit Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }}" class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                    <svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.65 3.65M4 20h4l10.5-10.5a2.121 2.121 0 0 0-3-3L5 17v3Z"/></svg>
                                </button>
                                <button type="button" data-delete-cage="{{ $cage->id }}" data-cage-name="Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }}" title="Delete cage" aria-label="Delete Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }}" class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
                                    <svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                                </button>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-500">No cages found. Create your first cage to start assigning birds.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($this->viewingCage)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/65 p-4 backdrop-blur-sm" wire:click.self="closeCageDetails">
            <section role="dialog" aria-modal="true" aria-labelledby="cage-details-title" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5">
                <header class="flex items-start justify-between gap-4 border-b border-emerald-100 bg-linear-to-br from-emerald-50 to-white px-6 py-5">
                    <div class="flex items-center gap-3"><span class="grid size-11 place-items-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-sm">CG</span><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Cage details</p><h2 id="cage-details-title" class="mt-1 text-xl font-semibold text-slate-900">Cage CG-{{ str_pad((string) $this->viewingCage->id, 4, '0', STR_PAD_LEFT) }}</h2></div></div>
                    <button type="button" wire:click="closeCageDetails" aria-label="Close cage details" class="grid size-9 place-items-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-white hover:text-slate-700">&times;</button>
                </header>
                <dl class="grid grid-cols-2 gap-3 px-6 py-6">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3"><dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Status</dt><dd class="mt-1 font-semibold text-slate-800">{{ ucfirst($this->viewingCage->status) }}</dd></div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3"><dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Birds</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingCage->birds->count() }}</dd></div>
                </dl>
                <div class="px-6 pb-6"><p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Assigned birds</p><div class="flex flex-wrap gap-2">@forelse ($this->viewingCage->birds as $bird)<span class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800">{{ $bird->name }}</span>@empty<span class="text-sm text-slate-500">No birds assigned.</span>@endforelse</div></div>
                <footer class="flex justify-end border-t border-slate-100 bg-slate-50/60 px-6 py-4"><button type="button" wire:click="closeCageDetails" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Close</button></footer>
            </section>
        </div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/65 p-4 backdrop-blur-sm" wire:click.self="closeForm">
            <section role="dialog" aria-modal="true" aria-labelledby="cage-form-title" class="max-h-[95vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5">
                <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-emerald-100 bg-linear-to-br from-emerald-50 to-white px-6 py-5">
                    <div><div class="flex items-center gap-3"><span class="grid size-11 place-items-center rounded-xl bg-emerald-600 text-lg font-bold text-white shadow-sm">CG</span><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Cage management</p><h2 id="cage-form-title" class="mt-1 text-xl font-semibold text-slate-900">{{ $editingCageId ? 'Edit cage' : 'Create cage' }}</h2></div></div><p class="mt-3 text-sm text-slate-500">{{ $editingCageId ? 'Update the cage status and assigned birds.' : 'Create a cage and assign one or more birds to it.' }}</p></div>
                    <button type="button" wire:click="closeForm" aria-label="Close cage form" class="grid size-9 place-items-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-white hover:text-slate-700">&times;</button>
                </div>
                <form wire:submit="save" class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-3 text-sm text-slate-700 sm:col-span-2"><span class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Unique cage ID</span><strong class="mt-1 block text-lg text-slate-900">CG-{{ $editingCageId ? str_pad((string) $editingCageId, 4, '0', STR_PAD_LEFT) : 'Generated automatically' }}</strong><p class="mt-1 text-xs text-slate-500">This identifier is generated automatically and cannot be changed.</p></div>
                    <div><label for="cage-status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label><select id="cage-status" wire:model="status" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10"><option value="active">Active</option><option value="available">Available</option><option value="maintenance">Maintenance</option></select></div>
                    <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-500"><span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">Assignment</span><span class="mt-1 block">Birds can be moved between cages at any time.</span></div>
                    <div class="sm:col-span-2">
                        <div class="mb-2 flex items-end justify-between gap-3">
                            <div><label for="cage-birds" class="block text-sm font-semibold text-slate-700">Assign birds</label><p class="mt-1 text-xs text-slate-500">Search by bird name or band number.</p></div>
                            <span class="text-xs font-medium text-slate-400">{{ count($selectedBirdIds) }} selected</span>
                        </div>
                        <label class="mb-2 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-400 transition focus-within:border-emerald-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-emerald-500/10">
                            <svg aria-hidden="true" class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/></svg>
                            <input type="search" wire:model.live.debounce.250ms="birdSearch" placeholder="Search birds or band numbers..." aria-label="Search birds to assign" class="min-w-0 flex-1 border-0 bg-transparent text-sm text-slate-800 outline-none placeholder:text-slate-400">
                        </label>
                        <select id="cage-birds" wire:model="selectedBirdIds" multiple size="6" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">@forelse ($this->availableBirds as $bird)<option value="{{ $bird->id }}">{{ $bird->name }}{{ $bird->band_number ? ' · '.$bird->band_number : '' }}{{ $bird->cage_id && $bird->cage_id !== $editingCageId ? ' (currently assigned)' : '' }}</option>@empty<option disabled>No birds match your search.</option>@endforelse</select><p class="mt-2 text-xs text-slate-500">Hold Ctrl/Cmd to select multiple birds. Selected birds will be linked to this cage.</p>@error('selectedBirdIds')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sticky bottom-0 -mx-6 -mb-6 flex justify-end gap-3 border-t border-slate-100 bg-white/95 px-6 py-4 backdrop-blur sm:col-span-2"><button type="button" wire:click="closeForm" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button><button type="submit" wire:loading.attr="disabled" wire:target="save" class="dashboard-primary-button disabled:opacity-50"><span wire:loading.remove wire:target="save">{{ $editingCageId ? 'Save changes' : 'Create cage' }}</span><span wire:loading wire:target="save">Saving...</span></button></div>
                </form>
            </section>
        </div>
    @endif
</div>