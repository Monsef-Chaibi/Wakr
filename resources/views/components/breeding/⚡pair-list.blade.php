<?php

use App\Models\Bird;
use App\Models\BreedingPair;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public bool $showForm = false;
    public ?int $viewingPairId = null;
    public ?int $editingPairId = null;
    public ?string $male_bird_id = null;
    public ?string $female_bird_id = null;
    /** @var array<int, string> */
    public array $eggDates = [];

    public function showPair(int $pairId): void
    {
        auth()->user()->breedingPairs()->findOrFail($pairId);
        $this->viewingPairId = $pairId;
    }

    public function closePairDetails(): void
    {
        $this->viewingPairId = null;
    }

    public function getViewingPairProperty(): ?BreedingPair
    {
        return $this->viewingPairId
            ? auth()->user()->breedingPairs()->with(['maleBird:id,name,cage_id', 'femaleBird:id,name,cage_id'])->findOrFail($this->viewingPairId)
            : null;
    }

    /** @return Collection<int, BreedingPair> */
    public function getPairsProperty(): Collection
    {
        return auth()->user()->breedingPairs()->with(['maleBird:id,name,cage_id', 'femaleBird:id,name,cage_id'])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->whereHas('maleBird', fn ($bird) => $bird->where('name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('femaleBird', fn ($bird) => $bird->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->latest('id')->get();
    }

    /** @return Collection<int, Bird> */
    public function getBirdsProperty(): Collection
    {
        return auth()->user()->birds()->whereIn('sex', ['male', 'female'])->orderBy('name')->get(['id', 'name', 'sex']);
    }

    public function openCreateForm(): void { $this->resetForm(); $this->showForm = true; }

    public function openEditForm(int $pairId): void
    {
        $pair = auth()->user()->breedingPairs()->findOrFail($pairId);
        $this->editingPairId = $pair->id;
        $this->male_bird_id = (string) $pair->male_bird_id;
        $this->female_bird_id = (string) $pair->female_bird_id;
        $this->eggDates = $pair->egg_dates ?: array_fill(0, $pair->eggs, '');
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'male_bird_id' => ['required', 'different:female_bird_id', Rule::exists('birds', 'id')->where('user_id', auth()->id())->where('sex', 'male')],
            'female_bird_id' => ['required', Rule::exists('birds', 'id')->where('user_id', auth()->id())->where('sex', 'female')],
            'eggDates' => ['array'],
            'eggDates.*' => ['required', 'date'],
        ]);
        $eggDates = array_values($validated['eggDates']);
        $pair = $this->editingPairId ? auth()->user()->breedingPairs()->findOrFail($this->editingPairId) : auth()->user()->breedingPairs()->make();
        $pair->fill([
            'male_bird_id' => $validated['male_bird_id'],
            'female_bird_id' => $validated['female_bird_id'],
            'eggs' => count($eggDates),
            'egg_dates' => $eggDates,
            'status' => count($eggDates) ? 'hatching' : 'ready',
            'next_milestone' => $eggDates ? max($eggDates) : null,
        ]);
        $pair->user_id = auth()->id();
        $pair->save();
        $this->closeForm();
        $this->dispatch('pair-saved');
    }

    public function deletePair(int $pairId): void
    {
        auth()->user()->breedingPairs()->findOrFail($pairId)->delete();
        if ($this->viewingPairId === $pairId) {
            $this->closePairDetails();
        }
        $this->dispatch('pair-deleted');
    }

    public function closeForm(): void { $this->showForm = false; $this->resetForm(); }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->reset(['editingPairId', 'male_bird_id', 'female_bird_id', 'eggDates']);
    }

    public function addEgg(): void { $this->eggDates[] = ''; }

    public function removeEgg(int $index): void
    {
        unset($this->eggDates[$index]);
        $this->eggDates = array_values($this->eggDates);
    }
};
?>

