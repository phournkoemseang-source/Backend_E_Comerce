@extends('layouts.admin')

@section('title', 'Create Category')
@section('page_title', 'Create New Category')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6">
            @csrf

            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-list text-green-600 mr-2"></i>Category Name
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Enter category name"
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 pt-6">
                <button 
                    type="submit" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center"
                >
                    <i class="fas fa-save mr-2"></i>Create Category
                </button>
                <a 
                    href="{{ route('admin.categories.index') }}" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition text-center flex items-center justify-center"
                >
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
