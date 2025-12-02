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
            navSublinks: null,
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
            this.initOptionSelect();
            this.initApiDocsTabs();
            this.initSliders();
            this.initSearch();
            this.initRepeaters();
            this.initWebhooks();
        },

        /**
         * Cache DOM elements
         */
        cacheElements() {
            this.elements.wrap = $('.bizzplugin-framework-wrap');
            this.elements.form = $('#bizzplugin-options-form');
            this.elements.saveButton = $('#bizzplugin-save-options');
            this.elements.saveStatus = $('.bizzplugin-save-status');
            this.elements.navLinks = $('.bizzplugin-nav-link');
            this.elements.navSublinks = $('.bizzplugin-nav-sublink');
            this.elements.sections = $('.bizzplugin-section');
        },

        /**
         * Bind events
         */
        bindEvents() {
            // Navigation - main section links
            this.elements.navLinks.on('click', this.handleMainNavigation.bind(this));
            
            // Navigation - arrow icon click for toggle
            $(document).on('click', '.bizzplugin-nav-arrow', this.handleArrowClick.bind(this));
            
            // Navigation - subsection links
            this.elements.navSublinks.on('click', this.handleSubNavigation.bind(this));

            // Save options
            this.elements.saveButton.on('click', this.saveOptions.bind(this));

            // Reset section
            $(document).on('click', '.bizzplugin-reset-section', this.resetSection.bind(this));
            
            // Reset all
            $(document).on('click', '.bizzplugin-reset-all', this.resetAll.bind(this));
            
            // Export options
            $(document).on('click', '#bizzplugin-export-options', this.exportOptions.bind(this));
            
            // Import options - trigger file input
            $(document).on('click', '#bizzplugin-import-trigger', this.triggerImport.bind(this));
            
            // Import options - file change
            $(document).on('change', '#bizzplugin-import-file', this.importOptions.bind(this));

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
            
            // Option select (text-based selection)
            $(document).on('change', '.bizzplugin-option-select-input', this.handleOptionSelectChange.bind(this));
            
            // Slider input
            $(document).on('input', '.bizzplugin-slider', this.handleSliderChange.bind(this));

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
            
            // Copy button
            $(document).on('click', '.bizzplugin-copy-btn', this.handleCopyClick.bind(this));
            
            // Test webhook button
            $(document).on('click', '#bizzplugin-test-webhook', this.testWebhook.bind(this));
            
            // Generate API key button
            $(document).on('click', '#bizzplugin-generate-api-key', this.generateApiKey.bind(this));
            
            // API docs tabs
            $(document).on('click', '.bizzplugin-docs-tab', this.handleDocsTabClick.bind(this));
            
            // Plugin install/activate buttons
            $(document).on('click', '.bizzplugin-install-plugin', this.installPlugin.bind(this));
            $(document).on('click', '.bizzplugin-activate-plugin', this.activatePlugin.bind(this));
            
            // Search functionality
            $(document).on('input', '#bizzplugin-settings-search', this.handleSearchInput.bind(this));
            $(document).on('click', '.bizzplugin-search-clear', this.clearSearch.bind(this));
            
            // Repeater field events
            $(document).on('click', '.bizzplugin-repeater-add', this.repeaterAddItem.bind(this));
            $(document).on('click', '.bizzplugin-repeater-item-remove', this.repeaterRemoveItem.bind(this));
            $(document).on('click', '.bizzplugin-repeater-item-toggle', this.repeaterToggleItem.bind(this));
            $(document).on('input', '.bizzplugin-repeater-item input[type="text"], .bizzplugin-repeater-item input[type="email"], .bizzplugin-repeater-item input[type="url"]', this.repeaterUpdateItemTitle.bind(this));
        },

        /**
         * Handle arrow icon click for toggling submenu
         */
        handleArrowClick(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $arrow = $(e.currentTarget);
            const $navItem = $arrow.closest('.bizzplugin-nav-item');
            const $submenu = $navItem.find('.bizzplugin-nav-submenu');
            
            if ($navItem.hasClass('expanded')) {
                // Collapse this submenu
                $navItem.removeClass('expanded');
                $submenu.slideUp(200);
            } else {
                // Expand this submenu
                $navItem.addClass('expanded');
                $submenu.slideDown(200);
            }
        },

        /**
         * Handle main navigation click
         */
        handleMainNavigation(e) {
            e.preventDefault();
            
            // Check if arrow was clicked (handle separately)
            if ($(e.target).hasClass('bizzplugin-nav-arrow')) {
                return;
            }
            
            const $link = $(e.currentTarget);
            const sectionId = $link.data('section');
            const hasFields = $link.data('has-fields') === 1 || $link.data('has-fields') === '1';
            const hasSubsections = $link.data('has-subsections') === 1 || $link.data('has-subsections') === '1';
            
            // Collapse all other expanded menus
            $('.bizzplugin-nav-item').not($link.closest('.bizzplugin-nav-item')).removeClass('expanded');
            $('.bizzplugin-nav-item').not($link.closest('.bizzplugin-nav-item')).find('.bizzplugin-nav-submenu').slideUp(200);
            
            // Update active state on nav items
            $('.bizzplugin-nav-item').removeClass('active');
            $('.bizzplugin-nav-link').removeClass('current');
            $('.bizzplugin-nav-sublink').removeClass('current');
            $('.bizzplugin-nav-subitem').removeClass('active');
            
            $link.closest('.bizzplugin-nav-item').addClass('active');
            
            // Handle subsection expansion/collapse
            if (hasSubsections) {
                $link.closest('.bizzplugin-nav-item').addClass('expanded');
                $link.closest('.bizzplugin-nav-item').find('.bizzplugin-nav-submenu').slideDown(200);
            }
            
            // If section has no fields but has subsections, auto-select first subsection
            if (!hasFields && hasSubsections) {
                const $firstSublink = $link.closest('.bizzplugin-nav-item').find('.bizzplugin-nav-sublink').first();
                if ($firstSublink.length) {
                    $firstSublink.trigger('click');
                    return;
                }
            }
            
            // Mark this link as current
            $link.addClass('current');

            // Show/hide sections - only show section content, not subsections
            this.elements.sections.hide();
            $(`#section-${sectionId}`).fadeIn(200);

            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('section', sectionId);
            url.searchParams.delete('subsection');
            window.history.replaceState({}, '', url);
        },

        /**
         * Handle subsection navigation click
         */
        handleSubNavigation(e) {
            e.preventDefault();
            
            const $link = $(e.currentTarget);
            const sectionId = $link.data('section');
            const subsectionId = $link.data('subsection');

            // Update active state
            $('.bizzplugin-nav-item').removeClass('active');
            $('.bizzplugin-nav-link').removeClass('current');
            $('.bizzplugin-nav-sublink').removeClass('current');
            $('.bizzplugin-nav-subitem').removeClass('active');
            
            $link.closest('.bizzplugin-nav-item').addClass('active expanded');
            $link.addClass('current');
            $link.closest('.bizzplugin-nav-subitem').addClass('active');
            
            // Keep submenu visible
            $link.closest('.bizzplugin-nav-item').find('.bizzplugin-nav-submenu').show();

            // Show/hide sections - show subsection content
            this.elements.sections.hide();
            $(`#subsection-${subsectionId}`).fadeIn(200);

            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('section', sectionId);
            url.searchParams.set('subsection', subsectionId);
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
            const subsectionId = $button.data('subsection');
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
                    section_id: sectionId,
                    subsection_id: subsectionId || ''
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
         * Reset all settings
         */
        resetAll(e) {
            e.preventDefault();
            
            if (!confirm(this.config.strings.confirm_reset_all || 'Are you sure you want to reset all settings to default values?')) {
                return;
            }
            
            const panelId = this.elements.wrap.data('panel-id');
            const optionName = this.elements.wrap.data('option-name');
            const $button = $(e.currentTarget);
            
            $button.prop('disabled', true);
            this.setStatus('saving', this.config.strings.resetting);
            
            // Send AJAX request to reset all options
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_reset_all',
                    nonce: this.config.nonce,
                    panel_id: panelId,
                    option_name: optionName
                },
                success: (response) => {
                    if (response.success) {
                        this.setStatus('saved', this.config.strings.reset_all_success || 'All settings reset successfully!');
                        
                        // Update field values with defaults
                        this.updateFieldValues(response.data.defaults);
                        
                        // Trigger custom event
                        $(document).trigger('bizzplugin:all_reset', [response.data.defaults]);
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
            
            // Selector to exclude hidden template inputs from form serialization
            const templateExclude = '.bizzplugin-repeater-template input, .bizzplugin-repeater-template textarea, .bizzplugin-repeater-template select';

            // Text inputs, textareas, selects (exclude hidden template inputs)
            $form.find('input:not([type="checkbox"]):not([type="radio"]), textarea, select').not(templateExclude).each(function() {
                const $field = $(this);
                const name = $field.attr('name');
                
                if (!name) return;

                // Handle repeater fields (e.g., repeater_field[0][subfield])
                const repeaterMatch = name.match(/^([^\[]+)\[(\d+)\]\[([^\]]+)\]$/);
                if (repeaterMatch) {
                    const fieldName = repeaterMatch[1];
                    const index = parseInt(repeaterMatch[2]);
                    const subFieldName = repeaterMatch[3];
                    
                    if (!formData[fieldName]) {
                        formData[fieldName] = [];
                    }
                    if (!formData[fieldName][index]) {
                        formData[fieldName][index] = {};
                    }
                    formData[fieldName][index][subFieldName] = $field.val();
                    return;
                }

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

            // Checkboxes (exclude hidden template inputs)
            $form.find('input[type="checkbox"]').not(templateExclude).each(function() {
                const $checkbox = $(this);
                const name = $checkbox.attr('name');
                
                if (!name) return;

                // Handle repeater checkbox fields (e.g., repeater_field[0][subfield])
                const repeaterMatch = name.match(/^([^\[]+)\[(\d+)\]\[([^\]]+)\]$/);
                if (repeaterMatch) {
                    const fieldName = repeaterMatch[1];
                    const index = parseInt(repeaterMatch[2]);
                    const subFieldName = repeaterMatch[3];
                    
                    if (!formData[fieldName]) {
                        formData[fieldName] = [];
                    }
                    if (!formData[fieldName][index]) {
                        formData[fieldName][index] = {};
                    }
                    formData[fieldName][index][subFieldName] = $checkbox.is(':checked') ? '1' : '0';
                    return;
                }

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

            // Radio buttons (exclude hidden template inputs)
            $form.find('input[type="radio"]:checked').not(templateExclude).each(function() {
                const $radio = $(this);
                const name = $radio.attr('name');
                
                if (name) {
                    formData[name] = $radio.val();
                }
            });

            // Compact repeater arrays (remove gaps from sparse arrays)
            for (const key of Object.keys(formData)) {
                if (Array.isArray(formData[key]) && formData[key].length > 0) {
                    // Find the first defined element to check if this is a repeater field (array of objects)
                    const firstDefined = formData[key].find(item => item !== undefined && item !== null);
                    if (firstDefined && typeof firstDefined === 'object') {
                        formData[key] = formData[key].filter(item => item !== undefined && item !== null);
                    }
                }
            }

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
            this.showNotification(message || this.config.strings[status] || '', status === 'error' ? 'error' : 'success');
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
            
            // Initialize section dependencies
            this.initSectionDependencies();
        },
        
        /**
         * Initialize section-level dependencies
         */
        initSectionDependencies() {
            const $conditionalSections = $('[data-section-dependency]');
            
            $conditionalSections.each((index, element) => {
                const $section = $(element);
                const dependencyField = $section.data('section-dependency');
                const dependencyValue = String($section.data('section-dependency-value'));
                
                // Find the dependency field
                const $dependency = $(`[name="${dependencyField}"]`);
                
                // Initial state
                this.updateSectionDependency($section, $dependency, dependencyValue);
            });
        },
        
        /**
         * Update section visibility based on dependency
         */
        updateSectionDependency($section, $dependency, dependencyValue) {
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
                $section.removeClass('bizzplugin-section-hidden').slideDown(200);
            } else {
                $section.addClass('bizzplugin-section-hidden').slideUp(200);
                
                // If this section is currently active, navigate to first visible section
                if ($section.hasClass('active')) {
                    const $firstVisible = $('.bizzplugin-nav-item').not('.bizzplugin-section-hidden').first();
                    if ($firstVisible.length) {
                        $firstVisible.find('.bizzplugin-nav-link').trigger('click');
                    }
                }
            }
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
            
            // Find sections dependent on this toggle
            $(`[data-section-dependency="${fieldName}"]`).each((index, element) => {
                const $section = $(element);
                const dependencyValue = String($section.data('section-dependency-value'));
                this.updateSectionDependency($section, $toggle, dependencyValue);
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
            
            // Find sections dependent on this select
            $(`[data-section-dependency="${fieldName}"]`).each((index, element) => {
                const $section = $(element);
                const dependencyValue = String($section.data('section-dependency-value'));
                this.updateSectionDependency($section, $select, dependencyValue);
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
         * Initialize option select (text-based selection)
         */
        initOptionSelect() {
            // Mark selected items
            $('.bizzplugin-option-select-input:checked').closest('.bizzplugin-option-select-item').addClass('selected');
        },

        /**
         * Handle option select change
         */
        handleOptionSelectChange(e) {
            const $input = $(e.currentTarget);
            const $wrap = $input.closest('.bizzplugin-option-select-wrap');
            
            // Remove selected class from all items
            $wrap.find('.bizzplugin-option-select-item').removeClass('selected');
            
            // Add selected class to current item
            $input.closest('.bizzplugin-option-select-item').addClass('selected');
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
        },
        
        /**
         * Export options to JSON file
         */
        exportOptions(e) {
            e.preventDefault();
            
            const panelId = this.elements.wrap.data('panel-id');
            const optionName = this.elements.wrap.data('option-name');
            const formData = this.serializeFormData();
            
            // Create export data
            const exportData = {
                plugin: 'bizzplugin-option-framework',
                panel_id: panelId,
                option_name: optionName,
                exported_at: new Date().toISOString(),
                data: formData
            };
            
            // Create and download file
            const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${optionName}-export-${new Date().toISOString().substring(0, 10)}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            this.showNotification(this.config.strings.export_success || 'Settings exported successfully!', 'info');
        },
        
        /**
         * Trigger import file input
         */
        triggerImport(e) {
            e.preventDefault();
            $('#bizzplugin-import-file').trigger('click');
        },
        
        /**
         * Import options from JSON file
         */
        importOptions(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            const self = this;
            
            reader.onload = function(event) {
                try {
                    const importData = JSON.parse(event.target.result);
                    
                    // Validate import data
                    if (!importData.plugin || importData.plugin !== 'bizzplugin-option-framework') {
                        self.setStatus('error', self.config.strings.import_invalid || 'Invalid import file format.');
                        return;
                    }
                    
                    // Check if panel_id matches (optional warning)
                    const currentPanelId = self.elements.wrap.data('panel-id');
                    if (importData.panel_id && importData.panel_id !== currentPanelId) {
                        if (!confirm(self.config.strings.import_panel_mismatch || 'This export was created from a different panel. Do you want to continue importing?')) {
                            return;
                        }
                    }
                    
                    // Update field values
                    if (importData.data) {
                        self.updateFieldValues(importData.data);
                        self.elements.saveButton.addClass('button-primary-changed');
                        self.showNotification(self.config.strings.import_success || 'Settings imported successfully! Please save to apply changes.', 'info');
                    }
                } catch (error) {
                    self.setStatus('error', self.config.strings.import_error || 'Error parsing import file.');
                    console.error('Import error:', error);
                }
            };
            
            reader.readAsText(file);
            
            // Reset file input
            e.target.value = '';
        },
        
        /**
         * Handle copy button click
         */
        handleCopyClick(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const textToCopy = $button.data('copy');
            
            if (navigator.clipboard && textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const $icon = $button.find('.dashicons');
                    const originalClass = $icon.attr('class');
                    $icon.removeClass('dashicons-admin-page').addClass('dashicons-yes');
                    
                    setTimeout(() => {
                        $icon.attr('class', originalClass);
                    }, 2000);
                });
            }
        },
        
        /**
         * Test webhook
         */
        testWebhook(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const webhookUrl = $('#bizzplugin_webhook_url').val();
            const $response = $('#bizzplugin-webhook-response');
            
            if (!webhookUrl) {
                this.setStatus('error', this.config.strings.webhook_url_required || 'Please enter a webhook URL first.');
                return;
            }
            
            $button.prop('disabled', true).text(this.config.strings.testing || 'Testing...');
            
            const panelId = this.elements.wrap.data('panel-id');
            const optionName = this.elements.wrap.data('option-name');
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_test_webhook',
                    nonce: this.config.nonce,
                    panel_id: panelId,
                    option_name: optionName,
                    webhook_url: webhookUrl
                },
                success: (response) => {
                    $response.show().find('.bizzplugin-code-block').text(
                        JSON.stringify(response, null, 2)
                    );
                },
                error: (xhr, status, error) => {
                    $response.show().find('.bizzplugin-code-block').text(
                        'Error: ' + error
                    );
                },
                complete: () => {
                    $button.prop('disabled', false).html('<span class="dashicons dashicons-migrate"></span> Test Webhook');
                }
            });
        },
        
        /**
         * Generate API key
         */
        generateApiKey(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const panelId = this.elements.wrap.data('panel-id');
            
            if (!confirm(this.config.strings.confirm_regenerate_api_key || 'Are you sure you want to generate a new API key? The old key will be invalidated.')) {
                return;
            }
            
            $button.prop('disabled', true);
            const originalText = $button.html();
            $button.html('<span class="dashicons dashicons-update bizzplugin-spin"></span> ' + (this.config.strings.generating || 'Generating...'));
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_generate_api_key',
                    nonce: this.config.nonce,
                    panel_id: panelId
                },
                success: (response) => {
                    if (response.success) {
                        const apiKey = response.data.api_key;
                        
                        // Update the API key display
                        const $displayContainer = $('.bizzplugin-api-key-display');
                        
                        // Create new API key row using DOM manipulation for security
                        const $newRow = $('<div>').addClass('bizzplugin-api-key-row');
                        const $codeEl = $('<code>')
                            .addClass('bizzplugin-api-key-code')
                            .attr('id', 'bizzplugin-api-key-value')
                            .text(apiKey);
                        const $copyBtn = $('<button>')
                            .attr('type', 'button')
                            .addClass('button bizzplugin-copy-btn')
                            .attr('data-copy', apiKey)
                            .html('<span class="dashicons dashicons-admin-page"></span>');
                        
                        $newRow.append($codeEl).append($copyBtn);
                        $displayContainer.empty().append($newRow);
                        
                        // Update button text
                        $button.html('<span class="dashicons dashicons-update" style="margin-right: 5px; margin-top: 3px;"></span> ' + (this.config.strings.regenerate_api_key || 'Regenerate API Key'));
                        
                        this.showNotification(response.data.message, 'success');
                    } else {
                        $button.html(originalText);
                        this.showNotification(response.data.message, 'error');
                    }
                },
                error: (xhr, status, error) => {
                    $button.html(originalText);
                    this.showNotification(this.config.strings.api_key_error || 'Error generating API key', 'error');
                    console.error('API key generation error:', error);
                },
                complete: () => {
                    $button.prop('disabled', false);
                }
            });
        },
        
        /**
         * Initialize API docs tabs
         */
        initApiDocsTabs() {
            // Initial state - show first tab content
            $('.bizzplugin-docs-content[data-tab-content="get"]').addClass('active');
        },
        
        /**
         * Initialize sliders
         */
        initSliders() {
            $('.bizzplugin-slider').each((index, element) => {
                this.updateSliderUI($(element));
            });
        },
        
        /**
         * Update slider UI (value display and track background)
         */
        updateSliderUI($slider) {
            const value = $slider.val();
            const min = parseFloat($slider.attr('min')) || 0;
            const max = parseFloat($slider.attr('max')) || 100;
            const progress = ((value - min) / (max - min)) * 100;
            
            // Update the value display
            const $wrap = $slider.closest('.bizzplugin-slider-wrap');
            $wrap.find('.bizzplugin-slider-value-number').text(value);
            
            // Update the slider track background
            $slider.css('--slider-progress', progress + '%');
        },
        
        /**
         * Handle slider change
         */
        handleSliderChange(e) {
            const $slider = $(e.currentTarget);
            this.updateSliderUI($slider);
            this.elements.saveButton.addClass('button-primary-changed');
        },
        
        /**
         * Handle docs tab click
         */
        handleDocsTabClick(e) {
            e.preventDefault();
            
            const $tab = $(e.currentTarget);
            const tabId = $tab.data('tab');
            const $container = $tab.closest('.bizzplugin-api-card-body');
            
            // Update tab active state
            $container.find('.bizzplugin-docs-tab').removeClass('active');
            $tab.addClass('active');
            
            // Show/hide content
            $container.find('.bizzplugin-docs-content').removeClass('active');
            $container.find(`.bizzplugin-docs-content[data-tab-content="${tabId}"]`).addClass('active');
        },

        /**
         * Install plugin via AJAX
         */
        installPlugin(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $card = $button.closest('.bizzplugin-plugin-card');
            const slug = $button.data('slug');
            const file = $button.data('file');
            
            if ($card.hasClass('bizzplugin-plugin-loading')) {
                return;
            }
            
            // Add loading state
            $card.addClass('bizzplugin-plugin-loading');
            const originalText = $button.html();
            // Use DOM manipulation instead of HTML string for security
            $button.empty()
                .append($('<span>').addClass('dashicons dashicons-update'))
                .append(' ' + $('<span>').text(this.config.strings.installing || 'Installing...').text());
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_install_plugin',
                    nonce: this.config.nonce,
                    slug: slug
                },
                success: (response) => {
                    if (response.success) {
                        // Update UI to show activate button
                        $card.find('.bizzplugin-plugin-status')
                            .removeClass('bizzplugin-plugin-status-not-installed')
                            .addClass('bizzplugin-plugin-status-installed')
                            .text(this.config.strings.installed_inactive || 'Installed (Inactive)');
                        
                        // Use DOM manipulation for security
                        $button
                            .removeClass('bizzplugin-install-plugin')
                            .addClass('bizzplugin-activate-plugin')
                            .empty()
                            .append($('<span>').addClass('dashicons dashicons-yes-alt'))
                            .append(' ' + $('<span>').text(this.config.strings.activate || 'Activate').text());
                        
                        this.showNotification(response.data.message, 'success');
                    } else {
                        $button.html(originalText);
                        this.showNotification(response.data.message, 'error');
                    }
                },
                error: (xhr, status, error) => {
                    $button.html(originalText);
                    this.showNotification(this.config.strings.install_error || 'Error installing plugin', 'error');
                    console.error('Plugin installation error:', error);
                },
                complete: () => {
                    $card.removeClass('bizzplugin-plugin-loading');
                }
            });
        },

        /**
         * Activate plugin via AJAX
         */
        activatePlugin(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $card = $button.closest('.bizzplugin-plugin-card');
            const slug = $button.data('slug');
            const file = $button.data('file');
            
            if ($card.hasClass('bizzplugin-plugin-loading')) {
                return;
            }
            
            // Add loading state
            $card.addClass('bizzplugin-plugin-loading');
            const originalText = $button.html();
            // Use DOM manipulation instead of HTML string for security
            $button.empty()
                .append($('<span>').addClass('dashicons dashicons-update'))
                .append(' ' + $('<span>').text(this.config.strings.activating || 'Activating...').text());
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bizzplugin_activate_plugin',
                    nonce: this.config.nonce,
                    file: file
                },
                success: (response) => {
                    if (response.success) {
                        // Update UI to show activated state
                        $card.find('.bizzplugin-plugin-status')
                            .removeClass('bizzplugin-plugin-status-installed bizzplugin-plugin-status-not-installed')
                            .addClass('bizzplugin-plugin-status-active')
                            .text(this.config.strings.active || 'Active');
                        
                        // Use DOM manipulation for security
                        const $activatedSpan = $('<span>').addClass('bizzplugin-plugin-activated')
                            .append($('<span>').addClass('dashicons dashicons-yes'))
                            .append($('<span>').text(this.config.strings.activated || 'Activated'));
                        $card.find('.bizzplugin-plugin-actions').empty().append($activatedSpan);
                        
                        this.showNotification(response.data.message, 'success');
                    } else {
                        $button.html(originalText);
                        this.showNotification(response.data.message, 'error');
                    }
                },
                error: (xhr, status, error) => {
                    $button.html(originalText);
                    this.showNotification(this.config.strings.activate_error || 'Error activating plugin', 'error');
                    console.error('Plugin activation error:', error);
                },
                complete: () => {
                    $card.removeClass('bizzplugin-plugin-loading');
                }
            });
        },

        /**
         * Show notification
         */
        showNotification(message, type = 'success') {
            // Use DOM manipulation for security instead of string concatenation
            const $notification = $('<div>')
                .addClass('bizzplugin-notification')
                .addClass('bizzplugin-notification-' + (type === 'error' ? 'error' : type === 'info' ? 'info' : 'success'));
            
            const $message = $('<span>')
                .addClass('bizzplugin-notification-message')
                .text(message);
            
            const $closeBtn = $('<button>')
                .addClass('bizzplugin-notification-close')
                .attr('aria-label', 'Close')
                .html('&times;');
            
            $notification.append($message).append($closeBtn);
            
            $closeBtn.on('click', () => {
                $notification.remove();
            });
            
            this.elements.wrap.append($notification);
            
            setTimeout(() => {
                $notification.fadeOut(() => {
                    $notification.remove();
                });
            }, 2000);
        },
        
        /**
         * Initialize search functionality
         */
        initSearch() {
            this.searchTimeout = null;
            this.isSearchActive = false;
            this.originalNavState = null;
        },
        
        /**
         * Handle search input
         */
        handleSearchInput(e) {
            const searchTerm = $(e.currentTarget).val().trim().toLowerCase();
            const $clearBtn = $('.bizzplugin-search-clear');
            const $resultsInfo = $('.bizzplugin-search-results-info');
            
            // Clear any existing timeout
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            // Show/hide clear button
            if (searchTerm.length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
                this.clearSearchHighlights();
                $resultsInfo.hide();
                return;
            }
            
            // Debounce the search
            this.searchTimeout = setTimeout(() => {
                this.performSearch(searchTerm);
            }, 200);
        },
        
        /**
         * Perform the actual search
         */
        performSearch(searchTerm) {
            const $navItems = $('.bizzplugin-nav-item');
            const $navSubitems = $('.bizzplugin-nav-subitem');
            const $fields = $('.bizzplugin-field[data-search-keywords]');
            const $resultsInfo = $('.bizzplugin-search-results-info');
            let matchCount = 0;
            
            // Mark search as active
            this.isSearchActive = true;
            this.elements.wrap.addClass('bizzplugin-search-active');
            
            // Search in navigation items
            $navItems.each((index, item) => {
                const $item = $(item);
                const keywords = $item.data('search-keywords') || '';
                
                if (keywords.indexOf(searchTerm) !== -1) {
                    $item.removeClass('bizzplugin-search-hidden').addClass('bizzplugin-search-match');
                    matchCount++;
                    
                    // If this is a parent with subsections, show matching subsections
                    $item.find('.bizzplugin-nav-subitem').each((subIndex, subItem) => {
                        const $subItem = $(subItem);
                        const subKeywords = $subItem.data('search-keywords') || '';
                        
                        if (subKeywords.indexOf(searchTerm) !== -1) {
                            $subItem.removeClass('bizzplugin-search-hidden').addClass('bizzplugin-search-match');
                        } else {
                            $subItem.addClass('bizzplugin-search-hidden').removeClass('bizzplugin-search-match');
                        }
                    });
                    
                    // Expand the subsections if there are matches
                    $item.addClass('expanded');
                    $item.find('.bizzplugin-nav-submenu').show();
                } else {
                    $item.addClass('bizzplugin-search-hidden').removeClass('bizzplugin-search-match');
                }
            });
            
            // Search in fields (for current visible section)
            $fields.each((index, field) => {
                const $field = $(field);
                const keywords = $field.data('search-keywords') || '';
                
                if (keywords.indexOf(searchTerm) !== -1) {
                    $field.removeClass('bizzplugin-search-field-hidden').addClass('bizzplugin-search-field-match');
                } else {
                    $field.addClass('bizzplugin-search-field-hidden').removeClass('bizzplugin-search-field-match');
                }
            });
            
            // Show results info
            if (matchCount > 0) {
                let resultsText;
                if (matchCount === 1) {
                    resultsText = this.config.strings.search_result_single || '1 section found';
                } else {
                    const pluralTemplate = this.config.strings.search_results_plural || '%d sections found';
                    resultsText = pluralTemplate.replace('%d', matchCount);
                }
                $resultsInfo.text(resultsText).show();
            } else {
                $resultsInfo.text(this.config.strings.search_no_results || 'No results found').show();
            }
        },
        
        /**
         * Clear search
         */
        clearSearch(e) {
            if (e) {
                e.preventDefault();
            }
            
            const $searchInput = $('#bizzplugin-settings-search');
            $searchInput.val('').focus();
            
            this.clearSearchHighlights();
            
            $('.bizzplugin-search-clear').hide();
            $('.bizzplugin-search-results-info').hide();
        },
        
        /**
         * Clear search highlights and restore original state
         */
        clearSearchHighlights() {
            this.isSearchActive = false;
            this.elements.wrap.removeClass('bizzplugin-search-active');
            
            // Remove search classes from nav items
            $('.bizzplugin-nav-item').removeClass('bizzplugin-search-hidden bizzplugin-search-match');
            $('.bizzplugin-nav-subitem').removeClass('bizzplugin-search-hidden bizzplugin-search-match');
            
            // Remove search classes from fields
            $('.bizzplugin-field').removeClass('bizzplugin-search-field-hidden bizzplugin-search-field-match');
            
            // Restore nav submenu state based on active section
            const $activeNavItem = $('.bizzplugin-nav-item.active');
            $('.bizzplugin-nav-item').not($activeNavItem).removeClass('expanded').find('.bizzplugin-nav-submenu').hide();
            if ($activeNavItem.hasClass('has-subsections')) {
                $activeNavItem.addClass('expanded').find('.bizzplugin-nav-submenu').show();
            }
        },
        
        /**
         * Initialize repeater fields
         */
        initRepeaters() {
            const self = this;
            
            // Make repeater items sortable if jQuery UI is available
            if ($.fn.sortable) {
                $('.bizzplugin-repeater-wrap[data-sortable="1"] .bizzplugin-repeater-items').sortable({
                    handle: '.bizzplugin-repeater-item-handle',
                    axis: 'y',
                    placeholder: 'bizzplugin-repeater-placeholder',
                    update: function(event, ui) {
                        self.repeaterReindex($(this).closest('.bizzplugin-repeater-wrap'));
                        self.elements.saveButton.addClass('button-primary-changed');
                    }
                });
            }
            
            // Initialize color pickers within repeaters
            this.initRepeaterColorPickers();
        },
        
        /**
         * Initialize color pickers in repeater items
         */
        initRepeaterColorPickers() {
            const self = this;
            $('.bizzplugin-repeater-item .bizzplugin-repeater-color').not('.wp-color-picker').each(function() {
                $(this).wpColorPicker({
                    change: () => {
                        self.elements.saveButton.addClass('button-primary-changed');
                    },
                    clear: () => {
                        self.elements.saveButton.addClass('button-primary-changed');
                    }
                });
            });
        },
        
        /**
         * Add new repeater item
         */
        repeaterAddItem(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $wrap = $button.closest('.bizzplugin-repeater-wrap');
            const $items = $wrap.find('.bizzplugin-repeater-items');
            const $template = $wrap.find('.bizzplugin-repeater-template');
            const maxItems = parseInt($wrap.data('max-items')) || 0;
            const currentCount = $items.find('.bizzplugin-repeater-item').length;
            
            // Check max items limit
            if (maxItems > 0 && currentCount >= maxItems) {
                return;
            }
            
            // Get the new index
            const newIndex = currentCount;
            
            // Get template HTML and replace placeholders
            let newItemHtml = $template.html();
            newItemHtml = newItemHtml.replace(/\{\{INDEX\}\}/g, newIndex);
            newItemHtml = newItemHtml.replace(/\{\{DISPLAY_INDEX\}\}/g, newIndex + 1);
            
            // Create the new item element using jQuery.parseHTML for safer HTML parsing (no scripts)
            const $newItem = $($.parseHTML(newItemHtml, document, false));
            
            // Add to items container
            $items.append($newItem);
            
            // Initialize color pickers for new item
            this.initRepeaterColorPickers();
            
            // Update remove button visibility based on min items
            this.repeaterUpdateRemoveButtons($wrap);
            
            // Hide add button if max reached
            if (maxItems > 0 && currentCount + 1 >= maxItems) {
                $button.hide();
            }
            
            // Mark as changed
            this.elements.saveButton.addClass('button-primary-changed');
            
            // Scroll to new item
            $newItem[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        },
        
        /**
         * Remove repeater item
         */
        repeaterRemoveItem(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $item = $button.closest('.bizzplugin-repeater-item');
            const $wrap = $button.closest('.bizzplugin-repeater-wrap');
            const minItems = parseInt($wrap.data('min-items')) || 0;
            const currentCount = $wrap.find('.bizzplugin-repeater-item').length;
            
            // Check min items limit
            if (currentCount <= minItems) {
                return;
            }
            
            // Confirm removal
            if (!confirm(this.config.strings.confirm_remove_item || 'Are you sure you want to remove this item?')) {
                return;
            }
            
            // Remove the item with animation
            $item.slideUp(200, () => {
                $item.remove();
                this.repeaterReindex($wrap);
                this.repeaterUpdateRemoveButtons($wrap);
                
                // Show add button if it was hidden and allow_add is enabled
                const addNew = $wrap.data('allow-add') !== 0 && $wrap.data('allow-add') !== '0';
                const maxItems = parseInt($wrap.data('max-items')) || 0;
                const newCount = $wrap.find('.bizzplugin-repeater-item').length;
                if (addNew && (maxItems === 0 || newCount < maxItems)) {
                    $wrap.find('.bizzplugin-repeater-add').show();
                }
            });
            
            // Mark as changed
            this.elements.saveButton.addClass('button-primary-changed');
        },
        
        /**
         * Toggle repeater item content visibility
         */
        repeaterToggleItem(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const $item = $button.closest('.bizzplugin-repeater-item');
            const $content = $item.find('.bizzplugin-repeater-item-content');
            const $icon = $button.find('.dashicons');
            
            $content.slideToggle(200);
            $item.toggleClass('collapsed');
            
            // Toggle icon
            if ($item.hasClass('collapsed')) {
                $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
            } else {
                $icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
            }
        },
        
        /**
         * Update repeater item title from first text field
         */
        repeaterUpdateItemTitle(e) {
            const $input = $(e.currentTarget);
            const $item = $input.closest('.bizzplugin-repeater-item');
            const $title = $item.find('.bizzplugin-repeater-item-title');
            const index = $item.data('index');
            
            const value = $input.val().trim();
            if (value) {
                $title.text(value);
            } else {
                $title.text('Item #' + (parseInt(index) + 1));
            }
        },
        
        /**
         * Reindex repeater items after add/remove/reorder
         */
        repeaterReindex($wrap) {
            const fieldId = $wrap.data('field-id');
            const $items = $wrap.find('.bizzplugin-repeater-item');
            
            $items.each((index, item) => {
                const $item = $(item);
                const oldIndex = $item.data('index');
                
                // Skip if oldIndex is undefined or same as new index
                if (oldIndex === undefined || oldIndex === null) {
                    $item.attr('data-index', index).data('index', index);
                    return;
                }
                
                // Update data attribute
                $item.attr('data-index', index).data('index', index);
                
                // Skip regex replacement if indices are the same
                if (oldIndex === index) {
                    return;
                }
                
                // Escape special regex characters in fieldId
                const escapedFieldId = fieldId.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                
                // Update all input names and IDs
                $item.find('input, textarea, select').each((i, input) => {
                    const $input = $(input);
                    const name = $input.attr('name');
                    const id = $input.attr('id');
                    
                    if (name) {
                        // Replace the old index with new index in name
                        const newName = name.replace(new RegExp(escapedFieldId + '\\[' + oldIndex + '\\]'), fieldId + '[' + index + ']');
                        $input.attr('name', newName);
                    }
                    
                    if (id) {
                        // Replace the old index with new index in id
                        const newId = id.replace(new RegExp(escapedFieldId + '_' + oldIndex + '_'), fieldId + '_' + index + '_');
                        $input.attr('id', newId);
                    }
                });
                
                // Update labels
                $item.find('label').each((i, label) => {
                    const $label = $(label);
                    const forAttr = $label.attr('for');
                    if (forAttr) {
                        const newFor = forAttr.replace(new RegExp(escapedFieldId + '_' + oldIndex + '_'), fieldId + '_' + index + '_');
                        $label.attr('for', newFor);
                    }
                });
                
                // Update title if it was a default title
                const $title = $item.find('.bizzplugin-repeater-item-title');
                const titleText = $title.text();
                if (titleText.match(/^Item #\d+$/)) {
                    $title.text('Item #' + (index + 1));
                }
            });
        },
        
        /**
         * Update remove button visibility based on min items
         */
        repeaterUpdateRemoveButtons($wrap) {
            const minItems = parseInt($wrap.data('min-items')) || 0;
            const currentCount = $wrap.find('.bizzplugin-repeater-item').length;
            const $removeButtons = $wrap.find('.bizzplugin-repeater-item-remove');
            
            if (currentCount <= minItems) {
                $removeButtons.hide();
            } else {
                $removeButtons.show();
            }
        },
        
        /**
         * Initialize webhooks management
         */
        initWebhooks() {
            const self = this;
            const $container = $('.bizzplugin-webhooks-container');
            
            if (!$container.length) {
                return;
            }
            
            // Add webhook
            $container.on('click', '.bizzplugin-add-webhook', function(e) {
                e.preventDefault();
                self.addWebhook($container);
            });
            
            // Remove webhook
            $container.on('click', '.bizzplugin-remove-webhook', function(e) {
                e.preventDefault();
                const $item = $(this).closest('.bizzplugin-webhook-item');
                if (confirm(self.config.strings?.confirmRemove || 'Are you sure you want to remove this webhook?')) {
                    $item.slideUp(300, function() {
                        $(this).remove();
                        self.reindexWebhooks($container);
                    });
                }
            });
            
            // Auth type change
            $container.on('change', '.bizzplugin-webhook-auth-type', function() {
                const $item = $(this).closest('.bizzplugin-webhook-item');
                const authType = $(this).val();
                
                // Hide all auth fields
                $item.find('.bizzplugin-auth-fields').hide();
                
                // Show relevant auth fields
                if (authType !== 'none') {
                    $item.find('.bizzplugin-auth-' + authType).show();
                }
            });
        },
        
        /**
         * Add new webhook item
         */
        addWebhook($container) {
            const $template = $container.find('.bizzplugin-webhook-template');
            const $list = $container.find('.bizzplugin-webhooks-list');
            const newIndex = $list.find('.bizzplugin-webhook-item').length;
            
            // Clone template content (safely parse HTML without scripts)
            let templateHtml = $template.html();
            
            // Replace index placeholders
            templateHtml = templateHtml.replace(/\{\{INDEX\}\}/g, newIndex);
            templateHtml = templateHtml.replace(/\{\{DISPLAY_INDEX\}\}/g, newIndex + 1);
            
            // Use jQuery parseHTML with keepScripts=false to prevent script execution
            const parsed = $.parseHTML(templateHtml, document, false);
            const $newItem = $(parsed);
            
            // Append to list
            $list.append($newItem);
            
            // Trigger auth type change to show correct fields
            $newItem.find('.bizzplugin-webhook-auth-type').trigger('change');
            
            // Scroll to new item
            $newItem[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        },
        
        /**
         * Reindex webhook items after removal
         */
        reindexWebhooks($container) {
            const $items = $container.find('.bizzplugin-webhooks-list .bizzplugin-webhook-item');
            
            $items.each(function(index) {
                const $item = $(this);
                const oldIndex = $item.data('index');
                
                // Update data attribute
                $item.attr('data-index', index);
                $item.data('index', index);
                
                // Update header text
                $item.find('.bizzplugin-webhook-item-header strong').text('Webhook #' + (index + 1));
                
                // Update all input names and IDs
                $item.find('input, select, textarea').each(function() {
                    const $input = $(this);
                    
                    // Update name attribute
                    const name = $input.attr('name');
                    if (name) {
                        $input.attr('name', name.replace(
                            /bizzplugin_webhooks\[\d+\]/,
                            'bizzplugin_webhooks[' + index + ']'
                        ));
                    }
                    
                    // Update id attribute
                    const id = $input.attr('id');
                    if (id) {
                        $input.attr('id', id.replace(
                            /bizzplugin_webhook_\d+/,
                            'bizzplugin_webhook_' + index
                        ));
                    }
                });
                
                // Update label for attributes
                $item.find('label').each(function() {
                    const $label = $(this);
                    const forAttr = $label.attr('for');
                    if (forAttr) {
                        $label.attr('for', forAttr.replace(
                            /bizzplugin_webhook_\d+/,
                            'bizzplugin_webhook_' + index
                        ));
                    }
                });
            });
        },
        
        log(...args) {
            console.log('BizzPlugin Framework is initialized.');
            if (window?.console) console.log('[BizzPluginFramework]', ...args);
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
