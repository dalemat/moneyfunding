(function() {
    "use strict";
    
    var flarumCompat = flarum.core.compat;
    var extend = flarumCompat.extend;
    var Component = flarumCompat['Component'];
    var Button = flarumCompat['components/Button'];
    var Modal = flarumCompat['components/Modal'];
    var LoadingIndicator = flarumCompat['components/LoadingIndicator'];
    var Stream = flarumCompat['utils/Stream'];
    var app = flarumCompat.app;
    
    // ERC20 Fund Account Modal Component
    var FundAccountModal = function(Component) {
        function FundAccountModal() {
            Component.apply(this, arguments);
            
            this.txHash = Stream('');
            this.amount = Stream('');
            this.loading = false;
            this.submitting = false;
            this.step = 'form'; // 'form', 'pending', 'success', 'error'
            this.error = null;
            this.transaction = null;
        }
        
        if (Component) FundAccountModal.__proto__ = Component;
        FundAccountModal.prototype = Object.create(Component && Component.prototype);
        FundAccountModal.prototype.constructor = FundAccountModal;
        
        FundAccountModal.prototype.className = function() {
            return 'ERC20FundModal Modal--large';
        };
        
        FundAccountModal.prototype.title = function() {
            return app.translator.trans('cryptofund-erc20-money.forum.fund_modal.title');
        };
        
        FundAccountModal.prototype.content = function() {
            var settings = app.forum.attribute('cryptofundERC20Settings') || {};
            
            return m('div', { className: 'Modal-body' }, [
                this.step === 'form' ? this.renderForm(settings) : null,
                this.step === 'pending' ? this.renderPending() : null,
                this.step === 'success' ? this.renderSuccess() : null,
                this.step === 'error' ? this.renderError() : null
            ]);
        };
        
        FundAccountModal.prototype.renderForm = function(settings) {
            var self = this;
            var minDeposit = settings.min_deposit || 10;
            var conversionRate = settings.conversion_rate || 100;
            var walletAddress = settings.wallet_address || '';
            var contractAddress = settings.contract_address || '';
            
            if (!walletAddress || !contractAddress) {
                return m('div', { className: 'ERC20Fund-error' }, [
                    m('div', { className: 'Alert Alert--error' }, 
                        app.translator.trans('cryptofund-erc20-money.forum.fund_modal.not_configured')
                    )
                ]);
            }
            
            return m('div', { className: 'ERC20Fund-form' }, [
                m('div', { className: 'ERC20Fund-instructions' }, [
                    m('h4', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.instructions_title')),
                    m('ol', [
                        m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.step_1', { 
                            min: minDeposit 
                        })),
                        m('li', [
                            app.translator.trans('cryptofund-erc20-money.forum.fund_modal.step_2'),
                            m('div', { className: 'ERC20Fund-address' }, [
                                m('strong', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.wallet_address')),
                                m('code', { 
                                    className: 'ERC20Fund-addressCode',
                                    onclick: function() { self.copyToClipboard(walletAddress); }
                                }, walletAddress),
                                m('button', {
                                    className: 'Button Button--link',
                                    type: 'button',
                                    onclick: function() { self.copyToClipboard(walletAddress); }
                                }, app.translator.trans('cryptofund-erc20-money.forum.fund_modal.copy'))
                            ]),
                            m('div', { className: 'ERC20Fund-address' }, [
                                m('strong', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.contract_address')),
                                m('code', { 
                                    className: 'ERC20Fund-addressCode',
                                    onclick: function() { self.copyToClipboard(contractAddress); }
                                }, contractAddress),
                                m('button', {
                                    className: 'Button Button--link',
                                    type: 'button',
                                    onclick: function() { self.copyToClipboard(contractAddress); }
                                }, app.translator.trans('cryptofund-erc20-money.forum.fund_modal.copy'))
                            ])
                        ]),
                        m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.step_3')),
                        m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.step_4'))
                    ])
                ]),
                
                m('div', { className: 'Form' }, [
                    m('div', { className: 'Form-group' }, [
                        m('label', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.amount_label')),
                        m('input', {
                            className: 'FormControl',
                            type: 'number',
                            step: '0.000001',
                            min: minDeposit,
                            placeholder: minDeposit.toString(),
                            value: this.amount(),
                            oninput: function(e) { self.amount(e.target.value); }
                        }),
                        m('div', { className: 'helpText' }, 
                            app.translator.trans('cryptofund-erc20-money.forum.fund_modal.amount_help', {
                                min: minDeposit,
                                rate: conversionRate
                            })
                        ),
                        this.amount() ? m('div', { className: 'ERC20Fund-conversion' },
                            app.translator.trans('cryptofund-erc20-money.forum.fund_modal.points_estimate', {
                                points: Math.floor(parseFloat(this.amount()) * conversionRate)
                            })
                        ) : null
                    ]),
                    
                    m('div', { className: 'Form-group' }, [
                        m('label', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.tx_hash_label')),
                        m('input', {
                            className: 'FormControl',
                            type: 'text',
                            placeholder: '0x...',
                            value: this.txHash(),
                            oninput: function(e) { self.txHash(e.target.value); }
                        }),
                        m('div', { className: 'helpText' },
                            app.translator.trans('cryptofund-erc20-money.forum.fund_modal.tx_hash_help')
                        )
                    ]),
                    
                    m('div', { className: 'Form-group' }, [
                        m(Button, {
                            className: 'Button Button--primary',
                            type: 'submit',
                            loading: this.submitting,
                            disabled: !this.canSubmit(),
                            onclick: function() { self.submit(); }
                        }, app.translator.trans('cryptofund-erc20-money.forum.fund_modal.submit_button'))
                    ])
                ])
            ]);
        };
        
        FundAccountModal.prototype.renderPending = function() {
            return m('div', { className: 'ERC20Fund-pending' }, [
                m(LoadingIndicator),
                m('h4', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.pending_title')),
                m('p', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.pending_message')),
                this.transaction ? m('div', { className: 'ERC20Fund-transactionInfo' }, [
                    m('strong', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.transaction_id')),
                    ' #' + this.transaction.id
                ]) : null
            ]);
        };
        
        FundAccountModal.prototype.renderSuccess = function() {
            var self = this;
            return m('div', { className: 'ERC20Fund-success' }, [
                m('div', { className: 'Alert Alert--success' }, [
                    m('h4', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.success_title')),
                    m('p', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.success_message', {
                        points: this.transaction ? this.transaction.points : 0
                    }))
                ]),
                m(Button, {
                    className: 'Button Button--primary',
                    onclick: function() { self.hide(); }
                }, app.translator.trans('cryptofund-erc20-money.forum.fund_modal.close_button'))
            ]);
        };
        
        FundAccountModal.prototype.renderError = function() {
            var self = this;
            return m('div', { className: 'ERC20Fund-error' }, [
                m('div', { className: 'Alert Alert--error' }, [
                    m('h4', app.translator.trans('cryptofund-erc20-money.forum.fund_modal.error_title')),
                    m('p', this.error || app.translator.trans('cryptofund-erc20-money.forum.fund_modal.error_message'))
                ]),
                m('div', { className: 'ERC20Fund-actions' }, [
                    m(Button, {
                        className: 'Button Button--primary',
                        onclick: function() { 
                            self.step = 'form'; 
                            self.error = null;
                            m.redraw();
                        }
                    }, app.translator.trans('cryptofund-erc20-money.forum.fund_modal.try_again_button')),
                    m(Button, {
                        className: 'Button Button--link',
                        onclick: function() { self.hide(); }
                    }, app.translator.trans('cryptofund-erc20-money.forum.fund_modal.close_button'))
                ])
            ]);
        };
        
        FundAccountModal.prototype.canSubmit = function() {
            return this.txHash() && 
                   this.amount() && 
                   parseFloat(this.amount()) > 0 && 
                   !this.submitting;
        };
        
        FundAccountModal.prototype.submit = function() {
            var self = this;
            
            if (!this.canSubmit()) return;
            
            this.submitting = true;
            
            app.request({
                method: 'POST',
                url: app.forum.attribute('apiUrl') + '/erc20-fund',
                body: {
                    data: {
                        type: 'erc20-transactions',
                        attributes: {
                            txHash: this.txHash(),
                            amount: this.amount()
                        }
                    }
                }
            }).then(function(response) {
                self.transaction = response.data;
                self.step = 'pending';
                self.submitting = false;
                
                // Check transaction status periodically
                self.checkTransactionStatus();
                
                m.redraw();
            }).catch(function(error) {
                self.submitting = false;
                self.error = error.responseJSON && error.responseJSON.errors && error.responseJSON.errors[0] 
                    ? error.responseJSON.errors[0].detail 
                    : app.translator.trans('cryptofund-erc20-money.forum.fund_modal.submit_error');
                self.step = 'error';
                m.redraw();
            });
        };
        
        FundAccountModal.prototype.checkTransactionStatus = function() {
            var self = this;
            
            if (!this.transaction || this.step !== 'pending') return;
            
            var checkInterval = setInterval(function() {
                if (self.step !== 'pending') {
                    clearInterval(checkInterval);
                    return;
                }
                
                app.request({
                    method: 'GET',
                    url: app.forum.attribute('apiUrl') + '/erc20-transactions/' + self.transaction.id
                }).then(function(response) {
                    var transaction = response.data;
                    
                    if (transaction.attributes.status === 'confirmed') {
                        self.transaction = transaction.attributes;
                        self.step = 'success';
                        clearInterval(checkInterval);
                        
                        // Update user's money in the app state if available
                        if (app.session.user) {
                            var currentMoney = app.session.user.attribute('money') || 0;
                            app.session.user.pushAttributes({
                                money: currentMoney + transaction.attributes.points
                            });
                        }
                        
                        m.redraw();
                    } else if (transaction.attributes.status === 'failed') {
                        self.error = app.translator.trans('cryptofund-erc20-money.forum.fund_modal.transaction_failed');
                        self.step = 'error';
                        clearInterval(checkInterval);
                        m.redraw();
                    }
                }).catch(function() {
                    // Continue checking on API errors
                });
            }, 10000); // Check every 10 seconds
            
            // Stop checking after 10 minutes
            setTimeout(function() {
                clearInterval(checkInterval);
                if (self.step === 'pending') {
                    self.error = app.translator.trans('cryptofund-erc20-money.forum.fund_modal.verification_timeout');
                    self.step = 'error';
                    m.redraw();
                }
            }, 600000); // 10 minutes
        };
        
        FundAccountModal.prototype.copyToClipboard = function(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    app.alerts.show({ type: 'success' }, 
                        app.translator.trans('cryptofund-erc20-money.forum.fund_modal.copied')
                    );
                });
            } else {
                // Fallback for older browsers
                var textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    app.alerts.show({ type: 'success' }, 
                        app.translator.trans('cryptofund-erc20-money.forum.fund_modal.copied')
                    );
                } catch (err) {
                    console.error('Copy failed:', err);
                }
                
                textArea.remove();
            }
        };
        
        return FundAccountModal;
    }(Component);
    
    // ERC20 Transaction Item Component
    var ERC20TransactionItem = function(Component) {
        function ERC20TransactionItem() {
            Component.apply(this, arguments);
        }
        
        if (Component) ERC20TransactionItem.__proto__ = Component;
        ERC20TransactionItem.prototype = Object.create(Component && Component.prototype);
        ERC20TransactionItem.prototype.constructor = ERC20TransactionItem;
        
        ERC20TransactionItem.prototype.view = function() {
            var transaction = this.attrs.transaction;
            var status = transaction.attributes.status;
            
            return m('div', { className: 'ERC20Transaction' }, [
                m('div', { className: 'ERC20Transaction-info' }, [
                    m('div', { className: 'ERC20Transaction-amount' }, [
                        m('strong', transaction.attributes.amount + ' Tokens')
                    ]),
                    m('div', { className: 'ERC20Transaction-points' }, [
                        '→ ' + transaction.attributes.points + ' Points'
                    ]),
                    m('div', { className: 'ERC20Transaction-hash' }, [
                        m('a', {
                            href: 'https://etherscan.io/tx/' + transaction.attributes.txHash,
                            target: '_blank',
                            rel: 'noopener'
                        }, transaction.attributes.txHash.substring(0, 10) + '...')
                    ]),
                    m('div', { className: 'ERC20Transaction-date' }, [
                        dayjs(transaction.attributes.createdAt).format('MMM D, YYYY h:mm A')
                    ])
                ]),
                m('div', { 
                    className: 'ERC20Transaction-status ERC20Transaction-status--' + status 
                }, [
                    m('span', { className: 'ERC20Transaction-statusText' },
                        app.translator.trans('cryptofund-erc20-money.forum.transactions.status_' + status)
                    )
                ])
            ]);
        };
        
        return ERC20TransactionItem;
    }(Component);
    
    // ERC20 Transactions List Component  
    var ERC20TransactionsList = function(Component) {
        function ERC20TransactionsList() {
            Component.apply(this, arguments);
            
            this.loading = true;
            this.transactions = [];
            this.loadingMore = false;
            this.moreResults = false;
        }
        
        if (Component) ERC20TransactionsList.__proto__ = Component;
        ERC20TransactionsList.prototype = Object.create(Component && Component.prototype);
        ERC20TransactionsList.prototype.constructor = ERC20TransactionsList;
        
        ERC20TransactionsList.prototype.oninit = function() {
            Component.prototype.oninit.call(this);
            this.loadTransactions();
        };
        
        ERC20TransactionsList.prototype.view = function() {
            return m('div', { className: 'ERC20TransactionsList' }, [
                m('h3', app.translator.trans('cryptofund-erc20-money.forum.transactions.title')),
                
                this.loading ? m(LoadingIndicator) : null,
                
                !this.loading && this.transactions.length === 0 ? 
                    m('div', { className: 'ERC20TransactionsList-empty' },
                        app.translator.trans('cryptofund-erc20-money.forum.transactions.empty')
                    ) : null,
                    
                this.transactions.length > 0 ? 
                    m('div', { className: 'ERC20TransactionsList-items' },
                        this.transactions.map(function(transaction) {
                            return m(ERC20TransactionItem, { 
                                key: transaction.id, 
                                transaction: transaction 
                            });
                        })
                    ) : null,
                    
                this.moreResults && !this.loadingMore ? 
                    m(Button, {
                        className: 'Button',
                        onclick: this.loadMore.bind(this)
                    }, app.translator.trans('cryptofund-erc20-money.forum.transactions.load_more')) : null,
                    
                this.loadingMore ? m(LoadingIndicator) : null
            ]);
        };
        
        ERC20TransactionsList.prototype.loadTransactions = function() {
            var self = this;
            
            return app.request({
                method: 'GET',
                url: app.forum.attribute('apiUrl') + '/erc20-transactions'
            }).then(function(response) {
                self.transactions = response.data || [];
                self.moreResults = response.data && response.data.length >= 20;
                self.loading = false;
                m.redraw();
            }).catch(function(error) {
                console.error('Failed to load transactions:', error);
                self.loading = false;
                m.redraw();
            });
        };
        
        ERC20TransactionsList.prototype.loadMore = function() {
            var self = this;
            
            if (this.loadingMore || !this.moreResults) return;
            
            this.loadingMore = true;
            
            var offset = this.transactions.length;
            
            return app.request({
                method: 'GET',
                url: app.forum.attribute('apiUrl') + '/erc20-transactions?page[offset]=' + offset
            }).then(function(response) {
                var newTransactions = response.data || [];
                self.transactions = self.transactions.concat(newTransactions);
                self.moreResults = newTransactions.length >= 20;
                self.loadingMore = false;
                m.redraw();
            }).catch(function(error) {
                console.error('Failed to load more transactions:', error);
                self.loadingMore = false;
                m.redraw();
            });
        };
        
        return ERC20TransactionsList;
    }(Component);
    
    // Fund Account Page Component
    var FundAccountPage = function(Component) {
        function FundAccountPage() {
            Component.apply(this, arguments);
        }
        
        if (Component) FundAccountPage.__proto__ = Component;
        FundAccountPage.prototype = Object.create(Component && Component.prototype);
        FundAccountPage.prototype.constructor = FundAccountPage;
        
        FundAccountPage.prototype.view = function() {
            var settings = app.forum.attribute('cryptofundERC20Settings') || {};
            var userMoney = app.session.user ? (app.session.user.attribute('money') || 0) : 0;
            
            return m('div', { className: 'ERC20FundPage' }, [
                m('div', { className: 'container' }, [
                    m('div', { className: 'ERC20Fund-header' }, [
                        m('h2', app.translator.trans('cryptofund-erc20-money.forum.fund_page.title')),
                        m('div', { className: 'ERC20Fund-balance' }, [
                            app.translator.trans('cryptofund-erc20-money.forum.fund_page.current_balance', {
                                balance: userMoney
                            })
                        ])
                    ]),
                    
                    m('div', { className: 'ERC20Fund-content' }, [
                        m('div', { className: 'ERC20Fund-actions' }, [
                            m(Button, {
                                className: 'Button Button--primary Button--large',
                                onclick: function() {
                                    app.modal.show(FundAccountModal);
                                }
                            }, [
                                m('i', { className: 'fa fa-plus' }),
                                ' ' + app.translator.trans('cryptofund-erc20-money.forum.fund_page.add_funds_button')
                            ])
                        ]),
                        
                        m('div', { className: 'ERC20Fund-info' }, [
                            m('div', { className: 'ERC20Fund-infoBox' }, [
                                m('h4', app.translator.trans('cryptofund-erc20-money.forum.fund_page.how_it_works')),
                                m('ul', [
                                    m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_page.step_1', {
                                        min: settings.min_deposit || 10
                                    })),
                                    m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_page.step_2')),
                                    m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_page.step_3')),
                                    m('li', app.translator.trans('cryptofund-erc20-money.forum.fund_page.step_4', {
                                        rate: settings.conversion_rate || 100
                                    }))
                                ])
                            ]),
                            
                            settings.contract_address ? m('div', { className: 'ERC20Fund-addresses' }, [
                                m('div', { className: 'ERC20Fund-addressGroup' }, [
                                    m('label', app.translator.trans('cryptofund-erc20-money.forum.fund_page.contract_address')),
                                    m('code', settings.contract_address)
                                ]),
                                settings.wallet_address ? m('div', { className: 'ERC20Fund-addressGroup' }, [
                                    m('label', app.translator.trans('cryptofund-erc20-money.forum.fund_page.wallet_address')),
                                    m('code', settings.wallet_address)
                                ]) : null
                            ]) : null
                        ]),
                        
                        app.session.user ? m(ERC20TransactionsList) : null
                    ])
                ])
            ]);
        };
        
        return FundAccountPage;
    }(Component);
    
    // Initialize extension
    app.initializers.add('cryptofund-erc20-money', function() {
        // Add fund account route
        app.routes['fund-account'] = { 
            path: '/fund-account', 
            component: FundAccountPage 
        };
        
        // Add navigation link
        extend(flarumCompat['components/HeaderSecondary'], 'items', function(items) {
            if (app.session.user) {
                items.add('fund-account', 
                    m(flarumCompat['components/LinkButton'], {
                        href: app.route('fund-account'),
                        icon: 'fa fa-credit-card'
                    }, app.translator.trans('cryptofund-erc20-money.forum.nav.fund_account')),
                    10
                );
            }
        });
        
        // Add fund button to user dropdown
        extend(flarumCompat['components/SessionDropdown'], 'items', function(items) {
            if (app.session.user) {
                items.add('fund-account',
                    m(flarumCompat['components/Button'], {
                        icon: 'fas fa-coins',
                        onclick: function() {
                            app.modal.show(FundAccountModal);
                        }
                    }, app.translator.trans('cryptofund-erc20-money.forum.nav.fund_account')),
                    85
                );
            }
        });
        
        // Export components for potential extensions
        app.ERC20 = {
            FundAccountModal: FundAccountModal,
            ERC20TransactionItem: ERC20TransactionItem,
            ERC20TransactionsList: ERC20TransactionsList,
            FundAccountPage: FundAccountPage
        };
    });
    
})();
