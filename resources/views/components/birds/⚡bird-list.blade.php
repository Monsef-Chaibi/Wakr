<?php

use App\Models\Bird;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public string $sexFilter = 'all';

    public ?int $viewingBirdId = null;

    public ?int $editingBirdId = null;

    public string $name = '';

    public string $species = '';

    public string $sex = 'unknown';

    public ?string $cage_id = null;

    public string $status = 'active';

    public ?string $band_number = null;

    public ?string $color_mutation = null;

    public ?string $birth_date = null;

    public ?string $parent_male_id = null;

    public ?string $parent_female_id = null;

    #[On('bird-created')]
    public function refreshBirds(): void
    {
    }

    public function showBird(int $birdId): void
    {
        $this->findUserBird($birdId);
        $this->viewingBirdId = $birdId;
    }

    public function closeBirdDetails(): void
    {
        $this->viewingBirdId = null;
    }

    public function editBird(int $birdId): void
    {
        $bird = $this->findUserBird($birdId);

        $this->editingBirdId = $bird->id;
        $this->name = $bird->name;
        $this->species = $bird->species;
        $this->sex = $bird->sex;
        $this->cage_id = $bird->cage_id ? (string) $bird->cage_id : null;
        $this->status = $bird->status;
        $this->band_number = $bird->band_number;
        $this->color_mutation = $bird->color_mutation;
        $this->birth_date = $bird->birth_date?->format('Y-m-d');
        $this->parent_male_id = $bird->parent_male_id ? (string) $bird->parent_male_id : null;
        $this->parent_female_id = $bird->parent_female_id ? (string) $bird->parent_female_id : null;
    }

    public function closeEditBird(): void
    {
        $this->editingBirdId = null;
        $this->resetValidation();
    }

    /** @return Collection<int, \App\Models\Cage> */
    public function getCagesProperty(): Collection
    {
        return auth()->user()->cages()->orderBy('id')->get(['id']);
    }

    /** @return Collection<int, Bird> */
    public function getMaleBirdsProperty(): Collection
    {
        return auth()->user()->birds()
            ->where('sex', 'male')
            ->when($this->editingBirdId, fn ($query) => $query->where('id', '!=', $this->editingBirdId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /** @return Collection<int, Bird> */
    public function getFemaleBirdsProperty(): Collection
    {
        return auth()->user()->birds()
            ->where('sex', 'female')
            ->when($this->editingBirdId, fn ($query) => $query->where('id', '!=', $this->editingBirdId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function updateBird(): void
    {
        $bird = $this->findUserBird($this->editingBirdId);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'in:male,female,unknown'],
            'cage_id' => ['nullable', 'integer', Rule::exists('cages', 'id')->where('user_id', auth()->id())],
            'status' => ['required', 'in:active,breeding,sick,retired,deceased'],
            'band_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('birds', 'band_number')->ignore($bird->id),
            ],
            'color_mutation' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'parent_male_id' => ['nullable', 'integer', Rule::exists('birds', 'id')->where('user_id', auth()->id())->where('sex', 'male')->where('id', '!=', $bird->id)],
            'parent_female_id' => ['nullable', 'integer', Rule::exists('birds', 'id')->where('user_id', auth()->id())->where('sex', 'female')->where('id', '!=', $bird->id)],
        ]);

        $bird->update([
            ...$validated,
            'cage_id' => $validated['cage_id'] ?: null,
            'parent_male_id' => $validated['parent_male_id'] ?: null,
            'parent_female_id' => $validated['parent_female_id'] ?: null,
        ]);

        $this->closeEditBird();
        $this->dispatch('bird-updated');
    }

    public function deleteBird(int $birdId): void
    {
        $this->findUserBird($birdId)->delete();

        if ($this->viewingBirdId === $birdId) {
            $this->closeBirdDetails();
        }

        if ($this->editingBirdId === $birdId) {
            $this->closeEditBird();
        }

        $this->dispatch('bird-deleted');
    }

    private function findUserBird(?int $birdId): Bird
    {
        abort_unless($birdId, 404);

        return auth()->user()->birds()->findOrFail($birdId);
    }

    public function getViewingBirdProperty(): ?Bird
    {
        return $this->viewingBirdId ? $this->findUserBird($this->viewingBirdId) : null;
    }

    /**
     * @return Collection<int, Bird>
     */
    public function getBirdsProperty(): Collection
    {
        return auth()->user()->birds()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('species', 'like', '%'.$this->search.'%')
                        ->orWhere('band_number', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->sexFilter !== 'all', fn ($query) => $query->where('sex', $this->sexFilter))
            ->latest('id')
            ->get();
    }

};
?>

<div>
    <section class="birds-stats" aria-label="{{ __('birds_title') }}">
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-green">&#9670;</span><div><span>{{ __('birds_total') }}</span><strong>{{ auth()->user()->birds()->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-blue">&#10003;</span><div><span>{{ __('birds_active') }}</span><strong>{{ auth()->user()->birds()->where('status', 'active')->count() }}</strong></div></article>
        <article class="birds-stat"><span class="birds-stat-icon birds-stat-coral">&#9672;</span><div><span>{{ __('birds_pairs') }}</span><strong>{{ auth()->user()->birds()->where('status', 'breeding')->count() }}</strong></div></article>
    </section>

    <section class="birds-panel panel">
        <div class="birds-toolbar">
            <label class="birds-search">
                <span aria-hidden="true">&#9906;</span>
                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('birds_search') }}" aria-label="{{ __('birds_search') }}">
            </label>
            <div class="birds-filters" role="group" aria-label="{{ __('birds_filter_status') }}">
                <button class="birds-filter {{ $sexFilter === 'all' ? 'is-selected' : '' }}"
                    wire:click="$set('sexFilter', 'all')" type="button">{{ __('birds_filter_all') }}</button>
                <button class="birds-filter {{ $sexFilter === 'male' ? 'is-selected' : '' }}"
                    wire:click="$set('sexFilter', 'male')" type="button">{{ __('birds_filter_male') }}</button>
                <button class="birds-filter {{ $sexFilter === 'female' ? 'is-selected' : '' }}"
                    wire:click="$set('sexFilter', 'female')" type="button">{{ __('birds_filter_female') }}</button>
            </div>
        </div>
        <div class="birds-table-wrap">
            <table class="birds-table">
                <thead>
                    <tr>
                        <th>{{ __('birds_table_bird') }}</th><th>{{ __('birds_table_species') }}</th>
                        <th>{{ __('birds_table_sex') }}</th><th>{{ __('birds_table_cage') }}</th>
                        <th>{{ __('birds_table_status') }}</th><th>{{ __('birds_table_added') }}</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->birds as $bird)
                        <tr wire:key="bird-{{ $bird->id }}">
                            <td><div class="bird-identity"><span class="bird-avatar bird-avatar-green">{{ str($bird->name)->substr(0, 1)->upper() }}</span><span><strong>{{ $bird->name }}</strong><small>{{ $bird->band_number ?: 'BRD-'.str_pad((string) $bird->id, 5, '0', STR_PAD_LEFT) }}</small></span></div></td>
                            <td>{{ str_replace('_', ' ', $bird->species) }}</td>
                            <td>{{ $bird->sex === 'male' ? __('birds_male') : ($bird->sex === 'female' ? __('birds_female') : ucfirst($bird->sex)) }}</td>
                            <td>{{ $bird->cage_id ? 'Cage '.$bird->cage_id : __('birds_no_cage') }}</td>
                            <td><span class="bird-status status-{{ $bird->status }}">{{ ucfirst($bird->status) }}</span></td>
                            <td>{{ $bird->created_at?->format('M d, Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="showBird({{ $bird->id }})"
                                        title="View bird details" aria-label="View {{ $bird->name }} details"
                                        class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        <svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="editBird({{ $bird->id }})"
                                        title="Edit bird" aria-label="Edit {{ $bird->name }}"
                                        class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        <svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.65 3.65M4 20h4l10.5-10.5a2.121 2.121 0 0 0-3-3L5 17v3Z"/>
                                        </svg>
                                    </button>
                                    <button type="button" data-delete-bird="{{ $bird->id }}" data-bird-name="{{ $bird->name }}"
                                        title="Delete bird" aria-label="Delete {{ $bird->name }}"
                                        class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
                                        <svg aria-hidden="true" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">No birds found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($this->viewingBird)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/65 p-4 backdrop-blur-sm" wire:click.self="closeBirdDetails">
            <section role="dialog" aria-modal="true" aria-labelledby="bird-details-title"
                class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5">
                <div class="flex items-start justify-between gap-4 border-b border-emerald-100 bg-linear-to-br from-emerald-50 to-white px-6 py-5">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="grid size-11 place-items-center rounded-xl bg-emerald-600 text-lg font-bold text-white shadow-sm">{{ str($this->viewingBird->name)->substr(0, 1)->upper() }}</span>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Bird details</p>
                                <h2 id="bird-details-title" class="mt-1 text-xl font-semibold text-slate-900">{{ $this->viewingBird->name }}</h2>
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="closeBirdDetails" aria-label="Close bird details"
                        class="grid size-9 place-items-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-white hover:text-slate-700">&times;</button>
                </div>
                <dl class="grid grid-cols-1 gap-3 px-6 py-6 sm:grid-cols-2">
                    @foreach ([['Species', $this->viewingBird->species], ['Sex', ucfirst($this->viewingBird->sex)], ['Cage', $this->viewingBird->cage_id ?: 'Not assigned'], ['Status', ucfirst($this->viewingBird->status)], ['Band number', $this->viewingBird->band_number ?: 'Not provided'], ['Color / mutation', $this->viewingBird->color_mutation ?: 'Not provided'], ['Birth date', $this->viewingBird->birth_date?->format('M d, Y') ?: 'Not provided'], ['Father ID', $this->viewingBird->parent_male_id ?: 'Not provided'], ['Mother ID', $this->viewingBird->parent_female_id ?: 'Not provided'], ['Added', $this->viewingBird->created_at?->format('M d, Y H:i')], ['Last updated', $this->viewingBird->updated_at?->format('M d, Y H:i')], ['Bird ID', $this->viewingBird->id]] as [$label, $value])
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3"><dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 wrap-break-word font-semibold text-slate-800">{{ $value }}</dd></div>
                    @endforeach
                </dl>
                <footer class="flex justify-end border-t border-slate-100 bg-slate-50/60 px-6 py-4"><button type="button" wire:click="closeBirdDetails" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Close</button></footer>
            </section>
        </div>
    @endif

    @if ($editingBirdId)
        <div class="fixed inset-0 z-1000 flex items-center justify-center bg-slate-950/65 p-4 backdrop-blur-sm" wire:click.self="closeEditBird">
            <section role="dialog" aria-modal="true" aria-labelledby="edit-bird-title"
                class="max-h-[95vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5">
                <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-emerald-100 bg-linear-to-br from-emerald-50 to-white px-6 py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Bird management</p>
                        <h2 id="edit-bird-title" class="mt-1 text-xl font-semibold text-slate-900">Edit bird</h2>
                        <p class="mt-1 text-sm text-slate-500">Update the bird profile and assignment details.</p>
                    </div>
                    <button type="button" wire:click="closeEditBird" aria-label="Close edit bird form"
                        class="grid size-9 place-items-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-white hover:text-slate-700">&times;</button>
                </div>
                <form wire:submit="updateBird" class="grid grid-cols-1 gap-5 px-6 py-6 sm:grid-cols-2">
                    @foreach ([
                        ['name', 'Name', 'text'],
                        ['species', 'Species', 'text'],
                        ['band_number', 'Band number', 'text'],
                        ['color_mutation', 'Color / mutation', 'text'],
                        ['birth_date', 'Birth date', 'date'],
                    ] as [$field, $label, $type])
                        <div>
                            <label for="edit-{{ $field }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</label>
                            <input id="edit-{{ $field }}" type="{{ $type }}" wire:model="{{ $field }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                            @error($field) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                    <div>
                        <label for="edit-cage_id" class="mb-1 block text-sm font-medium text-slate-700">Cage</label>
                        <select id="edit-cage_id" wire:model="cage_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                            <option value="">No cage assigned</option>
                            @foreach ($this->cages as $cage)
                                <option value="{{ $cage->id }}">Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                        @error('cage_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="edit-parent_male_id" class="mb-1 block text-sm font-medium text-slate-700">Father</label>
                        <select id="edit-parent_male_id" wire:model="parent_male_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                            <option value="">No father selected</option>
                            @foreach ($this->maleBirds as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->id }})</option>
                            @endforeach
                        </select>
                        @error('parent_male_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="edit-parent_female_id" class="mb-1 block text-sm font-medium text-slate-700">Mother</label>
                        <select id="edit-parent_female_id" wire:model="parent_female_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                            <option value="">No mother selected</option>
                            @foreach ($this->femaleBirds as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->id }})</option>
                            @endforeach
                        </select>
                        @error('parent_female_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="edit-sex" class="mb-1 block text-sm font-medium text-slate-700">Sex</label>
                        <select id="edit-sex" wire:model="sex" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                            <option value="unknown">Unknown</option><option value="male">Male</option><option value="female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit-status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                        <select id="edit-status" wire:model="status" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                            <option value="active">Active</option><option value="breeding">Breeding</option><option value="sick">Sick</option><option value="retired">Retired</option><option value="deceased">Deceased</option>
                        </select>
                    </div>
                    <div class="sticky bottom-0 -mx-6 -mb-6 flex justify-end gap-3 border-t border-slate-100 bg-white/95 px-6 py-4 backdrop-blur sm:col-span-2">
                        <button type="button" wire:click="closeEditBird" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="updateBird" class="dashboard-primary-button disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateBird">Save changes</span>
                            <span wire:loading wire:target="updateBird">Saving...</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>
