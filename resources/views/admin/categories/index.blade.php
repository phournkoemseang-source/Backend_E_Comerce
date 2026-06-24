@extends('layouts.admin')

@section('title', 'Categories')
@section('page_title', 'Categories Management')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">All Categories</h3>
        <a href="{{ route('admin.categories.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-plus mr-2"></i>Add New Category
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Slug</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Products</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $category->slug }}</code>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold">
                                    {{ $category->products_count }} products
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $category->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline delete-form-{{ $category->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="delete-category-btn text-red-600 hover:text-red-800 text-sm font-semibold" data-category-name="{{ $category->name }}" data-category-id="{{ $category->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-600">
                                <i class="fas fa-inbox text-4xl mb-2 text-gray-400"></i>
                                <p class="mt-2">No categories found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-category-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Delete Category</h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete the category <span id="modal-category-name" class="font-semibold text-gray-800"></span>? This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-delete-category-btn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                        Cancel
                    </button>
                    <button type="button" id="confirm-delete-category-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                        Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-category-btn');
    const modal = document.getElementById('delete-category-modal');
    const modalCategoryName = document.getElementById('modal-category-name');
    const confirmButton = document.getElementById('confirm-delete-category-btn');
    const cancelButton = document.getElementById('cancel-delete-category-btn');
    let deleteForm = null;

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.dataset.categoryId;
            deleteForm = document.querySelector('.delete-form-' + categoryId);
            const categoryName = this.dataset.categoryName;
            modalCategoryName.textContent = categoryName;
            modal.style.display = 'flex';
        });
    });

    confirmButton.addEventListener('click', function() {
        if (deleteForm) {
            deleteForm.submit();
        }
    });

    cancelButton.addEventListener('click', function() {
        modal.style.display = 'none';
        deleteForm = null;
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            deleteForm = null;
        }
    });
});
</script>
@endpush