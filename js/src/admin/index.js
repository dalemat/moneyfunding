import app from 'flarum/admin/app';

app.initializers.add('funding-wallet', () => {
  app.extensionData
    .for('funding-wallet')
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
});
