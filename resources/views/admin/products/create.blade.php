@extends('layouts.admin')

@section('title', 'Create Product')
@section('page_title', 'Create New Product')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-list text-blue-600 mr-2"></i>Category
                </label>
                <select 
                    name="category_id" 
                    id="category_id"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-box text-blue-600 mr-2"></i>Product Name
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter product name"
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left text-blue-600 mr-2"></i>Description
                </label>
                <textarea 
                    name="description" 
                    id="description"
                    rows="5"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter product description"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>Price
                </label>
                <input 
                    type="number" 
                    name="price" 
                    id="price"
                    value="{{ old('price') }}"
                    step="0.01"
                    min="0"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00"
                >
                @error('price')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stock -->
            <div>
                <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-warehouse text-purple-600 mr-2"></i>Stock Quantity
                </label>
                <input 
                    type="number" 
                    name="stock" 
                    id="stock"
                    value="{{ old('stock') }}"
                    min="0"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0"
                >
                @error('stock')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Image - Drag & Drop -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-image text-indigo-600 mr-2"></i>Product Image
                </label>
                <div 
                    id="dropzone"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer"
                >
                    <input 
                        type="file" 
                        name="image" 
                        id="image"
                        accept="image/*"
                        class="hidden"
                    >
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-600 mb-2">Drop image here or click to upload</p>
                    <p class="text-gray-400 text-sm">Supports: JPG, PNG, GIF, WEBP (Max 2MB)</p>
                </div>
                <div id="preview" class="mt-4 hidden">
                    <img id="previewImg" class="w-32 h-32 object-cover rounded-lg mx-auto" alt="Preview">
                </div>
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4 pt-6">
                <button 
                    type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center"
                >
                    <i class="fas fa-save mr-2"></i>Create Product
                </button>
                <a 
                    href="{{ route('admin.products.index') }}" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition text-center flex items-center justify-center"
                >
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('image');
        const preview = document.getElementById('preview');
        const previewImg = document.getElementById('previewImg');

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            fileInput.files = e.dataTransfer.files;
            handleFiles();
        });

        fileInput.addEventListener('change', handleFiles);

        function handleFiles() {
            const files = fileInput.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(files[0]);
            }
        }
    </script>
    @endpush
@endsection