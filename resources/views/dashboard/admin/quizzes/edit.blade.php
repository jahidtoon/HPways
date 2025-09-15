@extends('layouts.dashboard')

@section('title', 'Edit Quiz Node')
@section('page-title', 'Edit Quiz Node')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Quiz Node: {{ $quizNode->node_id }}
                    </h5>
                    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Quiz Management
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quizzes.update', $quizNode) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="node_id" class="form-label">Node ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('node_id') is-invalid @enderror" 
                                           id="node_id" name="node_id" value="{{ old('node_id', $quizNode->node_id) }}" placeholder="e.g., 1, 2A, 3B1">
                                    @error('node_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Question Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">Select Type</option>
                                        <option value="single" {{ old('type', $quizNode->type) === 'single' ? 'selected' : '' }}>Single Choice</option>
                                        <option value="multi" {{ old('type', $quizNode->type) === 'multi' ? 'selected' : '' }}>Multiple Choice</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $quizNode->title) }}" placeholder="Brief title for this node">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('question') is-invalid @enderror" 
                                      id="question" name="question" rows="3" placeholder="Enter the quiz question">{{ old('question', $quizNode->question) }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="x" class="form-label">X Position</label>
                                    <input type="number" class="form-control @error('x') is-invalid @enderror" 
                                           id="x" name="x" value="{{ old('x', $quizNode->x) }}" min="0">
                                    @error('x')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="y" class="form-label">Y Position</label>
                                    <input type="number" class="form-control @error('y') is-invalid @enderror" 
                                           id="y" name="y" value="{{ old('y', $quizNode->y) }}" min="0">
                                    @error('y')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Answer Options <span class="text-danger">*</span></label>
                            <div id="options-container">
                                @php
                                    $options = old('options', $quizNode->options);
                                @endphp
                                @foreach($options as $index => $option)
                                    <div class="option-row mb-2">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="options[{{ $index }}][code]" 
                                                       placeholder="Code (e.g., A, YES)" value="{{ $option['code'] ?? '' }}">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="options[{{ $index }}][label]" 
                                                       placeholder="Option Label" value="{{ $option['label'] ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="options[{{ $index }}][next]" 
                                                       placeholder="Next Node (optional)" value="{{ $option['next'] ?? '' }}">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-option">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                            @error('options')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Quiz Node
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let optionIndex = {{ count(old('options', $quizNode->options)) }};

document.getElementById('add-option').addEventListener('click', function() {
    const container = document.getElementById('options-container');
    const newOption = document.createElement('div');
    newOption.className = 'option-row mb-2';
    newOption.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" name="options[${optionIndex}][code]" placeholder="Code (e.g., A, YES)">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="options[${optionIndex}][label]" placeholder="Option Label">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="options[${optionIndex}][next]" placeholder="Next Node (optional)">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-option">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newOption);
    optionIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-option') || e.target.parentElement.classList.contains('remove-option')) {
        const optionRow = e.target.closest('.option-row');
        if (document.querySelectorAll('.option-row').length > 1) {
            optionRow.remove();
        } else {
            alert('At least one option is required.');
        }
    }
});
</script>
@endpush