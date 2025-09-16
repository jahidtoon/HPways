@extends('layouts.dashboard')

@section('title', 'Quiz Management')
@section('page-title', 'Visual Quiz Flow Management')

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

    .quiz-container {
        min-height: calc(100vh - 120px);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
    }

    .quiz-toolbar {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        padding: 15px 25px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .toolbar-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-visual {
        padding: 8px 16px;
        border: none;
        border-radius: 25px;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary-visual {
        background: linear-gradient(135deg, var(--primary) 0%, #3730a3 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .btn-primary-visual:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-success-visual {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-success-visual:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
        text-decoration: none;
    }

        /* Canvas Container */
        .quiz-canvas {
            position: relative;
            width: 100%;
            /* let it flex to fill container height */
            height: auto;
            flex: 1 1 auto;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
            min-height: 600px;
            cursor: grab;
            user-select: none;
        }
        
        .quiz-canvas.panning {
            cursor: grabbing !important;
        }

    .canvas-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 3000px;
        height: 2000px;
        transform-origin: 0 0;
        transition: transform 0.06s ease-out;
        will-change: transform;
        background-image: 
            linear-gradient(to right, #e9ecef 1px, transparent 1px),
            linear-gradient(to bottom, #e9ecef 1px, transparent 1px);
        background-size: 30px 30px;
        background-position: 0 0;
    }

    .quiz-node {
        position: absolute;
        background: white;
        border-radius: 15px;
        padding: 20px;
        min-width: 200px;
        max-width: 300px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        cursor: move;
        transition: all 0.3s ease;
        border: 3px solid transparent;
    }

    .quiz-node:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        border-color: var(--primary);
    }

    .quiz-node.dragging {
        transform: rotate(5deg) scale(1.1);
        z-index: 1000;
        opacity: 0.9;
    }

    .node-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
    }

    .node-id {
        background: linear-gradient(135deg, var(--primary) 0%, #3730a3 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .node-type {
        background: var(--success);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 500;
    }

    .node-title {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
        font-size: 16px;
    }

    .node-question {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 15px;
    }

    .node-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .option-item {
        background: var(--light);
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .option-code {
        background: var(--info);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 10px;
    }

    .node-actions {
        position: absolute;
        top: -10px;
        right: -10px;
        display: flex;
        gap: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .quiz-node:hover .node-actions {
        opacity: 1;
    }

    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .edit-btn {
        background: linear-gradient(135deg, var(--primary) 0%, #3730a3 100%);
        color: white;
    }

    .delete-btn {
        background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        color: white;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

        .connection-line {
        position: absolute;
        height: 4px;
        background: linear-gradient(90deg, #007bff, #0056b3);
        z-index: 5;
        pointer-events: auto;
        opacity: 0.9;
        box-shadow: 0 2px 6px rgba(0,123,255,0.4);
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .connection-line:hover {
        height: 6px;
        opacity: 1;
        box-shadow: 0 3px 10px rgba(0,123,255,0.6);
        transform: translateY(-1px);
    }
    
    .connection-line:after {
        content: '';
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 10px solid #007bff;
        border-top: 5px solid transparent;
        border-bottom: 5px solid transparent;
        transition: all 0.3s ease;
    }
    
    .connection-line:hover:after {
        border-left: 12px solid #007bff;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        right: -12px;
    }

        /* Zoom Controls */
    .zoom-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.95);
        padding: 12px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .zoom-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        background: rgba(255,255,255,0.9);
        color: var(--dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
    }

    .zoom-btn:hover {
        background: white;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

        .zoom-info {
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        color: #495057;
        margin-top: 5px;
        padding: 2px 8px;
        background: rgba(248, 249, 250, 0.8);
        border-radius: 4px;
        transition: color 0.3s ease;
    }

    /* Notification System */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        color: #333;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 9999;
        border-left: 4px solid #007bff;
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 300px;
    }

    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification.success {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
    }

    .notification.error {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
    }

    .notification i {
        font-size: 16px;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .view-toggle {
        background: white;
        border-radius: 25px;
        padding: 4px;
        display: flex;
        gap: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .toggle-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: transparent;
        color: #6b7280;
    }

    .toggle-btn.active {
        background: linear-gradient(135deg, var(--primary) 0%, #3730a3 100%);
        color: white;
        box-shadow: 0 2px 10px rgba(79, 70, 229, 0.3);
    }

    .table-view {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, var(--primary) 0%, #3730a3 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .custom-table {
        width: 100%;
        margin: 0;
    }

    .custom-table th {
        background: var(--light);
        color: var(--dark);
        font-weight: 600;
        padding: 15px 20px;
        border: none;
        font-size: 14px;
    }

    .custom-table td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .custom-table tbody tr:hover {
        background: var(--light);
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    }

    .notification.success {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    }

    .notification.error {
        background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    }

    .notification.show {
        transform: translateX(0);
    }

    @media (max-width: 768px) {
        .quiz-toolbar {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }
        
        .toolbar-group {
            justify-content: center;
        }
        
        .quiz-canvas {
            height: 400px;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $statsTotal ?? $quizNodes->total() }}</div>
            <div class="stat-label">Total Nodes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statsSingle ?? $quizNodes->where('type', 'single')->count() }}</div>
            <div class="stat-label">Single Choice</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statsMulti ?? $quizNodes->where('type', 'multi')->count() }}</div>
            <div class="stat-label">Multi Choice</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statsOptions ?? $quizNodes->sum(function($node) { return count($node->options); }) }}</div>
            <div class="stat-label">Total Options</div>
        </div>
    </div>

    <!-- Main Quiz Container -->
    <div class="quiz-container">
        <!-- Toolbar -->
        <div class="quiz-toolbar">
            <div class="toolbar-group">
                <h4 class="mb-0" style="color: var(--dark); font-weight: 600;">
                    <i class="fas fa-project-diagram me-2"></i>
                    Quiz Flow Builder
                </h4>
            </div>
            
            <div class="toolbar-group">
                <div class="view-toggle">
                    <button class="toggle-btn active" onclick="switchView('flowchart')" id="flowchart-btn">
                        <i class="fas fa-project-diagram me-1"></i> Flowchart
                    </button>
                    <button class="toggle-btn" onclick="switchView('table')" id="table-btn">
                        <i class="fas fa-table me-1"></i> Table
                    </button>
                </div>
                
                <a href="{{ route('admin.quizzes.create') }}" class="btn-visual btn-success-visual">
                    <i class="fas fa-plus"></i>
                    Add Node
                </a>
                
                <button onclick="autoArrange()" class="btn-visual btn-primary-visual">
                    <i class="fas fa-magic"></i>
                    Auto Arrange
                </button>
                
                <button onclick="saveAllNodePositions()" class="btn-visual btn-primary-visual">
                    <i class="fas fa-save"></i>
                    Save Layout
                </button>
            </div>
        </div>

        <!-- Flowchart View -->
        <div id="flowchart-view" class="quiz-canvas">
            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button class="zoom-btn" onclick="zoomIn()" title="Zoom In">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="zoom-btn" onclick="resetZoom()" title="Reset Zoom">
                    <i class="fas fa-home"></i>
                </button>
                <button class="zoom-btn" onclick="fitToScreen()" title="Fit to Screen">
                    <i class="fas fa-expand"></i>
                </button>
                <div class="zoom-info" id="zoom-info">
                    <span id="zoom-level">100%</span>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="canvas-container" id="canvas-container">
                @php($canvasNodes = isset($allQuizNodes) ? $allQuizNodes : $quizNodes)
                @if($canvasNodes->count() > 0)
                    @foreach($canvasNodes as $node)
                        <div class="quiz-node" 
                             data-node-id="{{ $node->node_id }}" 
                             style="left: {{ $node->x }}px; top: {{ $node->y }}px;">
                            
                            <div class="node-header">
                                <div class="node-id">{{ $node->node_id }}</div>
                                <div class="node-type">{{ ucfirst($node->type) }}</div>
                            </div>
                            
                            <div class="node-title">{{ $node->title }}</div>
                            <div class="node-question">{{ Str::limit($node->question, 60) }}</div>
                            
                            <div class="node-options">
                                @foreach($node->options as $option)
                                    <div class="option-item">
                                        <span>{{ Str::limit($option['label'], 25) }}</span>
                                        @if(!empty($option['next']))
                                            <div class="option-code">→ {{ $option['next'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="node-actions">
                                <a href="{{ route('admin.quizzes.edit', $node) }}" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.quizzes.destroy', $node) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" 
                                            onclick="return confirm('Are you sure you want to delete this node?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center text-white" 
                         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <i class="fas fa-project-diagram fa-5x mb-4" style="opacity: 0.3;"></i>
                        <h3 class="mb-3">No Quiz Nodes Found</h3>
                        <p class="mb-4 text-center" style="opacity: 0.8;">Start building your quiz flow by creating your first node</p>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn-visual btn-success-visual">
                            <i class="fas fa-plus me-2"></i>Create First Node
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Table View -->
        <div id="table-view" class="table-view" style="display: none;">
            <div class="table-header">
                <h5 class="table-title">Quiz Nodes Management</h5>
            </div>
            
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Node ID</th>
                            <th>Title</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Options</th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizNodes as $node)
                            <tr>
                                <td>
                                    <span class="node-id">{{ $node->node_id }}</span>
                                </td>
                                <td>{{ $node->title }}</td>
                                <td>{{ Str::limit($node->question, 50) }}</td>
                                <td>
                                    <span class="badge {{ $node->type === 'single' ? 'bg-success' : 'bg-info' }}">
                                        {{ ucfirst($node->type) }}
                                    </span>
                                </td>
                                <td>{{ count($node->options) }}</td>
                                <td>
                                    <small class="text-muted">
                                        X: {{ $node->x }}, Y: {{ $node->y }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.quizzes.edit', $node) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.quizzes.destroy', $node) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to delete this node?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-question-circle fa-3x mb-3"></i>
                                        <p>No quiz nodes found. <a href="{{ route('admin.quizzes.create') }}">Create your first node</a></p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($quizNodes->hasPages())
                <div class="d-flex justify-content-center p-3">
                    {{ $quizNodes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let nodes = [];
let isDragging = false;
let dragNode = null;
let dragOffset = { x: 0, y: 0 };
let isPanning = false;
let panStart = { x: 0, y: 0 };
let panOffset = { x: 0, y: 0 };
let zoomLevel = 1;
let minZoom = 0.1;
let maxZoom = 3;
let userInteracted = false; // prevent auto-fit after user action

// Global zoom functions - must be declared before DOM ready
window.zoomIn = function() {
    console.log('Zoom In clicked!');
    const newZoom = Math.min(maxZoom, zoomLevel + 0.2);
    if (newZoom !== zoomLevel) {
        zoomLevel = newZoom;
        updateTransform();
        updateZoomInfo();
        setTimeout(drawConnections, 10);
        userInteracted = true;
    }
};

window.zoomOut = function() {
    console.log('Zoom Out clicked!');
    const newZoom = Math.max(minZoom, zoomLevel - 0.2);
    if (newZoom !== zoomLevel) {
        zoomLevel = newZoom;
        updateTransform();
        updateZoomInfo();
        setTimeout(drawConnections, 10);
        userInteracted = true;
    }
};

window.resetZoom = function() {
    console.log('Reset Zoom clicked!');
    zoomLevel = 1;
    panOffset = { x: 0, y: 0 };
    updateTransform();
    updateZoomInfo();
    setTimeout(drawConnections, 10);
    userInteracted = true;
};

// Initialize the flowchart
document.addEventListener('DOMContentLoaded', function() {
    initializeFlowchart();
    setupDragAndDrop();
    setupZoomAndPan();
    drawConnections();
    updateZoomInfo();
    // Auto-fit initially for better layout visibility
    if (nodes.length > 0) {
        setTimeout(() => !userInteracted && fitToScreen(120), 200);
    }
});

function initializeFlowchart() {
    nodes = Array.from(document.querySelectorAll('.quiz-node')).map(node => ({
        id: node.dataset.nodeId,
        element: node,
        x: parseInt(node.style.left) || 100,
        y: parseInt(node.style.top) || 100
    }));
}

function setupZoomAndPan() {
    const canvas = document.querySelector('.quiz-canvas');
    const container = document.getElementById('canvas-container');
    
    console.log('Setting up zoom and pan...', canvas, container);
    
    // Mouse wheel zoom (focus on cursor)
    const onWheel = function(e) {
        console.log('Wheel event detected!');
        e.preventDefault();

        const rect = canvas.getBoundingClientRect();
        const cursorX = e.clientX - rect.left;
        const cursorY = e.clientY - rect.top;

        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const newZoom = Math.max(minZoom, Math.min(maxZoom, zoomLevel + delta));

        if (newZoom !== zoomLevel) {
            // Compute canvas point under cursor before zoom
            const canvasPointX = (cursorX - panOffset.x) / zoomLevel;
            const canvasPointY = (cursorY - panOffset.y) / zoomLevel;

            // Update zoom, then adjust pan so point stays under cursor
            zoomLevel = newZoom;
            panOffset.x = cursorX - canvasPointX * zoomLevel;
            panOffset.y = cursorY - canvasPointY * zoomLevel;

            updateTransform();
            updateZoomInfo();
            setTimeout(drawConnections, 10);
            userInteracted = true;
        }
    };
    canvas.addEventListener('wheel', onWheel, { passive: false });
    
    // Pan functionality with simple detection
    canvas.addEventListener('mousedown', function(e) {
        console.log('Mouse down on canvas!', e.target);
        
        // Simple check - if not clicking on a node
        if (!e.target.classList.contains('quiz-node') && !e.target.closest('.quiz-node')) {
            console.log('Starting pan...');
            isPanning = true;
            canvas.classList.add('panning');
            panStart.x = e.clientX - panOffset.x;
            panStart.y = e.clientY - panOffset.y;
            e.preventDefault();
            userInteracted = true;
        }
    });
    
    document.addEventListener('mousemove', function(e) {
        if (isPanning) {
            console.log('Panning...');
            panOffset.x = e.clientX - panStart.x;
            panOffset.y = e.clientY - panStart.y;
            updateTransform();
            e.preventDefault();
        }
    });
    
    document.addEventListener('mouseup', function(e) {
        if (isPanning) {
            console.log('Stopping pan...');
            isPanning = false;
            canvas.classList.remove('panning');
            drawConnections();
            userInteracted = true;
        }
    });
    
    // Test the zoom buttons
    console.log('Pan offset:', panOffset);
    console.log('Zoom level:', zoomLevel);
}

function updateTransform() {
    const container = document.getElementById('canvas-container');
    if (container) {
        container.style.transform = `translate(${panOffset.x}px, ${panOffset.y}px) scale(${zoomLevel})`;
    }
}

function updateZoomInfo() {
    const zoomInfo = document.getElementById('zoom-info');
    if (zoomInfo) {
        const percentage = Math.round(zoomLevel * 100);
        const zoomText = document.getElementById('zoom-level');
        if (zoomText) {
            zoomText.textContent = percentage + '%';
        } else {
            zoomInfo.textContent = percentage + '%';
        }
        
        // Update zoom info color based on zoom level
        if (percentage < 50) {
            zoomInfo.style.color = '#dc3545'; // Red
        } else if (percentage > 150) {
            zoomInfo.style.color = '#fd7e14'; // Orange
        } else {
            zoomInfo.style.color = '#28a745'; // Green
        }
    }
}



function setupDragAndDrop() {
    nodes.forEach(node => {
        const element = node.element;
        
        element.addEventListener('mousedown', function(e) {
            if (e.target.closest('.node-actions')) return;
            
            isDragging = true;
            dragNode = node;
            element.classList.add('dragging');
            
            const rect = element.getBoundingClientRect();
            const container = document.getElementById('canvas-container');
            const containerRect = container.getBoundingClientRect();
            
            dragOffset.x = (e.clientX - containerRect.left) / zoomLevel - node.x;
            dragOffset.y = (e.clientY - containerRect.top) / zoomLevel - node.y;
            
            e.preventDefault();
            e.stopPropagation();
            userInteracted = true;
        });
    });
    
    document.addEventListener('mousemove', function(e) {
        if (!isDragging || !dragNode) return;
        
        const container = document.getElementById('canvas-container');
        const containerRect = container.getBoundingClientRect();
        
        const x = (e.clientX - containerRect.left) / zoomLevel - dragOffset.x;
        const y = (e.clientY - containerRect.top) / zoomLevel - dragOffset.y;
        
        dragNode.x = Math.max(0, x);
        dragNode.y = Math.max(0, y);
        
        dragNode.element.style.left = dragNode.x + 'px';
        dragNode.element.style.top = dragNode.y + 'px';
        
        drawConnections();
        userInteracted = true;
    });
    
    document.addEventListener('mouseup', function() {
        if (isDragging && dragNode) {
            dragNode.element.classList.remove('dragging');
            showNotification('Node position updated! Don\'t forget to save.', 'success');
        }
        
        isDragging = false;
        dragNode = null;
    });
}

function drawConnections() {
    // Remove existing connection lines
    document.querySelectorAll('.connection-line').forEach(line => line.remove());
    
    const container = document.getElementById('canvas-container');
    
    nodes.forEach(sourceNode => {
        const sourceElement = sourceNode.element;
        
        // Get all options with next references
        const options = sourceElement.querySelectorAll('.option-code');
        
        options.forEach(option => {
            const nextNodeId = option.textContent.replace('→ ', '').trim();
            const targetNode = nodes.find(n => n.id === nextNodeId);
            
            if (targetNode) {
                // Calculate connection points based on actual node sizes
                const srcW = sourceElement.offsetWidth || 300;
                const srcH = sourceElement.offsetHeight || 200;
                const tgtEl = targetNode.element;
                const tgtW = tgtEl.offsetWidth || 300;
                const tgtH = tgtEl.offsetHeight || 200;

                const startX = sourceNode.x + srcW; // Right edge of source node
                const startY = sourceNode.y + (srcH / 2); // Middle of source node
                const endX = targetNode.x - 10; // Slight inset before left edge of target
                const endY = targetNode.y + (tgtH / 2); // Middle of target node
                
                // Create connection line with better visual
                const line = document.createElement('div');
                line.className = 'connection-line';
                
                const deltaX = endX - startX;
                const deltaY = endY - startY;
                const length = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
                const angle = Math.atan2(deltaY, deltaX) * 180 / Math.PI;
                
                line.style.width = length + 'px';
                line.style.left = startX + 'px';
                line.style.top = (startY - 2) + 'px'; // Center the line
                line.style.transform = `rotate(${angle}deg)`;
                line.style.transformOrigin = '0 50%';
                
                // Add enhanced hover effect and tooltip
                line.title = `Connection: ${sourceNode.question || sourceNode.id} → ${targetNode.question || targetNode.id}`;
                line.style.cursor = 'pointer';
                
                line.addEventListener('mouseenter', function() {
                    this.style.transform = `rotate(${angle}deg) scale(1.05)`;
                    this.style.zIndex = '10';
                    
                    // Highlight connected nodes
                    sourceElement.style.boxShadow = '0 0 20px rgba(0,123,255,0.6)';
                    targetNode.element.style.boxShadow = '0 0 20px rgba(40,167,69,0.6)';
                });
                
                line.addEventListener('mouseleave', function() {
                    this.style.transform = `rotate(${angle}deg) scale(1)`;
                    this.style.zIndex = '5';
                    
                    // Remove highlight from nodes
                    sourceElement.style.boxShadow = '';
                    targetNode.element.style.boxShadow = '';
                });
                
                container.appendChild(line);
            }
        });
    });
}

// Fit all nodes into view, with optional padding
window.fitToScreen = function(padding = 100) {
    const canvas = document.querySelector('.quiz-canvas');
    const container = document.getElementById('canvas-container');
    if (!canvas || !container || nodes.length === 0) {
        resetZoom();
        return;
    }

    // Compute bounding box of all nodes
    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
    nodes.forEach(n => {
        const el = n.element;
        const w = el.offsetWidth || 300;
        const h = el.offsetHeight || 200;
        minX = Math.min(minX, n.x);
        minY = Math.min(minY, n.y);
        maxX = Math.max(maxX, n.x + w);
        maxY = Math.max(maxY, n.y + h);
    });

    const bboxW = Math.max(1, maxX - minX);
    const bboxH = Math.max(1, maxY - minY);

    const viewW = canvas.clientWidth - padding * 2;
    const viewH = canvas.clientHeight - padding * 2;
    const scaleX = viewW / bboxW;
    const scaleY = viewH / bboxH;
    const newZoom = Math.max(minZoom, Math.min(maxZoom, Math.min(scaleX, scaleY)));

    zoomLevel = newZoom;
    // Center content
    const contentW = bboxW * zoomLevel;
    const contentH = bboxH * zoomLevel;
    panOffset.x = padding + (canvas.clientWidth - padding * 2 - contentW) / 2 - minX * zoomLevel;
    panOffset.y = padding + (canvas.clientHeight - padding * 2 - contentH) / 2 - minY * zoomLevel;

    updateTransform();
    updateZoomInfo();
    setTimeout(drawConnections, 10);
};

window.switchView = function(viewType) {
    const flowchartView = document.getElementById('flowchart-view');
    const tableView = document.getElementById('table-view');
    const flowchartBtn = document.getElementById('flowchart-btn');
    const tableBtn = document.getElementById('table-btn');
    
    if (viewType === 'flowchart') {
        flowchartView.style.display = 'block';
        tableView.style.display = 'none';
        flowchartBtn.classList.add('active');
        tableBtn.classList.remove('active');
        
        setTimeout(() => {
            drawConnections();
        }, 100);
    } else {
        flowchartView.style.display = 'none';
        tableView.style.display = 'block';
        flowchartBtn.classList.remove('active');
        tableBtn.classList.add('active');
    }
}

window.autoArrange = function() {
    if (nodes.length === 0) {
        showNotification('No nodes to arrange!', 'error');
        return;
    }
    
    // Reset zoom and pan for better arrangement
    zoomLevel = 1;
    panOffset = { x: 0, y: 0 };
    updateTransform();
    updateZoomInfo();
    
    const nodeWidth = 320;
    const nodeHeight = 250;
    const marginX = 100;
    const marginY = 50;
    
    // Arrange in a more logical flow pattern
    const startNodes = nodes.filter(node => node.id.includes('1') || node.id.includes('START'));
    const regularNodes = nodes.filter(node => !startNodes.includes(node));
    
    let currentX = 50;
    let currentY = 50;
    
    // Place start nodes first
    startNodes.forEach((node, index) => {
        node.x = currentX;
        node.y = currentY + (index * (nodeHeight + marginY));
        node.element.style.left = node.x + 'px';
        node.element.style.top = node.y + 'px';
        
        // Animate the movement
        node.element.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        setTimeout(() => {
            node.element.style.transition = '';
        }, 500);
    });
    
    // Arrange other nodes in a grid
    currentX += nodeWidth + marginX;
    const columns = Math.max(2, Math.ceil(Math.sqrt(regularNodes.length)));
    
    regularNodes.forEach((node, index) => {
        const row = Math.floor(index / columns);
        const col = index % columns;
        
        const x = currentX + col * (nodeWidth + marginX);
        const y = 50 + row * (nodeHeight + marginY);
        
        node.x = x;
        node.y = y;
        node.element.style.left = x + 'px';
        node.element.style.top = y + 'px';
        
        // Animate the movement
        node.element.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        setTimeout(() => {
            node.element.style.transition = '';
        }, 500);
    });
    
    setTimeout(() => {
        drawConnections();
    }, 600);
    
    showNotification('Nodes auto-arranged in logical flow! Don\'t forget to save.', 'success');
}

window.saveAllNodePositions = function() {
    if (nodes.length === 0) {
        showNotification('No nodes to save!', 'error');
        return;
    }
    
    const nodeData = nodes.map(node => ({
        id: node.id,
        x: node.x,
        y: node.y
    }));
    
    fetch('{{ route("admin.quizzes.save-flowchart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            nodes: nodeData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Node positions saved successfully!', 'success');
        } else {
            showNotification('Error saving positions!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving positions!', 'error');
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle';
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Auto-save positions when window is closed
window.addEventListener('beforeunload', function() {
    if (nodes.length > 0) {
        navigator.sendBeacon('{{ route("admin.quizzes.save-flowchart") }}', 
            JSON.stringify({
                nodes: nodes.map(node => ({
                    id: node.id,
                    x: node.x,
                    y: node.y
                }))
            })
        );
    }
});

// Redraw connections when window is resized
window.addEventListener('resize', function() {
    setTimeout(() => {
        if (!userInteracted) {
            fitToScreen(120);
        } else {
            drawConnections();
        }
    }, 150);
});

// Auto-hide success messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }
    });
}, 3000);
</script>
@endsection