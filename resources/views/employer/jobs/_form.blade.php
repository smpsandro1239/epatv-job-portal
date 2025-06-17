@csrf
<div class="mb-4">
    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Job Title:</label>
    <input type="text" id="title" name="title" value="{{ old('title', $job->title ?? '') }}" required
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror">
    @error('title')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="area_of_interest_id" class="block text-gray-700 text-sm font-bold mb-2">Area of Interest:</label>
    <select name="area_of_interest_id" id="area_of_interest_id" required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('area_of_interest_id') border-red-500 @enderror">
        <option value="">Select an Area of Interest</option>
        @foreach($areasOfInterest as $area)
            <option value="{{ $area->id }}" {{ (old('area_of_interest_id', $job->area_of_interest_id ?? '') == $area->id) ? 'selected' : '' }}>
                {{ $area->name }}
            </option>
        @endforeach
    </select>
    @error('area_of_interest_id')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
    <textarea id="description" name="description" rows="5" required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $job->description ?? '') }}</textarea>
    @error('description')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location:</label>
        <input type="text" id="location" name="location" value="{{ old('location', $job->location ?? '') }}"
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('location') border-red-500 @enderror">
        @error('location')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="contract_type" class="block text-gray-700 text-sm font-bold mb-2">Contract Type:</label>
        <input type="text" id="contract_type" name="contract_type" value="{{ old('contract_type', $job->contract_type ?? '') }}"
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('contract_type') border-red-500 @enderror">
        @error('contract_type')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <label for="salary" class="block text-gray-700 text-sm font-bold mb-2">Salary:</label>
        <input type="text" id="salary" name="salary" value="{{ old('salary', $job->salary ?? '') }}"
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('salary') border-red-500 @enderror">
        @error('salary')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="expiration_date" class="block text-gray-700 text-sm font-bold mb-2">Expiration Date:</label>
        <input type="date" id="expiration_date" name="expiration_date" value="{{ old('expiration_date', isset($job->expiration_date) ? (\Carbon\Carbon::parse($job->expiration_date)->format('Y-m-d')) : '') }}"
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('expiration_date') border-red-500 @enderror">
        @error('expiration_date')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex items-center justify-between mt-6">
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        {{ $submitButtonText ?? 'Submit' }}
    </button>
    <a href="{{ route('employer.jobs.index') }}" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800">
        Cancel
    </a>
</div>
