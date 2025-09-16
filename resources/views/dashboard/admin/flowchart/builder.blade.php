@extends('layouts.dashboard')

@section('title', 'Quiz Flowchart Builder')

@section('content')
<script>
    // Add body class for CSS targeting
    document.body.classList.add('flowchart-fullscreen');
</script>
<div id="flowchartPage" class="container-fluid p-0" style="height: 100vh; width: 100%; overflow: hidden;">
    <div class="row g-0 h-100">
        <div class="col-12 h-100">
            <!-- Header Controls -->
            <div class="d-flex justify-content-between align-items-center p-3" style="position: absolute; top: 0; left: 0; right: 0; z-index: 1001; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                <h1 class="h4 mb-0 text-dark">Quiz Flowchart Builder</h1>
                <div>
                    <button id="saveFlowchart" class="btn btn-success btn-sm">
                        <i class="fas fa-save"></i> Save Flowchart
                    </button>
                    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Quiz Management
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Beautiful Quiz Flowchart Script -->
            <script>
                console.log('🚀 Quiz Flowchart Initializing...');
                
                let allNodes = [];
                let currentZoom = 1;
                let isPanning = false;
                let panStartX = 0;
                let panStartY = 0;
                let canvasX = 0;
                let canvasY = 0;
                
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('🚀 Quiz Flowchart Builder Loading...');
                    const canvas = document.getElementById('flowchartCanvas');
                    console.log('Canvas element:', canvas);
                    if (!canvas) {
                        console.error('❌ Canvas not found!');
                        return;
                    }
                    console.log('✅ Canvas found, initializing...');
                    
                    // Load and display nodes from unified quiz spec (single source of truth)
                    fetch('/api/quiz/spec')
                        .then(r => r.json())
                        .then(spec => {
                            const rawNodes = Array.isArray(spec.nodes) ? spec.nodes : [];
                            console.log('📊 Loaded ' + rawNodes.length + ' quiz nodes from unified spec');
                            // Adapt nodes to legacy shape expected by builder: node_id instead of id
                            const adapted = rawNodes.map(n => ({
                                node_id: n.id,
                                title: n.title,
                                question: n.question,
                                type: n.type,
                                options: n.options,
                                x: n.x || 100,
                                y: n.y || 100,
                            }));
                            allNodes = adapted;
                            createBeautifulFlowchart(canvas, adapted);
                            setTimeout(() => { autoLayoutNodes(); fitAllNodesToScreen(); }, 400);
                        })
                        .catch(err => {
                            console.error('❌ Error loading nodes:', err);
                            canvas.innerHTML = '<div style="color: red; padding: 20px;">Error loading quiz data!</div>';
                        });
                    
                    // Initialize zoom and pan controls
                    initializeZoomPanControls();
                });
                
                function createBeautifulFlowchart(canvas, nodes) {
                    canvas.innerHTML = ''; // Clear canvas
                    
                    // Create node map for connections
                    const nodeMap = {};
                    nodes.forEach(node => {
                        nodeMap[node.node_id] = node;
                    });
                    
                    // Create nodes with better positioning
                    nodes.forEach((node, index) => {
                        createFlowchartNode(canvas, node, nodeMap);
                    });
                    
                    // Add title
                    const title = document.createElement('div');
                    title.style.cssText = 'position: absolute; top: 10px; left: 20px; font-size: 16px; font-weight: bold; color: #333; background: #f8f9fa; padding: 10px; border-radius: 5px; border-left: 4px solid #007bff;';
                    title.innerHTML = '🌟 Immigration Quiz Decision Tree - ' + nodes.length + ' Nodes';
                    canvas.appendChild(title);
                    
                    console.log('✅ Flowchart created with ' + nodes.length + ' nodes');
                    
                    // Update statistics
                    updateStatistics();
                    
                    // Auto fit after 500ms
                    setTimeout(() => {
                        fitAllNodesToScreen();
                    }, 500);
                }
                
                function createFlowchartNode(canvas, node, nodeMap) {
                    const isTerminal = node.type === 'terminal';
                    const isMainQuestion = node.node_id === 'Q1';
                    
                    // Create node element
                    const nodeDiv = document.createElement('div');
                    nodeDiv.className = 'flowchart-node';
                    nodeDiv.dataset.nodeId = node.node_id;
                    
                    // Node styling based on type
                    let bgColor = isTerminal ? '#d4edda' : (isMainQuestion ? '#fff3cd' : '#ffffff');
                    let borderColor = isTerminal ? '#28a745' : (isMainQuestion ? '#ffc107' : '#007bff');
                    let textColor = isTerminal ? '#155724' : (isMainQuestion ? '#856404' : '#333');
                    
                    nodeDiv.style.cssText = `
                        position: absolute;
                        left: ${node.x || 100}px;
                        top: ${node.y || 100}px;
                        width: ${isMainQuestion ? '280px' : '220px'};
                        min-height: ${isMainQuestion ? '180px' : '120px'};
                        background: ${bgColor};
                        border: 2px solid ${borderColor};
                        border-radius: 12px;
                        padding: 12px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        cursor: move;
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        transition: transform 0.2s, box-shadow 0.2s;
                        z-index: 10;
                    `;
                    
                    // Add hover effect
                    nodeDiv.onmouseenter = () => {
                        nodeDiv.style.transform = 'scale(1.02)';
                        nodeDiv.style.boxShadow = '0 6px 20px rgba(0,0,0,0.2)';
                    };
                    nodeDiv.onmouseleave = () => {
                        nodeDiv.style.transform = 'scale(1)';
                        nodeDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                    };
                    
                    // Create node content
                    let optionsHtml = '';
                    if (node.options && node.options.length > 0) {
                        optionsHtml = '<div style="margin-top: 8px; font-size: 11px;">';
                        node.options.forEach((opt, i) => {
                            const nextNode = nodeMap[opt.next];
                            const nextTitle = nextNode ? nextNode.title : opt.next;
                            optionsHtml += `
                                <div style="margin: 4px 0; padding: 4px 8px; background: rgba(255,255,255,0.7); border-radius: 4px; border-left: 3px solid ${getOptionColor(i)};">
                                    <strong>${opt.value}:</strong> ${opt.label}
                                    ${nextTitle ? `<br><small style="color: #666;">→ ${nextTitle}</small>` : ''}
                                </div>
                            `;
                        });
                        optionsHtml += '</div>';
                    }
                    
                    nodeDiv.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div style="background: ${borderColor}; color: white; padding: 4px 8px; border-radius: 15px; font-size: 12px; font-weight: bold;">
                                ${node.node_id}
                            </div>
                            <div style="display: flex; gap: 4px;">
                                <button class="edit-node-btn" style="background: #17a2b8; color: white; border: none; padding: 2px 6px; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Edit Node">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="connect-node-btn" style="background: #ffc107; color: #333; border: none; padding: 2px 6px; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Connect to Another Node">
                                    <i class="fas fa-link"></i>
                                </button>
                                <button class="delete-node-btn" style="background: #dc3545; color: white; border: none; padding: 2px 6px; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Delete Node">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ${isTerminal ? '<div style="background: #28a745; color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px;">RESULT</div>' : ''}
                            </div>
                        </div>
                        <div class="node-title" style="font-weight: bold; font-size: 13px; color: ${textColor}; margin-bottom: 6px; line-height: 1.3; cursor: text;" title="Double-click to edit">
                            ${node.title || 'No Title'}
                        </div>
                        <div class="node-question" style="font-size: 11px; color: #666; line-height: 1.2; max-height: 40px; overflow: hidden; cursor: text;" title="Double-click to edit">
                            ${node.question ? node.question.substring(0, 80) + (node.question.length > 80 ? '...' : '') : ''}
                        </div>
                        ${optionsHtml}
                    `;
                    
                    // Add connection points
                    if (!isTerminal) {
                        const outputPoint = document.createElement('div');
                        outputPoint.style.cssText = 'position: absolute; right: -8px; top: 50%; width: 16px; height: 16px; background: #dc3545; border: 2px solid white; border-radius: 50%; cursor: pointer; z-index: 20;';
                        outputPoint.title = 'Output connections';
                        nodeDiv.appendChild(outputPoint);
                    }
                    
                    if (node.node_id !== 'Q1') {
                        const inputPoint = document.createElement('div');
                        inputPoint.style.cssText = 'position: absolute; left: -8px; top: 50%; width: 16px; height: 16px; background: #28a745; border: 2px solid white; border-radius: 50%; cursor: pointer; z-index: 20;';
                        inputPoint.title = 'Input connection';
                        nodeDiv.appendChild(inputPoint);
                    }
                    
                    canvas.appendChild(nodeDiv);
                    
                    // Add edit functionality
                    addNodeInteractivity(nodeDiv, node);
                    
                    // Make draggable
                    makeNodeDraggable(nodeDiv);
                }
                
                function getOptionColor(index) {
                    const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'];
                    return colors[index % colors.length];
                }
                
                // Global variables for connection mode
                let isConnecting = false;
                let sourceNode = null;
                let connections = [];
                
                function addNodeInteractivity(nodeDiv, node) {
                    const editBtn = nodeDiv.querySelector('.edit-node-btn');
                    const connectBtn = nodeDiv.querySelector('.connect-node-btn');
                    const deleteBtn = nodeDiv.querySelector('.delete-node-btn');
                    const titleElement = nodeDiv.querySelector('.node-title');
                    const questionElement = nodeDiv.querySelector('.node-question');
                    
                    // Edit button functionality
                    if (editBtn) {
                        editBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            openNodeEditModal(node, nodeDiv);
                        });
                    }
                    
                    // Connect button functionality
                    if (connectBtn) {
                        connectBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            toggleConnectionMode(nodeDiv, node);
                        });
                    }
                    
                    // Delete button functionality
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (confirm(`Are you sure you want to delete node ${node.node_id}?`)) {
                                deleteNode(nodeDiv, node);
                            }
                        });
                    }
                    
                    // Double-click to edit title
                    if (titleElement) {
                        titleElement.addEventListener('dblclick', (e) => {
                            e.stopPropagation();
                            editInlineText(titleElement, (newText) => {
                                node.title = newText;
                                updateNodeData(node);
                            });
                        });
                    }
                    
                    // Double-click to edit question
                    if (questionElement) {
                        questionElement.addEventListener('dblclick', (e) => {
                            e.stopPropagation();
                            editInlineText(questionElement, (newText) => {
                                node.question = newText;
                                updateNodeData(node);
                            });
                        });
                    }
                }
                
                function toggleConnectionMode(sourceNodeDiv, sourceNodeData) {
                    if (isConnecting && sourceNode === sourceNodeDiv) {
                        // Cancel connection mode
                        exitConnectionMode();
                        return;
                    }
                    
                    // Enter connection mode
                    isConnecting = true;
                    sourceNode = sourceNodeDiv;
                    
                    // Highlight source node
                    sourceNodeDiv.style.border = '3px solid #ff6b6b';
                    sourceNodeDiv.style.boxShadow = '0 0 20px rgba(255, 107, 107, 0.5)';
                    
                    // Show connection overlay
                    showConnectionOverlay(sourceNodeData);
                    
                    // Add click listeners to all other nodes
                    const allNodes = document.querySelectorAll('.flowchart-node');
                    allNodes.forEach(targetNodeDiv => {
                        if (targetNodeDiv !== sourceNodeDiv) {
                            targetNodeDiv.style.cursor = 'crosshair';
                            targetNodeDiv.style.border = '2px dashed #4ecdc4';
                            
                            const clickHandler = (e) => {
                                e.stopPropagation();
                                const targetNodeId = targetNodeDiv.dataset.nodeId;
                                const targetNodeData = allNodes.find(n => n.node_id === targetNodeId);
                                createConnection(sourceNodeData, targetNodeData);
                                exitConnectionMode();
                            };
                            
                            targetNodeDiv.addEventListener('click', clickHandler, { once: true });
                            targetNodeDiv._connectionHandler = clickHandler;
                        }
                    });
                }
                
                function exitConnectionMode() {
                    isConnecting = false;
                    sourceNode = null;
                    hideConnectionOverlay();
                    
                    // Reset all node styles
                    const allNodes = document.querySelectorAll('.flowchart-node');
                    allNodes.forEach(nodeDiv => {
                        nodeDiv.style.cursor = 'move';
                        nodeDiv.style.border = nodeDiv.style.border.replace('3px solid #ff6b6b', '').replace('2px dashed #4ecdc4', '');
                        nodeDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                        
                        // Remove connection handlers
                        if (nodeDiv._connectionHandler) {
                            nodeDiv.removeEventListener('click', nodeDiv._connectionHandler);
                            delete nodeDiv._connectionHandler;
                        }
                    });
                }
                
                function createConnection(sourceNode, targetNode) {
                    const connection = {
                        id: Date.now(),
                        from: sourceNode.node_id,
                        to: targetNode.node_id,
                        fromTitle: sourceNode.title,
                        toTitle: targetNode.title
                    };
                    
                    connections.push(connection);
                    drawConnection(connection);
                    
                    console.log(`✅ Connected ${sourceNode.node_id} → ${targetNode.node_id}`);
                    updateStatistics();
                }
                
                function drawConnection(connection) {
                    const canvas = document.getElementById('flowchartCanvas');
                    const sourceElement = document.querySelector(`[data-node-id="${connection.from}"]`);
                    const targetElement = document.querySelector(`[data-node-id="${connection.to}"]`);
                    
                    if (!sourceElement || !targetElement) return;
                    
                    // Create SVG for connection line
                    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    svg.style.cssText = `
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        pointer-events: none;
                        z-index: 5;
                    `;
                    
                    // Calculate positions
                    const sourceRect = sourceElement.getBoundingClientRect();
                    const targetRect = targetElement.getBoundingClientRect();
                    const canvasRect = canvas.getBoundingClientRect();
                    
                    const startX = sourceRect.right - canvasRect.left;
                    const startY = sourceRect.top + sourceRect.height / 2 - canvasRect.top;
                    const endX = targetRect.left - canvasRect.left;
                    const endY = targetRect.top + targetRect.height / 2 - canvasRect.top;
                    
                    // Create arrow line
                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', startX);
                    line.setAttribute('y1', startY);
                    line.setAttribute('x2', endX);
                    line.setAttribute('y2', endY);
                    line.setAttribute('stroke', '#28a745');
                    line.setAttribute('stroke-width', '2');
                    line.setAttribute('marker-end', 'url(#arrowhead)');
                    
                    // Create arrowhead marker
                    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                    const marker = document.createElementNS('http://www.w3.org/2000/svg', 'marker');
                    marker.setAttribute('id', 'arrowhead');
                    marker.setAttribute('markerWidth', '10');
                    marker.setAttribute('markerHeight', '7');
                    marker.setAttribute('refX', '9');
                    marker.setAttribute('refY', '3.5');
                    marker.setAttribute('orient', 'auto');
                    
                    const polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
                    polygon.setAttribute('points', '0 0, 10 3.5, 0 7');
                    polygon.setAttribute('fill', '#28a745');
                    
                    marker.appendChild(polygon);
                    defs.appendChild(marker);
                    svg.appendChild(defs);
                    svg.appendChild(line);
                    
                    canvas.appendChild(svg);
                    
                    // Store connection element for later removal
                    connection.element = svg;
                }
                
                function showConnectionOverlay(sourceNode) {
                    const overlay = document.createElement('div');
                    overlay.id = 'connection-overlay';
                    overlay.style.cssText = `
                        position: fixed;
                        top: 20px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: #2c3e50;
                        color: white;
                        padding: 15px 25px;
                        border-radius: 8px;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                        z-index: 1000;
                        font-size: 14px;
                        text-align: center;
                    `;
                    overlay.innerHTML = `
                        <strong>🔗 Connection Mode</strong><br>
                        Click on another node to connect from <strong>${sourceNode.node_id}</strong><br>
                        <small>Click anywhere else to cancel</small>
                    `;
                    document.body.appendChild(overlay);
                    
                    // Click outside to cancel
                    document.addEventListener('click', function cancelConnection(e) {
                        if (!e.target.closest('.flowchart-node')) {
                            exitConnectionMode();
                            document.removeEventListener('click', cancelConnection);
                        }
                    }, { once: true });
                }
                
                function hideConnectionOverlay() {
                    const overlay = document.getElementById('connection-overlay');
                    if (overlay) {
                        overlay.remove();
                    }
                }
                
                function editInlineText(element, callback) {
                    const currentText = element.textContent.trim();
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = currentText;
                    input.style.cssText = `
                        width: 100%;
                        background: transparent;
                        border: 2px solid #007bff;
                        border-radius: 4px;
                        padding: 2px 4px;
                        font-size: inherit;
                        font-family: inherit;
                        color: inherit;
                    `;
                    
                    element.innerHTML = '';
                    element.appendChild(input);
                    input.focus();
                    input.select();
                    
                    function saveEdit() {
                        const newText = input.value.trim() || currentText;
                        element.textContent = newText;
                        callback(newText);
                    }
                    
                    input.addEventListener('blur', saveEdit);
                    input.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            input.blur();
                        }
                    });
                }
                
                function openNodeEditModal(node, nodeDiv) {
                    // Create modal
                    const modal = document.createElement('div');
                    modal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0,0,0,0.7);
                        z-index: 2000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    `;
                    
                    modal.innerHTML = `
                        <div style="background: white; padding: 30px; border-radius: 12px; width: 500px; max-width: 90%;">
                            <h3 style="margin-top: 0; color: #333;">Edit Node: ${node.node_id}</h3>
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Title:</label>
                                <input type="text" id="edit-title" value="${node.title || ''}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Question:</label>
                                <textarea id="edit-question" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">${node.question || ''}</textarea>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Type:</label>
                                <select id="edit-type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    <option value="question" ${node.type === 'question' ? 'selected' : ''}>Question</option>
                                    <option value="terminal" ${node.type === 'terminal' ? 'selected' : ''}>Terminal</option>
                                </select>
                            </div>
                            <div style="text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
                                <button id="cancel-edit" style="padding: 8px 16px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                                <button id="save-edit" style="padding: 8px 16px; border: none; background: #28a745; color: white; border-radius: 4px; cursor: pointer;">Save Changes</button>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(modal);
                    
                    // Event listeners
                    modal.querySelector('#cancel-edit').addEventListener('click', () => {
                        modal.remove();
                    });
                    
                    modal.querySelector('#save-edit').addEventListener('click', () => {
                        node.title = modal.querySelector('#edit-title').value;
                        node.question = modal.querySelector('#edit-question').value;
                        node.type = modal.querySelector('#edit-type').value;
                        
                        updateNodeData(node);
                        refreshNode(nodeDiv, node);
                        modal.remove();
                    });
                    
                    // Close on background click
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            modal.remove();
                        }
                    });
                }
                
                function updateNodeData(node) {
                    // Update in allNodes array
                    const index = allNodes.findIndex(n => n.node_id === node.node_id);
                    if (index !== -1) {
                        allNodes[index] = node;
                    }
                    console.log(`✅ Updated node ${node.node_id}`);
                }
                
                function refreshNode(nodeDiv, node) {
                    // Remove old node and create new one
                    const canvas = document.getElementById('flowchartCanvas');
                    const nodeMap = {};
                    allNodes.forEach(n => nodeMap[n.node_id] = n);
                    
                    // Store position
                    node.x = parseFloat(nodeDiv.style.left) || node.x;
                    node.y = parseFloat(nodeDiv.style.top) || node.y;
                    
                    // Remove old node
                    nodeDiv.remove();
                    
                    // Create new node
                    createFlowchartNode(canvas, node, nodeMap);
                }
                
                function deleteNode(nodeDiv, node) {
                    // Remove from allNodes array
                    const index = allNodes.findIndex(n => n.node_id === node.node_id);
                    if (index !== -1) {
                        allNodes.splice(index, 1);
                    }
                    
                    // Remove connections
                    connections = connections.filter(conn => {
                        if (conn.from === node.node_id || conn.to === node.node_id) {
                            if (conn.element) {
                                conn.element.remove();
                            }
                            return false;
                        }
                        return true;
                    });
                    
                    // Remove node element
                    nodeDiv.remove();
                    
                    updateStatistics();
                    console.log(`🗑️ Deleted node ${node.node_id}`);
                }
                
                function makeNodeDraggable(element) {
                    let isDragging = false;
                    let initialOffsetX = 0, initialOffsetY = 0;
                    let startMouseX = 0, startMouseY = 0;
                    const container = document.getElementById('canvasContainer');

                    element.addEventListener('mousedown', (e) => {
                        if (e.target.classList.contains('connector')) return;
                        const rect = container.getBoundingClientRect();
                        startMouseX = (e.clientX - rect.left - canvasX) / currentZoom;
                        startMouseY = (e.clientY - rect.top - canvasY) / currentZoom;
                        initialOffsetX = parseFloat(element.style.left) || 0;
                        initialOffsetY = parseFloat(element.style.top) || 0;
                        isDragging = true;
                        element.style.zIndex = 1000;
                    });

                    document.addEventListener('mousemove', (e) => {
                        if (!isDragging) return;
                        e.preventDefault();
                        const rect = container.getBoundingClientRect();
                        const mouseX = (e.clientX - rect.left - canvasX) / currentZoom;
                        const mouseY = (e.clientY - rect.top - canvasY) / currentZoom;
                        const dx = mouseX - startMouseX;
                        const dy = mouseY - startMouseY;
                        element.style.left = (initialOffsetX + dx) + 'px';
                        element.style.top = (initialOffsetY + dy) + 'px';
                    });

                    document.addEventListener('mouseup', () => {
                        if (!isDragging) return;
                        isDragging = false;
                        element.style.zIndex = 10;
                    });
                }
                
                // Initialize Zoom and Pan Controls
                function initializeZoomPanControls() {
                    const container = document.getElementById('canvasContainer');
                    const canvas = document.getElementById('flowchartCanvas');

                    // Mouse wheel zoom with preventDefault
                    container.addEventListener('wheel', function(e) {
                        e.preventDefault();
                        const rect = container.getBoundingClientRect();
                        const mouseX = e.clientX - rect.left;
                        const mouseY = e.clientY - rect.top;
                        const oldZoom = currentZoom;
                        const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
                        currentZoom = Math.max(0.1, Math.min(3, currentZoom * zoomFactor));
                        const zoomRatio = currentZoom / oldZoom;
                        canvasX = mouseX - (mouseX - canvasX) * zoomRatio;
                        canvasY = mouseY - (mouseY - canvasY) * zoomRatio;
                        updateCanvasTransform();
                        updateZoomDisplay();
                    }, { passive: false });

                    // Mouse pan
                    container.addEventListener('mousedown', function(e) {
                        if (e.target === container || e.target === canvas) {
                            isPanning = true;
                            panStartX = e.clientX - canvasX;
                            panStartY = e.clientY - canvasY;
                            container.style.cursor = 'grabbing';
                        }
                    });
                    
                    document.addEventListener('mousemove', function(e) {
                        if (isPanning) {
                            canvasX = e.clientX - panStartX;
                            canvasY = e.clientY - panStartY;
                            updateCanvasTransform();
                        }
                    });
                    
                    document.addEventListener('mouseup', function() {
                        if (isPanning) {
                            isPanning = false;
                            container.style.cursor = 'grab';
                        }
                    });
                    
                    // Buttons
                    document.getElementById('zoomIn').addEventListener('click', function() {
                        currentZoom = Math.min(3, currentZoom * 1.2);
                        updateCanvasTransform();
                        updateZoomDisplay();
                    });
                    document.getElementById('zoomOut').addEventListener('click', function() {
                        currentZoom = Math.max(0.1, currentZoom * 0.8);
                        updateCanvasTransform();
                        updateZoomDisplay();
                    });
                    document.getElementById('resetZoom').addEventListener('click', function() {
                        currentZoom = 1;
                        canvasX = 0;
                        canvasY = 0;
                        updateCanvasTransform();
                        updateZoomDisplay();
                    });
                    document.getElementById('fitToScreen').addEventListener('click', function() {
                        fitAllNodesToScreen();
                    });
                    
                    // Keyboard shortcuts
                    document.addEventListener('keydown', (e) => {
                        if (e.key === '+' || e.key === '=') {
                            currentZoom = Math.min(3, currentZoom * 1.2);
                            updateCanvasTransform();
                            updateZoomDisplay();
                        } else if (e.key === '-' || e.key === '_') {
                            currentZoom = Math.max(0.1, currentZoom * 0.8);
                            updateCanvasTransform();
                            updateZoomDisplay();
                        }
                    });
                }
                
                function updateCanvasTransform() {
                    const canvas = document.getElementById('flowchartCanvas');
                    if (!canvas) return;
                    canvas.style.transformOrigin = '0 0';
                    canvas.style.transform = `translate(${canvasX}px, ${canvasY}px) scale(${currentZoom})`;
                }
                
                function updateZoomDisplay() {
                    document.getElementById('zoomLevel').textContent = `Zoom: ${Math.round(currentZoom * 100)}%`;
                }
                
                function fitAllNodesToScreen() {
                    const nodes = document.querySelectorAll('.flowchart-node');
                    if (nodes.length === 0) return;
                    
                    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
                    
                    nodes.forEach(node => {
                        const x = parseInt(node.style.left);
                        const y = parseInt(node.style.top);
                        const width = node.offsetWidth;
                        const height = node.offsetHeight;
                        
                        minX = Math.min(minX, x);
                        minY = Math.min(minY, y);
                        maxX = Math.max(maxX, x + width);
                        maxY = Math.max(maxY, y + height);
                    });
                    
                    const container = document.getElementById('canvasContainer');
                    const containerWidth = container.offsetWidth;
                    const containerHeight = container.offsetHeight;
                    
                    const nodesWidth = maxX - minX;
                    const nodesHeight = maxY - minY;
                    
                    const zoomX = containerWidth / (nodesWidth + 100);
                    const zoomY = containerHeight / (nodesHeight + 100);
                    currentZoom = Math.min(zoomX, zoomY, 1);
                    
                    canvasX = (containerWidth - nodesWidth * currentZoom) / 2 - minX * currentZoom;
                    canvasY = (containerHeight - nodesHeight * currentZoom) / 2 - minY * currentZoom;
                    
                    updateCanvasTransform();
                    updateZoomDisplay();
                }
                
                function autoLayoutNodes() {
                    const nodes = allNodes;
                    if (!nodes || nodes.length === 0) return;
                    
                    // Find main node (Q1)
                    const mainNode = nodes.find(n => n.node_id === 'Q1');
                    if (!mainNode) return;
                    
                    // Auto-arrange in tree layout
                    const arranged = {};
                    const levels = {};
                    
                    // Level 0: Main question
                    levels[0] = [mainNode];
                    arranged[mainNode.node_id] = {x: 400, y: 100, level: 0};
                    
                    // Level 1: Main branches (Q2-Q7)
                    const mainBranches = nodes.filter(n => ['Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'Q7'].includes(n.node_id));
                    levels[1] = mainBranches;
                    mainBranches.forEach((node, i) => {
                        arranged[node.node_id] = {x: 100 + i * 300, y: 300, level: 1};
                    });
                    
                    // Level 2: Sub questions and terminals
                    const subNodes = nodes.filter(n => !['Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'Q7'].includes(n.node_id));
                    levels[2] = subNodes;
                    subNodes.forEach((node, i) => {
                        arranged[node.node_id] = {x: 50 + (i % 8) * 250, y: 500 + Math.floor(i / 8) * 200, level: 2};
                    });
                    
                    // Update DOM positions
                    Object.keys(arranged).forEach(nodeId => {
                        const nodeElement = document.querySelector(`[data-node-id="${nodeId}"]`);
                        if (nodeElement) {
                            nodeElement.style.left = arranged[nodeId].x + 'px';
                            nodeElement.style.top = arranged[nodeId].y + 'px';
                        }
                    });
                    
                    // Fit to screen after layout
                    setTimeout(() => fitAllNodesToScreen(), 100);
                }
                
                // Update statistics
                function updateStatistics() {
                    const totalNodes = document.querySelectorAll('.flowchart-node').length;
                    const questionNodes = document.querySelectorAll('.flowchart-node[data-node-id^="Q"]').length;
                    const terminalNodes = totalNodes - questionNodes;
                    
                    document.getElementById('totalNodes').textContent = totalNodes;
                    document.getElementById('questionNodes').textContent = questionNodes;
                    document.getElementById('terminalNodes').textContent = terminalNodes;
                }

                // Keep view fitted when the viewport changes size
                let fitTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(fitTimeout);
                    fitTimeout = setTimeout(() => {
                        try { fitAllNodesToScreen(); } catch(e) {}
                    }, 150);
                });
            </script>

            <!-- Full Screen Layout with Integrated Controls -->
            <div style="height: calc(100vh - 80px); margin-top: 80px; position: relative; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: block !important; visibility: visible !important;">
                
                <!-- Integrated Control Panel -->
                <div style="position: absolute; top: 15px; left: 15px; z-index: 1000; display: flex; gap: 10px; flex-wrap: wrap;">
                    <!-- Node Creation Controls -->
                    <div class="btn-group shadow" role="group">
                        <button id="addQuestionNode" class="btn btn-primary btn-sm">
                            <i class="fas fa-question-circle"></i> Add Question
                        </button>
                        <button id="addTerminalNode" class="btn btn-success btn-sm">
                            <i class="fas fa-flag-checkered"></i> Add Terminal
                        </button>
                    </div>
                    
                    <!-- Zoom Controls -->
                    <div class="btn-group shadow" role="group">
                        <button id="zoomIn" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button id="zoomOut" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button id="resetZoom" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </button>
                        <button id="fitToScreen" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-compress-arrows-alt"></i>
                        </button>
                    </div>
                    
                    <!-- Layout Actions -->
                    <div class="btn-group shadow" role="group">
                        <button id="autoLayout" class="btn btn-info btn-sm">
                            <i class="fas fa-sitemap"></i> Auto Arrange
                        </button>
                        <button id="clearCanvas" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt"></i> Clear All
                        </button>
                    </div>
                </div>
                
                <!-- Right Control Panel -->
                <div style="position: absolute; top: 15px; right: 15px; z-index: 1000; display: flex; gap: 10px;">
                    <!-- Statistics Display -->
                    <div class="btn-group shadow" role="group">
                        <span class="btn btn-dark btn-sm">
                            <i class="fas fa-chart-bar"></i> Nodes: <span id="totalNodes">0</span>
                        </span>
                        <span class="btn btn-info btn-sm">
                            Q: <span id="questionNodes">0</span>
                        </span>
                        <span class="btn btn-success btn-sm">
                            T: <span id="terminalNodes">0</span>
                        </span>
                    </div>
                    
                    <!-- View Controls -->
                    <div class="btn-group shadow" role="group">
                        <button id="fullscreen" class="btn btn-dark btn-sm">
                            <i class="fas fa-expand"></i> Fullscreen
                        </button>
                        <button id="minimap" class="btn btn-secondary btn-sm">
                            <i class="fas fa-map"></i> Minimap
                        </button>
                        <button id="exportImage" class="btn btn-warning btn-sm">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    
                    <!-- Zoom Level Display -->
                    <span id="zoomLevel" class="btn btn-outline-light btn-sm">100%</span>
                </div>
                
                <!-- Main Canvas Area -->
                <div id="mainCanvasArea" style="width: 100%; height: 100%; position: relative; overflow: hidden; display: block !important; visibility: visible !important;">
                    
                    <!-- Canvas Container with Zoom -->
                    <div id="canvasContainer" style="
                        height: calc(100vh - 150px); 
                        width: 100%; 
                        position: relative; 
                        overflow: auto;
                        transform-origin: 0 0;
                        transition: transform 0.3s ease;
                        background: rgba(0,0,0,0.1);
                        border: 2px solid #fff;
                    ">
                        <!-- Main Flowchart Canvas -->
                        <div id="flowchartCanvas" class="flowchart-canvas" style="
                            height: 3000px; 
                            width: 4000px; 
                            position: relative;
                            background: 
                                radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0);
                            background-size: 40px 40px;
                            cursor: grab;
                            display: block !important;
                            visibility: visible !important;
                            z-index: 1;
                            min-height: 100%;
                            top: 0;
                            left: 0;
                        ">
                            <!-- Nodes will be dynamically added here -->
                            <div style="position: absolute; top: 50px; left: 50px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10;">
                                <h3>🚀 Flowchart Loading...</h3>
                                <p>If you see this message, the canvas is working. Nodes should load automatically.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Adjust flowchart to use available space without overflow */
    body.flowchart-fullscreen .main-content {
        margin-left: 280px !important; /* Keep sidebar space */
        margin-right: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
        width: calc(100vw - 280px) !important; /* Account for sidebar */
        height: 100vh !important;
        position: relative !important;
        max-width: none !important;
    }
    
    /* Don't hide sidebar - keep it visible */
    body.flowchart-fullscreen .sidebar {
        display: flex !important;
    }
    
    /* Override responsive styles */
    @media (max-width: 991.98px) {
        body.flowchart-fullscreen .main-content {
            margin-left: 0 !important;
            width: 100vw !important;
        }
        
        body.flowchart-fullscreen .sidebar {
            display: none !important;
        }
    }
    
    /* Properly sized page layout within main content */
    #flowchartPage {
        width: 100% !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        position: relative !important;
    }
    
    /* Fullscreen fallback when browser doesn't support fullscreen API */
    .fullscreen-fallback {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 9999 !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    /* Ensure outer container fits within available space */
    .container-fluid.p-0 { 
        height: 100vh !important; 
        width: 100% !important;
        overflow: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Canvas styling improvements */
    .flowchart-canvas {
        cursor: grab;
        display: block !important;
        visibility: visible !important;
    }
    
    .flowchart-canvas:active {
        cursor: grabbing;
    }
    
    /* Ensure all flowchart elements are visible */
    #flowchartPage * {
        display: block !important;
        visibility: visible !important;
    }
    
    #flowchartPage .btn-group {
        display: flex !important;
    }
    
    /* Button group improvements for better visibility */
    .btn-group .btn {
        backdrop-filter: blur(10px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }
        
        #flowchartPage .btn-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    }
            margin-left: 0;
            margin-right: 0;
        }
    /* Hard override: remove main area padding while on this page only */
    .main-content { padding: 0 !important; }
    /* Keep it responsive on smaller screens where main-content has no side padding */
    @media (max-width: 991.98px) {
        #flowchartPage {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }
    }
    /* Ensure the main canvas and container truly stretch */
    #mainCanvasArea { width: 100%; }
    #canvasContainer { width: 100%; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsPlumb/2.15.6/js/jsplumb.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Quiz Flowchart Builder Initializing...');
    
    // Auto layout button
    document.getElementById('autoLayout').addEventListener('click', function() {
        autoLayoutNodes();
        setTimeout(() => fitAllNodesToScreen(), 200);
    });
    
    // Save flowchart button
    document.getElementById('saveFlowchart').addEventListener('click', function() {
        saveFlowchart();
    });
});

function saveFlowchart() {
    // Save flowchart data
    fetch('{{ route("admin.quizzes.save-flowchart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nodes: allNodes,
            connections: []
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Flowchart saved successfully!');
        } else {
            alert('Error saving flowchart: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving flowchart');
    });
}

// Fullscreen toggle for main canvas area
(function setupFullscreenToggle(){
  const btn = document.getElementById('fullscreen');
  const flowchartPage = document.getElementById('flowchartPage');
  if (!btn || !flowchartPage) return;
  
  let isFull = false;
  
  btn.addEventListener('click', async () => {
    isFull = !isFull;
    
    if (isFull) {
      try {
        // Try native fullscreen API first
        if (flowchartPage.requestFullscreen) {
          await flowchartPage.requestFullscreen();
        } else if (flowchartPage.webkitRequestFullscreen) {
          await flowchartPage.webkitRequestFullscreen();
        } else if (flowchartPage.msRequestFullscreen) {
          await flowchartPage.msRequestFullscreen();
        } else {
          // Fallback to CSS-based fullscreen
          flowchartPage.classList.add('fullscreen-fallback');
          document.body.style.overflow = 'hidden';
        }
        btn.innerHTML = '<i class="fas fa-compress"></i> Exit Fullscreen';
      } catch (err) {
        // Fallback to CSS-based fullscreen
        flowchartPage.classList.add('fullscreen-fallback');
        document.body.style.overflow = 'hidden';
        btn.innerHTML = '<i class="fas fa-compress"></i> Exit Fullscreen';
      }
    } else {
      try {
        if (document.exitFullscreen) {
          await document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
          await document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
          await document.msExitFullscreen();
        } else {
          // Fallback
          flowchartPage.classList.remove('fullscreen-fallback');
          document.body.style.overflow = '';
        }
        btn.innerHTML = '<i class="fas fa-expand"></i> Fullscreen';
      } catch (err) {
        // Fallback
        flowchartPage.classList.remove('fullscreen-fallback');
        document.body.style.overflow = '';
        btn.innerHTML = '<i class="fas fa-expand"></i> Fullscreen';
      }
    }
    
    // Refit nodes after fullscreen change
    setTimeout(() => {
      try { fitAllNodesToScreen(); } catch(e) {}
    }, 300);
  });
  
  // Listen for fullscreen change events
  document.addEventListener('fullscreenchange', handleFullscreenChange);
  document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
  document.addEventListener('msfullscreenchange', handleFullscreenChange);
  
  function handleFullscreenChange() {
    const isCurrentlyFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
    
    if (!isCurrentlyFullscreen && isFull) {
      // User exited fullscreen using ESC key
      isFull = false;
      flowchartPage.classList.remove('fullscreen-fallback');
      document.body.style.overflow = '';
      btn.innerHTML = '<i class="fas fa-expand"></i> Fullscreen';
    }
  }
})();
</script>
@endpush