<div>
    <section class="birds-stats" aria-label="{{ __('breeding_title') }}">
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-green">&#9670;</span><div><span>{{ __('breeding_total') }}</span><strong>{{ auth()->user()->breedingPairs()->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-blue">&#10003;</span><div><span>{{ __('breeding_nesting') }}</span><strong>{{ auth()->user()->breedingPairs()->where('status', 'nesting')->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-coral">&#9672;</span><div><span>{{ __('breeding_hatching') }}</span><strong>{{ auth()->user()->breedingPairs()->where('status', 'hatching')->count() }}</strong></div></article>
    </section>
    <section class="birds-panel panel">
        <div class="birds-toolbar"><label class="birds-search"><span aria-hidden="true">&#9906;</span><input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('breeding_search') }}" aria-label="{{ __('breeding_search') }}"></label></div>
        <div class="birds-table-wrap"><table class="birds-table"><thead><tr><th>{{ __('breeding_table_pair') }}</th><th>{{ __('breeding_table_birds') }}</th><th>{{ __('breeding_table_cage') }}</th><th>{{ __('breeding_table_eggs') }}</th><th>{{ __('breeding_table_next') }}</th><th>Actions</th></tr></thead><tbody>
            @forelse ($this->pairs as $pair)
                <tr wire:key="pair-{{ $pair->id }}"><td><div class="bird-identity"><span class="bird-avatar bird-avatar-coral">{{ str($pair->maleBird->name)->substr(0, 1)->upper() }}{{ str($pair->femaleBird->name)->substr(0, 1)->upper() }}</span><span><strong>{{ $pair->maleBird->name }} & {{ $pair->femaleBird->name }}</strong><small>PAIR-{{ str_pad((string) $pair->id, 4, '0', STR_PAD_LEFT) }}</small></span></div></td><td>{{ $pair->maleBird->name }} / {{ $pair->femaleBird->name }}</td><td>{{ $pair->maleBird->cage_id ? 'CG-'.str_pad((string) $pair->maleBird->cage_id, 4, '0', STR_PAD_LEFT) : '—' }}</td><td>{{ $pair->egg_dates ? count($pair->egg_dates) : $pair->eggs }}</td><td><span class="bird-status status-pair">{{ $pair->egg_dates ? max($pair->egg_dates) : 'No eggs recorded' }}</span></td><td><div class="flex items-center justify-end gap-1"><button type="button" wire:click="showPair({{ $pair->id }})" title="View pair details" aria-label="View {{ $pair->maleBird->name }} and {{ $pair->femaleBird->name }} details" class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"><svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/><circle cx="12" cy="12" r="3"/></svg></button><button type="button" wire:click="openEditForm({{ $pair->id }})" title="Edit pair" aria-label="Edit {{ $pair->maleBird->name }} and {{ $pair->femaleBird->name }}" class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"><svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.65 3.65M4 20h4l10.5-10.5a2.121 2.121 0 0 0-3-3L5 17v3Z"/></svg></button><button type="button" data-delete-pair="{{ $pair->id }}" data-pair-name="{{ $pair->maleBird->name }} & {{ $pair->femaleBird->name }}" title="Delete pair" aria-label="Delete {{ $pair->maleBird->name }} and {{ $pair->femaleBird->name }}" class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"><svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button></div></td></tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-slate-500">No breeding pairs found. Create your first pair.</td></tr>
            @endforelse
        </tbody></table></div>
    </section>
    @if ($this->viewingPair)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" wire:click.self="closePairDetails">
            <section role="dialog" aria-modal="true" aria-labelledby="pair-details-title" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
                <header class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50/80 px-6 py-5">
                    <div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Pair details</p><h2 id="pair-details-title" class="mt-1 text-xl font-semibold text-slate-900">{{ $this->viewingPair->maleBird->name }} & {{ $this->viewingPair->femaleBird->name }}</h2><p class="mt-1 text-sm text-slate-500">PAIR-{{ str_pad((string) $this->viewingPair->id, 4, '0', STR_PAD_LEFT) }}</p></div>
                    <button type="button" wire:click="closePairDetails" aria-label="Close pair details" class="grid size-9 place-items-center rounded-full text-xl text-slate-400 hover:bg-white hover:text-slate-700">&times;</button>
                </header>
                <dl class="grid grid-cols-2 gap-4 px-6 py-6">
                    <div><dt class="text-xs text-slate-500">Male bird</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingPair->maleBird->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Female bird</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingPair->femaleBird->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Eggs</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingPair->egg_dates ? count($this->viewingPair->egg_dates) : $this->viewingPair->eggs }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Latest egg</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingPair->egg_dates ? max($this->viewingPair->egg_dates) : 'No eggs recorded' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Male cage</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingPair->maleBird->cage_id ? 'CG-'.str_pad((string) $this->viewingPair->maleBird->cage_id, 4, '0', STR_PAD_LEFT) : 'Not assigned' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Female cage</dt><dd class="mt-1 font-semibold text-slate-800">{{ $this->viewingPair->femaleBird->cage_id ? 'CG-'.str_pad((string) $this->viewingPair->femaleBird->cage_id, 4, '0', STR_PAD_LEFT) : 'Not assigned' }}</dd></div>
                </dl>
                <footer class="flex justify-end border-t border-slate-100 px-6 py-4"><button type="button" wire:click="closePairDetails" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Close</button></footer>
            </section>
        </div>
    @endif
    @if ($showForm)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" wire:click.self="closeForm">
            <section role="dialog" aria-modal="true" aria-labelledby="pair-modal-title" class="flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <header class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50/80 px-6 py-5">
                    <div class="flex items-start gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-rose-100 text-lg text-rose-700">♥</span>
                        <div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Breeding workspace</p><h2 id="pair-modal-title" class="mt-1 text-xl font-semibold text-slate-900">{{ $editingPairId ? 'Edit breeding pair' : 'Create breeding pair' }}</h2><p class="mt-1 text-sm text-slate-500">Choose the parents and record each egg date.</p></div>
                    </div>
                    <button type="button" wire:click="closeForm" aria-label="Close modal" class="grid size-9 shrink-0 place-items-center rounded-full text-xl text-slate-400 transition hover:bg-white hover:text-slate-700">&times;</button>
                </header>
                <form wire:submit="save" class="flex min-h-0 flex-1 flex-col">
                    <div class="grid gap-5 overflow-y-auto px-6 py-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="grid gap-2 text-sm font-semibold text-slate-700">Male bird
                                <select wire:model="male_bird_id" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal text-slate-800 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                                    <option value="">Select male bird</option>@foreach ($this->birds->where('sex', 'male') as $bird)<option value="{{ $bird->id }}">{{ $bird->name }}</option>@endforeach
                                </select>
                                @error('male_bird_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
                            </label>
                            <label class="grid gap-2 text-sm font-semibold text-slate-700">Female bird
                                <select wire:model="female_bird_id" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal text-slate-800 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                                    <option value="">Select female bird</option>@foreach ($this->birds->where('sex', 'female') as $bird)<option value="{{ $bird->id }}">{{ $bird->name }}</option>@endforeach
                                </select>
                                @error('female_bird_id')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
                            </label>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div><h3 class="font-semibold text-slate-800">Egg dates</h3><p class="mt-1 text-xs text-slate-500">Add one date for every egg laid by this pair.</p></div>
                                <button type="button" wire:click="addEgg" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-800">＋ Add egg</button>
                            </div>
                            <div class="mt-4 grid gap-2">
                                @forelse ($eggDates as $index => $date)
                                    <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white p-2" wire:key="egg-{{ $index }}">
                                        <span class="grid size-7 shrink-0 place-items-center rounded-md bg-amber-100 text-xs font-bold text-amber-700">{{ $index + 1 }}</span>
                                        <input aria-label="Egg {{ $index + 1 }} date" type="date" wire:model="eggDates.{{ $index }}" class="min-w-0 flex-1 rounded-md border border-transparent px-2 py-1.5 text-sm text-slate-700 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/10" required>
                                        <button type="button" wire:click="removeEgg({{ $index }})" aria-label="Remove egg {{ $index + 1 }}" class="rounded-md px-2 py-1.5 text-xs font-medium text-slate-400 transition hover:bg-red-50 hover:text-red-600">Remove</button>
                                    </div>
                                @empty
                                    <div class="rounded-lg border border-dashed border-slate-300 px-4 py-6 text-center"><span class="text-2xl">🥚</span><p class="mt-2 text-sm font-medium text-slate-600">No eggs added yet</p><p class="mt-1 text-xs text-slate-400">Click “Add egg” to record a date.</p></div>
                                @endforelse
                            </div>
                            @error('eggDates.*')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <footer class="flex items-center justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4">
                        <button type="button" wire:click="closeForm" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="dashboard-primary-button disabled:cursor-wait disabled:opacity-60"><span wire:loading.remove wire:target="save">Save pair</span><span wire:loading wire:target="save">Saving...</span></button>
                    </footer>
                </form>
            </section>
        </div>
    @endif
</div>
