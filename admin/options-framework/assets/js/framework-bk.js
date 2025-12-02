/**
 * BizzPlugin Options Framework - Main JavaScript
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * BizzPlugin Framework Module
     */
    const BizzPluginFramework = {
        
        /**
         * Configuration
         */
        config: {
            ajaxUrl: '',
            nonce: '',
            strings: {}
        },

        /**
         * DOM Elements
         */
        elements: {
            wrap: null,
            form: null,
            saveButton: null,
            saveStatus: null,
            navLinks: null,
            sections: null
        },

        /**
         * Initialize the framework
         */
        init() {
            // Set configuration from localized data
            if (typeof bizzpluginFramework !== 'undefined') {
                this.config.ajaxUrl = bizzpluginFramework.ajaxUrl;
                this.config.nonce = bizzpluginFramework.nonce;
                this.config.strings = bizzpluginFramework.strings;
            }

            // Cache DOM elements
            this.cacheElements();

            // Only initialize if we're on a framework page
            if (!this.elements.wrap.length) {
                return;
            }

            // Bind events
            this.bindEvents();

            // Initialize components
            this.initColorPickers();
            this.initDatePickers();
            this.initConditionalFields();
            this.initImageSelect();
        },

        /**
         * Cache DOM elements
         */
        cacheElements() {
            this.elements.wrap = $('.bizzplugin-framework-wrap');
            this.elements.form = $('#bizzplugin-options-form');
            this.elements.saveButton = $('#bizzplugin-save-options');
            this.elements.saveStatus = $('.bizzplugin-save-status');
            this.elements.navLinks = $('.bizzplugin-nav-link, .bizzplugin-nav-sublink');
            this.elements.sections = $('.bizzplugin-section');
        },

        /**
         * Bind events
         */
        bindEvents() {
            // Navigation
            this.elements.navLinks.on('click', this.handleNavigation.bind(this));

            // Save options
            this.elements.saveButton.on('click', this.saveOptions.bind(this));

            // Reset section
            $('.bizzplugin-reset-section').on('click', this.resetSection.bind(this));

            // Image upload
            $(document).on('click', '.bizzplugin-image-select', this.openMediaUploader.bind(this, 'image'));
            $(document).on('click', '.bizzplugin-image-remove', this.removeImage.bind(this));

            // File upload
            $(document).on('click', '.bizzplugin-file-select', this.openMediaUploader.bind(this, 'file'));
            $(document).on('click', '.bizzplugin-file-remove', this.removeFile.bind(this));

            // On/Off toggle - trigger conditional fields
            $(document).on('change', '.bizzplugin-toggle-input, .bizzplugin-checkbox', this.handleToggleChange.bind(this));

            // Select change - trigger conditional fields
            $(document).on('change', '.bizzplugin-select, .bizzplugin-radio', this.handleSelectChange.bind(this));

            // Image select
            $(document).on('change', '.bizzplugin-image-select-input', this.handleImageSelectChange.bind(this));

            // Form change detection
            this.elements.form.on('change', 'input, select, textarea', () => {
                this.elements.saveButton.addClass('button-primary-changed');
            });

            // Keyboard shortcut for saving (Ctrl+S / Cmd+S)
            $(document).on('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    this.saveOptions();
                }
            });
        },

        /**
         * Handle navigation click
         */
        handleNavigation(e) {
            e.preventDefault();
            
            const $link = $(e.currentTarget);
            const sectionId = $link.data('section');
            const subsectionId = $link.data('subsection');

            // Update active state
            $('.bizzplugin-nav-item').removeClass('active');
            $link.closest('.bizzplugin-nav-item').addClass('active');
            
            // Handle sublinks
            $('.bizzplugin-nav-sublink').removeClass('active');
            if ($link.hasClass('bizzplugin-nav-sublink')) {
                $link.addClass('active');
            }

            // Show/hide sections
            this.elements.sections.hide();
            $(`#section-${sectionId}`).fadeIn(200);

            // Scroll to subsection if specified
            if (subsectionId) {
                const $subsection = $(`#subsection-${subsectionId}`);
                if ($subsection.length) {
                    setTimeout(() => {
                        $('html, body').animate({
                            scrollTop: $subsection.offset().top - 50
                        }, 300);
                    }, 250);
                }
            }

            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('section', sectionId);
            if (subsectionId) {
                url.searchParams.set('subsection', subsectionId);
            } else {
                url.searchParams.delete('subsection');
            }
            window.history.replaceState({}, '', url);
        },

        /**
         * Save options via AJAX
         */
        saveOptions() {
            const $button = this.elements.saveButton;
            
            if ($button.prop('disabled')) {
                return;
            }

            const panelId = this.elements.wrap.data('panel-id');
            const optionName = this.elements.wrap.data('option-name');
            const formData = this.serializeFormData();

            // Clear previous errors
            this.clearValidationErrors();

            // Update UI
            this.setStatus('saving');
            $button.prop('disabled', true).addClass('bizzplugin-loading');

            // Send AJAX request
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_save_options',
                    nonce: this.config.nonce,
                    panel_id: panelId,
                    option_name: optionName,
                    data: formData
                },
                success: (response) => {
                    if (response.success) {
                        this.setStatus('saved');
                        $button.removeClass('button-primary-changed');
                        
                        // Trigger custom event
                        $(document).trigger('bizzplugin:options_saved', [formData, response.data]);
                    } else {
                        this.setStatus('error', response.data.message);
                        
                        // Show validation errors
                        if (response.data.errors) {
                            this.showValidationErrors(response.data.errors);
                        }
                    }
                },
                error: (xhr, status, error) => {
                    this.setStatus('error', this.config.strings.error);
                    console.error('BizzPlugin Framework Error:', error);
                },
                complete: () => {
                    $button.prop('disabled', false).removeClass('bizzplugin-loading');
                }
            });
        },

        /**
         * Reset section to defaults
         */
        resetSection(e) {
            e.preventDefault();
            
            if (!confirm(this.config.strings.confirm_reset)) {
                return;
            }

            const $button = $(e.currentTarget);
            const sectionId = $button.data('section');
            const panelId = this.elements.wrap.data('panel-id');
            const optionName = this.elements.wrap.data('option-name');

            $button.prop('disabled', true);
            this.setStatus('saving', this.config.strings.resetting);

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_reset_section',
                    nonce: this.config.nonce,
                    panel_id: panelId,
                    option_name: optionName,
                    section_id: sectionId
                },
                success: (response) => {
                    if (response.success) {
                        this.setStatus('saved', this.config.strings.reset_success);
                        
                        // Update field values with defaults
                        this.updateFieldValues(response.data.defaults);
                        
                        // Trigger custom event
                        $(document).trigger('bizzplugin:section_reset', [sectionId, response.data.defaults]);
                    } else {
                        this.setStatus('error', response.data.message);
                    }
                },
                error: () => {
                    this.setStatus('error', this.config.strings.reset_error);
                },
                complete: () => {
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Serialize form data
         */
        serializeFormData() {
            const formData = {};
            const $form = this.elements.form;

            // Text inputs, textareas, selects
            $form.find('input:not([type="checkbox"]):not([type="radio"]), textarea, select').each(function() {
                const $field = $(this);
                const name = $field.attr('name');
                
                if (!name) return;

                // Handle array fields (multi-select, checkbox groups)
                if (name.endsWith('[]')) {
                    const baseName = name.slice(0, -2);
                    if (!formData[baseName]) {
                        formData[baseName] = [];
                    }
                    if ($field.is('select[multiple]')) {
                        formData[baseName] = $field.val() || [];
                    } else {
                        formData[baseName].push($field.val());
                    }
                } else {
                    formData[name] = $field.val();
                }
            });

            // Checkboxes
            $form.find('input[type="checkbox"]').each(function() {
                const $checkbox = $(this);
                const name = $checkbox.attr('name');
                
                if (!name) return;

                if (name.endsWith('[]')) {
                    const baseName = name.slice(0, -2);
                    if (!formData[baseName]) {
                        formData[baseName] = [];
                    }
                    if ($checkbox.is(':checked')) {
                        formData[baseName].push($checkbox.val());
                    }
                } else {
                    formData[name] = $checkbox.is(':checked') ? '1' : '0';
                }
            });

            // Radio buttons
            $form.find('input[type="radio"]:checked').each(function() {
                const $radio = $(this);
                const name = $radio.attr('name');
                
                if (name) {
                    formData[name] = $radio.val();
                }
            });

            return formData;
        },

        /**
         * Update field values (after reset)
         */
        updateFieldValues(defaults) {
            for (const [fieldId, value] of Object.entries(defaults)) {
                const $field = $(`[name="${fieldId}"], [name="${fieldId}[]"]`);
                
                if (!$field.length) continue;

                const fieldType = $field.attr('type') || $field.prop('tagName').toLowerCase();

                switch (fieldType) {
                    case 'checkbox':
                        if ($field.length > 1) {
                            // Checkbox group
                            $field.prop('checked', false);
                            if (Array.isArray(value)) {
                                value.forEach(v => {
                                    $field.filter(`[value="${v}"]`).prop('checked', true);
                                });
                            }
                        } else {
                            $field.prop('checked', value === '1');
                        }
                        break;

                    case 'radio':
                        $field.filter(`[value="${value}"]`).prop('checked', true);
                        break;

                    case 'select':
                        $field.val(value);
                        break;

                    default:
                        $field.val(value);
                }

                // Trigger change for conditional fields
                $field.trigger('change');
            }

            // Reinitialize color pickers
            this.initColorPickers();
        },

        /**
         * Set status message
         */
        setStatus(status, message) {
            const $status = this.elements.saveStatus;
            
            $status.removeClass('saving saved error').addClass(status);

            switch (status) {
                case 'saving':
                    $status.text(message || this.config.strings.saving);
                    break;
                case 'saved':
                    $status.text(message || this.config.strings.saved);
                    setTimeout(() => {
                        $status.removeClass('saved').text('');
                    }, 3000);
                    break;
                case 'error':
                    $status.text(message || this.config.strings.error);
                    break;
            }
        },

        /**
         * Show validation errors
         */
        showValidationErrors(errors) {
            for (const [fieldId, message] of Object.entries(errors)) {
                const $field = $(`[name="${fieldId}"]`).closest('.bizzplugin-field');
                $field.addClass('has-error');
                
                // Remove existing error message
                $field.find('.bizzplugin-field-error').remove();
                
                // Add error message
                $field.find('.bizzplugin-field-content').append(
                    `<div class="bizzplugin-field-error">${message}</div>`
                );
            }
        },

        /**
         * Clear validation errors
         */
        clearValidationErrors() {
            $('.bizzplugin-field').removeClass('has-error');
            $('.bizzplugin-field-error').remove();
        },

        /**
         * Initialize color pickers
         */
        initColorPickers() {
            $('.bizzplugin-color-picker').wpColorPicker({
                change: () => {
                    this.elements.saveButton.addClass('button-primary-changed');
                },
                clear: () => {
                    this.elements.saveButton.addClass('button-primary-changed');
                }
            });
        },

        /**
         * Initialize date pickers
         */
        initDatePickers() {
            $('.bizzplugin-date-picker').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                onSelect: () => {
                    this.elements.saveButton.addClass('button-primary-changed');
                }
            });
        },

        /**
         * Initialize conditional fields
         */
        initConditionalFields() {
            const $conditionalFields = $('[data-dependency]');
            
            $conditionalFields.each((index, element) => {
                const $field = $(element);
                const dependencyField = $field.data('dependency');
                const dependencyValue = String($field.data('dependency-value'));
                
                // Find the dependency field
                const $dependency = $(`[name="${dependencyField}"]`);
                
                // Initial state
                this.updateConditionalField($field, $dependency, dependencyValue);
            });
        },

        /**
         * Update conditional field visibility
         */
        updateConditionalField($field, $dependency, dependencyValue) {
            let currentValue;
            
            if ($dependency.is(':checkbox')) {
                currentValue = $dependency.is(':checked') ? '1' : '0';
            } else if ($dependency.is(':radio')) {
                currentValue = $dependency.filter(':checked').val();
            } else {
                currentValue = $dependency.val();
            }

            // Handle multiple values (comma-separated)
            const requiredValues = dependencyValue.split(',').map(v => v.trim());
            
            if (requiredValues.includes(String(currentValue))) {
                $field.removeClass('hidden').slideDown(200);
            } else {
                $field.addClass('hidden').slideUp(200);
            }
        },

        /**
         * Handle toggle/checkbox change for conditional fields
         */
        handleToggleChange(e) {
            const $toggle = $(e.currentTarget);
            const fieldName = $toggle.attr('name');
            
            // Find fields dependent on this toggle
            $(`[data-dependency="${fieldName}"]`).each((index, element) => {
                const $field = $(element);
                const dependencyValue = String($field.data('dependency-value'));
                this.updateConditionalField($field, $toggle, dependencyValue);
            });
        },

        /**
         * Handle select/radio change for conditional fields
         */
        handleSelectChange(e) {
            const $select = $(e.currentTarget);
            const fieldName = $select.attr('name');
            
            // Find fields dependent on this select
            $(`[data-dependency="${fieldName}"]`).each((index, element) => {
                const $field = $(element);
                const dependencyValue = String($field.data('dependency-value'));
                this.updateConditionalField($field, $select, dependencyValue);
            });
        },

        /**
         * Initialize image select
         */
        initImageSelect() {
            // Mark selected items
            $('.bizzplugin-image-select-input:checked').closest('.bizzplugin-image-select-item').addClass('selected');
        },

        /**
         * Handle image select change
         */
        handleImageSelectChange(e) {
            const $input = $(e.currentTarget);
            const $wrap = $input.closest('.bizzplugin-image-select-wrap');
            
            // Remove selected class from all items
            $wrap.find('.bizzplugin-image-select-item').removeClass('selected');
            
            // Add selected class to current item
            $input.closest('.bizzplugin-image-select-item').addClass('selected');
        },

        /**
         * Open WordPress media uploader
         */
        openMediaUploader(type, e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $container = $button.closest(type === 'image' ? '.bizzplugin-image-upload' : '.bizzplugin-file-upload');
            const $input = $container.find(type === 'image' ? '.bizzplugin-image-input' : '.bizzplugin-file-input');

            const mediaUploader = wp.media({
                title: type === 'image' ? this.config.strings.select_image : this.config.strings.select_file,
                button: {
                    text: type === 'image' ? this.config.strings.use_image : this.config.strings.use_file
                },
                library: {
                    type: type === 'image' ? 'image' : ''
                },
                multiple: false
            });

            mediaUploader.on('select', () => {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                
                $input.val(attachment.id);

                if (type === 'image') {
                    const previewUrl = attachment.sizes?.thumbnail?.url || attachment.url;
                    const $preview = $container.find('.bizzplugin-image-preview');
                    
                    $preview.find('img').attr('src', previewUrl);
                    $preview.show();
                } else {
                    $container.find('.bizzplugin-file-name').text(attachment.filename);
                    $container.find('.bizzplugin-file-remove').show();
                }

                this.elements.saveButton.addClass('button-primary-changed');
            });

            mediaUploader.open();
        },

        /**
         * Remove image
         */
        removeImage(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $container = $button.closest('.bizzplugin-image-upload');
            
            $container.find('.bizzplugin-image-input').val('');
            $container.find('.bizzplugin-image-preview').hide();
            
            this.elements.saveButton.addClass('button-primary-changed');
        },

        /**
         * Remove file
         */
        removeFile(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $container = $button.closest('.bizzplugin-file-upload');
            
            $container.find('.bizzplugin-file-input').val('');
            $container.find('.bizzplugin-file-name').text('');
            $button.hide();
            
            this.elements.saveButton.addClass('button-primary-changed');
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(() => {
        BizzPluginFramework.init();
    });

    /**
     * Expose to global scope for extensibility
     */
    window.BizzPluginFramework = BizzPluginFramework;

})(jQuery);
