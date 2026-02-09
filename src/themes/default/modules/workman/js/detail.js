/**
 * NukeViet Content Management System - Workman Module
 * Task Detail Page JavaScript
 * @version 5.x
 */

var WorkmanDetail = (function() {
    'use strict';
    
    var config = {
        taskId: 0,
        urls: {
            update: '',
            submission: ''
        }
    };
    
    /**
     * Initialize module
     */
    function init(options) {
        config = Object.assign(config, options);
        
        // Init submission form if exists
        var submissionForm = document.getElementById('submissionForm');
        if (submissionForm) {
            initSubmissionForm();
        }
    }
    
    /**
     * Update task status
     */
    function updateStatus(newStatus) {
        if (!confirm('Bạn có chắc chắn muốn cập nhật trạng thái?')) {
            return;
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.urls.update, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.error === 0) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + res.message);
                        if (res.need_submit) {
                            // Scroll to submission form
                            var submissionPanel = document.querySelector('.task-panel-success');
                            if (submissionPanel) {
                                submissionPanel.scrollIntoView({behavior: 'smooth'});
                            }
                        }
                    }
                } catch (e) {
                    alert('Có lỗi xảy ra');
                    console.error('Parse error:', e);
                }
            }
        };
        
        xhr.send('id=' + config.taskId + '&status=' + newStatus);
    }
    
    /**
     * Initialize submission form
     */
    function initSubmissionForm() {
        var form = document.getElementById('submissionForm');
        var fileInput = document.getElementById('submissionFiles');
        var uploadZone = document.getElementById('fileUploadZone');
        var previewList = document.getElementById('filePreviewList');
        
        if (!form) {
            return;
        }
        
        // File input change handler
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                updateFilePreview(this.files);
            });
        }
        
        // Drag and drop handlers
        if (uploadZone) {
            uploadZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });
            
            uploadZone.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });
            
            uploadZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    updateFilePreview(e.dataTransfer.files);
                }
            });
        }
        
        // Form submit handler
        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                submitSubmissionForm(form, submitBtn);
                return false;
            });
        }
    }
    
    /**
     * Update file preview list
     */
    function updateFilePreview(files) {
        var previewList = document.getElementById('filePreviewList');
        if (!previewList) {
            return;
        }
        
        previewList.innerHTML = '';
        
        if (files.length > 5) {
            alert('Chỉ được chọn tối đa 5 file');
            var fileInput = document.getElementById('submissionFiles');
            if (fileInput) {
                fileInput.value = '';
            }
            return;
        }
        
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var item = document.createElement('div');
            item.className = 'file-preview-item';
            
            var icon = document.createElement('i');
            icon.className = 'fa ' + getFileIcon(file.name);
            
            var name = document.createElement('span');
            name.className = 'file-preview-name';
            name.textContent = file.name;
            
            var size = document.createElement('span');
            size.className = 'file-preview-size';
            size.textContent = formatBytes(file.size);
            
            item.appendChild(icon);
            item.appendChild(name);
            item.appendChild(size);
            previewList.appendChild(item);
        }
    }
    
    /**
     * Submit submission form
     */
    function submitSubmissionForm(form, submitBtn) {
        var descriptionField = form.querySelector('textarea[name="description"]');
        var fileInput = document.getElementById('submissionFiles');
        
        if (!descriptionField || !fileInput) {
            return;
        }
        
        // Sync CKEditor content to textarea if editor exists
        if (window.submissionEditor) {
            descriptionField.value = window.submissionEditor.getData();
        }
        
        // Get plain text for validation (remove HTML tags)
        var plainText = descriptionField.value.replace(/<[^>]*>/g, '').trim();
        if (plainText.length < 10) {
            alert('Mô tả kết quả phải có ít nhất 10 ký tự');
            if (window.submissionEditor) {
                window.submissionEditor.focus();
            }
            return;
        }
        
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Vui lòng chọn ít nhất 1 file kết quả');
            return;
        }
        
        var formData = new FormData(form);
        var originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang nộp...';
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.urls.submission, true);
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.error === 0) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + res.message);
                    }
                } catch (err) {
                    alert('Có lỗi xảy ra khi xử lý response');
                    console.error('Submit error:', err, xhr.responseText);
                }
            }
        };
        
        xhr.send(formData);
    }
    
    /**
     * Delete submission file
     */
    function deleteSubmissionFile(fileId) {
        if (!confirm('Bạn có chắc chắn muốn xóa file này?')) {
            return;
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.urls.submission + '&action=delete_file', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.error === 0) {
                        // Remove file item from DOM
                        var fileItem = document.querySelector('[data-file-id="' + fileId + '"]');
                        if (fileItem) {
                            fileItem.style.opacity = '0';
                            setTimeout(function() { 
                                fileItem.remove(); 
                            }, 300);
                        }
                        
                        // Update file count badge
                        var countBadge = document.querySelector('.file-count-badge');
                        if (countBadge) {
                            countBadge.textContent = res.total_files + ' file';
                        }
                    } else {
                        alert('Lỗi: ' + res.message);
                    }
                } catch (e) {
                    alert('Có lỗi xảy ra');
                    console.error('Delete file error:', e);
                }
            }
        };
        
        xhr.send('file_id=' + fileId);
    }
    
    /**
     * Delete entire submission
     */
    function deleteSubmission(submissionId) {
        if (!confirm('Bạn có chắc chắn muốn xóa toàn bộ lần nộp kết quả này không? Mọi file đính kèm cũng sẽ bị xóa.')) {
            return;
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.urls.submission + '&action=delete_submission', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.error === 0) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + res.message);
                    }
                } catch (e) {
                    alert('Có lỗi xảy ra: ' + e.message);
                    console.error('Delete submission error:', e);
                }
            }
        };
        
        xhr.send('submission_id=' + submissionId);
    }
    
    /**
     * Get file icon based on extension
     */
    function getFileIcon(filename) {
        var ext = filename.split('.').pop().toLowerCase();
        var icons = {
            'jpg': 'fa-file-image-o',
            'jpeg': 'fa-file-image-o',
            'png': 'fa-file-image-o',
            'gif': 'fa-file-image-o',
            'webp': 'fa-file-image-o',
            'pdf': 'fa-file-pdf-o',
            'doc': 'fa-file-word-o',
            'docx': 'fa-file-word-o',
            'xls': 'fa-file-excel-o',
            'xlsx': 'fa-file-excel-o',
            'zip': 'fa-file-archive-o',
            'rar': 'fa-file-archive-o',
            '7z': 'fa-file-archive-o'
        };
        return icons[ext] || 'fa-file-o';
    }
    
    /**
     * Format bytes to human readable
     */
    function formatBytes(bytes) {
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        }
        if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        }
        return bytes + ' bytes';
    }
    
    // Public API
    return {
        init: init,
        updateStatus: updateStatus,
        deleteSubmissionFile: deleteSubmissionFile,
        deleteSubmission: deleteSubmission
    };
})();

// Make functions globally accessible for onclick handlers
function updateStatus(newStatus) {
    WorkmanDetail.updateStatus(newStatus);
}

function deleteSubmissionFile(fileId) {
    WorkmanDetail.deleteSubmissionFile(fileId);
}

function deleteSubmission(submissionId) {
    WorkmanDetail.deleteSubmission(submissionId);
}
