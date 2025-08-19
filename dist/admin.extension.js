(function () {
  var app = window.app || (window.app = {});
  app.initializers = app.initializers || { add: function(){} };
  app.extensionData = app.extensionData || { for: function(){ return { registerSetting: function(){ return this; }, registerPage: function(){ return this; } } } };

  app.initializers.add('funding-wallet-admin', function(){
    try {
      app.extensionData.for('funding-wallet')
        .registerSetting({
          setting: 'funding-wallet.deposit_address',
          label: 'Deposit Address (ERC20)',
          type: 'text'
        })
        .registerSetting({
          setting: 'funding-wallet.conversion_rate',
          label: 'Conversion Rate (credits per token)',
          type: 'number'
        });
    } catch (e) {
      console.error('FundingWallet admin init error', e);
    }
  });
})();