(function () {
  if (!window.app) return;
  app.initializers.add('funding-wallet', function () {
    try {
      if (app.extensionData && app.extensionData.for) {
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
      }
    } catch (e) {
      console.error('[funding-wallet] admin init failed', e);
    }
  });
})();