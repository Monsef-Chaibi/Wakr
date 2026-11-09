<?php

use App\Models\Bird;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

new class extends Component
{
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

    /** @return Collection<int, \App\Models\Cage> */
    public function getCagesProperty(): Collection
    {
        return auth()->user()->cages()->orderBy('id')->get(['id']);
    }

    /** @return Collection<int, Bird> */
    public function getMaleBirdsProperty(): Collection
    {
        return auth()->user()->birds()->where('sex', 'male')->orderBy('name')->get(['id', 'name']);
    }

    /** @return Collection<int, Bird> */
    public function getFemaleBirdsProperty(): Collection
    {
        return auth()->user()->birds()->where('sex', 'female')->orderBy('name')->get(['id', 'name']);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'in:male,female,unknown'],
            'cage_id' => ['nullable', 'integer', Rule::exists('cages', 'id')->where('user_id', auth()->id())],
            'status' => ['required', 'in:active,breeding,sick,retired,deceased'],
            'band_number' => ['nullable', 'string', 'max:255', 'unique:birds,band_number'],
            'color_mutation' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'parent_male_id' => ['nullable', 'integer', Rule::exists('birds', 'id')->where('user_id', auth()->id())->where('sex', 'male')],
            'parent_female_id' => ['nullable', 'integer', Rule::exists('birds', 'id')->where('user_id', auth()->id())->where('sex', 'female')],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->birds()->create([
            ...$validated,
            'cage_id' => $validated['cage_id'] ?: null,
            'parent_male_id' => $validated['parent_male_id'] ?: null,
            'parent_female_id' => $validated['parent_female_id'] ?: null,
        ]);

        $this->reset();
        $this->dispatch('bird-created');
    }
};
?>

<div>
    <form wire:submit="save">
        <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-3 text-sm text-slate-600">
            <span class="font-semibold text-blue-800">Profile basics</span>
            <p class="mt-1 text-xs">Add the bird’s identity first, then assign its cage and family information below.</p>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Bird Name</label>
                <input type="text" id="name" wire:model="name" required placeholder="e.g. Red 001"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="species" class="block text-sm font-medium text-slate-700 mb-1">Species</label>
                    <select id="species" wire:model="species"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Canary">Canary</option>
                        <option value="Gouldian Finch">Gouldian finch</option>
                    </select>
                @error('species') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sex" class="block text-sm font-medium text-slate-700 mb-1">Sex</label>
                <select id="sex" wire:model="sex"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="unknown">Unknown</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div>
                <label for="cage_id" class="block text-sm font-medium text-slate-700 mb-1">Cage</label>
                <select id="cage_id" wire:model="cage_id"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Cage</option>
                    @foreach ($this->cages as $cage)
                        <option value="{{ $cage->id }}">Cage CG-{{ str_pad((string) $cage->id, 4, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
                @error('cage_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select id="status" wire:model="status"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active">Active</option>
                    <option value="breeding">Breeding</option>
                    <option value="sick">Sick</option>
                    <option value="retired">Retired</option>
                    <option value="deceased">Deceased</option>
                </select>
            </div>

            <div>
                <label for="band_number" class="block text-sm font-medium text-slate-700 mb-1">Band Number</label>
                <input type="text" id="band_number" wire:model="band_number" placeholder="e.g. M001"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('band_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="color_mutation" class="block text-sm font-medium text-slate-700 mb-1">Color / Mutation</label>
                <input type="text" id="color_mutation" wire:model="color_mutation" placeholder="e.g. Red Factor Mosaic"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('color_mutation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-1">Birth Date</label>
                <input type="date" id="birth_date" wire:model="birth_date"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('birth_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="parent_male_id" class="block text-sm font-medium text-slate-700 mb-1">Father</label>
                <select id="parent_male_id" wire:model="parent_male_id"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">No father selected</option>
                @foreach ($this->maleBirds as $bird)
                    <option value="{{ $bird->id }}">{{ $bird->name }} ({{ $bird->id }})</option>
                @endforeach
                </select>
                @error('parent_male_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="parent_female_id" class="block text-sm font-medium text-slate-700 mb-1">Mother</label>
                <select id="parent_female_id" wire:model="parent_female_id"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">No mother selected</option>
                @foreach ($this->femaleBirds as $bird)
                    <option value="{{ $bird->id }}">{{ $bird->name }} ({{ $bird->id }})</option>
                @endforeach
                </select>
                @error('parent_female_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="sticky bottom-0 -mx-6 -mb-6 mt-6 flex justify-end gap-3 border-t border-slate-100 bg-white/95 px-6 py-4 backdrop-blur">
            <button type="button" id="cancelBtn"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                Cancel
            </button>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="dashboard-primary-button disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Accept</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
