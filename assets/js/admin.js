document.addEventListener('DOMContentLoaded', function() {
    const bodyClasses = document.body.className;
    if (!bodyClasses.includes('post-type-running_route') || 
        (!bodyClasses.includes('post-php') && !bodyClasses.includes('post-new-php'))) {
        return;
    }
    
    const metabox = document.querySelector('.running-routes-gpx-metabox');
    if (!metabox) return;
    
    let mediaUploader = null;
    const postId = document.getElementById('post_ID')?.value || 0;
    const fileInput = metabox.querySelector('#rr-gpx-device-upload');
    const attachmentIdField = metabox.querySelector('#_rr_gpx_attachment_id');
    
    // Проверка nonce из локализованных данных
    const nonce = runningRoutesAdminData?.nonce || '';
    
    // Initialize WordPress media uploader
    function initMediaUploader() {
        if (mediaUploader) {
            mediaUploader.open();
            return mediaUploader;
        }
        
        mediaUploader = wp.media({
            title: runningRoutesAdminData?.i18n?.selectGpx || 'Выберите GPX файл',
            button: {
                text: runningRoutesAdminData?.i18n?.useFile || 'Использовать файл'
            },
            multiple: false,
            library: {
                type: 'application/gpx+xml'
            }
        });
        
        return mediaUploader;
    }
    
    // Copy text to clipboard
    function copyToClipboard(text, element) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Visual feedback
        const originalTitle = element.getAttribute('title');
        element.setAttribute('title', runningRoutesAdminData?.i18n?.copySuccess || 'Скопировано!');
        
        // Show tooltip
        const tooltip = document.createElement('span');
        tooltip.className = 'copy-tooltip';
        tooltip.textContent = runningRoutesAdminData?.i18n?.copySuccess || 'Скопировано!';
        tooltip.style.cssText = `
            position: absolute;
            background: #32373c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 100000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        `;
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = `${rect.left + window.scrollX}px`;
        tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
        document.body.appendChild(tooltip);
        
        // Animate tooltip
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
        
        // Remove tooltip after delay
        setTimeout(() => {
            tooltip.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(tooltip);
                element.setAttribute('title', originalTitle);
            }, 200);
        }, 1500);
    }
    
    // Save attachment ID via AJAX
    function saveAttachmentId(attachmentId) {
        return fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'rr_save_attachment_id',
                post_id: postId,
                attachment_id: attachmentId,
                nonce: nonce
            })
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Error saving attachment ID:', error);
            throw error;
        });
    }
    
    // Upload from device - trigger file input
    metabox.querySelector('.upload-from-device')?.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
    });
    
    // Handle file selection from device
    fileInput?.addEventListener('change', function(e) {
        if (!this.files.length) return;
        
        const file = this.files[0];
        const fileName = file.name.toLowerCase();
        
        if (!fileName.endsWith('.gpx')) {
            alert(runningRoutesAdminData?.i18n?.invalidFile || 'Пожалуйста, выберите файл GPX');
            this.value = '';
            return;
        }
        
        // Show loading state
        const originalButtonText = metabox.querySelector('.upload-from-device').textContent;
        metabox.querySelector('.upload-from-device').textContent = 'Загрузка...';
        metabox.querySelector('.upload-from-device').disabled = true;
        
        // Create FormData
        const formData = new FormData();
        formData.append('action', 'rr_upload_gpx_file');
        formData.append('nonce', nonce);
        formData.append('post_id', postId);
        formData.append('gpx_file', file);
        
        // Send AJAX request
        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                updateGpxUI(data.data);
            } else {
                throw new Error(data.data || 'Ошибка загрузки файла');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при загрузке файла: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            metabox.querySelector('.upload-from-device').textContent = originalButtonText;
            metabox.querySelector('.upload-from-device').disabled = false;
            // Clear file input
            this.value = '';
        });
    });
    
    // Select from library
    metabox.querySelector('.select-from-library')?.addEventListener('click', function(e) {
        e.preventDefault();
        const uploader = initMediaUploader();
        
        uploader.on('select', function() {
            const attachment = uploader.state().get('selection').first().toJSON();
            if (attachment.mime !== 'application/gpx+xml') {
                alert(runningRoutesAdminData?.i18n?.invalidFile || 'Пожалуйста, выберите файл GPX');
                return;
            }
            
            // Save attachment ID immediately
            saveAttachmentId(attachment.id)
                .then(data => {
                    if (data.success && data.data) {
                        updateGpxUI(data.data);
                    } else {
                        throw new Error(data.data?.message || 'Ошибка сохранения файла');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при сохранении файла: ' + error.message);
                });
        });
        
        uploader.open();
    });
    
    // Copy URL
    metabox.addEventListener('click', function(e) {
        if (e.target.classList.contains('copy-url')) {
            e.preventDefault();
            const url = e.target.closest('.gpx-url-container').querySelector('.gpx-url')?.value;
            if (url) copyToClipboard(url, e.target);
        }
        
        if (e.target.classList.contains('copy-shortcode')) {
            e.preventDefault();
            const shortcode = e.target.closest('.shortcode-container').querySelector('.shortcode')?.value;
            if (shortcode) copyToClipboard(shortcode, e.target);
        }
    });
    
    // Remove file (delete from media library)
    metabox.querySelector('.remove-gpx-url')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!confirm('Вы уверены, что хотите удалить GPX файл из медиабиблиотеки? Это действие нельзя отменить.')) {
            return;
        }
        
        if (!attachmentIdField.value) {
            alert('Файл не найден');
            return;
        }
        
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'rr_remove_gpx_file',
                post_id: postId,
                nonce: nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearGpxUI();
            } else {
                alert(data.data || 'Ошибка при удалении файла');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при удалении файла: ' + error.message);
        });
    });
    
    // Detach file (remove from post but keep in media library)
    metabox.querySelector('.detach-gpx-url')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!confirm('Вы уверены, что хотите открепить GPX файл от этой записи? Файл останется в медиабиблиотеке.')) {
            return;
        }
        
        if (!attachmentIdField.value) {
            alert('Файл не найден');
            return;
        }
        
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'rr_detach_gpx_file',
                post_id: postId,
                nonce: nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearGpxUI();
            } else {
                alert(data.data || 'Ошибка при откреплении файла');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при откреплении файла: ' + error.message);
        });
    });
    
    // Update UI with GPX file data
    function updateGpxUI(data) {
        const gpxUrlInput = metabox.querySelector('.gpx-url');
        const shortcodeInput = metabox.querySelector('.shortcode');
        const fileInfo = metabox.querySelector('.gpx-file-info');
        const downloadLink = fileInfo?.querySelector('a[download]');
        
        if (attachmentIdField) attachmentIdField.value = data.attachment_id || '';
        if (gpxUrlInput) gpxUrlInput.value = data.url || '';
        if (shortcodeInput) shortcodeInput.value = data.shortcode || '';
        if (fileInfo) fileInfo.style.display = 'block';
        if (downloadLink) downloadLink.href = data.url || '';
        if (downloadLink) downloadLink.download = data.filename || 'route.gpx';
    }
    
    // Clear UI
    function clearGpxUI() {
        const fileInfo = metabox.querySelector('.gpx-file-info');
        
        if (attachmentIdField) attachmentIdField.value = '';
        if (fileInfo) fileInfo.style.display = 'none';
    }
    
    // Initialize UI
    if (attachmentIdField.value) {
        const fileInfo = metabox.querySelector('.gpx-file-info');
        if (fileInfo) fileInfo.style.display = 'block';
    }
});