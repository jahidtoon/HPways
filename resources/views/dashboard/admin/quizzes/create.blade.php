@extends('layouts.dashboard')

@section('title', 'Create Quiz Node')
@section('page-title', 'Create New Quiz Node')

@php
function getTerminalPackages($terminalCode) {
    if (!$terminalCode) return null;
    
    $terminalToVisaType = config('quiz.terminal_to_visa_type', []);
    $visaType = $terminalToVisaType[$terminalCode] ?? null;
    
    if (!$visaType) return null;
    
    return \App\Models\Package::where('visa_type', $visaType)
        ->where('active', true)
        ->orderBy('price_cents')
        ->get();
}
@endphp

@section('styles')
<style>
    :root {
        --primary: #4f46e5;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
    }

    .form-container {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border);
    }

    .form-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 2rem;
    }

    .form-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 10px;
    }

    .form-subtitle {
        color: #6b7280;
        font-size: 1.1rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
        display: block;
        font-size: 15px;
    }

    .form-control {
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 15px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 16px 12px;
        padding-right: 40px;
    }

    .options-container {
        border: 2px dashed var(--border);
        border-radius: 15px;
        padding: 20px;
        background: var(--light);
        transition: all 0.3s ease;
    }

    .options-container:hover {
        border-color: var(--primary);
        background: rgba(79, 70, 229, 0.05);
    }

    .option-item {
        background: white;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        position: relative;
    }

    .option-item:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.1);
    }

    .option-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .option-badge {
        background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .remove-option {
        background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-option:hover {
        transform: scale(1.1);
    }

    .add-option {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 12px 24px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 20px auto 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary) 0%, #3730a3 100%);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 15px 30px;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-custom {
        background: #6b7280;
        color: white;
        border: none;
        border-radius: 25px;
        padding: 15px 30px;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-secondary-custom:hover {
        background: #4b5563;
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid var(--border);
    }

    .preview-container {
        background: var(--light);
        border: 2px solid var(--border);
        border-radius: 15px;
        padding: 20px;
        margin-top: 30px;
    }

    .preview-title {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preview-node {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .invalid-feedback {
        color: var(--danger);
        font-size: 14px;
        margin-top: 5px;
    }

    .position-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 768px) {
        .form-container {
            margin: 20px;
            padding: 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .position-inputs {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <div class="form-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h1 class="form-title">Create Quiz Node</h1>
            <p class="form-subtitle">Design a new decision point in your quiz flow</p>
        </div>

        <form action="{{ route('admin.quizzes.store') }}" method="POST" id="quiz-form">
            @csrf

            <!-- Node ID -->
            <div class="form-group">
                <label for="node_id" class="form-label">
                    <i class="fas fa-hashtag me-2"></i>Node ID
                </label>
                <input type="text" 
                       class="form-control @error('node_id') is-invalid @enderror" 
                       id="node_id" 
                       name="node_id" 
                       value="{{ old('node_id') }}"
                       placeholder="e.g., Q1, START, TERMINAL_1"
                       required>
                @error('node_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Unique identifier for this quiz node</small>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title" class="form-label">
                    <i class="fas fa-heading me-2"></i>Title
                </label>
                <input type="text" 
                       class="form-control @error('title') is-invalid @enderror" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}"
                       placeholder="Short descriptive title for this node"
                       required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Question -->
            <div class="form-group">
                <label for="question" class="form-label">
                    <i class="fas fa-question-circle me-2"></i>Question
                </label>
                <textarea class="form-control @error('question') is-invalid @enderror" 
                          id="question" 
                          name="question" 
                          rows="4"
                          placeholder="Enter the main question or prompt for this node"
                          required>{{ old('question') }}</textarea>
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Type -->
            <div class="form-group">
                <label for="type" class="form-label">
                    <i class="fas fa-list me-2"></i>Answer Type
                </label>
                <select class="form-control form-select @error('type') is-invalid @enderror" 
                        id="type" 
                        name="type" 
                        required>
                    <option value="">Select answer type</option>
                    <option value="single" {{ old('type') === 'single' ? 'selected' : '' }}>Single Choice</option>
                    <option value="multi" {{ old('type') === 'multi' ? 'selected' : '' }}>Multiple Choice</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Position -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-map-pin me-2"></i>Flowchart Position
                </label>
                <div class="position-inputs">
                    <div>
                        <input type="number" 
                               class="form-control @error('x') is-invalid @enderror" 
                               name="x" 
                               value="{{ old('x', 100) }}"
                               placeholder="X Position"
                               min="0">
                        @error('x')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Horizontal position</small>
                    </div>
                    <div>
                        <input type="number" 
                               class="form-control @error('y') is-invalid @enderror" 
                               name="y" 
                               value="{{ old('y', 100) }}"
                               placeholder="Y Position"
                               min="0">
                        @error('y')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Vertical position</small>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-list-ul me-2"></i>Answer Options
                </label>
                <div class="options-container">
                    <div id="options-list">
                        @if(old('options'))
                            @foreach(old('options') as $index => $option)
                                <div class="option-item">
                                    <div class="option-header">
                                        <span class="option-badge">Option {{ $index + 1 }}</span>
                                        <button type="button" class="remove-option" onclick="removeOption(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Code</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="options[{{ $index }}][code]" 
                                                   value="{{ $option['code'] ?? '' }}"
                                                   placeholder="A, B, YES, NO">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Label</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="options[{{ $index }}][label]" 
                                                   value="{{ $option['label'] ?? '' }}"
                                                   placeholder="Option text">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Next Node</label>
                                            <select class="form-control" name="options[{{ $index }}][next]" onchange="togglePackageSelection(this, {{ $index }})">
                                                <option value="">Select Next (optional)</option>
                                                @foreach($nextOptions as $nextOption)
                                                    <option value="{{ $nextOption }}" 
                                                            {{ ($option['next'] ?? '') === $nextOption ? 'selected' : '' }}>
                                                        {{ $nextOption }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="package-selection" id="package-selection-{{ $index }}" style="display: none;">
                                        <div class="col-12">
                                            <label class="form-label">Available Packages</label>
                                            <div class="package-options">
                                                @php
                                                    $terminalPackages = getTerminalPackages($option['next'] ?? '');
                                                @endphp
                                                @if($terminalPackages)
                                                    @foreach($terminalPackages as $package)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="options[{{ $index }}][packages][]" 
                                                                   value="{{ $package->code }}" 
                                                                   id="package-{{ $index }}-{{ $package->code }}"
                                                                   {{ in_array($package->code, $option['packages'] ?? []) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="package-{{ $index }}-{{ $package->code }}">
                                                                {{ $package->name }} - ${{ number_format($package->price_cents / 100, 2) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <small class="text-muted">No packages available for this terminal</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <label class="form-label">Available Packages (for end nodes)</label>
                                            <div id="packages-{{ $index }}" class="packages-container">
                                                @php
                                                    $selectedPackages = $option['packages'] ?? [];
                                                    $availablePackages = \App\Models\Package::where('active', true)->get();
                                                @endphp
                                                @foreach($availablePackages as $package)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="options[{{ $index }}][packages][]" 
                                                               value="{{ $package->id }}"
                                                               {{ in_array($package->id, $selectedPackages) ? 'checked' : '' }}>
                                                        <label class="form-check-label">
                                                            {{ $package->name }} ({{ $package->code }} - ${{ number_format($package->price_cents / 100, 2) }})
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="option-item">
                                <div class="option-header">
                                    <span class="option-badge">Option 1</span>
                                    <button type="button" class="remove-option" onclick="removeOption(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" name="options[0][code]" placeholder="A, B, YES, NO">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Label</label>
                                        <input type="text" class="form-control" name="options[0][label]" placeholder="Option text">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Next Node</label>
                                        <select class="form-control" name="options[0][next]" onchange="togglePackageSelection(this, 0)">
                                            <option value="">Select Next (optional)</option>
                                            @foreach($nextOptions as $nextOption)
                                                <option value="{{ $nextOption }}">{{ $nextOption }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="package-selection" id="package-selection-0" style="display: none;">
                                    <div class="col-12">
                                        <label class="form-label">Available Packages</label>
                                        <div class="package-options">
                                            <small class="text-muted">Select a terminal first to see available packages</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="add-option" onclick="addOption()">
                        <i class="fas fa-plus"></i>
                        Add Option
                    </button>
                </div>
                @error('options')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.quizzes.index') }}" class="btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i>
                    Back to Quiz Management
                </a>
                <button type="submit" class="btn-primary-custom">
                    <i class="fas fa-save"></i>
                    Create Quiz Node
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let optionCounter = {{ old('options') ? count(old('options')) : 1 }};
const nextOptions = @json($nextOptions);

function addOption() {
    const optionsList = document.getElementById('options-list');
    const newOption = document.createElement('div');
    newOption.className = 'option-item';
    
    let nextOptionsHtml = '<option value="">Select Next (optional)</option>';
    nextOptions.forEach(option => {
        nextOptionsHtml += `<option value="${option}">${option}</option>`;
    });
    
    newOption.innerHTML = `
        <div class="option-header">
            <span class="option-badge">Option ${optionCounter + 1}</span>
            <button type="button" class="remove-option" onclick="removeOption(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Code</label>
                <input type="text" class="form-control" name="options[${optionCounter}][code]" placeholder="A, B, YES, NO">
            </div>
            <div class="col-md-5">
                <label class="form-label">Label</label>
                <input type="text" class="form-control" name="options[${optionCounter}][label]" placeholder="Option text">
            </div>
            <div class="col-md-4">
                <label class="form-label">Next Node</label>
                <select class="form-control" name="options[${optionCounter}][next]">
                    ${nextOptionsHtml}
                </select>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <label class="form-label">Available Packages (for end nodes)</label>
                <div id="packages-${optionCounter}" class="packages-container">
                    @php
                        $availablePackages = \App\Models\Package::where('active', true)->get();
                    @endphp
                    @foreach($availablePackages as $package)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" 
                                   name="options[${optionCounter}][packages][]" 
                                   value="{{ $package->id }}">
                            <label class="form-check-label">
                                {{ $package->name }} ({{ $package->code }} - ${{ number_format($package->price_cents / 100, 2) }})
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    `;
    
    optionsList.appendChild(newOption);
    optionCounter++;
    updateOptionNumbers();
}

function removeOption(button) {
    const optionItem = button.closest('.option-item');
    optionItem.remove();
    updateOptionNumbers();
}

function updateOptionNumbers() {
    const options = document.querySelectorAll('.option-item');
    options.forEach((option, index) => {
        const badge = option.querySelector('.option-badge');
        badge.textContent = `Option ${index + 1}`;
        
        // Update input names
        const inputs = option.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/options\[\d+\]/, `options[${index}]`);
                input.setAttribute('name', newName);
            }
        });
    });
}

function togglePackageSelection(selectElement, optionIndex) {
    const selectedValue = selectElement.value;
    const packageContainer = document.getElementById(`package-selection-${optionIndex}`);
    
    if (selectedValue && isActionableTerminal(selectedValue)) {
        // Show package selection
        packageContainer.style.display = 'block';
        loadPackagesForTerminal(selectedValue, optionIndex);
    } else {
        // Hide package selection
        packageContainer.style.display = 'none';
    }
}

function isActionableTerminal(terminalCode) {
    const actionableTerminals = @json(config('quiz.actionable_terminals', []));
    return actionableTerminals.includes(terminalCode);
}

function loadPackagesForTerminal(terminalCode, optionIndex) {
    const packageContainer = document.querySelector(`#package-selection-${optionIndex} .package-options`);
    
    // Get visa type from terminal
    const terminalToVisaType = @json(config('quiz.terminal_to_visa_type', []));
    const visaType = terminalToVisaType[terminalCode];
    
    if (!visaType) {
        packageContainer.innerHTML = '<small class="text-muted">No packages available for this terminal</small>';
        return;
    }
    
    // Fetch packages for this visa type
    fetch(`/admin/api/packages?visa_type=${visaType}`)
        .then(response => response.json())
        .then(packages => {
            if (packages.length === 0) {
                packageContainer.innerHTML = '<small class="text-muted">No packages available for this terminal</small>';
                return;
            }
            
            let html = '';
            packages.forEach(package => {
                const checked = package.code === 'basic' ? 'checked' : ''; // Default to basic package
                html += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               name="options[${optionIndex}][packages][]" 
                               value="${package.code}" 
                               id="package-${optionIndex}-${package.code}" ${checked}>
                        <label class="form-check-label" for="package-${optionIndex}-${package.code}">
                            ${package.name} - $${(package.price_cents / 100).toFixed(2)}
                        </label>
                    </div>
                `;
            });
            packageContainer.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading packages:', error);
            packageContainer.innerHTML = '<small class="text-danger">Error loading packages</small>';
        });
}
</script>
@endpush