/**
 * Fallback Rich Text Editor
 * Simple WYSIWYG editor when CKEditor cannot load
 */

class FallbackEditor {
	constructor(elementId, options = {}) {
		this.elementId = elementId;
		this.element = document.getElementById(elementId);
		this.options = {
			height: options.height || '400px',
			language: options.language || 'vi',
			toolbar: options.toolbar || [
				'bold',
				'italic',
				'underline',
				'link',
				'list',
				'justify',
			],
		};
		this.init();
	}

	init() {
		if (!this.element) {
			console.error(
				'Fallback Editor: Element not found:',
				this.elementId
			);
			return;
		}

		// Create editor container
		const container = document.createElement('div');
		container.className = 'fallback-editor-container';
		container.innerHTML = this.createEditorHTML();

		// Replace textarea with editor
		this.element.style.display = 'none';
		this.element.parentNode.insertBefore(
			container,
			this.element.nextSibling
		);

		// Initialize editor
		this.setupEditor();
		this.bindEvents();

		// Load initial content
		this.setData(this.element.value);

		console.log('✅ Fallback Editor initialized for:', this.elementId);
	}

	createEditorHTML() {
		return `
            <div class="fallback-editor">
                <div class="editor-toolbar">
                    <button type="button" data-command="bold" title="Bold (Ctrl+B)"><b>B</b></button>
                    <button type="button" data-command="italic" title="Italic (Ctrl+I)"><i>I</i></button>
                    <button type="button" data-command="underline" title="Underline (Ctrl+U)"><u>U</u></button>
                    <span class="separator">|</span>
                    <button type="button" data-command="insertUnorderedList" title="Bullet List">• List</button>
                    <button type="button" data-command="insertOrderedList" title="Numbered List">1. List</button>
                    <span class="separator">|</span>
                    <button type="button" data-command="justifyLeft" title="Align Left">◀</button>
                    <button type="button" data-command="justifyCenter" title="Align Center">▬</button>
                    <button type="button" data-command="justifyRight" title="Align Right">▶</button>
                    <span class="separator">|</span>
                    <button type="button" data-command="createLink" title="Insert Link">Link</button>
                    <button type="button" data-command="unlink" title="Remove Link">Unlink</button>
                    <span class="separator">|</span>
                    <button type="button" data-command="removeFormat" title="Remove Format">Clear</button>
                </div>
                <div class="editor-content" contenteditable="true" style="min-height: ${this.options.height}; border: 1px solid #ddd; padding: 10px; background: white;"></div>
            </div>
        `;
	}

	setupEditor() {
		this.editorContent = document.querySelector(
			`#${this.elementId} + .fallback-editor-container .editor-content`
		);
		this.toolbar = document.querySelector(
			`#${this.elementId} + .fallback-editor-container .editor-toolbar`
		);
	}

	bindEvents() {
		// Toolbar buttons
		this.toolbar.addEventListener('click', (e) => {
			if (e.target.hasAttribute('data-command')) {
				e.preventDefault();
				const command = e.target.getAttribute('data-command');
				this.execCommand(command);
			}
		});

		// Update original textarea on content change
		this.editorContent.addEventListener('input', () => {
			this.element.value = this.getData();
		});

		// Handle keyboard shortcuts
		this.editorContent.addEventListener('keydown', (e) => {
			if (e.ctrlKey) {
				switch (e.key) {
					case 'b':
						e.preventDefault();
						this.execCommand('bold');
						break;
					case 'i':
						e.preventDefault();
						this.execCommand('italic');
						break;
					case 'u':
						e.preventDefault();
						this.execCommand('underline');
						break;
				}
			}
		});
	}

	execCommand(command) {
		if (command === 'createLink') {
			const url = prompt('Nhập URL:');
			if (url) {
				document.execCommand(command, false, url);
			}
		} else {
			document.execCommand(command, false, null);
		}
		this.editorContent.focus();
	}

	getData() {
		return this.editorContent.innerHTML;
	}

	setData(html) {
		this.editorContent.innerHTML = html;
		this.element.value = html;
	}
}

// Auto-initialize for textareas with class 'rich-editor'
document.addEventListener('DOMContentLoaded', function () {
	// Check if CKEditor is available
	if (typeof CKEDITOR === 'undefined') {
		console.log('⚠️ CKEditor not available, using fallback editor');

		// Find textareas that need rich editing
		const textareas = document.querySelectorAll(
			'textarea[data-rich-editor="true"], textarea#content'
		);
		textareas.forEach((textarea) => {
			if (textarea.id) {
				new FallbackEditor(textarea.id);
			}
		});
	}
});
