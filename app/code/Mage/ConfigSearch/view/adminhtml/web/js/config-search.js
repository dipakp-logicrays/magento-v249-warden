define([
    'jquery'
], function ($) {
    'use strict';

    var loaderContainerSelector = '#page\\:main-container',
        loaderHtml = '<div class="ci-section-loader"><div class="ci-section-spinner"></div></div>';

    /**
     * Show loader overlay on target container
     */
    function showLoader() {
        var $el = $(loaderContainerSelector);

        if ($el.length && !$el.find('.ci-section-loader').length) {
            if ($el.css('position') === 'static') {
                $el.css('position', 'relative');
            }

            $el.append(loaderHtml);
        }
    }

    /**
     * Hide loader overlay from target container
     */
    function hideLoader() {
        $(loaderContainerSelector + ' .ci-section-loader').fadeOut(200, function () {
            $(this).remove();
        });
    }

    $.widget('lr.configSearch', {
        options: {
            searchUrl: '',
            scope: 'default',
            scopeCode: '',
            currentSection: '',
            debounceDelay: 300,
            minChars: 2
        },

        _activeIndex: -1,
        _xhr: null,
        _debounceTimer: null,
        _isInFixedHeader: false,

        _create: function () {
            this.$input = this.element.find('.lr-config-search-input');
            this.$clear = this.element.find('.lr-config-search-clear');
            this.$dropdown = this.element.find('.lr-config-search-dropdown');
            this.$pageMainActions = this.element.closest('.page-main-actions');

            this._bindEvents();
            this._setupStickySync();
        },

        _bindEvents: function () {
            var self = this;

            this.$input.on('input', function () {
                self._onInput();
            });

            this.$input.on('keydown', function (e) {
                self._onKeydown(e);
            });

            this.$clear.on('click', function () {
                self._clearSearch();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#lr-config-search').length) {
                    self._hideDropdown();
                }
            });
        },

        /**
         * Sync search element position with floatingHeader's _fixed state.
         * Uses a scroll listener that checks for _hidden class on parent,
         * matching floatingHeader's own scroll-based approach.
         */
        _setupStickySync: function () {
            var self = this;

            $(window).on('scroll.lrConfigSearch resize.lrConfigSearch', function () {
                self._syncPosition();
            });
        },

        _syncPosition: function () {
            var isHidden = this.$pageMainActions.hasClass('_hidden');

            if (isHidden && !this._isInFixedHeader) {
                // Parent is hidden by floatingHeader — move search into the _fixed element
                var $fixedInner = this.$pageMainActions.find('.page-actions._fixed .page-actions-inner');

                if ($fixedInner.length) {
                    $fixedInner.prepend(this.element);
                    this._isInFixedHeader = true;
                }
            } else if (!isHidden && this._isInFixedHeader) {
                // No longer sticky — move search back to original position
                var $switcher = this.$pageMainActions.find('.store-switcher');

                if ($switcher.length) {
                    $switcher.after(this.element);
                } else {
                    this.$pageMainActions.prepend(this.element);
                }

                this._isInFixedHeader = false;
            }
        },

        _onInput: function () {
            var self = this,
                query = this.$input.val().trim();

            clearTimeout(this._debounceTimer);

            if (query.length > 0) {
                this.$clear.show();
            } else {
                this.$clear.hide();
            }

            if (query.length < this.options.minChars) {
                this._hideDropdown();
                return;
            }

            this._debounceTimer = setTimeout(function () {
                self._doSearch(query);
            }, this.options.debounceDelay);
        },

        _doSearch: function (query) {
            var self = this;

            if (this._xhr) {
                this._xhr.abort();
            }

            showLoader();

            this._xhr = $.ajax({
                url: this.options.searchUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    query: query,
                    scope: this.options.scope,
                    scope_code: this.options.scopeCode
                },
                success: function (response) {
                    self._renderResults(response.results || [], query);
                },
                error: function (xhr) {
                    if (xhr.statusText !== 'abort') {
                        self._hideDropdown();
                    }
                },
                complete: function () {
                    self._xhr = null;
                    hideLoader();
                }
            });
        },

        _renderResults: function (results, query) {
            this._activeIndex = -1;

            if (!results.length) {
                this.$dropdown.html(
                    '<div class="lr-config-search-no-results">' +
                    $.mage.__('No results found') +
                    '</div>'
                );
                this._showDropdown();
                return;
            }

            var html = '';

            results.forEach(function (item) {
                var url = item.url || '#',
                    breadcrumb = this._buildBreadcrumb(item.breadcrumbParts, query),
                    typeBadge = this._getTypeBadge(item.type);

                html += '<a class="lr-config-search-result" href="' + this._escapeAttr(url) + '">' +
                    '<span class="lr-config-search-breadcrumb">' + breadcrumb + '</span>' +
                    typeBadge +
                    '</a>';
            }.bind(this));

            this.$dropdown.html(html);
            this._showDropdown();
        },

        _buildBreadcrumb: function (parts, query) {
            if (!parts || !parts.length) {
                return '';
            }

            var self = this;

            return parts.map(function (part) {
                var label = self._highlightMatch(self._escapeHtml(part.label), query),
                    cssClass = 'lr-crumb lr-crumb-' + (part.type || 'field');

                return '<span class="' + cssClass + '">' + label + '</span>';
            }).join('<span class="lr-crumb-sep"> &gt; </span>');
        },

        _getTypeBadge: function (type) {
            var labels = {
                'section': 'Section',
                'group': 'Group',
                'field': 'Field',
                'tab': 'Tab'
            };

            if (labels[type]) {
                return '<span class="lr-config-search-type lr-config-search-type-' + type + '">' +
                    labels[type] + '</span>';
            }

            return '';
        },

        _highlightMatch: function (text, query) {
            if (!query) {
                return text;
            }

            var escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'),
                regex = new RegExp('(' + escapedQuery + ')', 'gi');

            return text.replace(regex, '<mark>$1</mark>');
        },

        _onKeydown: function (e) {
            var $items = this.$dropdown.find('.lr-config-search-result');

            if (!$items.length || this.$dropdown.is(':hidden')) {
                if (e.keyCode === 27) {
                    this.$input.blur();
                }
                return;
            }

            switch (e.keyCode) {
                case 40: // Down
                    e.preventDefault();
                    this._activeIndex = Math.min(this._activeIndex + 1, $items.length - 1);
                    this._updateActiveItem($items);
                    break;

                case 38: // Up
                    e.preventDefault();
                    this._activeIndex = Math.max(this._activeIndex - 1, -1);
                    this._updateActiveItem($items);
                    break;

                case 13: // Enter
                    e.preventDefault();
                    if (this._activeIndex >= 0 && $items.eq(this._activeIndex).length) {
                        window.location.href = $items.eq(this._activeIndex).attr('href');
                    }
                    break;

                case 27: // Escape
                    e.preventDefault();
                    this._hideDropdown();
                    this.$input.blur();
                    break;
            }
        },

        _updateActiveItem: function ($items) {
            $items.removeClass('_active');
            if (this._activeIndex >= 0) {
                var $active = $items.eq(this._activeIndex).addClass('_active');

                var dropdown = this.$dropdown[0],
                    activeEl = $active[0];
                if (activeEl.offsetTop + activeEl.offsetHeight > dropdown.scrollTop + dropdown.clientHeight) {
                    dropdown.scrollTop = activeEl.offsetTop + activeEl.offsetHeight - dropdown.clientHeight;
                } else if (activeEl.offsetTop < dropdown.scrollTop) {
                    dropdown.scrollTop = activeEl.offsetTop;
                }
            }
        },

        _clearSearch: function () {
            this.$input.val('').focus();
            this.$clear.hide();
            this._hideDropdown();
        },

        _showDropdown: function () {
            this.$dropdown.show();
        },

        _hideDropdown: function () {
            this.$dropdown.hide();
            this._activeIndex = -1;
        },

        _escapeHtml: function (str) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        },

        _escapeAttr: function (str) {
            return str.replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    });

    return $.lr.configSearch;
});
