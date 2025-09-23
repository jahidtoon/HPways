<?php
// Document Viewer - Shows uploaded documents
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$app->boot();

use App\Models\Document;

$isPrintMode = isset($_GET['print']) && $_GET['print'] == '1';

try {
    $docId = $_GET['id'] ?? null;
    $action = $_GET['action'] ?? 'view';
    $download = isset($_GET['download']) && $_GET['download'] == '1';
    
    if (!$docId) {
        throw new Exception('Document ID is required');
    }
    
    $document = Document::findOrFail($docId);
    
    // Helper to resolve an absolute path for a stored document, trying common locations
    $resolvePath = function(string $relative) {
        $candidates = [
            storage_path('app/' . ltrim($relative, '/')),                // private storage
            storage_path('app/public/' . ltrim($relative, '/')),         // storage/app/public
            public_path('storage/' . ltrim($relative, '/')),             // public/storage (symlink target)
        ];
        foreach ($candidates as $p) {
            if (is_file($p)) {
                return $p;
            }
        }
        return null;
    };

    // Handle file download/view action
    if ($action === 'download' && $document->stored_path) {
        $fullPath = $resolvePath($document->stored_path);
        if ($fullPath) {
            $filename = $document->original_name ?: 'document.' . pathinfo($document->stored_path, PATHINFO_EXTENSION);
            
            // Set appropriate headers for file download/view
            if ($download) {
                header('Content-Disposition: attachment; filename="' . $filename . '"');
            } else {
                header('Content-Disposition: inline; filename="' . $filename . '"');
            }
            
            header('Content-Type: ' . ($document->mime ?: 'application/octet-stream'));
            header('Content-Length: ' . filesize($fullPath));
            header('Cache-Control: no-cache, must-revalidate');
            
            // Output file contents
            readfile($fullPath);
            exit;
        } else {
            throw new Exception('File not found');
        }
    }
    
    if ($isPrintMode) {
        // Print-friendly version - ONLY document content, no headers or text
        echo "<!DOCTYPE html>";
        echo "<html><head>";
        echo "<title>Document</title>";
        echo "<style>";
        echo "body { margin: 0; padding: 0; }";
        echo "img { max-width: 100%; height: auto; display: block; margin: 0; }";
        echo "iframe { width: 100%; height: 100vh; border: none; margin: 0; }";
        echo "@media print { ";
        echo "  body { margin: 0 !important; padding: 0 !important; } ";
        echo "  img { max-width: 100% !important; height: auto !important; page-break-inside: avoid; } ";
        echo "  iframe { width: 100% !important; height: 100vh !important; } ";
        echo "}";
        echo "</style>";
        echo "</head><body>";
    }
    
    // Document display content - only show info in non-print mode
    if (!$isPrintMode) {
        echo "<div class='document-info' style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 8px;'>";
        echo "<h3 style='color: #495057; margin-bottom: 15px;'><i class='fas fa-file-alt'></i> Document Information</h3>";
        echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>";
        
        echo "<div>";
        echo "<p><strong>Document Name:</strong><br>" . ($document->original_name ?: 'Unnamed Document') . "</p>";
        echo "<p><strong>Document Type:</strong><br>" . $document->type . "</p>";
        echo "</div>";
        
        echo "<div>";
        echo "<p><strong>Status:</strong><br><span style='background: #28a745; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px;'>" . ucfirst($document->status) . "</span></p>";
        echo "<p><strong>Upload Date:</strong><br>" . $document->created_at->format('M d, Y H:i') . "</p>";
        echo "</div>";
        
        echo "</div>";
    }
    
    if ($document->stored_path) {
        // Only show file location info in non-print mode
        if (!$isPrintMode) {
            echo "<div style='margin-top: 15px; padding: 15px; background: #e3f2fd; border-left: 4px solid #2196f3;'>";
            echo "<p><strong><i class='fas fa-folder'></i> File Location:</strong> " . $document->stored_path . "</p>";
        }
        
        // Check if file exists and determine type
        $fullPath = $resolvePath($document->stored_path);
        $checked = [
            storage_path('app/' . $document->stored_path),
            storage_path('app/public/' . $document->stored_path),
            public_path('storage/' . $document->stored_path),
        ];
        
        if ($fullPath) {
            $fileSize = round(filesize($fullPath) / 1024, 2);
            $fileExtension = strtoupper(pathinfo($document->stored_path, PATHINFO_EXTENSION));
            
            // Only show file info in non-print mode
            if (!$isPrintMode) {
                echo "<p><strong>File Size:</strong> " . $fileSize . " KB</p>";
                echo "<p><strong>File Type:</strong> " . $fileExtension . "</p>";
            }
            
            // Create a secure URL for the document
            $documentUrl = "/view-document.php?id=" . $document->id . "&action=download";
            
            // If it's an image, show it
            if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) {
                if ($isPrintMode) {
                    // For print mode, show ONLY the raw image - no borders, no text
                    echo "<img src='data:" . $document->mime . ";base64," . base64_encode(file_get_contents($fullPath)) . "' style='width: 100%; height: auto;' alt=''>";
                } else {
                    echo "<div style='margin-top: 15px; text-align: center; background: #f8f9fa; padding: 20px; border-radius: 8px;'>";
                    // Show actual image with better styling and controls
                    echo "<div style='position: relative; display: inline-block; max-width: 100%;'>";
                    echo "<img id='documentImage' src='data:" . $document->mime . ";base64," . base64_encode(file_get_contents($fullPath)) . "' style='max-width: 100%; max-height: 500px; border: 2px solid #dee2e6; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); cursor: zoom-in;' alt='Document Image' onclick='toggleImageSize(this)'>";
                    echo "</div>";
                    
                    echo "<div style='padding: 15px; margin-top: 15px; background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
                    echo "<h6 style='margin: 0 0 10px 0; color: #495057;'><i class='fas fa-image me-1'></i> Image Actions</h6>";
                    echo "<div style='display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;'>";
                    echo "<button onclick='toggleImageSize(document.getElementById(\"documentImage\"))' style='background: #6f42c1; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;'>";
                    echo "<i class='fas fa-search-plus'></i> Toggle Size</button>";
                    echo "<a href='" . $documentUrl . "' target='_blank' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>";
                    echo "<i class='fas fa-external-link-alt'></i> Open Full Size</a>";
                    echo "<a href='" . $documentUrl . "&download=1' style='background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>";
                    echo "<i class='fas fa-download'></i> Download</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    
                    // Add JavaScript for image zoom functionality
                    echo "<script>";
                    echo "function toggleImageSize(img) {";
                    echo "  if (img.style.maxHeight === '500px' || !img.style.maxHeight) {";
                    echo "    img.style.maxHeight = 'none';";
                    echo "    img.style.maxWidth = '100%';";
                    echo "    img.style.cursor = 'zoom-out';";
                    echo "  } else {";
                    echo "    img.style.maxHeight = '500px';";
                    echo "    img.style.maxWidth = '100%';";
                    echo "    img.style.cursor = 'zoom-in';";
                    echo "  }";
                    echo "}";
                    echo "</script>";
                }
            } elseif (in_array(strtolower($fileExtension), ['pdf'])) {
                if ($isPrintMode) {
                    // For print mode, try to show PDF directly - no text or decorations
                    echo "<iframe src='data:application/pdf;base64," . base64_encode(file_get_contents($fullPath)) . "' style='width: 100%; height: 100vh; border: none;'></iframe>";
                } else {
                    echo "<div style='margin-top: 15px; text-align: center;'>";
                    echo "<div style='padding: 20px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;'>";
                    echo "<i class='fas fa-file-pdf fa-3x text-danger mb-2'></i>";
                    echo "<p><strong>PDF Document</strong></p>";
                    echo "<p class='text-muted'>Click to view PDF</p>";
                    echo "<a href='" . $documentUrl . "' target='_blank' style='background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>";
                    echo "<i class='fas fa-eye'></i> View PDF</a>";
                    echo "<a href='" . $documentUrl . "&download=1' style='background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>";
                    echo "<i class='fas fa-download'></i> Download</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                if ($isPrintMode) {
                    // For non-printable files in print mode, show nothing or minimal content
                    // Just end the print page here
                } else {
                    echo "<div style='margin-top: 15px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; text-align: center;'>";
                    echo "<i class='fas fa-file-alt fa-3x text-muted mb-2'></i>";
                    echo "<p><strong>File Preview Not Available</strong></p>";
                    echo "<p class='text-muted'>This file type (" . $fileExtension . ") cannot be previewed in the browser.</p>";
                    echo "<p><a href='" . $documentUrl . "' target='_blank' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>";
                    echo "<i class='fas fa-download'></i> Download File</a></p>";
                    echo "</div>";
                }
            }
        } else {
            if (!$isPrintMode) {
                echo "<p style='color: #dc3545;'><i class='fas fa-exclamation-triangle'></i> File not found at specified location.</p>";
                echo "<div style='margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-family: monospace; font-size: 12px;'>";
                echo "<p><strong>Checked paths:</strong></p>";
                foreach ($checked as $p) { echo "<p>• " . $p . "</p>"; }
                echo "</div>";
            }
        }
        
        // Close the info div only for non-print mode
        if (!$isPrintMode) {
            echo "</div>";
        }
    } else {
        if ($isPrintMode) {
            // For print mode, show nothing when file is not available
            // Just end the print page here
        } else {
            echo "<div style='margin-top: 15px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>";
            echo "<p><i class='fas fa-exclamation-triangle'></i> No file path specified for this document.</p>";
            echo "<p class='text-muted'>This document may have been uploaded without a proper file path, or the file path data is missing from the database.</p>";
            
            // Provide some debugging information
            echo "<details style='margin-top: 10px;'>";
            echo "<summary style='cursor: pointer; color: #007bff;'>Show Debug Information</summary>";
            echo "<div style='margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-family: monospace; font-size: 12px;'>";
            echo "<p><strong>Document ID:</strong> " . $document->id . "</p>";
            echo "<p><strong>Original Name:</strong> " . ($document->original_name ?: 'Not set') . "</p>";
            echo "<p><strong>Type:</strong> " . $document->type . "</p>";
            echo "<p><strong>Status:</strong> " . $document->status . "</p>";
            echo "<p><strong>Size:</strong> " . ($document->size_bytes ? number_format($document->size_bytes) . ' bytes' : 'Not set') . "</p>";
            echo "<p><strong>MIME Type:</strong> " . ($document->mime ?: 'Not set') . "</p>";
            echo "</div>";
            echo "</details>";
            echo "</div>";
        }
    }
    
    echo "</div>";
    
    // Buttons are now handled by the modal in the main application
    // No need for duplicate buttons here
    
    if ($isPrintMode) {
        echo "</body></html>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 8px; margin: 20px;'>";
    echo "<h3><i class='fas fa-exclamation-triangle'></i> Error Loading Document</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    if (!$isPrintMode) {
        echo "<button onclick='history.back()' style='background: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;'>Go Back</button>";
    }
    echo "</div>";
}
?>