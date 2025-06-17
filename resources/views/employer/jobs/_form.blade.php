@csrf
<div class="space-y-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $job->title ?? '') }}" required
               class="shadow-sm appearance-none border @error('title') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               placeholder="e.g., Senior Software Engineer">
        @error('title')
            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="area_of_interest_id" class="block text-sm font-medium text-gray-700 mb-1">Area of Interest <span class="text-red-500">*</span></label>
        <select name="area_of_interest_id" id="area_of_interest_id" required
                class="shadow-sm appearance-none border @error('area_of_interest_id') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select an Area of Interest</option>
            @foreach($areasOfInterest as $area)
                <option value="{{ $area->id }}" {{ (old('area_of_interest_id', $job->area_of_interest_id ?? '') == $area->id) ? 'selected' : '' }}>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
        @error('area_of_interest_id')
            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
        <textarea id="description" name="description" rows="6" required
                  class="shadow-sm appearance-none border @error('description') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Provide a detailed job description, responsibilities, and requirements.">{{ old('description', $job->description ?? '') }}</textarea>
        @error('description')
            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input type="text" id="location" name="location" value="{{ old('location', $job->location ?? '') }}"
                   class="shadow-sm appearance-none border @error('location') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="e.g., New York, NY or Remote">
            @error('location')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">Contract Type</label>
            <input type="text" id="contract_type" name="contract_type" value="{{ old('contract_type', $job->contract_type ?? '') }}"
                   class="shadow-sm appearance-none border @error('contract_type') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="e.g., Full-Time, Part-Time, Contract">
            @error('contract_type')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
        <div>
            <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Salary (Optional)</label>
            <input type="text" id="salary" name="salary" value="{{ old('salary', $job->salary ?? '') }}"
                   class="shadow-sm appearance-none border @error('salary') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="e.g., $70,000 - $90,000 per year, or Negotiable">
            @error('salary')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date (Optional)</label>
            <input type="date" id="expiration_date" name="expiration_date"
                   value="{{ old('expiration_date', isset($job->expiration_date) ? (\Carbon\Carbon::parse($job->expiration_date)->format('Y-m-d')) : '') }}"
                   class="shadow-sm appearance-none border @error('expiration_date') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('expiration_date')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end space-x-4">
    <a href="{{ route('employer.jobs.index') }}"
       class="text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium py-2 px-4 rounded-md border border-gray-300 shadow-sm transition duration-150">
        Cancel
    </a>
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 shadow-md hover:shadow-lg transition duration-150 inline-flex items-center">
        <svg class="inline-block w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ $submitButtonText ?? 'Submit Job' }}
    </button>
</div>
